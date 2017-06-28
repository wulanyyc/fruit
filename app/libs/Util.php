<?php

class Util
{
    public static function arrayToObject($array)
    {
        $object = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->arrayToObject($value);
            }
            $object->$key = $value;
        }
        return $object;
    }

    public static function objectToArray($obj)
    {   
        $data = [];
        if (is_object($obj)) {
            foreach ($obj as $key => $value) {
                if (is_array($obj)) {
                    $value = $this->objectToArray($value);
                }
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * 从数组中获取数据
     * @param array $array    数据集
     * @param mixed $field    获取的键
     * @param mixed $default  无数据的默认值
     * @return mixed
     */
    public function arrayGet($array, $field, $default = null)
    {
        return isset($array[$field]) ? $array[$field] : $default;
    }
}
