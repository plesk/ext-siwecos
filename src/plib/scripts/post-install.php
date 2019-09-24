<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

use PleskExt\Siwecos\Api;
use PleskExt\Siwecos\Config;

try {
    $type = $_SERVER['argv'][1] ?? 'install';

    if ($type === 'install') {
        $token = Api::registerToken();

        Config::setToken($token);
    }
} catch (\Exception $e) {
    \pm_Log::err($e);

    exit(1);
}
