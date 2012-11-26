<?php
/**
 * Xoopartners module
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         Xoopartners
 * @since           2.6.0
 * @author          Laurent JEN (Aka DuGris)
 * @version         $Id$
 */

defined('XOOPS_ROOT_PATH') or die('Restricted access');

class Xoopartners_category extends XoopsObject
{
    // constructor
    public function __construct()
    {
        $this->initVar('xoopartners_category_id',            XOBJ_DTYPE_INT,               0, true,      5);
        $this->initVar('xoopartners_category_parent_id',     XOBJ_DTYPE_INT,               0, false,     5);
        $this->initVar('xoopartners_category_title',         XOBJ_DTYPE_TXTBOX,           '', true,     255);
        $this->initVar('xoopartners_category_description',   XOBJ_DTYPE_TXTAREA,          '', false);
        $this->initVar('xoopartners_category_image',         XOBJ_DTYPE_TXTBOX,  'blank.gif', false,    100);
        $this->initVar('xoopartners_category_order',         XOBJ_DTYPE_INT,               0, false,     3);
        $this->initVar('xoopartners_category_online',        XOBJ_DTYPE_INT,               1, false,     1);
        $this->initVar('xoopartners_category_partners',      XOBJ_DTYPE_INT,               0, false,     10);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    private function Xoopartners_category()
    {
        $this->__construct();
    }

    public function toArray()
    {
        $xoops = Xoops::getInstance();
        $myts = MyTextSanitizer::getInstance();
        $ret = $this->getValues();
        $ret['xoopartners_category_link'] = XOOPS_URL . '/modules/xoopartners/index.php?category_id=' . $ret['xoopartners_category_id'];
        if ($ret['xoopartners_category_image'] != 'blank.gif') {
            $ret['xoopartners_category_image_link'] = XOOPS_UPLOAD_URL . '/xoopartners/categories/images/' . $ret['xoopartners_category_image'];
        } else {
            $ret['xoopartners_category_image_link'] = XOOPS_URL . '/' . $xoops->theme->resourcePath('/modules/xoopartners/images/categories.png');
        }
        return $ret;
    }

    public function CleanVarsForDB()
    {
        $system = System::getInstance();
        foreach ( $this->getValues() as $k => $v ) {
            if ( $k != 'dohtml' ) {
                if ( $this->vars[$k]['data_type'] == XOBJ_DTYPE_STIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_MTIME || $this->vars[$k]['data_type'] == XOBJ_DTYPE_LTIME) {
                    $value = $system->CleanVars($_POST[$k], 'date', date('Y-m-d'), 'date') + $system->CleanVars($_POST[$k], 'time', date('u'), 'int');
                    $this->setVar( $k,  isset( $_POST[$k] ) ? $value : $v );
                } elseif ( $this->vars[$k]['data_type'] == XOBJ_DTYPE_INT ) {
                    $value = $system->CleanVars($_POST, $k, $v, 'int');
                    $this->setVar( $k,  $value );
                } elseif ( $this->vars[$k]['data_type'] == XOBJ_DTYPE_ARRAY ) {
                    $value = $system->CleanVars($_POST, $k, $v, 'array');
                    $this->setVar( $k,  $value );
                } else {
                    $value = $system->CleanVars($_POST, $k, $v, 'string');
                    $this->setVar( $k,  $value );
                }
            }
        }
    }
}

class Xoopartnersxoopartners_categoriesHandler extends XoopsPersistableObjectHandler
{
    private $_published = null;

    public function __construct(&$db)
    {
        parent::__construct($db, 'xoopartners_categories', 'xoopartners_category', 'xoopartners_category_id', 'xoopartners_category_title');
    }


    public function renderAdminList( $category_parent_id = 0)
    {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria('xoopartners_category_parent_id', $category_parent_id) ) ;
        $criteria->setSort( 'xoopartners_category_order' );
        $criteria->setOrder( 'asc' );

        $categories = $this->getObjects($criteria, true, false);
        foreach ($categories as $k => $category) {
            $categories[$k]['categories'] = $this->renderAdminList($category['xoopartners_category_id']);
        }
        return $categories;
    }

    public function GetCategories( $category_parent_id = 0, $main = true, $sub = true)
    {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria('xoopartners_category_parent_id', $category_parent_id) ) ;
        $criteria->add( new Criteria('xoopartners_category_online', 1) ) ;
        $criteria->add( new Criteria('xoopartners_category_partners', 0, '!=') ) ;
        $criteria->setSort( 'xoopartners_category_order' );
        $criteria->setOrder( 'asc' );

        $categories = $this->getObjects($criteria, true, false);
        if ($sub) {
            foreach ($categories as $k => $category) {
                $categories[$k]['categories'] = $this->GetCategories($category['xoopartners_category_id'], false);
            }
        }

