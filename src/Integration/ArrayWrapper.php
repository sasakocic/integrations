<?php

namespace Integration;

/**
 * Wrapper for arrays for controlled get with default values
 */
class ArrayWrapper
{
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

    /**
     * Get a value from a string if it exists or default.
     *
     * @param string $key
     * @param string $default
     *
     * @static
     * @return mixed
     */
    public function get($key = '', $default = '')
    {
        return ArrayHelper::get($this->array, $key, $default);
    }
}
