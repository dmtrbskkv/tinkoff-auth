<?php

namespace TinkoffAuth\Facades;

use TinkoffAuth\Config\Auth;
use TinkoffAuth\Exceptions\UnknownConfig;
use TinkoffAuth\Services\Http\Request;
use TinkoffAuth\Services\Http\Response;
use TinkoffAuth\Services\State\State;

/**
 * Фасад для работы с API
 */
class Api extends BaseFacade
{
    /**
     * Необходимые scopes для получения профиля пользователя
     */
    const SCOPES_FOR_AUTH = [
        'email',
        'profile',
        'phone'
    ];

    /**
     * Получение AccessToken. Обертка над $this->token()
     *
     * @param bool $validateState Проверить ли state
     *
     * @return string|null
     * @throws UnknownConfig
     */
    public function getAccessToken(bool $validateState = true): ?string
    {
        return $this->token($validateState)['access_token'] ?? null;
    }

    /**
     * Получение scopes. Обертка над $this->introspect()
     *
     * @param string|null $accessToken Access Token полученный раннее
     *
     * @return array
     * @throws UnknownConfig
     */
    public function getScopes(string $accessToken = null): array
    {
        return $this->introspect($accessToken)['scope'];
    }

    /**
     * Проверка scopes
     *
     * @param string|null $accessToken Access Token полученный раннее
     *
     * @return bool
     * @throws UnknownConfig
     */
    public function validateScopes(string $accessToken = null): bool
    {
        $scopes = $this->getScopes($accessToken);
        foreach (self::SCOPES_FOR_AUTH as $scope) {
            if ( ! in_array($scope, $scopes)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Запрос для получения Access Token
     *
     * @param bool $validateState Нужно ли проверять State
     *
     * @return array
     * @throws UnknownConfig
     */
    public function token(bool $validateState = true): array
    {
        $authConfig = Auth::getInstance();

        $authParams = $this->getAuthParams($validateState);
        $code       = $authParams['code'] ?? null;
        if ( ! $code) {
            return [];
        }

        $request  = $this->createRequest();
        $response = $request->post('/auth/token', [
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $authConfig->get(Auth::REDIRECT_URI)
        ]);

        return $this->getTokenParams($response);
    }

    /**
     * Запрос на получение данных пользователя
     *
     * @param $accessToken
     *
     * @return array
     * @throws UnknownConfig
     */
    public function userinfo($accessToken = null): array
    {
        $authConfig = Auth::getInstance();

        $request  = $this->createUserprofileRequest($accessToken);
        $response = $request->post('/userinfo/userinfo', [
            'client_id'     => $authConfig->get(Auth::CLIENT_ID),
            'client_secret' => $authConfig->get(Auth::CLIENT_SECRET)
        ]);

        return $response->json();
    }

    /**
     * Запрос на получение предоставленных данных пользователем
     *
     * @param string|null $accessToken Access Token полученный раннее
     *
     * @return array
     * @throws UnknownConfig
     */
    public function introspect(string $accessToken = null): array
    {
        if ( ! $accessToken) {
            $authConfig  = Auth::getInstance();
            $accessToken = $authConfig->get(Auth::ACCESS_TOKEN);
        }

        $request  = $this->createRequest();
        $response = $request->post('/auth/introspect', [
            'token' => $accessToken,
        ]);

        return $this->getIntrospectParams($response);
    }


    /**
     * Получение параметров авторизации для auth/complete роута
     *
     * @param bool $validateState
     *
     * @return array
     */
    public function getAuthParams(bool $validateState = true): array
    {
        $state = $_GET['state'] ?? -1;

        if ($validateState) {
            $stateService = new State();
            if ( ! $stateService->validate($state)) {
                return [];
            }
        }

        return [
            'state'         => $_GET['state'] ?? -1,
            'session_state' => $_GET['session_state'] ?? -1,
            'code'          => $_GET['code'] ?? null
        ];
    }

    /**
     * Формирование параметров Access Token
     *
     * @param Response $response
     *
     * @return array
     * @throws UnknownConfig
     */
    public function getTokenParams(Response $response): array
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
     * Формирование параметров Introspect
     *
     * @param Response $response
     *
     * @return array
     */
    public function getIntrospectParams(Response $response): array
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
     * Добавление Base авторизации запросу к client_id и client_secret
     *
     * @param Request $request
     *
     * @return Request
     * @throws UnknownConfig
     */
    public function addBaseAuthCredentials(Request $request): Request
    {
        $authConfig = Auth::getInstance();

        $username = $authConfig->getUsername();
        $password = $authConfig->getPassword();

        $request->basic($username, $password);

        return $request;
    }

    /**
     * Добавление Bearer авторизации с Access Token
     *
     * @param Request $request
     * @param $accessToken
     *
     * @return Request
     * @throws UnknownConfig
     */
    public function addBearerCredentials(Request $request, $accessToken = null): Request
    {
        $authConfig = Auth::getInstance();

        if ( ! $accessToken) {
            $accessToken = $authConfig->get(Auth::ACCESS_TOKEN);
        }

        $request->bearer($accessToken);

        return $request;
    }

    /**
     * Создание обыночного запроса с Base авторизацией
     *
     * @return Request
     * @throws UnknownConfig
     */
    private function createRequest(): Request
    {
        $request = new Request('https://id.tinkoff.ru/');

        return $this->addBaseAuthCredentials($request);
    }

    /**
     * Создание запроса для получения данных пользователя с Bearer авторизацией
     *
     * @param $accessToken
     *
     * @return Request
     * @throws UnknownConfig
     */
    private function createUserprofileRequest($accessToken = null): Request
    {
        $request = new Request('https://id.tinkoff.ru/');

        return $this->addBearerCredentials($request, $accessToken);
    }
}