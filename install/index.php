<?php

IncludeModuleLangFile(__FILE__);


class tinkoffid extends CModule
{
    var $MODULE_ID = "tinkoffid";
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

        $this->MODULE_NAME        = 'Tinkoff ID';
        $this->MODULE_DESCRIPTION = 'Авторизация через Тинькофф ID';

        $this->PARTNER_NAME = "Тинькофф";
        $this->PARTNER_URI = "https://www.tinkoff.ru/";
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB(false);
    }

    function InstallDB()
    {
        RegisterModule($this->MODULE_ID);

        return true;
    }

    function InstallFiles()
    {
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
        }
        return true;
    }

    function InstallEvents()
    {
        if($_ENV["COMPUTERNAME"]!='BX')
        {
            DeleteDirFilesEx("/bitrix/components/tinkoff");
        }
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallDB(false);

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        UnRegisterModule($this->MODULE_ID);

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
