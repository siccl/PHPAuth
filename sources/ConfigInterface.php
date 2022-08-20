<?php

namespace PHPAuth;

use PDO;

interface ConfigInterface
{
    public const CONFIG_TYPE_INI = 'ini';
    public const CONFIG_TYPE_ARRAY = 'array';
    public const CONFIG_TYPE_JSON = 'json';
    public const CONFIG_TYPE_YML = 'yml';
    public const CONFIG_TYPE_XML = 'xml';
    public const CONFIG_TYPE_SQL = 'sql';

    /**
     * Config::__construct()
     *
     * Create config class for PHPAuth\Auth.
     * Examples:
     *
     * new Config($dbh) -- Defaults will be used: load config from SQL table phpauth_config, language is 'en_GB'
     * new Config($dbh, 'config_table', '') -- 3rd argument is 'sql' or '' => 2rd argument determines config table in DB, default 'phpauth_config'
     * new Config($dbh, '$/config/phpauth.ini', 'ini') -- configuration will be loaded from INI file, '$' means Application basedir
     * new Config($dbh, $CONFIG_ARRAY, 'array') -- configuration must be defined in $CONFIG_ARRAY value
     *
     * in any case, 4th argument defines site language as locale code
     *
     * @param PDO $dbh
     * @param string|array $config_source -- declare source of config - table name, filepath or data-array
     * @param string $config_type -- default empty (means config in SQL table phpauth_config), possible values: 'sql', 'ini', 'array'
     * @param string $config_site_language -- declare site language, empty value means 'en_GB'
     */
    public function __construct(PDO $dbh, $config_source = null, string $config_type = '', string $config_site_language = '');

    /**
     * Return all config keys
     *
     * @return array
     */
    public function getAll(): array;


}