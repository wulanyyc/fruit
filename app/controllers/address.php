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

$app->get('/address/{id:\d+}', function ($id) use ($app) {
    $result = $app->db->fetchOne("select * from user_address where id=" . $id);

    return $result;
});

$app->post('/address/add/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();

    if ($params['default'] == true) {
        $app->db->query("update user_address set default_flag = 0 where user_id=" . $id);
    }

    $ar = new UserAddress();
    $ar->user_id = $id;
    $ar->receive_name = $params['name'];
    $ar->receive_phone = $params['phone'];
    $ar->receive_detail = $params['detail'];
    $ar->receive_region = $params['region'];
    $ar->default_flag = $params['default'] ? 1 : 0;

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});

$app->post('/address/update/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();

    if ($params['default'] == true) {
        $app->db->query("update user_address set default_flag = 0 where user_id=" . $id);
    }

    $ar = UserAddress::findFirst($params['id']);
    $ar->user_id = $id;
    $ar->receive_name = $params['name'];
    $ar->receive_phone = $params['phone'];
    $ar->receive_detail = $params['detail'];
    $ar->receive_region = $params['region'];
    $ar->default_flag = ($params['default'] == true) ? 1 : 0;

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});


$app->get('/address/list/{id:\d+}', function ($id) use ($app) {
    $result = UserAddress::find([
        'conditions' => 'user_id = ' . $id,
        'order' => 'id desc',
        'columns' => 'id,default_flag,receive_name,receive_phone,receive_region,receive_detail',
    ]);

    $data = [];
    foreach($result as $item) {
        $tmp = [];
        foreach($item as $k => $v) {
            $tmp[$k] = $v;
        }
        $data[] = $tmp;
    }

    return $data;
});