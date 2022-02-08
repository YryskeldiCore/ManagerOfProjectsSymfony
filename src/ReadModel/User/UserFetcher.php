<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;

class UserFetcher
{
    /**
     * @var Connection
     */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function existsByResetToken(string $token): bool
    {
        return $this->connection->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter(':token', $token)
            ->execute()->fetchFirstColumn() > 0;

    }

    public function findForAuthByEmail(string $email): array
    {
        $stmt =  $this->connection->createQueryBuilder()
            ->select('id, email, password_hash, role, status')
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();


        return $stmt->fetchAllAssociative();
    }

    public function findForAuthByNetwork(string $network, string $identity): array
    {
        $stmt =  $this->connection->createQueryBuilder()
            ->select('id, email, password_hash, role, status')
            ->from('user_users', 'u')
            ->innerJoin('u', 'user_user_networks', 'n', 'n.user_id = u.id' )
            ->where('n.network = :network AND n.identity = :identity')
            ->setParameters([
                'network' => $network,
                'identity' => $identity,
            ])
            ->execute();


        return $stmt->fetchAllAssociative();
    }
}