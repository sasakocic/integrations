<?php

namespace Integration;

/**
 * Configuration entity common for all integrations.
 */
class ConfigEntity
{
    /** @var string */
    public $id = '';
    /** @var string */
    public $key = '';
    /** @var string */
    public $secret = '';
    /** @var string */
    public $url = '';
    /** @var string */
    public $redirectHost = '';
    /** @var string */
    public $redirectUri = '';

    /**
     * ConfigEntity constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
