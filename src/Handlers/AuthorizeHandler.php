<?php

namespace Idp\Handlers;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use League\OAuth2\Server\ResponseTypes\RedirectResponse;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Idp\Repositories\{
  ClientRepository,
  AccessTokenRepository,
  ScopeRepository,
  AuthCodeRepository,
  RefreshTokenRepository,
  UserRepository
};
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\CryptKey;

class AuthorizeHandler
{
    public function handle(): void
    {
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
        );

        $request = $creator->fromGlobals();

        // Build auth server
        $authServer = new AuthorizationServer(
            new ClientRepository(),
            new AccessTokenRepository(),
            new ScopeRepository(),
            new CryptKey(BASE_PATH . '/private/private.key'),
            getenv('OAUTH2_ENCRYPTION_KEY')
        );

        // Enable Auth Code grant
        $authCodeGrant = new AuthCodeGrant(
            new AuthCodeRepository(),
            new RefreshTokenRepository(),
            new \DateInterval('PT10M') // auth code lifetime
        );
        $authServer->enableGrantType($authCodeGrant, new \DateInterval('PT1H'));

        try {
            $authRequest = $authServer->validateAuthorizationRequest($request);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = $_POST['username'] ?? '';
                $password = $_POST['password'] ?? '';

                $userRepo = new UserRepository();
                $user = $userRepo->getUserEntityByUserCredentials(
                    $username,
                    $password,
                    'authorization_code',
                    $authRequest->getClient()
                );

                if (!$user) {
                    throw new \RuntimeException('Invalid credentials');
                }

                $authRequest->setUser($user);
                $authRequest->setAuthorizationApproved(true);

                $response = $authServer->completeAuthorizationRequest($authRequest, $psr17Factory->createResponse());

                http_response_code($response->getStatusCode());
                foreach ($response->getHeaders() as $name => $values) {
                    foreach ($values as $value) {
                        header("$name: $value", false);
                    }
                }
                echo (string) $response->getBody();
                return;
            }

            // Render login form
            echo <<<HTML
            <html>
            <head>
            <title>oAuth2 idP</title>
            <style>h2{color:rgb(15, 108, 191)}</style>
            </head>
            <body>
              <h1>oAuth2 PHP Example (oauth2.thephpleague.com)</h1>
              <h2>Imagine this is the Avant Member Login</h2>
              <form method="post">
                <input type="text" name="username" placeholder="Username" required /><br>
                <input type="password" name="password" placeholder="Password" required /><br>
                <button type="submit">Login</button>
              </form>
            </body>
            </html>
            HTML;

        } catch (OAuthServerException $e) {
            http_response_code($e->getHttpStatusCode());
            echo json_encode($e->getPayload());
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
