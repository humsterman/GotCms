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
 * @package  Library
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Gc\DocumentType;

use Gc\Layout\Model as LayoutModel;
use Gc\User\Model as UserModel;
use Gc\View\Model as ViewModel;
use Zend\Db\Sql\Insert;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2012-10-17 at 20:40:09.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 * @group Gc
 * @category Gc_Tests
 * @package  Library
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     *
     * @return void
     */
    protected $object;

    /**
     * @var ViewModel
     *
     * @return void
     */
    protected $view;

    /**
     * @var LayoutModel
     *
     * @return void
     */
    protected $layout;

    /**
     * @var UserModel
     *
     * @return void
     */
    protected $user;

    /**
     * @var Model
     *
     * @return void
     */
    protected $documentTypechildren;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->view = ViewModel::fromArray(
            array(
                'name' => 'View Name',
                'identifier' => 'View identifier',
                'description' => 'View Description',
                'content' => 'View Content'
            )
        );
        $this->view->save();

        $this->layout = LayoutModel::fromArray(
            array(
                'name' => 'Layout Name',
                'identifier' => 'Layout identifier',
                'description' => 'Layout Description',
                'content' => 'Layout Content'
            )
        );
        $this->layout->save();

        $this->user = UserModel::fromArray(
            array(
                'lastname' => 'User test',
                'firstname' => 'User test',
                'email' => 'pierre.rambaud86@gmail.com',
                'login' => 'test',
                'user_acl_role_id' => 1,
            )
        );

        $this->user->setPassword('test');
        $this->user->save();

        $this->object = Model::fromArray(
            array(
                'name' => 'Document Type Name',
                'description' => 'Document Type description',
                'icon_id' => 1,
                'defaultview_id' => $this->view->getId(),
                'user_id' => $this->user->getId(),
            )
        );
        $this->object->save();

        $this->documentTypeChildren = Model::fromArray(
            array(
                'name' => 'Document Type children Name',
                'description' => 'Document Type children description',
                'icon_id' => 1,
                'defaultview_id' => $this->view->getId(),
                'user_id' => $this->user->getId(),
            )
        );
        $this->documentTypeChildren->save();

        $insert = new Insert();
        $insert->into('document_type_dependency')
            ->values(
                array(
                    'parent_id' => $this->object->getId(),
                    'children_id' => $this->documentTypeChildren->getId()
                )
            );

        $this->object->execute($insert);

        $insert = new Insert();
        $insert->into('document_type_view')
            ->values(
                array(
                    'view_id' => $this->view->getId(),
                    'document_type_id' => $this->object->getId()
                )
            );

        $this->object->execute($insert);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->documentTypeChildren->delete();
        unset($this->documentTypeChildren);

        $this->object->delete();
        unset($this->object);

        $this->view->delete();
        unset($this->view);

        $this->layout->delete();
        unset($this->layout);

        $this->user->delete();
        unset($this->user);
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::getUser
     *
     * @return void
     */
    public function testGetUser()
    {
        $this->assertInstanceOf('Gc\User\Model', $this->object->getUser());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::addView
     *
     * @return void
     */
    public function testAddView()
    {
        $this->assertInstanceOf('Gc\DocumentType\Model', $this->object->addView($this->view->getId()));
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::addViews
     *
     * @return void
     */
    public function testAddViews()
    {

        $this->assertInstanceOf('Gc\DocumentType\Model', $this->object->addViews(array($this->view->getId())));
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::getTabs
     *
     * @return void
     */
    public function testGetTabs()
    {
        $this->assertInternalType('array', $this->object->getTabs());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::getAvailableViews
     *
     * @return void
     */
    public function testGetAvailableViews()
    {
        $this->assertInstanceOf('Gc\View\Collection', $this->object->getAvailableViews());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::getDependencies
     *
     * @return void
     */
    public function testGetDependencies()
    {
        $this->assertInternalType('array', $this->object->getDependencies());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::save
     *
     * @return void
     */
    public function testSave()
    {
        $this->object->addViews(array($this->view->getId(), 0));
        $this->object->setDependencies(array($this->object->getId()));
        $this->assertInternalType('integer', $this->object->save());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::save
     *
     * @return void
     */
    public function testSaveWithWrongValues()
    {
        $this->setExpectedException('Gc\Exception');
        $model = $this->object->fromArray(
            array(
                'name' => null,
                'description' => null,
                'icon_id' => null,
                'defaultview_id' => null,
                'user_id' => null,
            )
        );
        $this->assertFalse($model->save());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::delete
     *
     * @return void
     */
    public function testDelete()
    {
        $this->assertTrue($this->object->delete());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::delete
     *
     * @return void
     */
    public function testDeleteWithoutId()
    {
        $model = new Model();
        $this->assertFalse($model->delete());
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::fromArray
     *
     * @return void
     */
    public function testFromArray()
    {
        $model = Model::fromArray($this->object->getData());
        $this->assertInstanceOf('Gc\DocumentType\Model', $model);
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::fromId
     *
     * @return void
     */
    public function testFromId()
    {
        $model = Model::fromId($this->object->getId());
        $this->assertInstanceOf('Gc\DocumentType\Model', $model);
    }

    /**
     * Test
     *
     * @covers Gc\DocumentType\Model::fromId
     *
     * @return void
     */
    public function testFromFakeId()
    {
        $model = Model::fromId(1000);
        $this->assertFalse($model);
    }
}
