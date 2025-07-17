<?php
namespace Idp\Handlers;

use Nyholm\Psr7\Factory\Psr17Factory;
use League\OAuth2\Server\AuthorizationServer;
use Nyholm\Psr7Server\ServerRequestCreator;

class TokenHandler {
    public function handle(): void {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
          $psr17Factory, // ServerRequestFactory
          $psr17Factory, // UriFactory
          $psr17Factory, // UploadedFileFactory
          $psr17Factory  // StreamFactory
        );

        $serverRequest = $creator->fromGlobals();

        $server = (new \Idp\AuthorizationServerFactory())->create();

        $response = $psr17Factory->createResponse();

        try {
            $response = $server->respondToAccessTokenRequest($serverRequest, $response);
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Token error: ' . $e->getMessage();
            return;
        }

        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }
        echo $response->getBody();
    }
}
