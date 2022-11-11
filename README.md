## WordPress
### Настройка плагина
Вся настройка плагина производится во вкладке "Настройки" в пункте "Тинькофф"

## Standalone

### Предварительная настройка

Для начала работы с Tinkoff API необходимо заполнить заявку на
подключение [на этой странице](https://www.tinkoff.ru/business/open-api/).
После рассмотрения заявки сотрудниками банка будут высланы client_id и client_secret на электронную почту,
которая была указана в партнерской анкете.

Одним из пунктов партнерской анкеты является указание параметра redirect_uri.
Необходимо создать эндпоинт, доступный по redirect_uri, который заканчивает процесс авторизации
путем обмена кода на Access и Refresh токены. В качестве примера, эндпоинт-ссылкой
будет https://myintegration.ru/auth/complete, где https://myintegration.ru - страница продукта.

```php
use TinkoffAuth\Config\Auth;

require_once __DIR__.'/../vendor/autoload.php';

$authConfig = Auth::getInstance();
$authConfig->push(Auth::CLIENT_ID, 'client_id');
$authConfig->push(Auth::CLIENT_SECRET, 'client_secret');
$authConfig->push(Auth::REDIRECT_URI, 'https://myintegration.ru/auth/complete')
```

### Получение ссылки для авторизации

Для получения ссылки на авторизацию, необходимо указать redirect_uri в
методе `getAuthURL($redirect_uri = null, $scope_parameters = [])`.
При необходимости, можно указать массив `scope_parameters`. Подробнее можно
почитать [тут](https://business.tinkoff.ru/openapi/docs#section/Partnerskij-scenarij/Process-avtorizacii)

```php
use TinkoffAuth\Facades\Tinkoff;

$tinkoff = new Tinkoff();

$linkWithoutScope = $tinkoff->getAuthURL('https://myintegration.ru/auth/complete');

$linkWithScope = $tinkoff->getAuthURL('https://myintegration.ru/auth/complete', [
    "inn" => "9999980892", 
    "kpp" => "999991001" 
]);
```

### Обработка данных после авторизации

#### Упрощенный режим

Для авторизации необходимо вызвать функцию `auth()` фасада `TinkoffAuth\Facades\Tinkoff::class`.
В ответ придет класс `FunctionMediator`, который содержит статус авторизации и данные об обшибке, либо данные о
пользователе

```php
use \TinkoffAuth\Facades\Tinkoff;

$tinkoff = new Tinkoff();

$mediator = $tinkoff->auth();
if (!$mediator->getStatus()) {
    $errorMessage = $mediator->getMessage();
    // Обработать ошибку
}

$credentials = $mediator->getPayload();
// Обработать данные пользователя
```

#### Расширенный режим

На указанный redirect_uri придет запрос вида. Чтобы его обработать, можно воспользоваться методами ниже

```
https://myintegration.ru/auth/complete?state=ABCxyz&code=c.1aGiAXX3Ni&session_state=hXXXXXXY3kgs3nx0H3RTj3JzCSrdaqaDhU6lS8XXXXX.i4kl6dsEB1SQogzq0Nj0
```

```php
use \TinkoffAuth\Config\State;
use \TinkoffAuth\Services\State\Providers\Session;
use \TinkoffAuth\Services\State\Providers\Cookies;

// Можно выбрать где хранить State в куках или сессии
$stateConfig = State::getInstance();
$stateConfig->push(State::PROVIDER, Session::class)
$stateConfig->push(State::PROVIDER, Cookies::class)

// Создаем объект для работы с API
$api = new TinkoffAuth\Facades\Api();
// Указываем необходимость проверки State
$validateState = true;

// Данные, пришедшие на текущий URL + проверка State
$authParams = $api->getAuthParams($validateState);

// Получение Access Token по возможности + проверка State
$accessToken = $api->getAccessToken($validateState);

// Сохранение Access Token
$authConfig->push(Auth::ACCESS_TOKEN, $accessToken);

// Проверка Scopes 
if (!$api->validateScopes($accessToken)) {
    // Если доступов недостаточно
}

// Получение данных пользователя
$userinfo = $api->userinfo($accessToken);
if (count($userinfo) === 0) {
    // Если пользовательских данных не найдено
}

// Обработать пользовательские данные
```