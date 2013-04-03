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
 * @category Gc_Tests
 * @package  ZfModules
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Development\Controller;

use Gc\Datatype\Model as DatatypeModel;
use Gc\DocumentType\Model as DocumentTypeModel;
use Gc\Property\Model as PropertyModel;
use Gc\Tab\Model as TabModel;
use Gc\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Gc\User\Model as UserModel;
use Gc\View\Model as ViewModel;
use Zend\Session\Container as SessionContainer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-15 at 23:50:36.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 * @group    ZfModules
 * @category Gc_Tests
 * @package  ZfModules
 */
class DocumentTypeControllerTest extends AbstractHttpControllerTestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->init();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::indexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        $this->dispatch('/admin/development/document-type/list');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeList');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::createAction
     *
     * @return void
     */
    public function testCreateAction()
    {
        $this->dispatch('/admin/development/document-type/create');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeCreate');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::createAction
     *
     * @return void
     */
    public function testCreateActionWithInvalidPostData()
    {
        $this->dispatch(
            '/admin/development/document-type/create',
            'POST',
            array(
                'infos' => array(
                    'icon_id' => 3,
                    'name' => 'test'
                ),
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeCreate');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::createAction
     *
     * @return void
     */
    public function testCreateActionWithPostData()
    {
        $view_model = ViewModel::fromArray(
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'content' => 'Test',
            )
        );
        $view_model->save();

        $datatype_model = DatatypeModel::fromArray(
            array(
                'name' => 'DatatypeTest',
                'model' => 'Textstring'
            )
        );
        $datatype_model->save();

        $this->dispatch(
            '/admin/development/document-type/create',
            'POST',
            array(
                'infos' => array(
                    'icon_id' => 3,
                    'name' => 'test'
                ),
                'views' => array(
                    'default_view' => $view_model->getId()
                ),
                'properties' => array(
                    'property1'=> array(
                        'datatype' => $datatype_model->getId(),
                        'identifier' => 'test',
                        'description' => 'test',
                        'name' => 'test',
                        'tab' => 21
                    ),
                    'wrongId'=> array(
                        'datatype' => $datatype_model->getId(),
                        'identifier' => 'test',
                        'description' => 'test',
                        'name' => 'test',
                        'tab' => 21
                    ),
                ),
                'tabs' => array(
                    'tab21' => array(
                        'description' => 'test',
                        'name' => 'test',
                    ),
                    'wrongId' => array(
                        'description' => 'test',
                        'name' => 'test',
                    ),
                ),
            )
        );
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeCreate');
        $view_model->delete();
        $datatype_model->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::editAction
     *
     * @return void
     */
    public function testEditAction()
    {
        $view_model = ViewModel::fromArray(
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'content' => 'Test',
            )
        );
        $view_model->save();

        $user_model = UserModel::fromArray(
            array(
                'lastname' => 'Test',
                'firstname' => 'Test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'testlogin',
                'user_acl_role_id' => 1,
            )
        );
        $user_model->setPassword('passwordtest');
        $user_model->save();

        $datatype_model = DatatypeModel::fromArray(
            array(
                'name' => 'DatatypeTest',
                'model' => 'Textstring'
            )
        );
        $datatype_model->save();

        $document_type_model = DocumentTypeModel::fromArray(
            array(
                'name' => 'TestDocumentType',
                'icon_id' => 3,
                'default_view_id' => $view_model->getId(),
                'user_id' => $user_model->getId()
            )
        );
        $document_type_model->save();

        $tab_model = TabModel::fromArray(
            array(
                'name' => 'test',
                'description' => 'test',
                'document_type_id' => $document_type_model->getId(),
            )
        );
        $tab_model->save();

        $property_model = PropertyModel::fromArray(
            array(
                'name' => 'test',
                'identifier' => 'test',
                'description'=> 'test',
                'tab_id' => $tab_model->getId(),
                'datatype_id' => $datatype_model->getId(),
            )
        );
        $property_model->save();

        $this->dispatch('/admin/development/document-type/edit/' . $document_type_model->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeEdit');
        $document_type_model->delete();
        $property_model->delete();
        $tab_model->delete();
        $view_model->delete();
        $user_model->delete();
        $datatype_model->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::editAction
     *
     * @return void
     */
    public function testEditActionWithPostData()
    {
        $view_model = ViewModel::fromArray(
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'content' => 'Test',
            )
        );
        $view_model->save();

        $user_model = UserModel::fromArray(
            array(
                'lastname' => 'Test',
                'firstname' => 'Test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'testlogin',
                'user_acl_role_id' => 1,
            )
        );
        $user_model->setPassword('passwordtest');
        $user_model->save();

        $datatype_model = DatatypeModel::fromArray(
            array(
                'name' => 'DatatypeTest',
                'model' => 'Textstring'
            )
        );
        $datatype_model->save();

        $document_type_model = DocumentTypeModel::fromArray(
            array(
                'name' => 'TestDocumentType',
                'icon_id' => 3,
                'default_view_id' => $view_model->getId(),
                'user_id' => $user_model->getId()
            )
        );
        $document_type_model->save();

        $tab_model = TabModel::fromArray(
            array(
                'name' => 'test',
                'description' => 'test',
                'document_type_id' => $document_type_model->getId(),
            )
        );
        $tab_model->save();

        $property_model = PropertyModel::fromArray(
            array(
                'name' => 'test',
                'identifier' => 'test',
                'description'=> 'test',
                'tab_id' => $tab_model->getId(),
                'datatype_id' => $datatype_model->getId(),
            )
        );
        $property_model->save();

        $this->dispatch(
            '/admin/development/document-type/edit/' . $document_type_model->getId(),
            'POST',
            array(
                'infos' => array(
                    'icon_id' => 3,
                    'name' => 'TestDocumentType'
                ),
                'views' => array(
                    'default_view' => $view_model->getId()
                ),
                'properties' => array(
                    'property1'=> array(
                        'datatype' => $datatype_model->getId(),
                        'identifier' => 'test',
                        'description' => 'test',
                        'name' => 'test',
                        'tab' => 21
                    ),
                    'wrongId'=> array(
                        'datatype' => $datatype_model->getId(),
                        'identifier' => 'test',
                        'description' => 'test',
                        'name' => 'test',
                        'tab' => 21
                    ),
                ),
                'tabs' => array(
                    'tab21' => array(
                        'description' => 'test',
                        'name' => 'test',
                    ),
                    'wrongId' => array(
                        'description' => 'test',
                        'name' => 'test',
                    ),
                ),
            )
        );
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeEdit');
        $document_type_model->delete();
        $property_model->delete();
        $tab_model->delete();
        $view_model->delete();
        $user_model->delete();
        $datatype_model->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::editAction
     *
     * @return void
     */
    public function testEditActionWithInvalidPostData()
    {
        $view_model = ViewModel::fromArray(
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'content' => 'Test',
            )
        );
        $view_model->save();

        $user_model = UserModel::fromArray(
            array(
                'lastname' => 'Test',
                'firstname' => 'Test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'testlogin',
                'user_acl_role_id' => 1,
            )
        );
        $user_model->setPassword('passwordtest');
        $user_model->save();

        $document_type_model = DocumentTypeModel::fromArray(
            array(
                'name' => 'TestDocumentType',
                'icon_id' => 3,
                'default_view_id' => $view_model->getId(),
                'user_id' => $user_model->getId()
            )
        );
        $document_type_model->save();

        $this->dispatch(
            '/admin/development/document-type/edit/' . $document_type_model->getId(),
            'POST',
            array(
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeEdit');
        $document_type_model->delete();
        $view_model->delete();
        $user_model->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::editAction
     *
     * @return void
     */
    public function testEditActionWithWrongId()
    {
        $this->dispatch('/admin/development/document-type/edit/999');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeEdit');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deleteAction
     *
     * @return void
     */
    public function testDeleteAction()
    {
        $view_model = ViewModel::fromArray(
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'content' => 'Test',
            )
        );
        $view_model->save();

        $user_model = UserModel::fromArray(
            array(
                'lastname' => 'Test',
                'firstname' => 'Test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'testlogin',
                'user_acl_role_id' => 1,
            )
        );
        $user_model->setPassword('passwordtest');
        $user_model->save();

        $document_type_model = DocumentTypeModel::fromArray(
            array(
                'name' => 'TestDocumentType',
                'icon_id' => 3,
                'default_view_id' => $view_model->getId(),
                'user_id' => $user_model->getId()
            )
        );
        $document_type_model->save();

        $this->dispatch('/admin/development/document-type/delete/' . $document_type_model->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeDelete');
        $document_type_model->delete();
        $view_model->delete();
        $user_model->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deleteAction
     *
     * @return void
     */
    public function testDeleteActionWithWrongId()
    {
        $this->dispatch('/admin/development/document-type/delete/999');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('documentTypeDelete');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::importTabAction
     *
     * @return void
     */
    public function testImportTabAction()
    {
        $this->dispatch('/admin/development/document-type/import-tab');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('importTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addTabAction
     *
     * @return void
     */
    public function testAddTabAction()
    {
        $this->dispatch('/admin/development/document-type/create-tab');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addTabAction
     *
     * @return void
     */
    public function testAddTabActionWithPostData()
    {
        $this->dispatch(
            '/admin/development/document-type/create-tab',
            'POST',
            array(
                'name' => 'Tab',
                'description' => 'Description',
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addTabAction
     *
     * @return void
     */
    public function testAddTabActionWithDuplicatename()
    {
        $session = new SessionContainer();
        $session->offsetSet(
            'document-type',
            array(
                'tabs' => array(
                    1 => array(
                        'name' => 'Tab',
                    )
                )
            )
        );
        $this->dispatch(
            '/admin/development/document-type/create-tab',
            'POST',
            array(
                'name' => 'Tab',
                'description' => 'Description',
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deleteTabAction
     *
     * @return void
     */
    public function testDeleteTabAction()
    {
        $session = new SessionContainer();
        $session->offsetSet(
            'document-type',
            array(
                'tabs' => array(
                    1 => array(
                        'name' => 'Tab',
                    )
                )
            )
        );
        $this->dispatch(
            '/admin/development/document-type/delete-tab',
            'POST',
            array(
                'tab' => 1,
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('deleteTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deleteTabAction
     *
     * @return void
     */
    public function testDeleteTabActionWithoutPostData()
    {
        $this->dispatch('/admin/development/document-type/delete-tab');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('deleteTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deleteTabAction
     *
     * @return void
     */
    public function testDeleteTabActionWithWrongId()
    {
        $this->dispatch(
            '/admin/development/document-type/delete-tab',
            'POST',
            array(
                'tab' => 1,
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('deleteTab');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addPropertyAction
     *
     * @return void
     */
    public function testAddPropertyAction()
    {
        $session = new SessionContainer();
        $session->offsetSet(
            'document-type',
            array(
                'tabs' => array(
                    1 => array(
                        'properties' => array()
                    ),
                    2 => array(
                        'properties' => array(
                            'name' => 'Test',
                            'identifier' => 'Test',
                            'tab' => 2,
                            'description' => 'Test',
                            'is_required' => true,
                            'datatype' => 'Test',
                        )
                    )
                )
            )
        );
        $this->dispatch(
            '/admin/development/document-type/create-property',
            'POST',
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'tab' => 1,
                'description' => 'Test',
                'is_required' => true,
                'datatype' => 'Test',
            )
        );

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addProperty');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addPropertyAction
     *
     * @return void
     */
    public function testAddPropertyActionWithDuplicateProperty()
    {
        $session = new SessionContainer();
        $session->offsetSet(
            'document-type',
            array(
                'tabs' => array(
                    1 => array(
                        'properties' => array()
                    ),
                    2 => array(
                        'properties' => array(
                            array(
                                'name' => 'Test',
                                'identifier' => 'Test',
                                'tab' => 2,
                                'description' => 'Test',
                                'is_required' => true,
                                'datatype' => 'Test',
                            )
                        )
                    )
                )
            )
        );
        $this->dispatch(
            '/admin/development/document-type/create-property',
            'POST',
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'tab' => 2,
                'description' => 'Test',
                'is_required' => true,
                'datatype' => 'Test',
            )
        );

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addProperty');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addPropertyAction
     *
     * @return void
     */
    public function testAddPropertyActionWithEmptyTab()
    {
        $this->dispatch(
            '/admin/development/document-type/create-property',
            'POST',
            array(
                'name' => 'Test',
                'identifier' => 'Test',
                'tab' => '0',
                'description' => 'Test',
                'is_required' => true,
                'datatype' => 'Test',
            )
        );

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addProperty');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::addPropertyAction
     *
     * @return void
     */
    public function testAddPropertyActionWithoutPostData()
    {
        $this->dispatch('/admin/development/document-type/create-property');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('addProperty');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deletePropertyAction
     *
     * @return void
     */
    public function testDeletePropertyAction()
    {
        $session = new SessionContainer();
        $session->offsetSet(
            'document-type',
            array(
                'tabs' => array(
                    1 => array(
                        'properties' => array()
                    ),
                    2 => array(
                        'properties' => array(
                            5 => array(
                                'name' => 'Test',
                                'identifier' => 'Test',
                                'tab' => 2,
                                'description' => 'Test',
                                'is_required' => true,
                                'datatype' => 'Test',
                            )
                        )
                    )
                )
            )
        );
        $this->dispatch(
            '/admin/development/document-type/delete-property',
            'POST',
            array(
                'property' => 5,
            )
        );
        $this->dispatch('/admin/development/document-type/delete-property');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('deleteProperty');
    }

    /**
     * Test
     *
     * @covers Development\Controller\DocumentTypeController::deletePropertyAction
     *
     * @return void
     */
    public function testDeletePropertyActionWithWrongId()
    {
        $session = new SessionContainer();
        $session->offsetSet('document-type', array('tabs' => array()));
        $this->dispatch(
            '/admin/development/document-type/delete-property',
            'POST',
            array(
                'property' => 1,
            )
        );
        $this->dispatch('/admin/development/document-type/delete-property');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('DocumentTypeController');
        $this->assertControllerClass('DocumentTypeController');
        $this->assertMatchedRouteName('deleteProperty');
    }
}
