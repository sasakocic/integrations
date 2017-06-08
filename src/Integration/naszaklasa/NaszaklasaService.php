<?php

namespace Integration\naszaklasa;

use Integration\ConfigEntity;

class NaszaklasaService
{
    const URL = 'https://nk.pl/oauth2/login';
    const REDIRECT_HOST = 'https://staging-6-www.jackpot.de';
    const REDIRECT_URI = '/login/naszaklasa';
    const PERMISSIONS = [
        'BASIC_PROFILE_ROLE',
        'BIRTHDAY_PROFILE_ROLE',
        'EMAIL_PROFILE_ROLE',
    ];
    const PROFILE_URL = 'http://opensocial.nk-net.pl/v09/social/rest/people/@me';
    const TOKEN_URL = "https://nk.pl/oauth2/token";

    /** @var ConfigEntity */
    private $config;
    /** @var \Closure */
    private $doCurl;

    /**
     * NaszaklasaService constructor.
     *
     * @param ConfigEntity $config
     */
    public function __construct(ConfigEntity $config)
    {
        $this->config = $config;
        $this->doCurl = function (...$args) {
            return call_user_func_array(NaszaklasaHelper::class . '::doCurl', $args);
        };
    }

    /**
     * @param \Closure $doCurl
     *
     * @return NaszaklasaService
     */
    public function setDoCurl(\Closure $doCurl)
    {
        $this->doCurl = $doCurl;

        return $this;
    }

    /**
     * Get Login url
     *
     * https://nk.pl/oauth2/login?
     * client_id=demo&
     * response_type=code&
     * redirect_uri=<?php echo urlencode(">&scope=BASIC_PROFILE_ROLE,EMAIL_PROFILE_ROLE,CREATE_SHOUTS_ROLE">
     *
     * @return string $url
     */
    public function getLoginUrl()
    {
        $query = [
            'client_id' => $this->config->key,
            'response_type' => 'code',
            'redirect_uri' => $this->config->redirectHost . $this->config->redirectUri,
            'scope' => implode(',', self::PERMISSIONS),
        ];
        $url = self::URL . '?' . http_build_query($query);

        return $url;
    }

    /**
     * Authenticate user for given params.
     *
     * @param array $query
     * @param array $params
     *
     * @return array $entity
     * @static
     */
    public static function authenticate(array $query, array $params)
    {
        $service = self::create($params);
        $user = $service->getUser($query);
        $entity = [
            'id' => $user->id,
            'name' => $user->name,
            'lastname' => $user->familyName,
            'firstname' => $user->givenName,
            'location' => $user->location,
            'age' => $user->age,
            'email' => $user->email,
            'thumbnailUrl' => $user->thumbnailUrl,
            'user' => $user,
            'array' => (array) $user,
        ];

        return $entity;
    }

    /**
     * Get User.
     *
     * @param array $query
     *
     * https://integrations.yahuah.net/naszaklasa/callback?
     * code=2135%7C1495137569%7C8e87a1f3619a1a4428f438707e07b09b
     *
     * @return NaszaklasaUser
     */
    public function getUser(array $query)
    {
        if (isset($query['error'])) {
            return $query;
        }
        if (!isset($query['code'])) {
            throw new NaszaklasaException('Missing code in callback ' . var_export($query, true));
        }
        $result = $this->getTokenForCode($query['code']);
        if (isset($result['error'])) {
            throw new NaszaklasaException($result['error']);
//            return $result;
        }
        if (!isset($result['access_token'])) {
            throw new NaszaklasaException('Missing token in ' . var_export($result, true));
        }

        return $this->getDataForToken($result['access_token']);
    }

    /**
     * Create service.
     *
     * @param array $config
     *
     * @static
     * @return self
     */
    public static function create(array $config = [])
    {
        $configEntity = new ConfigEntity($config);
        $configEntity->key = $configEntity->key
            ? $configEntity->key
            : getenv('NASZAKLASA_KEY');
        $configEntity->secret = $configEntity->secret
            ? $configEntity->secret
            : getenv('NASZAKLASA_SECRET');
        $configEntity->redirectHost = $configEntity->redirectHost
            ? $configEntity->redirectHost
            : self::REDIRECT_HOST;
        $configEntity->redirectUri = $configEntity->redirectUri
            ? $configEntity->redirectUri
            : self::REDIRECT_URI;

        return new self($configEntity);
    }

    /**
     * Get Token for given code.
     *
     * @param string $code
     * (
     * [access_token] => zfD7S3JlM-NK2T8qois9KhNrBC5dtyXh85yI4dXwSbzxrjdIV8E1XpDwqeY-4G9BEYPsLtB3DMEXE5Ik149uE1vFEG81RtBNDA36ZtuHt3L6Kh2u-ooiRCI7aSegmA7dHY0sPq32d35qY5Ibg39bmtXqTberp5JTSUz1nSDvN8CVdfhrrHPpMh64o5vT6jqcD2-Ib2IrsTbzPxfAFmwC4h7m1-R9SN93-rR9C2qI5sHPy5HikuajBZUBAwaEcGk6Xio-xcw-qtW5bparH4UyB7qch5g
     * [token_type] => Bearer
     * [expires_in] => 900
     * [refresh_token] => 5nS_4NXEULxcfeZZJqhvvoGGrruSt9e3EM9BbZKZ4iWp6uswBl3XE88UDqRAxqpVpKGJ5Vpmp1m-QV1YidS0UH7k8oijEsfUD9dqEij_iWxc7s7qxNXVFA_FljnlDahbF8voZi4xUOVqky99AGpdQXWVbbqKBidU2VeQfzz2bX0UzRUeZ4f0XTbMOOWdzT3J9vDO3g
     * )
     * array(3) {
     * 'error' =>
     * string(13) "invalid_grant"
     * 'error_description' =>
     * string(37) "Authorization code is no longer valid"
     * 'error_uri' =>
     * string(45) "http://developers.nk.pl/wiki/NKConnect_Errors"
     * }
     *
     * @return array
     */
    public function getTokenForCode($code)
    {
        $fields = [
            'client_id' => $this->config->key,
            'client_secret' => $this->config->secret,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->config->redirectHost . $this->config->redirectUri,
            'scope' => implode(',', self::PERMISSIONS),
            'code' => $code,
        ];
        $curl = $this->doCurl;

        return $curl(self::TOKEN_URL, $fields);
    }

    /**
     * Get user data by token.
     *
     * @param $token
     *
     * @return NaszaklasaUser
     */
    public function getDataForToken($token)
    {
        $params = [
            'nk_token' => $token,
            'fields' => 'id,age,gender,name,currentLocation,emails,thumbnailUrl,photos',
        ];
        $req = OAuthRequest::from_consumer_and_token($this->config, null, 'GET', self::PROFILE_URL, $params);
        $req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->config, null);
        $headers = [$req->to_header(), 'Content-Type: application/json'];
        $url = self::PROFILE_URL . '?' . OAuthUtil::build_http_query($params);
        $curl = $this->doCurl;
        $data = $curl($url, [], $headers);

        return new NaszaklasaUser($data);
    }
}
