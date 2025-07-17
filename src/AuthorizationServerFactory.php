<?php
namespace Idp;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\CryptKey;
use DateInterval;

use Idp\Repositories\{
    AccessTokenRepository,
    AuthCodeRepository,
    ClientRepository,
    ScopeRepository,
    RefreshTokenRepository
};

class AuthorizationServerFactory
{
  public function create(): AuthorizationServer
  {
      $clientRepo = new ClientRepository();
      $accessTokenRepo = new AccessTokenRepository();
      $scopeRepo = new ScopeRepository();
      $authCodeRepo = new AuthCodeRepository();
      $refreshTokenRepo = new RefreshTokenRepository();
      $privateKey = new CryptKey(BASE_PATH . '/private/private.key', null, false);

      $server = new AuthorizationServer(
          $clientRepo,
          $accessTokenRepo,
          $scopeRepo,
          $privateKey,
          getenv('OAUTH2_ENCRYPTION_KEY')
      );

      $authCodeGrant = new AuthCodeGrant(
          $authCodeRepo,
          $refreshTokenRepo,
          new DateInterval('PT10M') // Auth code expires in 10 minutes
      );

      $authCodeGrant->setRefreshTokenTTL(new DateInterval('P1M'));

      $server->enableGrantType(
          $authCodeGrant,
          new DateInterval('PT1H') // Access token TTL
      );

      return $server;
  }
}