        if ( $main ) {
            $categories[0]['xoopartners_category_id'] = 0;
            $categories[0]['xoopartners_category_parent_id'] = 0;
            $categories[0]['xoopartners_category_title'] = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_description'] = _XOO_PARTNERS_CATEGORY_NONE;
            $categories[0]['xoopartners_category_image'] = 'blank.gif';
            $categories[0]['xoopartners_category_order'] = 0;
            $categories[0]['xoopartners_category_online'] = 1;
            $categories[0]['xoopartners_category_link'] = 'index.php';
            $categories[0]['xoopartners_category_image_link'] = XOOPS_URL . '/modules/xoopartners/images/default.png';;
            ksort($categories);
        }
        return $categories;
    }

    public function getParents( $category_id )
    {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria('xoopartners_category_id', $category_id) ) ;
        $ret = $this->getObjects($criteria, false, false);
        if ( count($ret) ) {
            if ($ret[0]['xoopartners_category_parent_id'] != 0) {
                $tmp = $this->getParents( $ret[0]['xoopartners_category_parent_id'] );
                $ret[] = $tmp[0];
            }
        }
        return $ret;
    }

    public function SetOnline( $category_id )
    {
        if ($category_id != 0){
            $category = $this->get( $category_id );
            if ( $category->getVar('xoopartners_category_online') == 1 ) {
                $category->setVar('xoopartners_category_online', 0);
            } else {
                $category->setVar('xoopartners_category_online', 1);
            }
            $this->insert( $category );
            return true;
        }
        return false;
    }

    public function Addpartner( $category_id )
    {
        if ($category_id != 0){
            $category = $this->get( $category_id );
            $partner = $category->getVar('xoopartners_category_partners') + 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert( $category );
            return true;
        }
    }

    public function Delpartner( $category_id )
    {
        if ($category_id != 0){
            $category = $this->get( $category_id );
            $partner = $category->getVar('xoopartners_category_partners') - 1;
            $category->setVar('xoopartners_category_partners', $partner);
            $this->insert( $category );
            return true;
        }
    }

    public function upload_images( $image_name )
    {
        $xoops = Xoops::getInstance();
        $autoload = XoopsLoad::loadConfig( 'xoopartners' );
        $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();

        $uploader = new XoopsMediaUploader( $xoops->path('uploads') . '/xoopartners/categories/images', $autoload['mimetypes'], $Partners_config['xoopartners_category']['image_size'], $Partners_config['xoopartners_category']['image_width'], $Partners_config['xoopartners_category']['image_height']);

        $ret = array();
        foreach ( $_POST['xoops_upload_file'] as $k => $input_image ) {
            if ( $_FILES[$input_image]['tmp_name'] != '' || is_readable( $_FILES[$input_image]['tmp_name'] ) ) {
                $path_parts = pathinfo( $_FILES[$input_image]['name'] );
                $uploader->setTargetFileName( $this->CleanImage( strtolower($image_name . '.' . $path_parts['extension']) ) );
                if ( $uploader->fetchMedia( $_POST['xoops_upload_file'][$k] ) ) {
                    if ( $uploader->upload() ) {
                        $ret[$input_image] = array( 'filename' => $uploader->getSavedFileName(), 'error' => false, 'message' => '');
                    } else {
                        $ret[$input_image] = array( 'filename' => $_FILES[$input_image]['name'], 'error' => true , 'message' => $uploader->getErrors() );
                    }
                } else {
                    $ret[$input_image] = array( 'filename' => $_FILES[$input_image]['name'], 'error' => true , 'message' => $uploader->getErrors() );
                }
            }
        }
        return $ret;
    }

    public function CleanImage( $filename )
    {
        $path_parts = pathinfo( $filename );
        $string = $path_parts['filename'];

        $string = str_replace('_', md5('xoopartners'), $string);
        $string = str_replace('-', md5('xoopartners'), $string);
        $string = str_replace(' ', md5('xoopartners'), $string);

        $string = preg_replace('~\p{P}~','', $string);
        $string = htmlentities($string, ENT_NOQUOTES, _CHARSET);
        $string = preg_replace("~\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;~", "$1", $string);
        $string = preg_replace("~\&([A-za-z]{2})(?:lig)\;~", "$1", $string); // pour les ligatures e.g. "&oelig;"
        $string = preg_replace("~\&[^;]+\;~", "", $string); // supprime les autres caractères

        $string = str_replace(md5('xoopartners'), '_' , $string);
        return $string . '.' . $path_parts['extension'];
    }

    public function makeSelectBox( $name, $id , $none=true, $onchange='')
    {
        echo "<select name='".$name."'";
        if ( $onchange != '' ) {
            echo " onchange='" .$onchange . "';";
        }
        echo ">\n";
        if ( $none ) {
            echo "<option value='0'>" . _XOO_PARTNERS_CATEGORY_NONE . "</option>\n";
        }
        $this->makeSelectOptions( $this->renderAdminList(), $id );

        echo "</select>";
    }

    public function makeSelectOptions( $datas, $id )
    {
        static $level;
        if ( count($datas) != 0 ) {
            foreach( $datas as $key => $data ) {
                $level = isset($level) ? $level + 1: 0;
                $style = 5 * $level;
                $selected = "";
                if ( $data['xoopartners_category_id'] == $id ) {
                    $selected = " selected='selected'";
                }
                    echo "<option value='" . $data['xoopartners_category_id'] . "' " . $selected . ">" . str_repeat("-", $style) . " " . $data['xoopartners_category_title'] . "</option>\n";

                if ( count($data["categories"]) != 0 ) {
                    $this->makeSelectOptions($data["categories"], $id);
                }
                $level = $level -1;
            }
        }
    }
}
?>