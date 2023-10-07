<?php
use \Firebase\JWT\JWT;

function get_keys($path_to_private_key_file)
{
    $privateKey = file_get_contents($path_to_private_key_file);
    $key = openssl_pkey_get_private($privateKey);
    $res = openssl_pkey_get_details($key);
    return ['private' => $privateKey, 'public' => $res['key']];
}

function generate_jwt($issuer, $user_id, $email, $applicationId, $exp, $rolle)
{
    $keys = get_keys(__DIR__.'/../private.key');
    $payload = array(
        "iss" => $issuer,
        "exp" => $exp,
        "sub" => (string) $user_id,
        'email' => $email,
        #"data" => $data,
        #'applicationId' => $applicationId,
        'https://hasura.io/jwt/claims' => [
            'x-hasura-allowed-roles' => [$rolle],
            'x-hasura-default-role' => $rolle,
        #    #'x-hasura-role' => $role,
            'x-hasura-user-id' => (string) $user_id,
        #    'x-hasura-org-id' => $org_id,
        #    #'x-hasura-custom' => $data
        #    'x-hasura-api-token-id' => $api_token_id,
        ]
    );
    
    return JWT::encode($payload, $keys['private'], 'RS256');
}

function decode_jwt($jwt)
{
    $keys = get_keys(__DIR__.'/../private.key');
    return JWT::decode($jwt, $keys['public'], array('RS256'));
}