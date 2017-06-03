<?php

namespace Integration;

/**
 * Helper for arrays
 */
class ArrayHelper
{
    const SEPARATOR = '.';
    private $array = [];

    /**
     * ArrayHelper constructor.
     *
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    public function getValue($key = '', $default = '')
    {
        return self::get($this->array, $key, $default);
    }

    /**
     * Get a value from a string if it exists or default.
     *
     * @param        $array
     * @param string $key
     * @param string $default
     *
     * @static
     * @return mixed
     */
    public static function get($array, $key = '', $default = '')
    {
        if (!is_array($array) || $key === '') {
            return $default;
        }
        $pos = strpos($key, self::SEPARATOR);
        if ($pos === false) {
            return isset($array[$key])
                ? $array[$key]
                : $default;
        }
        $first = substr($key, 0, $pos);
        if ($first === '' || !isset($array[$first])) {
            return $default;
        }
        return self::get($array[$first], substr($key, $pos + 1), $default);
    }
}
