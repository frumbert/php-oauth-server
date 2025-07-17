<?php
namespace Idp\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Idp\Entities\ClientEntity;
use Idp\Database;

class ClientRepository implements ClientRepositoryInterface {

    protected \PDO $pdo;

    public function __construct()
    {
      $this->pdo = Database::connection();
    }

    public function getClientEntity($clientIdentifier): ?ClientEntityInterface {
        $stmt = $this->pdo->prepare('SELECT * FROM oauth_clients WHERE id = :id');
        $stmt->execute(['id' => $clientIdentifier]);
        $client = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$client) {
            return null;
        }

        return new ClientEntity(
            $client['id'],
            $client['name'] ?? '',
            $client['redirect_uri'],
            $client['confidential'] ?? true
        );

    }

    public function validateClient(
          $clientIdentifier,
          $clientSecret,
          $grantType
      ): bool {
          $stmt = $this->pdo->prepare('SELECT * FROM oauth_clients WHERE id = :id');
          $stmt->execute(['id' => $clientIdentifier]);
          $client = $stmt->fetch(\PDO::FETCH_ASSOC);

          if (!$client) {
              return false;
          }

          if (!empty($client['secret'])) {
              return password_verify($clientSecret ?? '', $client['secret']);
          }

          // Allow public clients
          return empty($client['secret']);
      }

}
