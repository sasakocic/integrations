<?php

namespace Integration\naszaklasa;

class NaszaklasaHelper
{
    /**
     * Curl wrapper.
     *
     * @param string $url
     * @param array  $post
     * @param array  $headers
     *
     * @return array
     * @static
     */
    public static function doCurl($url, $post = [], $headers = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($post)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $string = curl_exec($ch);
        curl_close($ch);
        if (!$string) {
            throw new NaszaklasaException('Curl failed for ' . $url);
        }
        $data = json_decode($string, true);
        if (!is_array($data)) {
            throw new NaszaklasaException('json decoding failed for ' . $string);
        }

        return $data;
    }
}
