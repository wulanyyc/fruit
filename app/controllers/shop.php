<?php
use Fruit\Model\Shops;
use Fruit\Model\Users;

// TODO店铺唯一性检测
$app->post('/shop/apply/{id:\d+}', function ($id) use ($app) {
    $params = $app->request->getPost();
    $ar = new Shops();
    $ar->user_id = $id;
    $ar->date = date("Ymd", time());
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
        $up = Users::findFirst($id);
        $up->user_type = 1;
        $up->save();
        return 'ok';
    }
});

$app->get('/shop/{uid:\d+}', function ($uid) use ($app) {
    $ar = Shops::findFirst("user_id=" . $uid);

    if ($ar) {
        $ret = [];
        $ret['name'] = $ar->name;
        $ret['shop_img_url'] = $ar->shop_img_url;

        return $ret;
    } else {
        throw new BusinessException(2000, '没有商铺');
    }
});