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
            'timeout'   => 30,
            'useragent' => "Mozilla/5.0 (compatible; SIWECOS Plesk Extension/{$info->version})",
        ]);

        $client->setAdapter(\Zend_Http_Client_Adapter_Curl::class);
        $client->setHeaders('Content-Type', 'application/json');

        return $client;
    }

    /**
     * Checks the availability of the requested domain
     *
     * @param \pm_Domain $domainObject
     *
     * @return bool
     */
    public static function domainAvailable($domainObject): bool
    {
        if (!$domainObject->isActive() || !$domainObject->hasHosting()) {
            return false;
        }

        if ($domainObject->isSuspended() || $domainObject->isDisabled()) {
            return false;
        }

        try {
            if (!self::isResolvingToPlesk($domainObject)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether domain name is resolving to Plesk server directly - if not then the domain name is pointing to another
     * IP address what happens if the user is using a proxy server or the domain is not mapping the Plesk server at all
     *
     * @param \pm_Domain $domainObject
     *
     * @return bool
     * @throws \Exception
     */
    private static function isResolvingToPlesk($domainObject): bool
    {
        $ipResolving = false;

        try {
            $records = @dns_get_record($domainObject->getName(), DNS_A | DNS_AAAA);
        } catch (\Exception $e) {

            return $ipResolving;
        }

        if (empty($records)) {
            return $ipResolving;
        }

        $domainIpAddresses = $domainObject->getIpAddresses();

        foreach ($records as $record) {
            $ipAddress = '';

            if (isset($record['ip'])) {
                $ipAddress = $record['ip'];
            } elseif (isset($record['ipv6'])) {
                $ipAddress = $record['ipv6'];
            }

            foreach ($domainIpAddresses as $domain_ip) {
                if ($ipAddress === $domain_ip) {
                    $ipResolving = true;
                }
            }
        }

        return $ipResolving;
    }
}
