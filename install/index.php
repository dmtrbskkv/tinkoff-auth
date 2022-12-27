<?php

IncludeModuleLangFile(__FILE__);


class tinkoffauth extends CModule
{
    var $MODULE_ID = "tinkoffauth";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $arModuleVersion = array();

        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION      = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME        = 'Tinkoff Auth';
        $this->MODULE_DESCRIPTION = 'Авторизация через Тинькофф ID';
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB(false);
    }

    function InstallDB()
    {
        RegisterModule("tinkoffauth");

        return true;
    }

    function InstallFiles()
    {
        return true;
    }

    function InstallEvents()
    {
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallDB(false);

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        UnRegisterModule("tinkoffauth");

        return true;
    }

    function UnInstallFiles($arParams = array())
    {
        return true;
    }

    function UnInstallEvents()
    {
        return true;
    }
}
