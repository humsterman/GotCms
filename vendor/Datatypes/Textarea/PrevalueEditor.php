<?php
/**
 * This source file is part of Got CMS.
 *
 * Got CMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Got CMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with Got CMS. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category Gc
 * @package  Datatype
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Datatypes\Textarea;

use Gc\Datatype\AbstractDatatype\AbstractPrevalueEditor,
    Zend\Form\Element;

class PrevalueEditor extends AbstractPrevalueEditor
{
    public function save()
    {
        //Save prevalue in column Datatypes\prevalue_value
        $post = $this->getRequest()->post();
        $rows = $post->get('rows', NULL);
        $cols = $post->get('cols', NULL);
        $wrap = $post->get('wrap', NULL);

        $this->setConfig(array(
            'cols' => $cols
            , 'rows' => $rows
            , 'wrap' => $wrap
        ));
    }

    public function load()
    {
        /*
            - cols     :   Number of caracters display per line
            - rows     :   Define the number of line visible in text zone
            - wrap     :   Possible values are : hard / off / soft
                                define if line returns are automatic (hard / soft)
                                or if the horizontal display if exceeded (off)
        */

        $config = $this->getConfig();
        $cols = new Element\Text('cols');
        $cols->setLabel('Cols');
        if(!empty($config['cols']))
        {
            $cols->setValue($config['cols']);
        }

        $rows = new Element\Text('rows');
        $rows->setLabel('Rows');
        if(!empty($config['rows']))
        {
            $rows->setValue($config['rows']);
        }

        $wrap = new Element\Select('wrap');
        $wrap->setLabel('Wrap');
        $wrap->addMultiOptions(array('hard'=>'hard', 'off'=>'off', 'soft'=>'soft'));
        if(!empty($config['wrap']))
        {
            $wrap->setValue($config['wrap']);
        }

        return array($cols, $rows, $wrap);
    }
}
