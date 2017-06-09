<?php

class BusinessException extends Exception
{

    protected $code;
    protected $data;

    public function __construct($code, $message = 'Unknown business error', $data = [])
    {   
        if (preg_match("/[\x{4e00}-\x{9fa5}]+/u", $message)) {
            $message = json_encode($message);
        }

        parent::__construct($message);

        $this->code = $code;
        $this->data = $data;
    }

    public function toJson()
    {
        return @json_encode([
            'code' => $this->code,
            'message' => $this->getMessage(),
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);
    }
}

require __DIR__ . '/autoload.php';

function confirm_jsonp()
{
    if (isset($_GET['cb']) && $_GET['cb']) {
        return $_GET['cb'];
    }
    if (isset($_GET['callback']) && $_GET['callback']) {
        return $_GET['callback'];
    }
    return false;
}

function load_modules($app)
{
    $found = false;
    if (isset($_GET['_url']) && $_GET['_url']) {
        $guess = explode('/', $_GET['_url']);
        while (!empty($guess)) {
            $filepath = sprintf('%s/controllers%s.php', __DIR__, implode('/', $guess));
            if (file_exists($filepath)) {
                include $filepath;
                $found = true;
                break;
            }
            array_pop($guess);
        }
    }

    if (!$found) {
        include __DIR__ . '/controllers/index.php';
    }
}

function init_app($di)
{
    $app = new Phalcon\Mvc\Micro($di);

    $app->notFound(function () use ($app) {
        //deal ajax CORS
        if ($app->request->isOptions()) {
            $app->response->setStatusCode(200);
            $app->response->setHeader('Access-Control-Allow-Origin', '*');
            $app->response->setHeader('Access-Control-Allow-Headers', 'X-ACCESS-TOKEN');
            $app->response->setHeader('Access-Control-Allow-Methods', 'GET,POST,PUT,DELETE,OPTIONS');
            $app->response->sendHeaders();
            return;
        }
        raise_not_found($app);
    });

    $app->before(function () use ($app) {
        ob_start();

        if (is_debugging($app)) {
            return true;
        }

        $requestUrl = $_SERVER['REQUEST_URI'];
        if (preg_match('/^\/login\/.*/', $requestUrl) || $requestUrl == "/") {
            return true;
        }

        is_valid_access($app);
    });

    $app->after(function () use ($app) {
        if ($ctx = ob_get_clean()) {
            send_response($app, $ctx);
        } else {
            $ctx = @json_encode([
                'code' => 1,
                'message' => 'success',
                'data' => $app->getReturnedValue(),
            ], JSON_UNESCAPED_UNICODE);
            send_response($app, $ctx);
        }
    });

    $app->error(function ($exception) use ($app) {
        $app->logger->error($exception->getMessage());
        if ($exception instanceof BusinessException) {
            $ctx = $exception->toJson();
            send_response($app, $ctx);
            return false;
        }
        
        $app->response->setStatusCode(500);
        if (is_debugging($app)) {
            send_response($app, json_encode(['error' => $exception->getMessage()]));
        } else {
            send_response($app, json_encode(['error' => 'service error']));
        }
    });

    $app->get('/version', function () {
        return '1.0.1';
    });

    load_modules($app);

    $app->handle();
}

function send_response($app, $ctx)
{
    if ($method = confirm_jsonp()) {
        $type = 'text/javascript; charset=utf8';
        $ctx = sprintf('%s(%s);', $method, $ctx);
    } else {
        $type = 'application/json; charset=utf8';
    }
    $app->response->setHeader('Content-Type', $type);
    $app->response->setHeader('Content-Length', strlen($ctx));
    $app->response->setHeader('Access-Control-Allow-Origin', '*');
    $app->response->sendHeaders();
    echo $ctx;
}

function is_debugging($app)
{
    if ($app->config->env !== 'production') {
        $tdd = $app->request->getHeader('HTTP_X_TDD');
        if ($tdd && $tdd === 'cff246d8280983c0c55c2f1280998919') {
            return true;
        }
    }
}

function is_valid_access($app)
{
    if ($app->request->getMethod() != 'OPTIONS') {
        $access_token = $app->request->getHeader('X_ACCESS_TOKEN');
        if (!$access_token) {
            raise_bad_request($app);
        }

        $user_id = $app->redis->get("token_" . $access_token);
        if (empty($user_id)) {
            raise_unauthorized($app);
        }
        $app->user_id = $user_id;
    }
}

function login_check($app)
{
    if (isset($_GET['_url']) && preg_match('#^/user/\d+#', $_GET['_url'])) {
        $router_params = $app->getRouter()->getParams();
        if (isset($router_params['user_id']) && $app->user_id != $router_params['user_id']) {
            $ctx = json_encode([
                'code' => 4001,
                'message' => 'User Authorize Failed'
            ]);
            send_response($app, $ctx);
            exit;
        }
    }
}

function raise_errors($app, $code, $text = '')
{
    static $HTTP_STATUS = [
        404 => 'Not Found',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
    ];

    $status = isset($HTTP_STATUS[$code]) ? $HTTP_STATUS[$code] : '';
    if (!$text) {
        $text = $status;
    }

    $ctx = @json_encode($text);
    $app->response->setStatusCode($code, $status);
    $app->response->setHeader('Content-Type', 'application/json; charset=utf8');
    $app->response->setHeader('Content-Length', strlen($ctx));
    $app->response->setHeader('Access-Control-Allow-Origin', '*');
    $app->response->sendHeaders();
    echo $ctx;
    exit;
}

function raise_not_found($app)
{
    raise_errors($app, 404);
}

function raise_bad_request($app)
{
    raise_errors($app, 400);
}

function raise_unauthorized($app)
{
    raise_errors($app, 401);
}

function raise_forbidden($app)
{
    raise_errors($app, 403);
}

set_error_handler(function ($err_no, $err_str, $err_file, $err_line) {
    $message = sprintf('%s in %s on line %d', $err_str, $err_file, $err_line);
    throw new \Exception($message, $err_no);
}, E_ALL);

$config = load_config();
init_loader();
$di = init_dependency_injection($config);
init_app($di);
