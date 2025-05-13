<?php

namespace SMTP2GO\App;

class SettingsHelper
{

    private static $fieldToConstantMapping = array(
        'smtp2go_api_key' => 'SMTP2GO_API_KEY',
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

    public static function settingHasEnvironmentVariable($field)
    {
        return isset($_ENV[$field]);
    }

    public static function getOption($field)
    {
        $envField = strtoupper($field);
        if (isset($_ENV[$envField])) {
            return $_ENV[$envField];
        }
        if (static::settingHasDefinedConstant($field)) {
            return constant(static::$fieldToConstantMapping[$field]);
        }
        return get_option($field);
    }
}
