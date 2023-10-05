<?php
error_reporting(E_ALL & ~E_NOTICE);

$loader = require __DIR__ . '/../vendor/autoload.php';

$hasura = get_client($_SERVER['HASURA_API'], $_SERVER['HASURA_GRAPHQL_ADMIN_SECRET']);
//$hasura = get_client('http://localhost:8080/v1/graphql', $_SERVER['HASURA_GRAPHQL_ADMIN_SECRET']);

/*
TODO: good logging
https://stackoverflow.com/questions/23844761/upstream-sent-too-big-header-while-reading-response-header-from-upstream
*/
//$res = shuttle($hasura, ['test' => 'huhu']);
//dbg('client test', $res);

$router = new \Bramus\Router\Router();

send_cors(get_req_headers($router));
$router->options('/.*', function () {
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
});

// list($post, $raw) = get_json_req();

$req = ['conf' => ['h' => $hasura], 'hdrs' => $_SERVER, 'get' => $_GET];

dbg('+++ incoming +++ ');
// dbg('hasura', $hasura, $_SERVER);

$router->get('/', function () use ($req) {
    resp(['res' => 'ok']);
});

/*
{
    "X-Hasura-User-Id": "25",
    "X-Hasura-Role": "user",
    "X-Hasura-Is-Owner": "true",
    "X-Hasura-Custom": "custom value"
}
*/

$router->mount('/events', function () use ($router, $hasura) {
    $api = new opbnb\events($hasura);

    $router->get('/(\w+)', function ($meth) use ($api) {
        $meth = "get_{$meth}";
        resp($api->$meth());
    });

    $router->post('/(\w+)', function ($meth) use ($api) {
        $meth = "{$meth}";
        $data = get_json_req();
        // dbg("+++ data", $data);

        resp($api->$meth($data));
    });
});

$router->mount('/actions', function () use ($router, $hasura) {
    $api = new opbnb\actions($hasura);

    $router->get('/(\w+)', function ($meth) use ($api) {
        $meth = "get_{$meth}";
        resp($api->$meth());
    });

    $router->post('/(\w+)', function ($meth) use ($api) {
        $meth = "{$meth}";
        $data = get_json_req();

        resp($api->$meth($data));
    });
});

$router->mount('/users', function () use ($router, $hasura) {

    session_name('weop');
    session_start();

    $api = new opbnb\users($hasura);

    $router->get('/(\w+)', function ($meth) use ($api) {
        $meth = "get_{$meth}";
        resp($api->$meth());
    });

    $router->post('/(\w+)', function ($meth) use ($api) {
        $meth = "{$meth}";
        $data = get_json_req();

        resp($api->$meth($data));
    });    

});

$router->get('/auth', function () use ($req) {
    $rsp = ['X-Hasura-User-Id' => '1', 'X-Hasura-Role' => 'user'];
    header('content-type: application/json');
    print json_encode($rsp);
});



$router->run();
