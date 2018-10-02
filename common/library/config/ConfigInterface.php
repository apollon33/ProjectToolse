<?php

namespace common\library\config;

interface ConfigInterface
{

    const STRICT = 0;
    // Should be used with ALLOW_EMPTY simultaneously, or fill trigger could not read from file Exception
    const ALLOW_CREATE = 1 << 0;
    const ALLOW_EMPTY  = 1 << 1;
    const ALLOW_RELOAD = 1 << 2;

    /**
     * Return the setting from config
     * @param string $section
     * @param string $key
     * @return mixed
     */
    public function get(string $section, string $key);

    /**
     * Update the setting in config
     * @param string $section
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $section, string $key, $value);
}
