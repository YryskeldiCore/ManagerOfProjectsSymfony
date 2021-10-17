<?php


namespace App\Security;


use App\Model\User\Entity\User\User;
use Symfony\Component\Mailer\Transport\Smtp\Auth\AuthenticatorInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method string getUserIdentifier()
 */
class UserIdentity implements UserInterface, EquatableInterface, AuthenticatorInterface
{

    private $id;
    private $username;
    private $password;
    private $role;
    private $status;

    public function __construct(
        string $id,
        string $username,
        string $password,
        string $role,
        string $status
    ){
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {

    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isActive():bool
    {
        return $this->status === User::STATUS_ACTIVE;
    }

    public function isEqualTo(UserInterface $user):bool
    {
        if(!$user instanceof self){
            return false;
        }

        return
            $this->id === $user->id &&
            $this->username === $user->username &&
            $this->password === $user->password &&
            $this->status === $user->status &&
            $this->role === $user->role;
    }

    public function authenticate(EsmtpTransport $client): void
    {
        // TODO: Implement authenticate() method.
    }

    public function getAuthKeyword(): string
    {
        // TODO: Implement getAuthKeyword() method.
    }
}