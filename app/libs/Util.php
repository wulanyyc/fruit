<?php

class Util
{
    public function arrayToObject($array)
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
