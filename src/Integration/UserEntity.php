<?php

namespace Integration;

/**
 * User entity common for all integrations.
 */
class UserEntity
{
    /** @var string */
    public $id = '';
    /** @var string */
    public $username = '';
    /** @var string */
    public $firstname = '';
    /** @var string */
    public $lastname = '';
    /** @var string */
    public $email = '';
    /** @var string */
    public $thumbnailUrl = '';
    /** @var string */
    public $city = '';

    /**
     * UserEntity constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
