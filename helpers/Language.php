<?php

namespace Helpers;

use Helpers\DB;
use Helpers\IFunction;

class Language
{
    private static $langData = null;

    public static function lang($ex = 2, $default = 'tr')
    {
        $_SESSION['LANG'] = $default;
        if (isset($_SESSION['ISOFTUSER'])) {
            $userLang = DB::table('user_category')->select('lang')->where(['company_id' => $_SESSION['ISOFTUSER']->company_id])->limit(0, 1)->first();
            $_SESSION['LANG'] = $userLang->lang;
        } else {
            $langs = DB::table('isoft_language')->select('lang_name')->get();
            if (isset($_SERVER['REQUEST_URI'])) {
                $explode = explode('/', $_SERVER['REQUEST_URI']);
                $uri = IFunction::security($explode[$ex]);
                if ($uri != null && $uri != "" && count($langs) > 1) {
                    foreach ($langs as $item) {
                        if ($item->lang_name == $uri) {
                            $_SESSION['LANG'] = $uri;
                            break;
                        }
                    }
                }
            }
        }
        return $_SESSION['LANG'];
    }

    private static function loadLangData($path)
    {
        if (self::$langData === null) {
            $json_file = file_get_contents($path);
            self::$langData = json_decode($json_file, true);
        }
    }

    public static function staticLang($lang, $path = "./assets/langy/lang.json")
    {
        self::loadLangData($path);
        if (isset(self::$langData[$lang])) {
            $langData = self::$langData[$lang];
            return $langData;
        }
        return array();
    }

    public static function homeLangPath()
    {
        $langChange = '';
        if (isset($_SESSION['ISOFTUSER'])) {
            # code...
        } else {
            $homeLang = DB::table('isoft_language')->get();
            if (count($homeLang) > 1) {
                foreach ($homeLang as $LCH) {
                    $langName = ($LCH->lang_name == 'tr') ? '' : $LCH->lang_name;
                    $langChange .= "
                     <li>
                         <a class=\"flag flag-$LCH->lang_name text-white fs-12 ml\" href='./$langName'>
                             <span>" . $LCH->lang_def . "</span>
                         </a>
                     </li>";
                }
            }
        }
        return $langChange;
    }

    public static function langChange($tableTr, $tableLang, $urlLang, $key, $value, $tableBool = false, $getLang = false, $cat_id = "")
    {
        $langChange = '';
        if (isset($_SESSION['ISOFTUSER'])) {
            # code...
        } else {
            $langs = DB::table('isoft_language')->get();
            $logEs = '';
            $a = '';
            $cats = $cat_id != "" ?  '-' . $cat_id : null;
            foreach ($langs as $languages) {
                @$langus = $languages->lang_name;
                if ($tableBool != false) {
                    $wheres = ($langus !== "tr") ? ["$key" => IFunction::numberFilter($value), "lang" => $langus] : ["$key" => IFunction::numberFilter($value)];
                    $table = ($langus !== "tr") ? $tableLang : $tableTr;
                    $caturlTr = DB::table($table)->select($urlLang)->where($wheres)->limit(1)->first();
                    @$url = $caturlTr->$urlLang . $cats;
                } else {
                    $caturlTr = self::staticLang($langus);
                    @$url = IFunction::hotLink($caturlTr[$urlLang]);
                }
                $langLink = ($langus == "tr") ? "" : $langus . "/";
                $a = $getLang ?? "class=\"sort_a flag flag-$langus'\" text-white fs-12 ml";
                $logEs .= "<li>
                            <a href='" . $langLink . $url . "' class='sort_a flag flag-$langus text-white fs-12 ml' data-lang='$langus' $a >
                                <span>" . ($langus !== 'tr' ?  $languages->lang_def : 'TÜRKÇE') . "</span>
                            </a>
                    </li>";
            }
            $langChange = $logEs;
        }

        return $langChange;
    }

    public static function langs()
    {
        $langs = DB::table('isoft_language')->get();
        return $langs;
    }
}