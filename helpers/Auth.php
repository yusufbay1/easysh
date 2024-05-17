<?php

namespace Helpers;

use Helpers\DB;

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
