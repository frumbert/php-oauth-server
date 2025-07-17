<?php
namespace Idp\Handlers;

use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Idp\Repositories\{
  UserRepository,
  AccessTokenRepository
};
use League\OAuth2\Server\CryptKey;

class UserInfoHandler {
    public function handle(): void {

        // --- Load request from globals ---
        $psr17Factory = new Psr17Factory();
        $creator = new ServerRequestCreator(
            $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
        );
        $request = $creator->fromGlobals();

        $publicKey = new CryptKey(BASE_PATH . '/private/public.key', null, getenv('OAUTH2_KEY_STRICT') !== 'false');

        // --- Validate token ---
        $server = new ResourceServer(
            new AccessTokenRepository(),
            $publicKey
        );

        try {
            $request = $server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $e) {
            http_response_code($e->getHttpStatusCode());
            echo json_encode($e->getPayload());
            exit;
        }

        $accessToken = $request->getAttribute('oauth_access_token_id');
        $scopes = $request->getAttribute('oauth_scopes') ?? [];
        $scopeNames = is_array($scopes)
            ? $scopes
            : array_map(fn($s) => $s->getIdentifier(), $scopes);

        // --- Extract user ID from token ---
        $userId = $request->getAttribute('oauth_user_id');

        if (!$userId) {
            http_response_code(400);
            echo json_encode(['error' => 'User ID missing in token']);
            exit;
        }

        // --- Fetch user info ---
        $userRepo = new UserRepository();
        $user = $userRepo->getUserEntityByIdentifier($userId);

        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
            exit;
        }

        // --- Return claims ---
        $responseData = [
            'sub' => $user->getIdentifier(),
        ];
        if (in_array('email', $scopeNames)) {
            $responseData['email'] = $user->getEmail();
        }

        if (in_array('profile', $scopeNames)) {
            $responseData['given_name'] = $user->getFirstname();
            $responseData['family_name'] = $user->getLastname();
            $responseData['preferred_username'] = $user->getCustomerId();
            $responseData['title'] = $user->getTitle(); // optional
        }

        header('Content-Type: application/json');
        //echo json_encode($responseData); // , JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo json_encode([
            'sub' => $user->getIdentifier(),
            'preferred_username' => $user->getCustomerId(),
            'email' => $user->getEmail(),
            'given_name' => $user->getFirstname(),
            'firstname' => $user->getFirstname(),
            'family_name' => $user->getLastname(),
            'lastname' => $user->getLastname(),
            'title' => $user->getTitle(),
        ]);
    }
}
