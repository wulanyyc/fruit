<?php
use Fruit\Model\Users;

//用户认证（手机动态登录）
$app->get('/user/info/{id:\d+}', function ($id) use ($app) {
    $data = Users::findFirst($id);
    return $data;
});

$app->post('/user/info/update/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();
    $ar = Users::findFirst($id);
    foreach($params as $key => $value) {
        $ar->$key = $value;
    }

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});
