<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Response;

class ProxyController extends BaseController
{
    public function __construct()
    {
        $this->host = getQuintypeAPIHost(Request());
    }

    function getRoute($request) {
        $baseUrl = $this->host . "/api/" . $request->route("route");
        $queryString = $request->getQueryString();

        if($queryString && $queryString != "")
            return $baseUrl . '?' . $queryString;
        else
            return $baseUrl;
    }

    public function proxyGet(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $request_headers = [];
        

        if (isset($_COOKIE['session-cookie']) && $_COOKIE['session-cookie'] != '') {
            $request_headers = ['X-QT-AUTH' => $_COOKIE['session-cookie']];
        }
        $res = $client->request('GET', $this->getRoute($request), ['headers' => $request_headers]);
        $headers = $res->getHeaders();
        unset($headers['Transfer-Encoding']);

        return response($res->getBody(), $res->getStatusCode())->withHeaders($headers);
    }

    public function proxyPost(Request $request, Response $response)
    {
        $client = new \GuzzleHttp\Client();
        $request_headers = [];
        if (isset($_COOKIE['session-cookie']) && $_COOKIE['session-cookie'] != '') {
            $request_headers = ['X-QT-AUTH' => $_COOKIE['session-cookie']];
        }
        $res = $client->request('POST', $this->getRoute($request), ['body' => $request->getContent(), 'headers' => $request_headers]);
        $headers = $res->getHeaders();
        unset($headers['Transfer-Encoding']);

        return response($res->getBody(), $res->getStatusCode())->withHeaders($headers);
    }
}
