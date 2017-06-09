<?php
use Fruit\Model\Users;

class UserHelper
{
    public static function checkUserExsit($phone) {
        $result = Users::findFirst('phone = ' . $phone . ' and deleteflag = 0');
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
