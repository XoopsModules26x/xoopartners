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

class Xoopaginate
{    private $prev = false;
    private $next = false;
    private $first = false;
    private $last  = false;

    public function __construct($total_items, $items_perpage, $current_start, $start_name = 'start', $extra_arg = '', $offset = 1)
    {        $this->total = intval($total_items);
        $this->perpage = intval($items_perpage);
        $this->current = intval($current_start);
        $this->extra = $extra_arg;
        if ($extra_arg != '' && (substr($extra_arg, - 5) != '&amp;' || substr($extra_arg, - 1) != '&')) {
            $this->extra = '&amp;' . $extra_arg;
        }
        $this->url = $_SERVER['PHP_SELF'] . '?' . trim($start_name) . '=';
        $this->offset = intval( $offset );

        $this->render();
    }

    public function getValue( $key )
    {        return $this->$key;    }

    public function display()
    {        echo $this->render();
    }
    private function render()
    {        $xoops = xoops::getinstance();
        $xoops->tpl()->assign('xoopaginate', $this);

        $total_pages = ceil($this->total / $this->perpage);
        $i = 0;
        if ($this->total != 0 && $this->perpage != 0) {
            if ( ($this->current - $this->perpage) >= 0 ) {                $this->prev = $this->url . ($this->current - $this->perpage) . $this->extra;
                $this->first = $this->url . 0 . $this->extra;
            }

            $counter = 1;
            $current_page = intval(floor(($this->current + $this->perpage) / $this->perpage));
            while ($counter <= $total_pages) {                if ($counter == $current_page) {                    $pages[$i]['text'] = $counter;
                    $pages[$i]['link'] = $this->url . (($counter - 1) * $this->perpage) . $this->extra;
                    $pages[$i]['value'] = (($counter - 1) * $this->perpage);

                    $i++;
                } elseif (($counter > $current_page - $this->offset && $counter < $current_page + $this->offset) || $counter == 1 || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $this->offset) {                        $pages[$i]['link'] = false;
                        $pages[$i]['text'] = '...';
                        $pages[$i]['value'] = '.';
                        $i++;
                    }
                    $pages[$i]['text'] = $counter;
                    $pages[$i]['link'] = $this->url . (($counter - 1) * $this->perpage) . $this->extra;
                    $pages[$i]['value'] = (($counter - 1) * $this->perpage);
                    $i++;
                    if ($counter == 1 && $current_page > 1 + $this->offset) {
                        $pages[$i]['link'] = false;
                        $pages[$i]['text'] = '...';
                        $pages[$i]['value'] = '.';
                        $i++;
                    }
                }
                $counter++;
            }

            if ( ($this->current + $this->perpage) < $this->total ) {
                $this->next = $this->url . ($this->current + $this->perpage) . $this->extra;
                $this->last  = $this->url . (($counter - 2) * $this->perpage) . $this->extra;
            }
        }
        if ($this->total >= $this->perpage && ceil($this->total / $this->perpage) > 1) {            $xoops->tpl()->assign('xoopages', $pages);
        }
        return $xoops->tpl()->fetch('module:xoopartners|xoo_paginate.html');
    }
}
?>