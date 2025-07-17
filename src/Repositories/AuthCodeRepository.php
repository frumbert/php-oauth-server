<?php
namespace Idp\Repositories;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Idp\Entities\AuthCodeEntity;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new AuthCodeEntity();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        // In-memory only for now, do nothing.
        // For real use: save to DB with expiry and revocation
    }

    public function revokeAuthCode($codeId): void
    {
        // No-op for now
    }

    public function isAuthCodeRevoked($codeId): bool
    {
        return false; // Accept all codes
    }
}
