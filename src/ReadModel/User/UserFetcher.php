<?php
declare(strict_types=1);

namespace App\ReadModel\User;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

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
            ->select('u.id, u.email, u.password_hash, u.role, u.status')
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

    /**
     * @throws Exception
     */
    public function findByEmail(string $email): ?ShortView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id, u.email, u.role, u.status')
            ->from('user_users', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->execute();

        $result = $stmt->fetch();

        if ($result === false) {
            return null;
        }

        return new ShortView($result['id'], $result['email'], $result['role'], $result['status']);
    }

    /**
     * @throws Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id, u.date, u.email, u.role, u.status')
            ->from('user_users', 'u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->execute();

        $result = $stmt->fetch();

        if ($result === false) {
            return null;
        }

        $detailView = new DetailView($result['id'],$result['date'], $result['email'], $result['role'], $result['status']);

        $stmt = $this->connection->createQueryBuilder()
            ->select('network', 'identity')
            ->from('user_user_networks')
            ->where('user_id = :id')
            ->setParameter('id', $id)
            ->execute();

        $result = $stmt->fetchAllAssociative();

        $networks = [];

        foreach ($result as $item) {
            $networks[] = new NetworkView($item['network'], $item['identity']);
        }

        $detailView->networks = $networks;

        return $detailView;
    }
}