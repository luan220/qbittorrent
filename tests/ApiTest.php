<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use silasmontgomery\QBittorrentWebApi\Api;

class ApiTest extends TestCase
{
    public function __construct()
    {
        parent::__construct();

        $dotenv = Dotenv\Dotenv::create(dirname(__DIR__, 1));
        $dotenv->load();
    }

    public function testApiConstructException(): void
    {
        $this->expectException(ArgumentCountError::class);
        $api = new Api();
    }

    public function testAuthenticateReturnsTrue(): void
    {
        $api = new Api(getenv('URL'), intval(getenv('PORT')));
        
        $this->assertTrue($api->authenticate(getenv('USERNAME'), getenv('PASSWORD')));
    }

    public function testAuthenticateReturnsFalse(): void
    {
        $api = new Api(getenv('URL'), intval(getenv('PORT')));
        
        $this->assertNotTrue($api->authenticate("", ""));
    }

    public function testAppVersionIsString(): void
    {
        $api = new Api(getenv('URL'), intval(getenv('PORT')));
        
        $this->assertIsString($api->appVersion());
    }

    public function testApiVersionIsString(): void
    {
        $api = new Api(getenv('URL'), intval(getenv('PORT')));
        
        $this->assertIsString($api->apiVersion());
    }
}
