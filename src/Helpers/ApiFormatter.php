<?php

namespace TinkoffAuth\Helpers;

use TinkoffAuth\Config\Api;
use TinkoffAuth\Config\Auth;
use TinkoffAuth\Services\Http\Response;

class ApiFormatter
{
    /**
     * Форматирование параметров Introspect
     *
     * @param Response $response
     *
     * @return array
     */
    public static function formatIntrospectParams(Response $response): array
    {
        $result = $response->json();

        return array_merge([
            'active'    => false,
            'scope'     => [],
            'client_id' => null,
            'iss'       => null,
        ], $result);
    }

    /**
     * Форматирование параметров Access Token
     *
     * @param Response $response
     *
     * @return array
     */
    public static function formatTokenParams(Response $response): array
    {
        $result = $response->json();

        $accessToken = $result['access_token'] ?? null;
        if ($accessToken) {
            $authConfig = Auth::getInstance();
            $authConfig->push(Auth::ACCESS_TOKEN, $accessToken);
        }

        return [
            'access_token'  => $accessToken,
            'token_type'    => $result['token_type'] ?? 'Bearer',
            'expires_in'    => $result['expires_in'] ?? 0,
            'refresh_token' => $result['refresh_token'] ?? null,
        ];
    }

    /**
     * Форматирование параметров для получений полной информации по пользователю
     *
     * @param array $data
     *
     * @return array
     */
    public static function formatUserinfoFull(array $data = []): array
    {
        return [
            Api::SCOPES_USERINFO             => $data[Api::SCOPES_USERINFO] ?? [],
            Api::SCOPES_PASSPORT_SHORT       => $data[Api::SCOPES_PASSPORT_SHORT] ?? [],
            Api::SCOPES_PASSPORT             => $data[Api::SCOPES_PASSPORT] ?? [],
            Api::SCOPES_DRIVER_LICENSES      => $data[Api::SCOPES_DRIVER_LICENSES] ?? [],
            Api::SCOPES_INN                  => $data[Api::SCOPES_INN] ?? [],
            Api::SCOPES_SNILS                => $data[Api::SCOPES_SNILS] ?? [],
            Api::SCOPES_ADDRESSES            => $data[Api::SCOPES_ADDRESSES] ?? [],
            Api::SCOPES_IDENTIFICATION       => $data[Api::SCOPES_IDENTIFICATION] ?? [],
            Api::SCOPES_SELF_EMPLOYED_STATUS => $data[Api::SCOPES_SELF_EMPLOYED_STATUS] ?? [],
            Api::SCOPES_DEBIT_CARDS          => $data[Api::SCOPES_DEBIT_CARDS] ?? [],
            Api::SCOPES_SUBSCRIPTION         => $data[Api::SCOPES_SUBSCRIPTION] ?? [],
            Api::SCOPES_COBRAND_STATUS       => $data[Api::SCOPES_COBRAND_STATUS] ?? [],
        ];
    }
}