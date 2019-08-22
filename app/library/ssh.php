<?php

class ssh
{
    private $host     = '127.0.0.1';
    private $user     = 'test';
    private $password = 'test';
    private $port     = 22;
    private static $connect;
    public function __construct(array $config = [])
    {
        if(!extension_loaded("ssh2")){
            die("needs ssh2 extension!");
        }
        if (!empty($config)) {
            $this->host     = $config['host'];
            $this->user     = $config['user'];
            $this->password = $config['password'];
            $this->port     = $config['port'];
        };
        $this->connect();
    }
    private function connect()
    {
        self::$connect = ssh2_connect($this->host, $this->port);
        $isAuth = ssh2_auth_password(self::$connect, $this->user, $this->password);
        if(!$isAuth){
            die('connect fail');
        }

    }
    // $cmd="cd /tmp; git pull;ls -a;";
    public function exec($cmd):bool
    {
        $ret=ssh2_exec(self::$connect,$cmd);
        stream_set_blocking($ret, true);
        return stream_get_contents($ret);
    }

}