<?php

namespace Helpers;

use Helpers\DB;

class Category
{
    public static function category($lang)
    {
        $category = ($lang === "tr") ? DB::table('category')->select('cat_id,cat_title,cat_url,cat_parent_id')->get() : DB::table('category_lang')->select('cat_id,cat_title,cat_url,cat_parent_id')->where(['lang' => $lang])->get();
        return $category;
    }
}