<?php
use Fruit\Model\Shops;

// TODO店铺唯一性检测
$app->get('/product/category', function () use ($app) {
    $phql = "SELECT id,text FROM product_category where deleteflag=0";
    $categorys = $app->db->fetchAll($phql);

    $ret = [];
    foreach($categorys as $item) {
        $tmp = [];
        $tmp['id'] = $item->id;
        $tmp['text'] = $item->text;
        $ret[] = $tmp;
    }

    return $ret;
});