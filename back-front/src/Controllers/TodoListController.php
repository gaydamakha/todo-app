<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class TodoListController
{
    protected $container;

    // constructor receives container instance
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function todos(Request $request) {
        /** @var Twig $view */
        $view = $this->container->get('view');
        /** @var Client $client */
        $client = $this->container->get('http_client');
        //Fetch the todos from API
        throw new \Exception($request->getHeader('Authorization'));
        //TODO: treat possible errors
        $todos = $client->request('GET', 'api/todos', ['headers'  => [
            'Content-type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization'=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzkwODMwMDYsImV4cCI6MTU3OTA4NjYwNiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYmlsbC5nYXRlc0BvdXRsb29rLmNvbSJ9.Chg-ni3ZBxpLC9_dZFTEUiJfiWWH2I7hzqpOzxYcPU9I5GhNJyc8LS3cwgC2T_mAHzmGm2upEhyBm4jb4zGa3hvjuSS3PJlnsbtLemFKpnAPxIxDN09-dqQxVXatCESf3TWhbDQXUOBHxWmAecBwb7pk0C3Qpx51e3H45HEPGIt-MEf5JdV22KYtrXtdNd0x84Gj7lU09A0V_0w5HF653jf10bk_6kiSqfMswwQpk8K_74m7EmnaH15jCDbtMdT4OECH7P72p_BvwG8q1QBhgvd6OK5idclV47yjk4C7qPL13Re2-AThTVU2Q_TuTPLfcOQER-3nuCIPXJf4ubMluGAgvhjzMFjSzYOmUKn-ZPwR_kMSFgYEXGFbiLa7f40D7yPNshHcg2KKIjf2ZVZFSsb3410pPjW8G6UpP8jDpGjQ7U4vHGva2IPwNDTtwSLB41Ex1EvyT_S3040oxb1mL5VPJkDW9HhjPyN0v5etA0Jlas4l567HSB_7yv7Dts8Uu2V46JFD_8ABjvdIT7OJ6_kjnp3-ksdA86TDdCg8hZVuyyb1Bz7MCcFvL7SGYOYp2FIEMOlnS2Wd7EdySBO6eTMclUJrlJhCgWqqonumqE5bJit9-mwLAB3aK3jXi1h8LsGTbY1OBRC6FuDLWABqVfJU2FvCynW0Nh_oE73ULZU"
        ]]);
        $response = $view->render(new Response(),'todo.html.twig', ['todos' => json_decode($todos->getBody(), true)['data']]);
        return $response;
    }

    public function profile() {
        //TODO: fetch username and token from session
        $username = "";
        /** @var Twig $view */
        $view = $this->container->get('view');
        $client = $this->container->get('http_client');
        //Fetch the user from API
        $user = $client->request('GET', 'users/' . $username,['headers'  => [
            'Content-type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization'=> "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NzkwODMwMDYsImV4cCI6MTU3OTA4NjYwNiwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYmlsbC5nYXRlc0BvdXRsb29rLmNvbSJ9.Chg-ni3ZBxpLC9_dZFTEUiJfiWWH2I7hzqpOzxYcPU9I5GhNJyc8LS3cwgC2T_mAHzmGm2upEhyBm4jb4zGa3hvjuSS3PJlnsbtLemFKpnAPxIxDN09-dqQxVXatCESf3TWhbDQXUOBHxWmAecBwb7pk0C3Qpx51e3H45HEPGIt-MEf5JdV22KYtrXtdNd0x84Gj7lU09A0V_0w5HF653jf10bk_6kiSqfMswwQpk8K_74m7EmnaH15jCDbtMdT4OECH7P72p_BvwG8q1QBhgvd6OK5idclV47yjk4C7qPL13Re2-AThTVU2Q_TuTPLfcOQER-3nuCIPXJf4ubMluGAgvhjzMFjSzYOmUKn-ZPwR_kMSFgYEXGFbiLa7f40D7yPNshHcg2KKIjf2ZVZFSsb3410pPjW8G6UpP8jDpGjQ7U4vHGva2IPwNDTtwSLB41Ex1EvyT_S3040oxb1mL5VPJkDW9HhjPyN0v5etA0Jlas4l567HSB_7yv7Dts8Uu2V46JFD_8ABjvdIT7OJ6_kjnp3-ksdA86TDdCg8hZVuyyb1Bz7MCcFvL7SGYOYp2FIEMOlnS2Wd7EdySBO6eTMclUJrlJhCgWqqonumqE5bJit9-mwLAB3aK3jXi1h8LsGTbY1OBRC6FuDLWABqVfJU2FvCynW0Nh_oE73ULZU"
        ]]);
        $response = $view->render(new Response(),'profile.html.twig', ['user' => $user]);
        return $response;
    }
}
