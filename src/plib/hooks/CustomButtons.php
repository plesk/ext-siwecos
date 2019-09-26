<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

use PleskExt\Siwecos\Helper;

class Modules_Siwecos_CustomButtons extends \pm_Hook_CustomButtons
{
    public function getButtons()
    {
        return [
            [
                'place'         => self::PLACE_DOMAIN_PROPERTIES,
                'title'         => \pm_Locale::lmsg('domainHook.title'),
                'description'   => \pm_Locale::lmsg('domainHook.description'),
                'link'          => \pm_Context::getBaseUrl() . 'index.php/scan',
                'icon'          => \pm_Context::getBaseUrl() . 'img/32x32.png',
                'contextParams' => true,
                'visibility'    => [
                    $this,
                    'getVisibility',
                ],
            ],
        ];
    }

    /**
     * Gets the visibility state of a specific domain
     *
     * @param array $options
     *
     * @return bool
     * @throws pm_Exception
     */
    public function getVisibility(array $options): bool
    {
        if (empty($options['site_id'])) {
            return false;
        }

        $domain = pm_Domain::getByDomainId($options['site_id']);
        $domainAvailable = Helper::domainAvailable($domain);

        if (empty($domainAvailable)) {
            return false;
        }

        if (!empty($options['alias_id'])) {
            return false;
        }

        return true;
    }
}
