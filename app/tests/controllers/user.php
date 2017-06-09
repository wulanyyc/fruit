<?php
class UserTest extends HttpTestCase
{
    public function testAuth()
    {
        $data = [
            'name' => 'daniel.qiu@toursforfun.com',
            'password' => '111111',
        ];
        $result = $this->post('/user/auth', $data);
        $this->assertEquals(200, $result['info']['http_code']);
        $r = @json_decode($result['result'], true);
        $this->assertNotEmpty($r);
        file_put_contents('/tmp/trest', "<?php\nreturn " . var_export($r['data'], true) . ';');
    }

    public function testExpenditure()
    {
        $result = $this->get('/user/458777/expenditure');
        $this->assertNotEmpty($result);
    }

}
