<?php

namespace Trest\Model;

use Phalcon\Validation,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Numericality,
    Phalcon\Validation\Validator\Email;

/**
 * 旅行定制数据对象
 */
class Customize
{

    /**
     * Copy from Yiimodel PtTempOrder
     */
    const PT_WEB        = 'web';
    const PT_M          = 'mobile';
    const PT_WEB_DETAIL = 'web-detail';
    const PT_APP        = 'app';
    const PT_SYS_USRR   = 100001; //default customer_id for guest

    /**
     * 旅行定制id
     * @var int
     */
    public $pt_temp_order_id;

    /**
     * 主题
     * @var string
     */
    public $topic;

    /**
     * 持续时间
     * @var int
     */
    public $duration = 0;

    /**
     * 和谁玩
     * @var string
     */
    public $partner;

    /**
     * 成人个数
     * @var int
     */
    public $adult = 0;

    /**
     * 小孩个数
     * @var int
     */
    public $kid = 0;

    /**
     * 预算
     * @var float
     */
    public $budget;

    /**
     * 景点
     * @var string
     */
    public $attraction;

    /**
     * 邮箱
     * @var string
     * @required
     */
    public $email;

    /**
     * 手机
     * @var string
     * @required
     */
    public $phone;

    /**
     * 联系姓名
     * @var string
     * @required
     */
    public $name;

     /**
     * 用户ID
     * @var int
     */
    public $customer_id;

    public function validate()
    {
        $validator = new Validation();
        // 除email/phone/name，其他的为可选参数
        $this->topic AND $validator->add('topic', new PresenceOf());
        $this->duration AND $validator->add('duration', new Numericality());
        $this->partner AND $validator->add('partner', new PresenceOf());
        $this->adult AND $validator->add('adult', new Numericality());
        $this->kid AND $validator->add('kid', new Numericality());
        $this->budget AND $validator->add('budget', new Numericality());
        $this->attraction AND $validator->add('attraction', new PresenceOf());
        $validator->add('email', new Email());
        $validator->add('phone', new PresenceOf());
        $validator->add('name', new PresenceOf());
        $messages = $validator->validate($this);
        if (count($messages))
            throw new \BusinessException(1000, $messages[0]->getMessage());
    }
}
