<?php

require_once __DIR__ . '/api.php';


use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;
use GuzzleHttp\Client;

function resp($data) {
    header('content-type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    print json_encode($data);

    dbg('+++ finished +++');
}

function check_token_jwks($token, $key = '') {
    $c = get_client('http://host.docker.internal:9011/api/jwt/', null, $token);
    $ok = shuttle($c, [], ['method' => 'GET', 'url' => 'validate']);

    $keyd = json_decode($key, true);
    $k = sprintf("-----BEGIN PUBLIC KEY-----\n%s\n-----END PUBLIC KEY-----", $keyd['keys'][0]['x5c'][0]);
    dbg('+++ decoded key ', $ok);
    dbg('+++ decoded key2 ', $k);
    $jwt = new JWT($k, 'RS256');
    $payload = $jwt->decode($token);
    return $payload;
}
function check_token($token, $pubkey) {
    JWT::$leeway = 6000;
    return JWT::decode($token, $pubkey, ['RS256']);
}

function get_token_config($file) {
    $conf = ['type' => 'RS256', 'key' => file_get_contents($file . '.pub')];
    return json_encode($conf);
}

function gen_token($file, $props = [], $hasura = []) {
    if (!$hasura) {
        $hasura = [
            'x-hasura-allowed-roles' => ['user'],
            'x-hasura-default-role' => 'user',
            'x-hasura-user-id' => '1',
            // "x-hasura-org-id" => "1",
            // "x-hasura-custom": "custom-value"
        ];
    }
    // $jwt = new JWT('secret', 'HS256', 900, 10);
    $key = __DIR__ . '/' . $file;
    dbg('++++ using key file', $key); 
   // print file_get_contents($key);
    $jwt = new JWT($key, 'RS256', 900, 10);
    $token = $jwt->encode([
        'uid' => 1,
        'aud' => 'http://site.com',
        'scopes' => ['user'],
        'iss' => 'http://api.mysite.com',
        'https://hasura.io/jwt/claims' => $hasura
    ]);
    return $token;
}

function get_client($url, $secret = null, $token = null, $hdrs = null) {
    $opts = [
        // Base URI is used with relative requests
        'base_uri' => $url,
        // You can set any number of default request options.
        'timeout' => 20.0,
    ];
    if ($secret) {
        $opts['headers'] = ['x-hasura-admin-secret' => $secret];
    }
    if ($token) {
        $opts['headers'] = ['Authorization' => 'Bearer ' . $token];
    }
    if ($hdrs) {
        $opts['headers'] = $hdrs;
    }
    // print_r($opts);
    $client = new Client($opts);
    dbg('get guzzle client', $client, $opts);
    return $client;
}

function post_graphql($client, $q, $data, $op = null) {
    $query = file_get_contents(__DIR__ . '/../queries/' . $q . '.gql');
    return post_graphql_q($client, $query, $data, $op);
}

function post_graphql_q($client, $query, $data = null, $op = null) {
    if (!$data) {
        $data = null;
    }
    $p = ['query' => $query, 'variables' => $data];
    if ($op) {
        $p['operationName'] = $op;
    }
    dbg('POST', $p);
    $resp = shuttle($client, $p);
    dbg('resp+++', $resp);
    return $resp['data'];
}


function post_req($client, $path = '', $data = []) {
    return shuttle($client, $data, ['url' => $path]);
}

function get_req($client, $path = '', $data = []) {
    return shuttle($client, $data, ['url' => $path, 'method' => 'GET']);
}

function shuttle($client, $data, $opts = []) {
    $meth = $opts['method'] ?? 'POST';
    $url = $opts['url'] ?? '';
    $extra_hdrs = $opts['headers'] ?? null;

    $hdrs = [
        'User-Agent' => 'testing/1.0',
        'Content-Type' => 'application/json'
    ];
    if($extra_hdrs != null){
        $hdrs = array_merge($hdrs, $extra_hdrs); 
    }

    if ($meth == 'GET') {
        $query = $data;
        $body = null;
        unset($hdrs['Content-Type']);
    } elseif ($meth == 'DELETE') {
        unset($hdrs['Content-Type']);
    } else {
        $query = null;
        $body = json_encode($data);
    }

    if ($meth == 'DELETE') {
        $body = null;
    }

    //dbg('++ guzzle', $meth, $url, $query);

    try {
        $resp = $client->request($meth, $url, [
            'headers' => $hdrs,
            'query' => $query,
            'body' => $body,
            //   'connect_timeout' => 20.0
        ]);
    } catch (throwable $e) {
        dbg('exception: ', $e->getMessage());
        // TODONE: fetch that damn response code
        return ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'data' => null];
    }

    $code = $resp->getStatusCode();
    $bdy = $resp->getBody();
    $dta = json_decode($bdy, true);
    //dbg('result: ', $code, $dta);
    return ['code' => $code, 'data' => $dta];
}

function dbg($txt, ...$vars) {
    // im servermodus wird der zeitstempel automatisch gesetzt
    //	$log = [date('Y-m-d H:i:s')];
    $log = [];
    if (!is_string($txt)) {
        array_unshift($vars, $txt);
    } else {
        $log[] = $txt;
    }
    $log[] = join(' ', array_map('json_encode', $vars));
    error_log(join(' ', $log));
}

function get_json_and_raw_req() {
    $raw = get_raw_req();
    $post = json_decode($raw, true);
    return [$post, $raw];
}

function get_json_req() {
    return json_decode(get_raw_req(), true);
}

function get_raw_req() {
    dbg('++++ raw input read ++++');
    return file_get_contents('php://input');
}

function get_req_headers($router)
{
    dbg('+++ router get headers');
    return array_change_key_case($router->getRequestHeaders());
}

function send_cors($hdrs)
{
    // dbg("hdrs", $hdrs);
    $origin = $hdrs['origin']??null;
    if ($origin && str_starts_with($origin, 'http://localhost')) {
        header("Access-Control-Allow-Origin: ".$origin);
        header('Access-Control-Allow-Credentials: true');
        header("Access-Control-Allow-Headers: Content-Type, Authorization, Access-Control-Allow-Origin");
    }
}

function base64_url_encode( $data ) {
    return strtr( base64_encode($data), '+/=', '-_,' );
}

function base64_url_decode( $data ) {
    return base64_decode( strtr($data, '-_,', '+/=') );
}



