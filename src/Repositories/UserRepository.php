<?php
namespace Idp\Repositories;

use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Idp\Entities\UserEntity;
use Idp\Database;

class UserRepository implements UserRepositoryInterface
{

    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    /**
     * Used by /userinfo endpoint
     */
    public function getUserEntityByIdentifier(string $userId): ?UserEntity
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user) {
            return new UserEntity(
              $user['id'],
              $user['email'],
              $user['firstname'],
              $user['lastname'],
              $user['title'],
              $user['customerid']
            );
        }

        return null;
    }

    /**
     * Required for password login (authorization step)
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
       $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return new UserEntity(
              $user['id'],
              $user['email'],
              $user['firstname'],
              $user['lastname'],
              $user['title'],
              $user['customerid']
            );
        }

        return null;

    }
}
