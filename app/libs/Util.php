<?php
use Services\Provider\Manager;
use Services\Resource\Currency;
use Trest\Model\Product;

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

    public function currencyConvert($amount, $from, $to)
    {
        $currency = new Currency();
        if (is_numeric($from)) {
            $provider = (new Manager())->get($from);
            if (!$provider) {
                throw new BusinessException(10000, 'Invalid currency code');
            }
            $currency = $currency->get($provider['provider']['currency_id']);
            if (!$currency) {
                throw new BusinessException(10000, 'Invalid currency code');
            }
            $from = $currency['code'];
        }

        return $currency->convert($amount, $from, $to);
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
