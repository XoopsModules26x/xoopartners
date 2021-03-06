<?php

namespace XoopsModules\Xoopartners;

/**
 * Created by PhpStorm.
 * User: mamba
 * Date: 2015-10-11
 * Time: 00:08
 */
class Utility
{
    /**
     * @param $string
     * @return mixed|string
     */
    public function getMetaDescription($string)
    {
        $xoops = \Xoops::getInstance();
        $string = $xoops->module->name() . ' : ' . $string;
        $string .= '. ' . $xoops->getConfig('meta_description', 3);

        $myts = \MyTextSanitizer::getInstance();
        $string = $myts->undoHtmlSpecialChars($string);
        $string = str_replace('[breakpage]', '', $string);
        // remove html tags
        $string = strip_tags($string);

        return $string;
    }

    /**
     * @param     $string
     * @param int $limit
     * @return string
     */
    public function getMetaKeywords($string, $limit = 5)
    {
        $xoops = \Xoops::getInstance();
        $string = mb_strtolower($string) . ', ' . mb_strtolower($xoops->getConfig('meta_keywords', 3));
        $keywords = [];
        $myts = \MyTextSanitizer::getInstance();
        $string = $myts->undoHtmlSpecialChars($string);
        $string = str_replace('[breakpage]', '', $string);
        // remove html tags
        $string = strip_tags($string);

        $string = html_entity_decode($string, ENT_QUOTES);
        $search_pattern = ["\t", "\r\n", "\r", "\n", ',', '.', "'", ';', ':', ')', '(', '"', '?', '!', '{', '}', '[', ']', '<', '>', '/', '+', '_', '\\', '*', 'pagebreak', 'page'];
        $replace_pattern = [' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        $string = str_replace($search_pattern, $replace_pattern, $string);

        $tmpkeywords = explode(' ', $string);

        $tmpkeywords = array_unique($tmpkeywords);
        foreach ($tmpkeywords as $keyword) {
            if (!is_numeric($keyword) && mb_strlen(trim($keyword)) >= $limit) {
                $keywords[] = htmlentities(trim($keyword));
            }
        }

        return implode(', ', $keywords);
    }

    /**
     * @param $filename
     * @return string
     */
    public function cleanImage($filename)
    {
        $path_parts = pathinfo($filename);
        $string = $path_parts['filename'];

        $string = str_replace('_', md5('xoopartners'), $string);
        $string = str_replace('-', md5('xoopartners'), $string);
        $string = str_replace(' ', md5('xoopartners'), $string);

        $string = preg_replace('~\p{P}~', '', $string);
        $string = htmlentities($string, ENT_NOQUOTES, _CHARSET);
        $string = preg_replace("~\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;~", '$1', $string);
        $string = preg_replace("~\&([A-za-z]{2})(?:lig)\;~", '$1', $string); // pour les ligatures e.g. "&oelig;"
        $string = preg_replace("~\&[^;]+\;~", '', $string); // supprime les autres caract�res

        $string = str_replace(md5('xoopartners'), '_', $string);

        return $string . '.' . $path_parts['extension'];
    }
}
