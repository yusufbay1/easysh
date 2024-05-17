<?php

namespace Helpers;

use Helpers\DB;

class IFunction
{
    public static function csrf()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $input = '<input type="hidden" name="_token" value="' . $token . '" />';
        return $input;
    }
    public static function csfrControl($token)
    {
        return isset($_SESSION['csrf_token']) && $_SESSION['csrf_token'] === $token;
    }
    private static function numberCharacter($value)
    {
        $process = preg_replace("/[^0-9]/", "", $value);
        return $process;
    }
    public static function numberFilter($value)
    {
        $trim = trim($value);
        $stripTags = strip_tags($trim, ENT_QUOTES);
        $htmlspecialchars = htmlspecialchars($stripTags, ENT_QUOTES);
        $clear = self::numberCharacter($htmlspecialchars);
        return $clear;
    }
    public static function price($value)
    {
        $process = number_format($value, "2", ",", ".");
        return $process;
    }
    public static function security($value)
    {
        $value = str_replace("'", '&#39;', $value);
        $value = str_replace('"', '&#34;', $value);
        $value = str_replace('or', 'or', $value);
        $value = str_replace('and', 'and', $value);
        $trim = trim($value);
        $stripTags = strip_tags($trim);
        $entQuate = htmlspecialchars($stripTags, ENT_QUOTES);
        $characters = str_split($entQuate);
        $encodedChars = array_map(function ($char) {
            if (!in_array($char, ['\'', '"'], true)) {
                return $char;
            }
            $ascii = ord($char);
            return '&#' . $ascii . ';';
        }, $characters);
        $result = implode('', $encodedChars);
        return $result;
    }
    public static function revert($value)
    {
        $decoded = preg_replace_callback('/&#(\d+);/', function ($matches) {
            return chr($matches[1]);
        }, $value);
        $result = htmlspecialchars_decode($decoded, ENT_QUOTES);
        return $result;
    }
    public static function hotLink($value)
    {
        $content = trim($value);
        $turkce_karakterler = array("ç", "Ç", "ğ", "Ğ", "ı", "İ", "ö", "Ö", "ş", "Ş", "ü", "Ü");
        $ingilizce_karakterler = array("c", "c", "g", "g", "i", "i", "o", "o", "s", "s", "u", "u");
        $content = str_replace($turkce_karakterler, $ingilizce_karakterler, $content);
        $content = mb_strtolower($content, "UTF-8");
        $content = preg_replace("/[^a-z0-9.]/", "-", $content);
        $content = preg_replace("/-+/", "-", $content);
        $content = trim($content, "-");
        return $content;
    }
    public static function generateOrderNumber($length = 20)
    {
        $uniqueId = uniqid();
        return 'IS-' . substr($uniqueId, 0, $length);
    }
    public static function randomNumber()
    {
        $one = rand(1, 9999) . '.';
        $two = rand(1, 9999) . '.';
        $theree = rand(1, 9999) . '.';
        $four = rand(1, 9999);
        $number = $one . $two . $theree . $four;
        return $number;
    }
    public static function default($lang)
    {
        $seo = DB::table('isoft_default_seo')->where(['lang' => $lang])->limit(1)->first();
        return $seo;
    }

   
}

// class Language
// {
//     private static $langData = null;

//     public static function lang($ex = 2, $default = 'tr')
//     {
//         $_SESSION['LANG'] = $default;
//         if (isset($_SESSION['ISOFTUSER'])) {
//             $userLang = DB::table('user_category')->select('lang')->where(['company_id' => $_SESSION['ISOFTUSER']->company_id])->limit(0, 1)->first();
//             $_SESSION['LANG'] = $userLang->lang;
//         } else {
//             $langs = DB::table('isoft_language')->select('lang_name')->get();
//             if (isset($_SERVER['REQUEST_URI'])) {
//                 $explode = explode('/', $_SERVER['REQUEST_URI']);
//                 $uri = IFunction::security($explode[$ex]);
//                 if ($uri != null && $uri != "" && count($langs) > 1) {
//                     foreach ($langs as $item) {
//                         if ($item->lang_name == $uri) {
//                             $_SESSION['LANG'] = $uri;
//                             break;
//                         }
//                     }
//                 }
//             }
//         }
//         return $_SESSION['LANG'];
//     }

//     private static function loadLangData($path)
//     {
//         if (self::$langData === null) {
//             $json_file = file_get_contents($path);
//             self::$langData = json_decode($json_file, true);
//         }
//     }

//     public static function staticLang($lang, $path = "./assets/langy/lang.json")
//     {
//         self::loadLangData($path);
//         if (isset(self::$langData[$lang])) {
//             $langData = self::$langData[$lang];
//             return $langData;
//         }
//         return array();
//     }

