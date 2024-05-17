<?php

namespace Helpers;

use Helpers\DB;
use Helpers\Category;

class InfinityCat
{
    public static function categoryTree($elements, $parent_id = 0)
    {
        $branch = array();
        if ($elements) {
            foreach ($elements as $element) {
                if ($element->cat_parent_id == $parent_id) {
                    $children = self::categoryTree($elements, $element->cat_id);
                    $element->children = $children ?? array();
                    $branch[] = $element;
                }
            }
        }
        return $branch;
    }

    public static function categoryDraw($items, $lang, $langUrl, $langLink)
    {
        $output = '';
        foreach ($items as $item) {
            $icon = sizeof($item->children) ? '<i class="fas fa-arrow-right-long menu-icon-is"></i>' : '';
            $div = sizeof($item->children) ? '<div class="menu-subs menu-mega menu-column-4">' : '';
            $divClose = sizeof($item->children) ? '</div>' : '';
            $output .= '<li class="menu-item-has-children"><a href="./' . $langUrl . $item->cat_url . '"> ' . $item->cat_title . ' </a> ' . $icon  . $div . ' ';
            if (sizeof($item->children) > 0) {
                foreach ($item->children as $child) {
                    $output .= '<div class="list-item"><h4 class="title"><a href="./' . $langUrl . $child->cat_url . '"> ' . $child->cat_title . '</a> <span class="title-spans">+</span></h4>';

                    $output .= '<ul>';
                    foreach ($child->children as $childs) {

                        $output .= ' <li><a href="./' . $langUrl . $childs->cat_url . '">' . $childs->cat_title . '</a></li>';
                    }
                    $output .= '</ul></div>';
                }
            }
            $cat_img = DB::table('cat_img')->where(['cat_id' => $item->cat_id, 'lang' => $lang])->get();
            if ($cat_img) {
                $output .= '<div class="cat-image-wrap">';
                foreach ($cat_img as $IMG) {
                    $output .= '
                        <div class="cat-image">
                            <a href="./' . $IMG->img_url . '">
                                <div class="wrapper-img"><img class="lozad" data-src="' . $IMG->img_path . '" alt="' . $IMG->img_name . '"></div>
                                <div class="wrapper-text fs-12 text-center"><span>' . $IMG->img_name . '</span></div>
                            </a>
                        </div>
                    ';
                }
                $output .= '</div>';
            }
            $output .= $divClose . ' </li>';
        }
        return $output;
    }

    public static function viewCategory($lang, $langUrl, $langLink)
    {
        $category = Category::category($lang);
        $categoryDraw = self::categoryDraw(self::categoryTree($category), $lang, $langUrl, $langLink);
        return $categoryDraw;
    }

}
