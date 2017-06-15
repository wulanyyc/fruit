<?php

class PictureUploader
{
    private $app;
    public $max_size = 2097152; //2M
    public $max_width = 1920;
    public $max_height = 1200;

    public static $allowed_mime_types = ['gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png' => 'image/png'];

    public function __construct($app)
    {
        $this->app = $app;
    }

    public static function isAllowedType($mime_type)
    {
        if (in_array($mime_type, self::$allowed_mime_types)) {
            return true;
        }
        return false;
    }

    public static function fileExtension($mime_type)
    {
        $extension = array_search($mime_type, self::$allowed_mime_types);
        if ($extension) {
            return ".{$extension}";
        }
        return null;
    }

    public static function parseHash($hash)
    {
        return sprintf(
            "%s/%s/%s",
            substr($hash, 0, 5),
            substr($hash, 5, 5),
            substr($hash, 10)
        );
    }

    private function parseParams($params)
    {
        if (isset($params['max_size'])) {
            $this->max_size = $params['max_size'];
        }
        if (isset($param['max_width'])) {
            $this->max_width = $params['max_width'];
        }
        if (isset($param['max_height'])) {
            $this->max_height = $params['max_height'];
        }

    }

    public function upload($params = [])
    {
        $app = $this->app;
        $errors = [];
        $rows = [];
        $files = $app->request->getUploadedFiles();
        $this->parseParams($params);

        foreach ($files as $file) {
            $original_name = $file->getName();
            $temp_name = $file->getTempName();
            $size = $file->getSize();
            if ($size <= 0) {
                $errors[$original_name] = ['code' => 1003, 'message' => 'empty file'];
                continue;
            }
            if ($size > $this->max_size) {
                $errors[$original_name] = ['code' => 1201, 'message' => 'size over limit'];
                continue;
            }

            $mime_type = $file->getRealType();
            if (!self::isAllowedType($mime_type)) {
                $errors[$original_name] = ['code' => 1202, 'message' => 'file type is not allowed'];
                continue;
            }

            $image = new \Phalcon\Image\Adapter\GD($temp_name);
            if ($image->getWidth() > $this->max_width) {
                $errors[$original_name] = ['code' => 1203, 'message' => 'width is over limit'];
                continue;
            }
            if ($image->getHeight() > $this->max_height) {
                $errors[$original_name] = ['code' => 1204, 'message' => 'height is over limit'];
                continue;
            }

            $hash = md5_file($temp_name);
            $filename = $app->config->picture->path . self::parseHash($hash) . self::fileExtension($mime_type);
            $app->logger->debug($filename);

            if (!file_exists($filename)) {
                $dir = dirname($filename);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }
                if (!$file->moveTo($filename)) {
                    $errors[$original_name] = ['code' => 1000, 'message' => 'upload failed'];
                    continue;
                }
            }
            
            $rows[] = [
                // 'hash' => $hash,
                // 'mime_type' => $mime_type,
                // 'size' => $size,
                'width' => $image->getWidth(),
                'height' => $image->getHeight(),
                // 'original_name' => urldecode(pathinfo(urlencode($original_name), PATHINFO_FILENAME)),
                'file_name' => self::parseHash($hash) . self::fileExtension($mime_type)
            ];

        }
        return ['uploaded' => $rows, 'errors' => $errors];
    }
}
