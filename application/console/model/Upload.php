<?php

namespace app\console\model;

use app\common\model\SystemUpload;

class Upload extends SystemUpload
{
    protected $table = 'db_system_upload';

    protected static function init()
    {
        /*写入事件*/
        self::event('before_delete', function ($query) {
            @unlink(ROOT_PATH . 'public' . $query->file_path);
        });
    }
}