<?php

namespace TinkoffAuth\Facades;

use TinkoffAuth\Config\Api as ApiConfig;
use TinkoffAuth\Config\Auth;
use TinkoffAuth\Exceptions\UnknownConfig;
use TinkoffAuth\Helpers\ApiFormatter;
use TinkoffAuth\Helpers\ApiHelper;
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
     * @param bool $useConfig Использовать ли конфиг для получения данных
     *
     * @return array
     * @throws UnknownConfig
     */
    public function getScopes(string $accessToken = null, bool $useConfig = true): array
    {
        $apiConfig = ApiConfig::getInstance();

        $scope = $apiConfig->get(ApiConfig::USER_SCOPES);
        if ($useConfig && $scope) {
            return $scope;
        }

        $scope = $this->introspect($accessToken)['scope'];

        if ($useConfig) {
            $apiConfig->push(ApiConfig::USER_SCOPES, $scope);
        }

        return $scope;
    }

    /**
     * Проверка scopes
     *
     * @param array $scopeForCompare
     * @param string|null $accessToken Access Token полученный раннее
     *
     * @return bool
     * @throws UnknownConfig
     */
    public function validateScopes(array $scopeForCompare = [], string $accessToken = null): bool
    {
        $userScopes = $this->getScopes($accessToken);

        return ApiHelper::validateScopes($userScopes, $scopeForCompare);
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

        $request  = $this->createTinkoffIDRequest();
        $response = $request->post('/auth/token', [
            'grant_type'   => 'authorization_code',
            'code'         => $code,
            'redirect_uri' => $authConfig->get(Auth::REDIRECT_URI)
        ]);

        return ApiFormatter::formatTokenParams($response);
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

        $request  = $this->createTinkoffIDBearerRequest($accessToken);
        $response = $request->post('/userinfo/userinfo', [
            'client_id'     => $authConfig->get(Auth::CLIENT_ID),
            'client_secret' => $authConfig->get(Auth::CLIENT_SECRET)
        ]);

        return $response->json();
    }

    /**
     * Получение максимально возможной информации о пользователе
     *
     * @param string|null $accessToken
     * @param array $replacement
     *
     * @return array
     * @throws UnknownConfig
     */
    public function userinfoFull(string $accessToken = null, array $replacement = []): array
    {
        $authConfig = Auth::getInstance();
        $apiConfig  = ApiConfig::getInstance();

        $accessToken = $accessToken ?? $authConfig->get(Auth::ACCESS_TOKEN);
        if ( ! $accessToken) {
            return ApiFormatter::formatUserinfoFull();
        }

        $routes       = $apiConfig->getScopesURLs();
        $neededScopes = $apiConfig->getScopes();

        $userinfoFull = [];
        foreach ($routes as $index => $route) {
            $userHasNeededScopes = $this->validateScopes($neededScopes[$index] ?? []);
            if ( ! $userHasNeededScopes) {
                continue;
            }

            $request = new Request();
            $request = $this->addBearerCredentials($request, $accessToken);

            try {
                $route = sprintf($route, ...($replacement[$index] ?? []));
            } catch (\Exception $e) {
            }

            switch ($index) {
                case ApiConfig::SCOPES_USERINFO:
                    $response = $request->post($route, [
                        'client_id'     => $authConfig->get(Auth::CLIENT_ID),
                        'client_secret' => $authConfig->get(Auth::CLIENT_SECRET)
                    ]);
                    break;
                default:
                    $response = $request->request($route);
            }

            $userinfoFull[$index] = $response->json();
        }

        return ApiFormatter::formatUserinfoFull($userinfoFull);
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

        $request  = $this->createTinkoffIDRequest();
        $response = $request->post('/auth/introspect', [
            'token' => $accessToken,
        ]);

        return ApiFormatter::formatIntrospectParams($response);
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
     * Создание запроса https://id.tinkoff.ru с Base авторизацией
     *
     * @return Request
     * @throws UnknownConfig
     */
    private function createTinkoffIDRequest(): Request
    {
        $request = new Request('https://id.tinkoff.ru/');

        return $this->addBaseAuthCredentials($request);
    }

    /**
     * Создание запроса https://id.tinkoff.ru с Bearer авторизацией. Для получения данных пользовтаеля
     *
     * @param $accessToken
     *
     * @return Request
     * @throws UnknownConfig
     */
    private function createTinkoffIDBearerRequest($accessToken = null): Request
    {
        $request = new Request('https://id.tinkoff.ru/');

        return $this->addBearerCredentials($request, $accessToken);
    }
}