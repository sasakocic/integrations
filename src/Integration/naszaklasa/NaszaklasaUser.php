<?php

namespace Integration\naszaklasa;

use Integration\ArrayWrapper;

/**
 * User data
 * @see http://developers.nk.pl/documentation/nk-api/opensocial-rest-api/people-services/
 */
class NaszaklasaUser
{
    /** @var string */
    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $displayName;
    /** @var string */
    public $familyName;
    /** @var string */
    public $givenName;
    /** @var string */
    public $email;
    /** @var string */
    public $thumbnailUrl;
    /** @var string */
    public $location;
    /** @var string */
    public $age;

    /**
     * NaszaklasaUser constructor.
     *
     * @param array $data
     * @param string $default
     */
    public function __construct(array $data, $default = '')
    {
        if (!isset($data['entry'])) {
            throw new \InvalidArgumentException('Invalid user data ' . var_export($data, true));
        }
        $wrapper = new ArrayWrapper($data['entry']);
        $this->id = $wrapper->get('id', $default);
        $this->name = $wrapper->get('name.formatted', $default);
        $this->familyName = $wrapper->get('name.familyName', $default);
        $this->givenName = $wrapper->get('name.givenName', $default);
        $this->displayName = $wrapper->get('name.displayName', $default);
        $this->email = $wrapper->get('emails.0.value', $default);
        $this->thumbnailUrl = $wrapper->get('thumbnailUrl', $default);
        $this->location = $wrapper->get('currentLocation.region', $default);
        $this->age = $wrapper->get('age', $default);
    }
}