//     public static function homeLangPath()
//     {
//         $langChange = '';
//         if (isset($_SESSION['ISOFTUSER'])) {
//             # code...
//         } else {
//             $homeLang = DB::table('isoft_language')->get();
//             if (count($homeLang) > 1) {
//                 foreach ($homeLang as $LCH) {
//                     $langName = ($LCH->lang_name == 'tr') ? '' : $LCH->lang_name;
//                     $langChange .= "
//                      <li>
//                          <a class=\"flag flag-$LCH->lang_name text-white fs-12 ml\" href='./$langName'>
//                              <span>" . $LCH->lang_def . "</span>
//                          </a>
//                      </li>";
//                 }
//             }
//         }
//         return $langChange;
//     }

//     public static function langChange($tableTr, $tableLang, $urlLang, $key, $value, $tableBool = false, $getLang = false, $cat_id = "")
//     {
//         $langChange = '';
//         if (isset($_SESSION['ISOFTUSER'])) {
//             # code...
//         } else {
//             $langs = DB::table('isoft_language')->get();
//             $logEs = '';
//             $a = '';
//             $cats = $cat_id != "" ?  '-' . $cat_id : null;
//             foreach ($langs as $languages) {
//                 @$langus = $languages->lang_name;
//                 if ($tableBool != false) {
//                     $wheres = ($langus !== "tr") ? ["$key" => IFunction::numberFilter($value), "lang" => $langus] : ["$key" => IFunction::numberFilter($value)];
//                     $table = ($langus !== "tr") ? $tableLang : $tableTr;
//                     $caturlTr = DB::table($table)->select($urlLang)->where($wheres)->limit(1)->first();
//                     @$url = $caturlTr->$urlLang . $cats;
//                 } else {
//                     $caturlTr = Language::staticLang($langus);
//                     @$url = IFunction::hotLink($caturlTr[$urlLang]);
//                 }
//                 $langLink = ($langus == "tr") ? "" : $langus . "/";
//                 $a = $getLang ?? "class=\"sort_a flag flag-$langus'\" text-white fs-12 ml";
//                 $logEs .= "<li>
//                             <a href='" . $langLink . $url . "' class='sort_a flag flag-$langus text-white fs-12 ml' data-lang='$langus' $a >
//                                 <span>" . ($langus !== 'tr' ?  $languages->lang_def : 'TÜRKÇE') . "</span>
//                             </a>
//                     </li>";
//             }
//             $langChange = $logEs;
//         }

//         return $langChange;
//     }

//     public static function langs()
//     {
//         $langs = DB::table('isoft_language')->get();
//         return $langs;
//     }
// }

// class DeCodeHelper
// {
//     public static function jsonDecode($jsonData)
//     {
//         $data = json_decode($jsonData, true);
//         return $data;
//     }

//     protected function jsonEncode($data)
//     {
//         header('Content-Type: application/json');
//         return json_encode($data);
//     }

//     protected static function table($lang, $mainTable, $langTable)
//     {
//         $table = ($lang === "tr") ? $mainTable : $langTable;
//         return $table;
//     }

//     protected static function data()
//     {
//         $data = json_decode(file_get_contents('php://input'), true);
//         return $data;
//     }

//     protected static function langData($lang)
//     {
//         $langData = Language::staticLang($lang);
//         return $langData;
//     }

//     public static function httpCode($code, $status, $message)
//     {
//         http_response_code($code);
//         return json_encode([$status => $message]);
//     }

//     protected static function tokenVisible($data)
//     {
//         return !!($data && $data == DB::$apiToken);
//     }
// }

class Auth extends DB
{
    public static $user_id = null;
    public static $admin_id = null;
    public static function loginUser($mail, $password, $sessName)
    {
        $result = self::table('user')
            ->where(['user_mail' => $mail, 'user_status' => 1])
            ->limit(1)
            ->first();

        if ($result && password_verify($password, $result->user_password)) {
            self::$user_id = $result->user_id;
            unset($result->user_password);
            $_SESSION[$sessName] = $result;
            return true;
        } else {
            return false;
        }
    }

    public static function loginAdmin($mail, $password, $sessName)
    {
        $result = self::table('admin')
            ->where(['admin_mail' => $mail, 'admin_status' => 1])
            ->limit(1)
            ->first();
        if ($result && password_verify($password, $result->admin_password)) {
            self::$admin_id = $result->admin_id;
            $_SESSION[$sessName] = $result;
            return true;
        } else {
            return false;
        }
    }
}
