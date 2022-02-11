<?php
declare(strict_types=1);

namespace App\Security;

use App\ReadModel\User\UserFetcher;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserFetcher $users;

    public function __construct(UserFetcher $users)
    {
        $this->users = $users;
    }

    public function loadUserByUsername($username): UserInterface
    {
        $user = $this->loadUser($username);
        return $this->identityByUser($user, $username);
    }

    public function refreshUser(UserInterface $identity):UserInterface
    {
        if(!$identity instanceof UserIdentity){
            throw new UnsupportedUserException('Invalid user class' . \get_class($identity));
        }
        $user = $this->loadUser($identity->getUsername());
        return $this->identityByUser($user, $identity->getUsername());
    }

    public function supportsClass($class): bool
    {
        return $class === UserIdentity::class;
    }

    public function loadUser($username)
    {
        $chunks = explode(':', $username);

        if(count($chunks) === 2 && $user = $this->users->findForAuthByNetwork($chunks[0], $chunks[1])){
            return $user;
        }

        if($user = $this->users->findForAuthByEmail($username)){
            return $user;
        }

        throw new UsernameNotFoundException('');
    }

    public function identityByUser($user, string $username): UserIdentity
    {
        return new UserIdentity(
            $user[0]['id'],
            $user[0]['email'] ?:$username,
            $user[0]['password_hash'] ? : '',
            $user[0]['role'],
            $user[0]['status']
        );
    }

}