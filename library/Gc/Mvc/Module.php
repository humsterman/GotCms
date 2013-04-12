<?php
/**
 * This source file is part of GotCms.
 *
 * GotCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GotCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with GotCms. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category   Gc
 * @package    Library
 * @subpackage Mvc
 * @author     Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license    GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link       http://www.got-cms.com
 */

namespace Gc\Mvc;

use Application\Controller\IndexController as RenderController;
use Gc\Core\Config as CoreConfig;
use Gc\Layout;
use Gc\Session\SaveHandler\DbTableGateway as SessionTableGateway;
use Gc\Registry;
use Gc\Module\Collection as ModuleCollection;
use Zend;
use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\Config\Reader\Ini;
use Zend\EventManager\Event;
use Zend\I18n\Translator\Translator;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Config\SessionConfig;
use Zend\Session\Container as SessionContainer;
use Zend\Session\SaveHandler\DbTableGatewayOptions;
use Zend\Session\SessionManager;

/**
 * Generic Module
 *
 * @category   Gc
 * @package    Library
 * @subpackage Mvc
 */
abstract class Module
{
    /**
     * Module directory path
     *
     * @var string
     */
    protected $directory = null;

    /**
     * Module namespace
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Module configuration
     *
     * @var array
     */
    protected $config;

    /**
     * On boostrap event
     *
     * @param Event $event Event
     *
     * @return void
     */
    public function onBootstrap(Event $event)
    {
        if (!Registry::isRegistered('Translator')) {
            $translator = $event->getApplication()->getServiceManager()->get('translator');

            if (Registry::isRegistered('Db')) {
                $translator->setLocale(CoreConfig::getValue('locale'));
            }

            \Zend\Validator\AbstractValidator::setDefaultTranslator($translator);
            Registry::set('Translator', $translator);

            $uri      = '';
            $uriClass = $event->getRequest()->getUri();
            if ($uriClass->getScheme()) {
                $uri .= $uriClass->getScheme() . ':';
            }

            if ($uriClass->getHost() !== null) {
                $uri .= '//';
                $uri .= $uriClass->getHost();
                if ($uriClass->getPort() and $uriClass->getPort() != 80) {
                    $uri .= ':' . $uriClass->getPort();
                }
            }

            $event->getRequest()->setBasePath($uri);
            $event->getApplication()->getEventManager()->attach(
                MvcEvent::EVENT_RENDER_ERROR,
                array($this, 'prepareException')
            );
        }
    }

    /**
     * Initialize Render error event
     *
     * @param Event $event Event
     *
     * @return void
     */
    public function prepareException($event)
    {
        if ($event->getApplication()->getMvcEvent()->getRouteMatch()->getMatchedRouteName() === 'renderWebsite') {
            $layout = Layout\Model::fromId(CoreConfig::getValue('site_exception_layout'));
            if (!empty($layout)) {
                $templatePathStack = $event->getApplication()->getServiceManager()->get(
                    'Zend\View\Resolver\TemplatePathStack'
                );
                $templatePathStack->setUseStreamWrapper(true);
                file_put_contents($templatePathStack->resolve(RenderController::LAYOUT_NAME), $layout->getContent());
            }
        }
    }

    /**
     * Get autoloader config
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->getDir() . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->getNamespace() => $this->getDir() . '/src/' . $this->getNamespace(),
                ),
            ),
        );
    }

    /**
     * Get module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        if (empty($this->config)) {
            $config = include $this->getDir() . '/config/module.config.php';
            $ini    = new Ini();
            $routes = $ini->fromFile($this->getDir() . '/config/routes.ini');
            $routes = $routes['production'];
            if (empty($config['router']['routes'])) {
                $config['router']['routes'] = array();
            }

            if (!empty($routes['routes'])) {
                $config['router']['routes'] += $routes['routes'];
            }

            if (Registry::isRegistered('Db')) {
                if (isset($config['view_manager']['display_exceptions']) and CoreConfig::getValue('debug_is_active')) {
                    $config['view_manager']['display_not_found_reason'] = true;
                    $config['view_manager']['display_exceptions']       = true;
                }
            }

            $this->config = $config;
        }

        return $this->config;
    }

    /**
     * Get module dir
     *
     * @return string
     */
    protected function getDir()
    {
        return $this->directory;
    }

    /**
     * get module namespace
     *
     * @return string
     */
    protected function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * initiliaze database connexion for every modules
     *
     * @param ModuleManager $moduleManager Module manager
     *
     * @return void
     */
    public function init(ModuleManager $moduleManager)
    {
        if (!Registry::isRegistered('Configuration')) {
            $configPaths = $moduleManager->getEvent()->getConfigListener()->getOptions()->getConfigGlobPaths();
            if (!empty($configPaths)) {
                $config = array();
                foreach ($configPaths as $path) {
                    foreach (glob(realpath(__DIR__ . '/../../../') . '/' . $path, GLOB_BRACE) as $filename) {
                        $config += include $filename;
                    }
                }

                if (!empty($config['db'])) {
                    $dbAdapter = new DbAdapter($config['db']);
                    \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);

                    Registry::set('Configuration', $config);
                    Registry::set('Db', $dbAdapter);

                    $sessionManager = SessionContainer::getDefaultManager();
                    $sessionConfig  = $sessionManager->getConfig();
                    $sessionConfig->setStorageOption('gc_maxlifetime', CoreConfig::getValue('session_lifetime'));
                    $sessionConfig->setStorageOption('cookie_path', CoreConfig::getValue('cookie_path'));
                    $sessionConfig->setStorageOption('cookie_domain', CoreConfig::getValue('cookie_domain'));

                    if (CoreConfig::getValue('session_handler') == CoreConfig::SESSION_DATABASE) {
                        $tablegatewayConfig = new DbTableGatewayOptions(
                            array(
                                'idColumn'   => 'id',
                                'nameColumn' => 'name',
                                'modifiedColumn' => 'updated_at',
                                'lifetimeColumn' => 'lifetime',
                                'dataColumn' => 'data',
                            )
                        );

                        $sessionTable = new SessionTableGateway(
                            new TableGateway('core_session', $dbAdapter),
                            $tablegatewayConfig
                        );
                        $sessionManager->setSaveHandler($sessionTable)->start();
                    }

                    //Initialize Observers
                    $moduleCollection = new ModuleCollection();
                    $modules          = $moduleCollection->getModules();
                    foreach ($modules as $module) {
                        $className = sprintf('\\Modules\\%s\\Observer', $module->getName());
                        if (class_exists($className)) {
                            $object = new $className();
                            $object->init();
                        }
                    }
                }
            }
        }
    }
}
