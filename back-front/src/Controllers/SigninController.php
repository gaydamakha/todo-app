<?php

namespace App\Controllers;

use Adbar\Session;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;
use Slim\Http\Request as ServerRequest;
use Slim\Http\Response;

class SigninController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function signin(ServerRequest $request)
    {
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');

        $req = new Request('POST', '/api/login_check', [],
            json_encode([
                'username' => $username,
                'password' => $password
            ])
        );

        try {
            $response = $client->send($req);
        } catch (ClientException $e) {
            $session->add('flash_error', json_decode($e->getResponse()->getBody()->getContents(), true)['message']);
            return (new Response())->withRedirect('/signin', 301);
        }

        if (200 === $response->getStatusCode()) {
            $session->set('username', $username);
            $session->set('token', 'Bearer ' . json_decode($response->getBody(), true)['token']);
            $session->set('is_logged', true);
            return (new Response())
                ->withRedirect('/todos', 301);
        }

        return new Response();
    }

    public function signup(ServerRequest $request)
    {
        /** @var Client $client */
        $client = $this->container->get('http_client');
        /** @var Session $session */
        $session = $this->container->get('session');
        $username = $request->getParsedBodyParam('username');
        $password = $request->getParsedBodyParam('password');
        $firstname = $request->getParsedBodyParam('firstname');
        $lastname = $request->getParsedBodyParam('lastname');

        $data = [
            'username' => $username,
            'password' => $password,
            'firstname' => $firstname,
            'lastname' => $lastname
        ];

        $req = new Request('POST', '/signup', [
            'Content-Type' => 'application/json'
        ],
            json_encode(['data' => $data])
        );

        try {
            $client->send($req);
        } catch (ClientException $e) {
            $session->add('flash_error', json_decode($e->getResponse()->getBody()->getContents(), true)['error']);
            return (new Response())->withRedirect('/signup', 301);
        }

        $session->add('flash_success', 'User ' . $username . ' is successfully created!');
        return (new Response())->withRedirect('signin', 301);
    }
}
