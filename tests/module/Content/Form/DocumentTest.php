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

namespace Content\Form;

use Gc\Datatype\Model as DatatypeModel;
use Gc\Document\Model as DocumentModel;
use Gc\DocumentType\Model as DocumentTypeModel;
use Gc\Layout\Model as LayoutModel;
use Gc\Property\Model as PropertyModel;
use Gc\Tab\Model as TabModel;
use Gc\User\Model as UserModel;
use Gc\View\Model as ViewModel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-15 at 23:50:20.
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 * @group    ZfModules
 * @category Gc_Tests
 * @package  ZfModules
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Document
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->object = new Document;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    protected function tearDown()
    {
        unset($this->object);
    }

    /**
     * Test
     *
     * @covers Content\Form\Document::init
     *
     * @return void
     */
    public function testInit()
    {
        $this->assertNull($this->object->init());
    }

    /**
     * Test
     *
     * @covers Content\Form\Document::isValid
     *
     * @return void
     */
    public function testIsValid()
    {
        $this->object->setData(array());
        $this->assertFalse($this->object->isValid());
    }

    /**
     * Test
     *
     * @covers Content\Form\Document::load
     * @covers Content\Form\Document::isValid
     *
     * @return void
     */
    public function testLoad()
    {
        $user = UserModel::fromArray(
            array(
                'lastname' => 'Test',
                'firstname' => 'Test',
                'email' => 'test@test.com',
                'login' => 'test-user-model',
                'user_acl_role_id' => 1,
            )
        );

        $user->setPassword('test-user-model-password');
        $user->save();

        $view = ViewModel::fromArray(
            array(
                'name' => 'View',
                'identifier' => 'ViewIdentifier',
                'description' => 'Description',
                'content' => '',
            )
        );
        $view->save();

        $layout = LayoutModel::fromArray(
            array(
                'name' => 'View',
                'identifier' => 'ViewIdentifier',
                'description' => 'Description',
                'content' => '',
            )
        );
        $layout->save();

        $documenttype = DocumentTypeModel::fromArray(
            array(
                'name' => 'DocumentType',
                'description' => 'description',
                'icon_id' => 1,
                'default_view_id' => $view->getId(),
                'user_id' => $user->getId(),
            )
        );
        $documenttype->save();
        $documenttype->setDependencies(array($documenttype->getId()));
        $documenttype->save();

        $datatype = DatatypeModel::fromArray(
            array(
                'name' => 'DatatypeTest',
                'model' => 'Textstring'
            )
        );
        $datatype->save();

        $tab = TabModel::fromArray(
            array(
                'name' => 'test',
                'description' => 'test',
                'document_type_id' => $documenttype->getId(),
            )
        );
        $tab->save();

        $property = PropertyModel::fromArray(
            array(
                'name' => 'test',
                'identifier' => 'test',
                'description'=> 'test',
                'tab_id' => $tab->getId(),
                'datatype_id' => $datatype->getId(),
                'is_required' => true
            )
        );
        $property->save();

        $document = DocumentModel::fromArray(
            array(
                'name' => 'test',
                'url_key' => '',
                'status' => DocumentModel::STATUS_ENABLE,
                'user_id' => $user->getId(),
                'document_type_id' => $documenttype->getId(),
                'view_id' => $view->getId(),
                'layout_id' => $layout->getId(),
                'parent_id' => null,
            )
        );
        $document->save();

        $this->assertNull($this->object->load($document));
        $this->object->setData(array());
        $this->assertFalse($this->object->isValid());

        $document->delete();
        $documenttype->delete();
        $property->delete();
        $tab->delete();
        $view->delete();
        $layout->delete();
        $user->delete();
        $datatype->delete();
        unset($documenttype);
        unset($document);
        unset($property);
        unset($tab);
        unset($view);
        unset($layout);
        unset($user);
        unset($datatype);
    }
}
