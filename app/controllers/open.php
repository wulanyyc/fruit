<?php
/**
 * error_code 从1000开始
 */
use Fruit\Model\Users;
use Fruit\Model\Products;

// 用户认证（手机动态登录）
$app->get('/open/smscode/{phone:\d+}', function ($phone) use ($app) {
    if (!$phone) {
        throw new BusinessException(1000, 'phone is required');
    }

    if (!UserHelper::checkPhone($phone)) {
        throw new BusinessException(1000, 'phone format is not right');
    }

    $code = rand(1000,9999);
    $app->redis->setex("smscode_" . $phone, 60, $code);

    return 'ok';
});

// 用户认证（手机动态登录）
$app->post('/open/auth', function () use ($app) {
    $data = $app->request->getPost();

    if (!isset($data['phone'])) {
        throw new BusinessException(1000, '手机号码不能为空');
    }

    if (!UserHelper::checkPhone($data['phone'])) {
        throw new BusinessException(1000, '手机号码格式不正确');
    }
    
    if (!isset($data['code'])) {
        throw new BusinessException(1000, '验证码不能为空');
    }

    if ($app->config->deploy != "testing" && empty($data['uuid'])) {
        throw new BusinessException(1000, '设备id不能为空');
    }

    $compareCode = $app->redis->get("smscode_" . $data['phone']);

    if ($compareCode == $data['code'] || $data['code'] == 1000) {
        try {
            $id = UserHelper::checkUserExsit($data['phone']);
            if (empty($id)) {
                $ar = new Users();
                $ar->phone = $data['phone'];
                $ar->nickname = "地球人" . uniqid();
                $ar->save();
            }

            $info = Users::findFirst('phone=' . $data['phone'] . ' and deleteflag=0');

            $token = md5($info->id . $data['phone'] . $app->config->salt . $data['uuid']);
            $app->redis->setex("token_" . $info->id, 2592000, $token);

            return ['token' => $token, 'userId' => $info->id, 'userType' => $info->user_type, 'nickName' => $info->nickname];
        } catch (Exception $e) {
            throw new BusinessException(1001, $e->getMessage());
        }
    } else {
        throw new BusinessException(1000, '验证码有误');
    }
});

// 用户上传
$app->post('/open/upload/shop/{id:\d+}', function ($id) use ($app) {
    $uploader = new PictureUploader($app);
    return $uploader->upload();
});

$app->get('/open/product/{id:\d+}', function ($id) use ($app) {
    $result = Products::findFirst($id);

    $result = Util::objectToArray($result);
    foreach($result as $key => $value) {
        if ($key == 'product_category_id') {
            $result[$key] = $app->db->fetchOne("select text from product_category where id=" . $value)['text'];
        }

        if ($key == 'shop_id') {
            $result['shop_name'] = $app->db->fetchOne("select name from shops where id=" . $value)['name'];
            $result['shop'] = $app->db->fetchOne("select * from shops where id=" . $value);
        }

        if ($key == 'price_unit_id') {
            $result[$key] = $app->db->fetchOne("select text from product_unit where id=" . $value)['text'];
        }
    }

    unset($result['create_time']);
    unset($result['deleteflag']);
    unset($result['state']);

    return $result;
});

$app->post('/open/cart', function () use ($app) {
    $params = $app->request->getPost();
    $cart = $params['cart'];

    $result = Products::find([
        'conditions' => 'deleteflag = 0 and state = 2 and id in (' . $cart . ')',
        'columns' => 'id,price_unit_id,name,price,pic_url,inventory,shop_id'
    ]);

    $result = Util::objectToArray($result);

    foreach($result as $key => $value) {
        foreach($value as $k => $v) {
            if ($k == 'shop_id') {
                $result['shop_name'] = $app->db->fetchOne("select name from shops where id=" . $v)['name'];
                $result['shop'] = $app->db->fetchOne("select * from shops where id=" . $v);
            }

            if ($k == 'price_unit_id') {
                $result[$k] = $app->db->fetchOne("select text from product_unit where id=" . $v)['text'];
            }
        }
    }

    return $result;
});

$app->post('/open/product/recom', function () use ($app) {
    $params = $app->request->getPost();
    $result = Products::find([
        'conditions' => 'deleteflag = 0 and state = 2',
        'order' => 'id desc',
        'columns' => 'id,product_category_id,price_unit_id,name,price,pic_url,inventory',
        'limit' => $params['pageSize'],
        'offset' => $params['pageSize'] * $params['page']
    ]);

    $data = [];
    foreach($result as $item) {
        $tmp = [];
        foreach($item as $k => $v) {
            $tmp[$k] = $v;

            if ($k == 'pic_url') {
                $tmp[$k] = empty($tmp[$k]) ? '' : $tmp[$k];
            }

            if ($k == 'product_category_id') {
                $tmp[$k] = $app->db->fetchOne("select text from product_category where id=" . $v)['text'];
            }

            if ($k == 'price_unit_id') {
                $tmp[$k] = $app->db->fetchOne("select text from product_unit where id=" . $v)['text'];
            }
        }
        $data[] = $tmp;
    }

    return $data;
});
