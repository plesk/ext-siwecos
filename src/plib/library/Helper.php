<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

namespace PleskExt\Siwecos;

class Helper
{
    public static function getTime(): int
    {
        return time();
    }

    public static function httpClient(string $url): \Zend_Http_Client
    {
        $info = \pm_Context::getModuleInfo();

        $client = new \Zend_Http_Client($url, [
            'timeout' => 30,
            'useragent' => "Mozilla/5.0 (compatible; SIWECOS Plesk Extension/{$info->version})",
        ]);

        $client->setAdapter(\Zend_Http_Client_Adapter_Curl::class);
        $client->setHeaders('Content-Type', 'application/json');

        return $client;
    }
}
