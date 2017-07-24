<?php
use Fruit\Model\Users;
use Fruit\Model\UserAddress;

//用户认证（手机动态登录）
$app->get('/address/region/list', function () use ($app) {
    $result = $app->db->fetchAll("select name,id from china_county where region_parent_id=510100");

    $data = [];
    foreach($result as $item) {
        $tmp = [];
        $tmp['text'] = $item['name'];
        $tmp['id'] = $item['id'];
        $data[] = $tmp;
    }

    return $data;
});

$app->post('/address/add/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();

    $ar = new UserAddress();
    $ar->user_id = $id;
    $ar->receive_name = $params['name'];
    $ar->receive_phone = $params['phone'];
    $ar->receive_detail = $params['detail'];
    $ar->receive_region = $params['region'];

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});
