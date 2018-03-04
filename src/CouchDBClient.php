<?php
namespace sn01615;

/**
 *
 * @author YangLong
 * Date: 2018-03-04
 */
class CouchDBClient
{

    private $httpClient, $uri;

    public function __construct($uri = 'http://user:pass@127.0.0.1:5984/')
    {
        $this->uri = $uri;
        
        $this->httpClient = new \GuzzleHttp\Client([
            \GuzzleHttp\RequestOptions::VERIFY => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath()
        ]);
    }

    public function hello()
    {
        $res = $this->httpClient->request('GET', $this->uri);
        $response = $res->getBody();
        $response = json_decode($response);
        return $response;
    }
}

