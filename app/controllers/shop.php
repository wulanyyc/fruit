<?php
use Fruit\Model\Shops;

$app->post('/shop/apply/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();
    $ar =  new Shops();
    $ar->user_id = $id;
    foreach($params as $key => $value) {
        if ($key == 'name') {
            $ar->name = $value;
        }

        if ($key == 'src') {
            $ar->shop_img_url = $value;
        }
    }

    if (!$ar->save()) {
        return $ar->getMessages();
    } else {
        return 'ok';
    }
});


