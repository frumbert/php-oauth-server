<?php

namespace Idp\Handlers;

class OpenIdConfigurationHandler
{
    public function handle(): void
    {
        $config = [
            'issuer' => URL,
            'authorization_endpoint' => URL."/authorize",
            'token_endpoint' => URL."/token",
            'userinfo_endpoint' => URL."/userinfo",
            'jwks_uri' => URL."/.well-known/jwks.json",
            'response_types_supported' => ['code'],
            'subject_types_supported' => ['public'],
            'id_token_signing_alg_values_supported' => ['RS256'],
            'scopes_supported' => ['openid', 'profile', 'email'],
            'token_endpoint_auth_methods_supported' => ['client_secret_post', 'client_secret_basic'],
            'claims_supported' => ['sub', 'email', 'given_name', 'family_name', 'preferred_username', 'title'],
        ];

        header('Content-Type: application/json');
        echo json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
