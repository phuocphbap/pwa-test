<?php

namespace App\Support;

class TokenCrypt
{
    protected $method = 'AES-256-CBC';
    protected $key = '';
    protected $iv = '';

    /**
     * Construct.
     */
    public function __construct()
    {
        $this->key = hash('sha256', env('app.key'));
        $this->iv = substr(hash('sha256', 'TokenCrypt'), 0, 16);
    }

    /**
     * Encode for token.
     *
     * @param string $data
     * @param string
     */
    public function encode($data)
    {
        return base64_encode(openssl_encrypt($data, $this->method, $this->key, 0, $this->iv));
    }

    /**
     * Decode for token.
     *
     * @param string $token
     * @param string
     */
    public function decode($token)
    {
        return openssl_decrypt(base64_decode($token), $this->method, $this->key, 0, $this->iv);
    }
}
