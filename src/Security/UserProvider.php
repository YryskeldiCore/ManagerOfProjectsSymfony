<?php


namespace App\Security;


use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


class UserProvider implements UserProviderInterface
{

    private $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function loadUserByUsername($username): UserInterface
    {
        $user = self::loadUser($username);
        return self::identityByUser($user);
    }

    public function refreshUser(UserInterface $identity):UserInterface
    {
        if(!$identity instanceof UserIdentity){
            throw new UnsupportedUserException('Invalid user class' . \get_class($identity));
        }
        $user = self::loadUser($identity->getUsername());
        return self::identityByUser($user);
    }

    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    public function loadUser($username)
    {
        if(!$user = $this->users->findForAuth($username)){
            throw new UserNotFoundException('User not found!');
        }

        return $user;
    }

    public function identityByUser($user): UserIdentity
    {
        return new UserIdentity(
            $user[0]['id'],
            $user[0]['email'],
            $user[0]['password_hash'],
            $user[0]['role'],
            $user[0]['status']
        );
    }

}