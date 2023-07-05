<?php

declare(strict_types=1); // Узнать для чего нужны и про UUID не забыть

namespace App\Model\User\Entity\User;


use App\Model\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepository extends ServiceEntityRepository
{

    private EntityManagerInterface $em;

    private $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    /**
     * @param string $token
     * @return User|object|null
     */
    public function findByConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['confirmToken' => $token]);
    }

    /**
     * @param string $resetToken
     * @return User|object|null
     */
    public function findByResetToken(string $resetToken):?User
    {
        return $this->repo->findOneBy(['resetToken.token' => $resetToken]);
    }


    public function get(Id $id): User
    {
        /**
         * @var User $user
         */
        if(!$user = $this->repo->find($id)){
            throw new EntityNotFoundException('User is not found!');
        }
        return $user;
    }

    public function getByEmail(Email $email): User
    {
        /**
         * @var User $user
         */
        if(!$user = $this->repo->findOneBy(['email' => $email])){
            throw new EntityNotFoundException('User is not found!');
        }

        return $user;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->andWhere('t.email = :email')
            ->setParameter(':email', $email->getValue())
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function hasByNetworkIdentity(string $network, string $identity): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->innerJoin('t.networks', 'n')
            ->andWhere('n.network = :network and n.identity = :identity')
            ->setParameter(':network', $network)
            ->setParameter(':identity', $identity)
            ->getQuery()
            ->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }
}