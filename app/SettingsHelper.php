<?php

namespace SMTP2GO\App;

class SettingsHelper
{

    private static $fieldToConstantMapping = array(
        'smtp2go_api_key' => 'SMTP2GO_API_KEY',
        'SMTP2GO_ENCRYPTION_KEY' => 'SMTP2GO_ENCRYPTION_KEY',
    );

    public static function settingHasDefinedConstant($field)
    {
        if (!defined('SMTP2GO_USE_CONSTANTS') || defined('SMTP2GO_USE_CONSTANTS') && constant('SMTP2GO_USE_CONSTANTS') === false) {
            return false;
        }

        if (isset(static::$fieldToConstantMapping[$field]) && defined(static::$fieldToConstantMapping[$field])) {
            return true;
        }

        return false;
    }

    /**
     * Get the setting value from the filesystem via a CONSTANT
     * @todo add .env support
     */
    public static function getSettingFromFileSystem($constantName)
    {

        if (defined($constantName)) {
            return constant($constantName);
        }
        //can either be a define()'d value or $_ENV variable
    }

    public static function getOption($field)
    {
        if (static::settingHasDefinedConstant($field)) {
            return constant(static::$fieldToConstantMapping[$field]);
        }
        return get_option($field);
    }
}
