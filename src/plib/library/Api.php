<?php
// Copyright 1999-2019. Plesk International GmbH. All rights reserved.

namespace PleskExt\Siwecos;

class Api
{
    private const API_URL = 'https://api.siwecos.de/api/v2';

    public static function registerToken(): string
    {
        $url = self::API_URL . '/token';
        $client = Helper::httpClient($url);

        $data = [
            'agb_check' => true,
        ];

        $json = json_encode($data);

        $client->setRawData($json);

        $response = $client->request('POST');

        if ($response->isSuccessful()) {
            $json = $response->getBody();
            $data = json_decode($json, true);

            if (isset($data['token'])) {
                return $data['token'];
            }
        }

        return '';
    }
}
