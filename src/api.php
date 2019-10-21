<?php 

namespace silasmontgomery\QBittorrentWebAPI;

class API
{
    private $hostname;
    private $cookie;

    function __construct(string $hostname) {
        $this->hostname = $hostname;
    }

    public function authenticate(string $username, string $password)
    {
        return "Authenticating with $hostname using Username: $username and Password: $password";
    }
}