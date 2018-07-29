<?php

use PHPUnit\Framework\TestCase;
use PhpUtils\RandString;
use sn01615\CouchDBClient;

class CouchDBClientTest extends TestCase
{

    public function testHello(): void
    {
        $client = new CouchDBClient([
            'server' => 'http://127.0.0.1:5984'
        ]);

        $this->assertInstanceOf(CouchDBClient::class, $client);

        $version = $client->get();

        $this->assertNotFalse($version);

        $this->assertEquals($version->couchdb, 'Welcome');
    }

    public function testGetAllDbs(): void
    {
        $instance = new CouchDBClient();

        $dbs = $instance->getAllDbs();

        $this->assertNotFalse($dbs);
    }

    public function testCreateDb(): void
    {
        $instance = new CouchDBClient();

        $dbName = RandString::getRandString(64);
        $dbName = strtolower($dbName);

        $result = $instance->put($dbName);

        $this->assertNotFalse($result);

        $this->assertEquals(isset($result->error), false);

        $this->assertEquals($result->ok, true);

        $result = $instance->get($dbName);

        $this->assertNotFalse($result);

        $this->assertEquals(isset($result->error), false);

        $this->assertEquals($result->db_name, $dbName);

        $testKey = strtolower(RandString::getRandString(64));

        $document = new stdClass();
        $document->id = 123;
        $document->text = "hello 中文";

        $result = $instance->setDbName($dbName)->putDocument($testKey, $document);

        $this->assertNotFalse($result);

        $this->assertEquals($result->ok, true);

        $result = $instance->setDbName($dbName)->getDocument($testKey);

        $this->assertNotFalse($result);

        $this->assertEquals($result->_id, $testKey);

        $document = new stdClass();
        $document->id = 123;
        $document->text = "hello 中文";

        try {
            $instance->setDbName($dbName)->updateDocument($testKey, $document);
        } catch (LogicException $e) {
            $this->assertEquals($e->getMessage(), 'no _rev set');
        }

        $document = new stdClass();
        $document->id = 123;
        $document->text = "hello 中文";
        $document->_rev = $result->_rev;

        $result = $instance->setDbName($dbName)->updateDocument($testKey, $document);

        $this->assertNotFalse($result);

        $this->assertEquals($result->ok, true);

        $document = new stdClass();
        $document->id = 123;
        $document->text = "hello 中文";

        $result = $instance->setDbName($dbName)->upsertDocument($testKey, $document);

        $this->assertNotFalse($result);

        $this->assertEquals($result->ok, true);

        $result = $instance->setDbName($dbName)->getUiid();

        $this->assertNotFalse($result);

        $this->assertNotFalse($result->uuids);

        $result = $instance->delete($dbName);

        $this->assertNotFalse($result);

        $this->assertEquals($result->ok, true);
    }
}

