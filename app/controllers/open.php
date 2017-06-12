<?php
/**
 * error_code 从1000开始
 */
use Fruit\Model\Users;
use Fruit\Model\PictureUploader;

//用户认证（手机动态登录）
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

//用户认证（手机动态登录）
$app->post('/open/auth', function () use ($app) {
    // $input = file_get_contents("php://input");
    // $data = json_decode($input, true);

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

    if ($compareCode == $data['code']) {
        try {
            $id = UserHelper::checkUserExsit($data['phone']);
            if (empty($id)) {
                $ar = new Users();
                $ar->phone = $data['phone'];
                $ar->nickname = "水果人" . uniqid();
                $ar->save();
                
                $id = Users::findFirst('phone=' . $data['phone'] . ' and deleteflag=0')->id;
            }

            $token = md5($id . $data['phone'] . $app->config->salt . $data['uuid']);
            $app->redis->setex("token_" . $token, 2592000, $id);

            return ['token' => $token, 'userId' => $id];
        } catch (Exception $e) {
            throw new BusinessException(1001, $e->getMessage());
        }
    } else {
        throw new BusinessException(1000, '验证码有误');
    }
});

//用户上传
$app->post('/open/upload/shop/{id:\d+}', function ($id) use ($app) {
    $uploader = new PictureUploader();
    $uploader->upload();
});
