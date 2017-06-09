<?php
/**
 * Ip2city class
 *
 * @author fedora.liu@toursforfun.com
 * @example
 *
 */
require_once __DIR__ . '/vendor/autoload.php';
use GeoIp2\Database\Reader;

class Ip2city
{
    public $reader = null;
    //下面这一些地方都默认显示为人民币。
    public static $china = array('香港', '香港特别行政区', '澳门', '澳门特别行政区', '台湾', '台湾省', '中国');

    public function __construct($db = null)
    {
        if (!file_exists($db)) {
            $this->reader = null;
        } else {
            try {
                $this->reader = new Reader($db);
            } catch (\Exception $e) {
                $this->reader = null;
            }
        }
    }

    //新浪API
    public static function getCityBySina($ip)
    {
        $maxmind = new Ip2city();
        if ($maxmind->reader != null) {
            try {
                $record = $maxmind->reader->city($ip);
                return $record->city->names['zh-CN'];
            } catch (\Exception $e) {
                return '北京';
            }
        } else {
            return '北京';
        }
    }

    /**
     *  通过返回IP返回国家名
     *
     * @author:robert
     *
     * @param null $ip
     * @param string $lang
     *
     * @return string
     */
    public static function getCountryName($ip, $lang = "zh-CN")
    {
        $maxmind = new Ip2city();
        if ($maxmind->reader != null) {
            try {
                $record = $maxmind->reader->country($ip);
                return $record->country->names[$lang];
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    //新浪API
    public static function getCountryBySina($ip)
    {
        $maxmind = new Ip2city();
        if ($maxmind->reader != null) {
            try {
                //--------- remove inaccurate method by robert ------------
                //                    $record = $maxmind->reader->city($ip);
                //                    return $record->city->names['zh-CN'];
                $record = $maxmind->reader->country($ip);
                return $record->country->names['zh-CN'];
            } catch (\Exception $e) {
                return '北京';
            }
        } else {
            return '中国';
        }
    }

    //蜂巢API
    public static function getCityByFeng($ip)
    {
        $maxmind = new Ip2city();
        if ($maxmind->reader != null) {
            try {
                $record = $maxmind->reader->city($ip);
                return $record->city->names['zh-CN'];
            } catch (\Exception $e) {
                return '北京';
            }
        } else {
            return '北京';
        }
    }

    /**
     * get ip info from geoip city
     */
    public function getIpData($ip)
    {
        $data = array();
        try {
            if ($this->reader != null) {
                $record = $this->reader->city($ip);
                $data['isoCode'] = $record->country->isoCode;
                $data['enCountryName'] = $record->country->name;
                $data['cnCountryName'] = $record->country->names['zh-CN'];
                $data['enRegionName'] = $record->mostSpecificSubdivision->name;
                $data['cnRegionName'] = $record->mostSpecificSubdivision->names['zh-CN'];
                $data['enCityName'] = $record->city->name;
                $data['cnCityName'] = $record->city->names['zh-CN'];
                $data['postCode'] = $record->postal->code;
                $data['latitude'] = $record->location->latitude;
                $data['longitude'] = $record->location->longitude;
            }
        } catch (\Exception $e) {
        }
        return $data;
    }
}
