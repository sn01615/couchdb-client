<?php
use PHPUnit\Framework\TestCase;
use sn01615\CouchDBClient;

class CouchDBClientTest extends TestCase
{

    public function testHello(): void
    {
        $instance = new CouchDBClient();
        $version = $instance->hello();
        
        echo PHP_EOL;
        print_r($version);
        
        $this->assertInstanceOf(CouchDBClient::class, $instance);
    }
}

