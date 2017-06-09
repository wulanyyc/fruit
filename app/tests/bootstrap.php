<?php
date_default_timezone_set('UTC');

class HttpTestCase extends \PHPUnit_Framework_TestCase
{

    private $test_url;
    private $accept_language = 'en';
    private $headers = [];

    public function __construct()
    {
        parent::__construct();
        if (defined('TEST_URL')) {
            $this->test_url = TEST_URL;
        }
        if (isset($_SERVER['TEST_URL'])) {
            $this->test_url = $_SERVER['TEST_URL'];
        }
        if (!$this->test_url) {
            throw new \Exception('test url is not defined');
        }
    }

    public function setHeader($data)
    {
        if (is_array($data)) {
            $this->headers = array_merge($this->headers, $data);
        } else {
            $this->headers[] = $data;
        }
    }
    /**
     * @param string $uri
     * @param string $method
     * @param array $args
     * @return array
     */
    private function request($uri, $method = 'GET', $args = null)
    {
        $ch = curl_init($this->test_url . $uri);
        if (file_exists('/tmp/trest')) {
            $tmp = include('/tmp/trest');
            $this->setHeader('HTTP-X-ACCESS-TOKEN: ' . $tmp['access_token']);
        }
        $this->setHeader('Expect: ');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (in_array($method, ['GET', 'DELETE']) && $args) {
            curl_setopt($ch, CURLOPT_URL, "{$this->test_url}{$uri}?" . http_build_query($args));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
            //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        }
        //curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        $data = [];
        $data['result'] = curl_exec($ch);
        $data['info'] = curl_getinfo($ch);
        $data['error'] = curl_error($ch);
        curl_close($ch);
        $this->assertEmpty($data['error']);
        return $data;
    }

    public function setLanguage($accept_language)
    {
        if (!in_array($accept_language, ['en', 'zh', 'es'])) {
            $this->throwException(new \Exception('unavailable language'));
        }
        $this->accept_language = $accept_language;
    }

    /**
     * @param string $uri
     * @param array $args
     * @return array
     */
    public function get($uri, $args = [])
    {
        return $this->request($uri, 'GET', $args);
    }

    /**
     * @param string $uri
     * @param array $args
     * @return array
     */
    public function post($uri, $args = [])
    {
        return $this->request($uri, 'POST', $args);
    }

    /**
     * @param string $uri
     * @param array $args
     * @return array
     */
    public function put($uri, $args = [])
    {
        return $this->request($uri, 'PUT', $args);
    }

    /**
     * @param string $uri
     * @param array $args
     * @return array
     */
    public function patch($uri, $args = [])
    {
        return $this->request($uri, 'PATCH', $args);
    }

    /**
     * @param $uri
     * @param $file_path
     * @param string $method
     * @return array
     * @desc 使用 PUT 方式上传文件有疑问，如果表单使用 query_string 接口将无法获取上传文件
     * 如果不使用 query_string 接口将无法获取 PUT 参数
     */
    public function upload($uri, $file_path, $method = 'POST')
    {
        return $this->request($uri, $method, ['file' => curl_file_create($file_path)]);
    }

    /**
     * @param string $uri
     * @param array $args
     * @return array
     */
    public function delete($uri, $args = [])
    {
        return $this->request($uri, 'DELETE', http_build_query($args));
    }
}
