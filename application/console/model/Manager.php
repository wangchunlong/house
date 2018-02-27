<?php

namespace app\console\model;

use app\common\model\AuthManager;

class Manager extends AuthManager
{
    protected $table = 'db_auth_manager';

}