<?php
namespace Idp\Repositories;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use Idp\Entities\RefreshTokenEntity;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function getNewRefreshToken(): RefreshTokenEntityInterface
    {
        return new RefreshTokenEntity();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        // For demo only – do nothing
    }

    public function revokeRefreshToken($tokenId): void
    {
        // For demo only – do nothing
    }

    public function isRefreshTokenRevoked($tokenId): bool
    {
        return false; // Accept all refresh tokens for demo
    }
}
