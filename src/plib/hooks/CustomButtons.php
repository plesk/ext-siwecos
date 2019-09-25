<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

class Modules_Siwecos_CustomButtons extends \pm_Hook_CustomButtons
{
    public function getButtons()
    {
        return [
            [
                'place' => self::PLACE_DOMAIN_PROPERTIES,
                'title' => \pm_Locale::lmsg('domainHook.title'),
                'description' => \pm_Locale::lmsg('domainHook.description'),
                'link' => \pm_Context::getBaseUrl() . 'index.php/scan',
                'icon' => \pm_Context::getBaseUrl() . 'img/32x32.png',
                'contextParams' => true,
            ],
        ];
    }
}
