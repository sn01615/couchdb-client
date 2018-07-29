<?php

namespace sn01615;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

/**
 *
 * @author YangLong
 *
 */
class CouchDBClient
{

    private $httpClient, $uri, $server;

    private $dbName;

    public function __construct(array $options = [])
    {
        if (!isset($options['server'])) {
            $options['server'] = 'http://127.0.0.1:5984';
        }
        $this->server = $options['server'];

        $this->httpClient = new Client([
            RequestOptions::VERIFY => CaBundle::getSystemCaRootBundlePath()
        ]);
    }

    private function query($method, $url, $data = null)
    {
        $url = trim($url);
        $url = ltrim($url, '/');
        $this->server = trim($this->server, '/');
        $this->uri = $this->server . '/' . $url;
        try {
            $options = [
                RequestOptions::HTTP_ERRORS => false,
            ];
            if ($data) {
                $options[RequestOptions::BODY] = $data;
            }
            $res = $this->httpClient->request($method, $this->uri, $options);
            $response = $res->getBody();
        } catch (GuzzleException $e) {
            return false;
        }

        $response = json_decode($response);
        return $response;
    }

    public function get($url = '')
    {
        $response = $this->query('GET', $url);
        return $response;
    }

    public function put($url = '', $data = null)
    {
        $response = $this->query('PUT', $url, $data);
        return $response;
    }

    public function delete($url = '')
    {
        $response = $this->query('DELETE', $url);
        return $response;
    }

    public function setDbName($dbName)
    {
        if (empty($dbName)) {
            throw new \LogicException('dbName can not empty');
        }
        $this->dbName = $dbName;
        return $this;
    }

    public function putDocument($key, \stdClass $document)
    {
        if (empty($this->dbName)) {
            throw new \LogicException('no database select');
        }

        $key = urlencode($key);
        $url = $this->dbName . '/' . $key;

        $document = json_encode($document);

        $response = $this->put($url, $document);
        return $response;
    }

    public function updateDocument($key, \stdClass $document)
    {
        if (empty($document->_rev)) {
            throw new \LogicException('no _rev set');
        }

        return $this->putDocument($key, $document);
    }

    public function upsertDocument($key, \stdClass $document)
    {
        $result = $this->putDocument($key, $document);
        if ($result && $result->error == 'conflict') {
            $result = $this->getDocument($key);
            if ($result) {
                $document->_rev = $result->_rev;
            }
        }
        return $this->putDocument($key, $document);
    }

    public function getDocument($key)
    {
        if (empty($this->dbName)) {
            throw new \LogicException('no database select');
        }

        $key = urlencode($key);
        $url = $this->dbName . '/' . $key;

        $response = $this->get($url);
        return $response;
    }

    public function getUiid()
    {
        $url = '_uuids';
        $response = $this->get($url);
        return $response;
    }

    public function getAllDbs()
    {
        $response = $this->get('_all_dbs');
        return $response;
    }
}

