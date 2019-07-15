<?php

namespace app\library;

use ext\http;
use ext\crypt;
use ext\conf;
use phpDocumentor\Reflection\Types\Boolean;

class rpc extends http
{
    /**
     * RPC constructor.
     *
     * @param string $api
     * @param string $token
     */
    public function __construct()
    {
        $this->unit_crypt = crypt::new(conf::get('openssl'));
    }

    /**
     * @param array  $data
     * @param string $content_type
     *
     * @return string
     * @throws \Exception
     */
    public function send(string $api,string $token,array $data = [], string $content_type = parent::CONTENT_TYPE_ENCODED): string
    {
        //Build package
        $package = [
            'url'          => $api,
            'header'       => [
                'S-TOKEN' => $this->unit_crypt->sign(json_encode([$token])),
            ],
            'content_type' => &$content_type
        ];

        if (!empty($data)) {
            $package['data'] = $data;
        }
        return $this->add($package)->fetch();
    }

    /**
     * @param array  $data
     * @param string $content_type
     *
     * @return string
     * @throws \Exception
     */
    public function checkToken(string $token)
    {
        return $this->unit_crypt->verify($token) === '' ? true : false;
    }


}