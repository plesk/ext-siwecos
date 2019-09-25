<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

namespace PleskExt\Siwecos;

class Api
{
    private const API_URL = 'https://api.siwecos.de/api/v2';

    private const DOMAIN_STATUS_INACTIVE = 1;
    private const DOMAIN_STATUS_UNVERIFIED = 2;
    private const DOMAIN_STATUS_OK = 3;

    public static function registerToken(): string
    {
        $url = self::API_URL . '/token';

        $data = [
            'agb_check' => true,
        ];

        $response = self::post($url, $data);

        if (!$response->isSuccessful()) {
            throw new \pm_Exception('Failed to register token');
        }

        $json = $response->getBody();
        $result = json_decode($json, true);

        return $result['token'];
    }

    public static function startScan(string $domain): int
    {
        $verificationToken = '';
        $status = self::domainStatus($domain, $verificationToken);

        if ($status === self::DOMAIN_STATUS_INACTIVE) {
            $verificationToken = self::addDomain($domain);
            self::verifyDomain($domain, $verificationToken);
        } elseif ($status == self::DOMAIN_STATUS_UNVERIFIED) {
            self::verifyDomain($domain, $verificationToken);
        }

        $url = self::API_URL . '/scan';

        $data = [
            'domain' => $domain,
        ];

        $response = self::post($url, $data);

        if (!$response->isSuccessful()) {
            throw new \pm_Exception('Failed to start scan for domain ' . $domain . ': ' . $response->getBody());
        }

        $json = $response->getBody();
        $data = json_decode($json, true);

        return $data['scan_id'];
    }

    public static function scanReport(int $scanId): array
    {
        $url = self::API_URL . '/scan/' . $scanId . '/en';
        $response = self::post($url);

        if (!$response->isSuccessful()) {
            throw new \pm_Exception('Failed to get scan report #' . $scanId . ': ' . $response->getBody());
        }

        $json = $response->getBody();

        return json_decode($json, true);
    }

    private static function request(string $url, array $data = [], string $method = 'GET'): \Zend_Http_Response
    {
        $client = Helper::httpClient($url);
        $token = Config::getToken();
        $json = json_encode($data);

        $client->setHeaders('SIWECOS-Token', $token);
        $client->setRawData($json);

        return $client->request($method);
    }

    private static function get(string $url, array $data = []): \Zend_Http_Response
    {
        return self::request($url, $data, 'GET');
    }

    private static function post(string $url, array $data = []): \Zend_Http_Response
    {
        return self::request($url, $data, 'POST');
    }

    private static function domainStatus(string $domain, string &$verificationToken = ''): int
    {
        $url = self::API_URL . '/domain/' . $domain;
        $response = self::get($url);

        if (!$response->isSuccessful()) {
            if ($response->getStatus() === 404) {
                return self::DOMAIN_STATUS_INACTIVE;
            } else {
                throw new \pm_Exception('Failed to get status for domain ' . $domain . ': ' . $response->getBody());
            }
        }

        $json = $response->getBody();
        $result = json_decode($json, true);

        $verificationToken = $result['verification_token'];

        return $result['is_verified'] ? self::DOMAIN_STATUS_OK : self::DOMAIN_STATUS_UNVERIFIED;
    }

    private static function addDomain(string $domain): string
    {
        $url = self::API_URL . '/domain';

        $data = [
            'domain' => $domain,
        ];

        $response = self::post($url, $data);

        if (!$response->isSuccessful()) {
            throw new \pm_Exception('Failed to add domain ' . $domain . ': ' . $response->getBody());
        }

        $json = $response->getBody();
        $result = json_decode($json, true);

        return $result['verification_token'];
    }

    private static function verifyDomain(string $domain, string $verificationToken): void
    {
        $pmDomain = \pm_Domain::getByName($domain);
        $fileManager = new \pm_FileManager($pmDomain->getId());
        $file = $pmDomain->getDocumentRoot() . '/' . $verificationToken . '.html';

        $fileManager->filePutContents($file, $verificationToken);

        $url = self::API_URL . '/domain/verify';

        $data = [
            'domain' => $domain,
        ];

        $response = self::post($url, $data);

        $fileManager->removeFile($file);

        if (!in_array($response->getStatus(), [200, 403])) {
            throw new \pm_Exception('Failed to verify domain ' . $domain . ': ' . $response->getBody());
        }
    }
}
