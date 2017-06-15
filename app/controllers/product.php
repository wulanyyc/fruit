<?php
use Fruit\Model\Shops;

// TODO店铺唯一性检测
$app->get('/product/category', function () use ($app) {
    $phql = "SELECT id,text FROM product_category where deleteflag=0";
    $categorys = $app->db->fetchAll($phql);

    return $categorys;
});

$app->get('/product/unit', function () use ($app) {
    $phql = "SELECT id,text FROM product_unit";
    $categorys = $app->db->fetchAll($phql);

    return $categorys;
});