<?php
// use Fruit\Model\Shops;
use Fruit\Model\Products;

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

$app->post('/product/add/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();
    $shopId = UserHelper::getShopId($id);

    $ar = new Products();
    $ar->user_id = $id;
    $ar->name = $params['name'];
    $ar->product_category_id = $params['category'];
    $ar->price = $params['price'];
    $ar->shop_id = $shopId;
    $ar->pic_url = $params['pic'];
    $ar->inventory = $params['sellnum'];
    $ar->date = date("Ymd", time());

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});

$app->get('/product/list/{id:\d+}', function ($id) use ($app) {
    $data = Products::find([
        'user_id' => $id,
        'audit_flag' => 1,
        'deleteflag' => 0
    ])->fetchAll();

    return $data;
});