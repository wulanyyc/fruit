<?php
use Fruits\Model\Users;

class UserHelper
{
    public static function checkExsit($phone) {
        $result = Users::findFirst(["phone" => $phone, "deleteflag" => 0]);
        if ($result && $result->id) {
            return $result->id;
        }

        return '';
    }

    public static function checkPhone($phone) {
        if (preg_match('/^1[34578]\d{9}$/', $phone)) {
            return true;
        }

        return false;
    }
}
