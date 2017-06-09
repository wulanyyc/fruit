<?php
class OrderTest extends HttpTestCase
{
    public function testCreateDayTourOrder()
    {
        $data = [
            'products' => [
                [
                    'product_id' => 148,
                    'departure' => [
                        'date' => '2015-08-06',
                        'time' => '12:22',
                        'location' => '成都',
                    ],
                    'quantity' => 1,
                    'passengers' => [
                        [
                            'name' => '陈尼玛',
                            'dob' => '1986-01-02',
                            'email' => '123@qq.com',
                            'country' => '中国',
                            'phone' => '13900000000',
                            'weight' => '',
                            'gender' => '',
                            'passport' => '',
                            'passport_expiry_date' => '',
                            'nationality' => '中国',
                        ],
                        [
                            'name' => '张尼玛',
                            'dob' => '1990-01-02',
                            'email' => '234@qq.com',
                            'country' => '中国',
                            'phone' => '13800000000',
                            'weight' => '',
                            'gender' => '',
                            'passport' => '',
                            'passport_expiry_date' => '',
                            'nationality' => '中国',
                        ],
                        [
                            'name' => '邓尼玛',
                            'dob' => '1985-01-02',
                            'email' => '345@qq.com',
                            'country' => '中国',
                            'phone' => '18900000000',
                            'weight' => '',
                            'gender' => '',
                            'passport' => '',
                            'passport_expiry_date' => '',
                            'nationality' => '中国',
                        ],
                    ],
                    'rooms' => [
                        [
                            'adult' => 2,
                            'child' => 1,
                        ],
                    ],
                    'attributes' => [
                        [
                            'product_option_id' => 23,
                            'product_option_value_id' => [156],
                        ],
                    ],
                ],
            ],
            'contact' => [
                'name' => '陈冠希',
                'phone' => '18900000000',
                'email' => 'edison.chen@example.com',
            ],
            'score' => '',
            'coupon' => '',
        ];
        $result = $this->post('/user/458777/order/day-tour', $data);
        print_r($result['result']);
        $this->assertNotEmpty($result);
    }

    public function testCreateTicketOrder()
    {
        $data = [
            'products' => [
                [
                    'product_id' => 3358,
                    'departure' => [
                        'date' => '2015-08-21',
                        'time' => '20:00:00',
                        'location' => '成都',
                    ],
                    'quantity' => 2,
                    'passengers' => [
                        [
                            'name' => '陈尼玛',
                            'dob' => '1986-01-02',
                            'email' => '123@qq.com',
                            'country' => '中国',
                            'phone' => '13900000000',
                        ],
                        [
                            'name' => '张尼玛',
                            'dob' => '1990-01-02',
                            'email' => '234@qq.com',
                            'country' => '中国',
                            'phone' => '13800000000',
                        ],
                    ],
                    'rooms' => [
                        [
                            'adult' => 2,
                        ],
                    ],
                ],
            ],
            'contact' => [
                'name' => '陈冠希',
                'phone' => '18900000000',
                'email' => 'edison.chen@example.com',
            ],
            'score' => '',
            'coupon' => '',
        ];
        $result = $this->post('/user/458777/order/ticket', $data);
        print_r($result['result']);
        $this->assertNotEmpty($result);
    }
}
