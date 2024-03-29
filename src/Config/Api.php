<?php

namespace TinkoffAuth\Config;

class Api extends Config
{
    const USER_SCOPES = 'user_scopes';

    const SCOPES_USERINFO = 'scopes_userinfo';
    const SCOPES_PASSPORT_SHORT = 'scopes_passport_short';
    const SCOPES_PASSPORT = 'scopes_passport';
    const SCOPES_DRIVER_LICENSES = 'scopes_driver_licenses';
    const SCOPES_INN = 'scopes_inn';
    const SCOPES_SNILS = 'scopes_snils';
    const SCOPES_ADDRESSES = 'scopes_addresses';
    const SCOPES_IDENTIFICATION = 'scopes_identification';
    const SCOPES_SELF_EMPLOYED_STATUS = 'scopes_self_employed_status';
    const SCOPES_DEBIT_CARDS = 'scopes_debit_cards';
    const SCOPES_SUBSCRIPTION = 'scopes_subscription';
    const SCOPES_COBRAND_STATUS = 'scopes_cobrand_status';
    const SCOPES_PUBLIC_OFFICIAL_PERSON = 'scopes_public_official_person';
    const SCOPES_FOREIGN_AGENT = 'scopes_is_foreign_agent';
    const SCOPES_BLACKLIST_STATUS = 'scopes_blacklist_status';
    const SCOPES_BANK_ACCOUNTS = 'scopes_bank_accounts';
    const SCOPES_COMPANY_INFO = 'scopes_company_info';
    const SCOPES_BANK_STATEMENTS = 'scopes_bank_statements';

    protected $availableIndexes = [
        self::USER_SCOPES,

        self::SCOPES_USERINFO,
        self::SCOPES_PASSPORT_SHORT,
        self::SCOPES_PASSPORT,
        self::SCOPES_DRIVER_LICENSES,
        self::SCOPES_INN,
        self::SCOPES_SNILS,
        self::SCOPES_ADDRESSES,
        self::SCOPES_IDENTIFICATION,
        self::SCOPES_SELF_EMPLOYED_STATUS,
        self::SCOPES_DEBIT_CARDS,
        self::SCOPES_SUBSCRIPTION,
        self::SCOPES_COBRAND_STATUS,
        self::SCOPES_PUBLIC_OFFICIAL_PERSON,
        self::SCOPES_FOREIGN_AGENT,
        self::SCOPES_BLACKLIST_STATUS,

        self::SCOPES_BANK_ACCOUNTS,
        self::SCOPES_COMPANY_INFO,
        self::SCOPES_BANK_STATEMENTS,
    ];

    /**
     * @var Api|null Текущий объект синглтона
     */
    protected static $instance = null;

    /**
     * @return Api
     */
    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        }

        self::$instance = new self();

        return self::getInstance();
    }

    /**
     * @return array[]
     */
    public function getScopes()
    {
        return [
            self::SCOPES_USERINFO               => [
                'phone',
            ],
            self::SCOPES_PASSPORT_SHORT         => [
                'opensme/individual/passport-short/get'
            ],
            self::SCOPES_PASSPORT               => [
                'opensme/individual/passport/get'
            ],
            self::SCOPES_DRIVER_LICENSES        => [
                'opensme/individual/driver-licenses/get'
            ],
            self::SCOPES_INN                    => [
                'opensme/individual/inn/get'
            ],
            self::SCOPES_SNILS                  => [
                'opensme/individual/snils/get'
            ],
            self::SCOPES_ADDRESSES              => [
                'opensme/individual/addresses/get'
            ],
            self::SCOPES_IDENTIFICATION         => [
                'opensme/individual/identification/status/get'
            ],
            self::SCOPES_SELF_EMPLOYED_STATUS   => [
                'opensme/individual/self-employed/status/get'
            ],
            self::SCOPES_DEBIT_CARDS            => [
                'opensme/individual/accounts/debit/get'
            ],
            self::SCOPES_SUBSCRIPTION           => [
                'opensme/individual/subscription/get'
            ],
            self::SCOPES_COBRAND_STATUS         => [
                'opensme/individual/cobrand/status/get'
            ],
            self::SCOPES_PUBLIC_OFFICIAL_PERSON => [
                'opensme/individual/pdl/status/get'
            ],
            self::SCOPES_FOREIGN_AGENT          => [
                'opensme/individual/foreignagent/status/get'
            ],
            self::SCOPES_BLACKLIST_STATUS       => [
                'opensme/individual/blacklist/status/get'
            ],

            self::SCOPES_BANK_ACCOUNTS   => [
                'opensme/inn/[{inn}]/kpp/[{kpp}]/bank-accounts/get'
            ],
            self::SCOPES_COMPANY_INFO    => [
                'opensme/inn/[{inn}]/kpp/[{kpp}]/company-info/get'
            ],
            self::SCOPES_BANK_STATEMENTS => [
                'opensme/inn/[{inn}]/kpp/[{kpp}]/bank-statements/get'
            ],
        ];
    }

    public function getScopesURLs()
    {
        return [
            self::SCOPES_USERINFO               => 'https://id.tinkoff.ru/userinfo/userinfo',
            self::SCOPES_PASSPORT_SHORT         => 'https://business.tinkoff.ru/openapi/api/v1/individual/documents/passport-short',
            self::SCOPES_PASSPORT               => 'https://business.tinkoff.ru/openapi/api/v1/individual/documents/passport',
            self::SCOPES_DRIVER_LICENSES        => 'https://business.tinkoff.ru/openapi/api/v1/individual/documents/driver-licenses',
            self::SCOPES_INN                    => 'https://business.tinkoff.ru/openapi/api/v1/individual/documents/inn',
            self::SCOPES_SNILS                  => 'https://business.tinkoff.ru/openapi/api/v1/individual/documents/snils',
            self::SCOPES_ADDRESSES              => 'https://business.tinkoff.ru/openapi/api/v1/individual/addresses',
            self::SCOPES_IDENTIFICATION         => 'https://business.tinkoff.ru/openapi/api/v1/individual/identification/status',
            self::SCOPES_SELF_EMPLOYED_STATUS   => 'https://business.tinkoff.ru/openapi/api/v1/individual/self-employed/status',
            self::SCOPES_DEBIT_CARDS            => 'https://business.tinkoff.ru/openapi/api/v1/individual/accounts/debit',
            self::SCOPES_SUBSCRIPTION           => 'https://business.tinkoff.ru/openapi/api/v1/individual/subscription',
            self::SCOPES_COBRAND_STATUS         => 'https://business.tinkoff.ru/openapi/api/v1/individual/cobrand/%s',
            self::SCOPES_PUBLIC_OFFICIAL_PERSON => 'https://business.tinkoff.ru/openapi/api/v1/individual/pdl/status',
            self::SCOPES_FOREIGN_AGENT          => 'https://business.tinkoff.ru/openapi/api/v1/individual/foreignagent/status',
            self::SCOPES_BLACKLIST_STATUS       => 'https://business.tinkoff.ru/openapi/api/v1/individual/blacklist/status',
            self::SCOPES_BANK_ACCOUNTS          => 'https://business.tinkoff.ru/openapi/api/v4/bank-accounts',
            self::SCOPES_COMPANY_INFO           => 'https://business.tinkoff.ru/openapi/api/v1/company',
            self::SCOPES_BANK_STATEMENTS        => 'https://business.tinkoff.ru/openapi/api/v1/statement',
        ];
    }
}