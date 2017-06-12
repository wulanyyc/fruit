<?php
use Fruit\Model\Users;
use Fruit\Model\Shops;

$app->post('/shop/apply/{id:\d+}', function ($id) use ($app) {
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


