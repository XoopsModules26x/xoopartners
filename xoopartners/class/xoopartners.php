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

class Xoopartners extends XoopsObject
{
    // constructor
    public function __construct()
    {
        $this->initVar('xoopartners_id',            XOBJ_DTYPE_INT,               0, true,      11);
        $this->initVar('xoopartners_category',      XOBJ_DTYPE_INT,               0, true,      11);
        $this->initVar('xoopartners_title',         XOBJ_DTYPE_TXTBOX,           '', true,     255);
        $this->initVar('xoopartners_description',   XOBJ_DTYPE_TXTAREA,          '', false);
        $this->initVar('xoopartners_url',           XOBJ_DTYPE_TXTBOX,           '', true,     255);
        $this->initVar('xoopartners_image',         XOBJ_DTYPE_TXTBOX,  'blank.gif', false,    100);
        $this->initVar('xoopartners_order',         XOBJ_DTYPE_INT,               0, false,     3);
        $this->initVar('xoopartners_display',       XOBJ_DTYPE_INT,               1, false,     1);
        $this->initVar('xoopartners_visit',         XOBJ_DTYPE_INT,               1, false,     10);
        $this->initVar('xoopartners_view',          XOBJ_DTYPE_INT,               1, false,     10);

        // Pour autoriser le html
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    private function Xoopartners()
    {
        $this->__construct();
    }

    public function setView()
    {
        $this->setVar('xoopartners_display', 1);
        return true;
    }

    public function setHide()
    {
        $this->setVar('xoopartners_display', 0);
        return true;
    }

    public function setVisit()
    {
        $visit = $this->getVar('xoopartners_visit') + 1;
        $this->setVar('xoopartners_visit', $visit);
        return true;
    }

    public function setDisplay()
    {
        $view = $this->getVar('xoopartners_view') + 1;
        $this->setVar('xoopartners_view', $view);
        return true;
    }

    public function toArray()
    {
        $xoops = Xoops::getInstance();
        $myts = MyTextSanitizer::getInstance();
        $ret = $this->getValues();

        $ret['xoopartners_link'] =  XOOPS_URL . '/modules/xoopartners/partner.php?partner_id=' . $ret['xoopartners_id'];
        if ($ret['xoopartners_image'] != 'blank.gif') {
            $ret['xoopartners_image_link'] = XOOPS_UPLOAD_URL . '/xoopartners/partners/images/' . $ret['xoopartners_image'];
        } else {
            $ret['xoopartners_image_link'] = XOOPS_URL . '/modules/xoopartners/images/default.png';
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

class XoopartnersxoopartnersHandler extends XoopsPersistableObjectHandler
{
    private $_published = null;

    public function __construct(&$db)
    {
        parent::__construct($db, 'xoopartners', 'xoopartners', 'xoopartners_id', 'xoopartners_title');
    }


    public function renderAdminList( $category_id = 0 )
    {
        $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();
        $criteria = new CriteriaCompo();
        if ($Partners_config['xoopartners_category']['use_categories']) {
            $criteria->add( new Criteria('xoopartners_category', $category_id) ) ;
        }
        $criteria->setSort( 'xoopartners_order' );
        $criteria->setOrder( 'asc' );
        return $this->getObjects($criteria, null, false);
    }

    public function GetPartners( $category_id = 0 )
    {
        $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();
        $criteria = new CriteriaCompo();
        if ($Partners_config['xoopartners_category']['use_categories'] && $category_id >= 0) {
            $criteria->add( new Criteria('xoopartners_category', $category_id) ) ;
        }
        $criteria->add( new Criteria('xoopartners_display', 1) ) ;
        $criteria->setSort( 'xoopartners_order' );
        $criteria->setOrder( 'asc' );
        return $this->getObjects($criteria, null, false);
    }

    public function upload_images()
    {
        $xoops = Xoops::getInstance();
        $autoload = XoopsLoad::loadConfig( 'xoopartners' );
        $Partners_config = XooPartnersPreferences::getInstance()->loadConfig();

        $uploader = new XoopsMediaUploader( $xoops->path('uploads') . '/xoopartners/partners/images', $autoload['mimetypes'], $Partners_config['xoopartners_partner']['image_size'], $Partners_config['xoopartners_partner']['image_width'], $Partners_config['xoopartners_partner']['image_height']);

        $ret = array();
        foreach ( $_POST['xoops_upload_file'] as $k => $input_image ) {
            if ( $_FILES[$input_image]['tmp_name'] != '' || is_readable( $_FILES[$input_image]['tmp_name'] ) ) {
                $uploader->setTargetFileName( $this->CleanImage($_FILES[$input_image]['name']) );
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

        $string = str_replace('_', md5('xooghost'), $string);
        $string = str_replace('-', md5('xooghost'), $string);
        $string = str_replace(' ', md5('xooghost'), $string);

        $string = preg_replace('~\p{P}~','', $string);
        $string = htmlentities($string, ENT_NOQUOTES, _CHARSET);
        $string = preg_replace("~\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;~", "$1", $string);
        $string = preg_replace("~\&([A-za-z]{2})(?:lig)\;~", "$1", $string); // pour les ligatures e.g. "&oelig;"
        $string = preg_replace("~\&[^;]+\;~", "", $string); // supprime les autres caractères

        $string = str_replace(md5('xooghost'), '_' , $string);
        return $string . '.' . $path_parts['extension'];
    }
}
?>