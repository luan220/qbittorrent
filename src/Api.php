<?php
declare(strict_types=1);

namespace silasmontgomery\QBittorrentWebApi;

use Curl\Curl;

class Api
{
    private $debug;
    private $url;
    private $api_version;
    private $curl;
    private $endpoints = [
        'login' => [
            '1' => '/login',
            '2' => '/api/v2/auth/login'
        ],
        'app_version' => [
            '1' => '/version/qbittorrent',
            '2' => '/api/v2/app/version'
        ],
        'api_version' => [
            '1' => '/version/api',
            '2' => '/api/v2/app/webapiVersion'
        ],
        'build_info' => [
            '1' => null,
            '2' => '/api/v2/app/buildInfo'
        ],
        'preferences' => [
            '1' => null,
            '2' => '/api/v2/app/preferences'
        ],
        'setPreferences' => [
            '1' => null,
            '2' => '/api/v2/app/setPreferences'
        ]
    ];

    public function __construct(string $url, string $username, string $password, int $api_version = 2, bool $debug = false)
    {
        $this->debug = $debug;
        $this->url = $url;
        $this->username = $username;
        $this->password = $password;
        $this->api_version = $api_version;
        $this->curl = new Curl();
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        // Authenticate and get cookie, else throw exception
        if (!$this->authenticate()) {
            throw new \Exception("Unable to authenticate with Web Api.");
        }
    }

    public function appVersion(): string
    {
        return $this->getData('app_version');
    }

    public function apiVersion(): string
    {
        return $this->getData('api_version');
    }

    public function buildInfo(): string
    {
        return $this->getData('build_info');
    }

    public function preferences(array $data = null): string
    {
        if (!empty($data)) {
            return $this->postData('setPreferences', $data);
        }
        
        return $this->getData('preferences');
    }

    private function getData(string $endpoint): string
    {
        $this->curl->get($this->url . $this->endpoints[$endpoint][$this->api_version]);

        if ($this->debug) {
            var_dump($this->curl->request_headers);
            var_dump($this->curl->response_headers);
        }

        if ($this->curl->error) {
            return $this->errorMessage();
        }
         
        return $this->curl->response;
    }

    private function postData(string $endpoint, array $data): string
    {
        $this->curl->post($this->url . $this->endpoints[$endpoint][$this->api_version], [
            'json' => json_encode($data),
        ]);

        if ($this->debug) {
            var_dump($this->curl->request_headers);
            var_dump($this->curl->response_headers);
        }

        if ($this->curl->error) {
            return $this->errorMessage();
        }
         
        return $this->curl->response;
    }

    private function authenticate(): bool
    {
        $this->curl->post($this->url . $this->endpoints['login'][$this->api_version], [
            'username' => $this->username,
            'password' => $this->password
        ]);

        if ($this->debug) {
            var_dump($this->curl->request_headers);
            var_dump($this->curl->response_headers);
        }

        // Find authentication cookie and set in curl connection
        foreach ($this->curl->response_headers as $header) {
            if (preg_match('/SID=(\S[^;]+)/', $header, $matches)) {
                $this->curl->setHeader('Cookie', $matches[0]);
                return true;
            }
        };

        return false;
    }

    private function errorMessage(): string
    {
        return 'Curl Error Code: ' . $this->curl->error_code . ' (' . $this->curl->response . ')';
    }
}
