<?php
namespace Idp\Repositories;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use Idp\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    // Called when the server needs a fresh access token instance
    public function getNewToken(
        ClientEntityInterface $clientEntity,
        array $scopes,
        $userIdentifier = null
    ): AccessTokenEntityInterface {
        $token = new AccessTokenEntity();
        $token->setClient($clientEntity);
        $token->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }
        return $token;
    }

    // Called when the server is about to persist the issued token (we’ll noop for now)
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        // For now, no persistence. In production, you'd store it in a DB or Redis.
    }

    public function revokeAccessToken($tokenId): void
    {
        // Not storing tokens = nothing to revoke
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return false; // Accept all tokens for demo
    }
}
