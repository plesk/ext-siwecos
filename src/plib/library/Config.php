<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

namespace PleskExt\Siwecos;

class Config
{
    public static function getToken(): string
    {
        return \pm_Settings::get('token', '');
    }

    public static function setToken(string $token): void
    {
        \pm_Settings::set('token', $token);
    }
}
