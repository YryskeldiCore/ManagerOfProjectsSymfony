<?php


namespace App\Tests\Builder\User;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private $id;
    private $date;

    private $email;
    private $hash;
    private $token;

    private $network;
    private $identity;
    private $confirmed;

    public function __construct()
    {
        $this->id = Id::next();
        $this->date = new \DateTimeImmutable();
    }

    public function confirmed(): self
    {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }

    public function viaEmail(Email $email = null, string $hash = null, string $token = null): self
    {
        $clone = clone $this;
        $clone->email = $email ?? new Email('test@app.com');
        $clone->hash = $hash ?? 'hash';
        $clone->token = $token ?? 'token';

        return $clone;
    }

    public function viaNetwork(string $network = null, string $identity = null): self
    {
        $clone = clone $this;
        $clone->network = $network ?? 'insta.com';
        $clone->identity = $identity ?? '1234';
        return $clone;
    }

    public function build(): User
    {
        $user = new User(
            $this->id,
            $this->date
        );

        if($this->email){
            $user->signUpByEmail(
                $this->email,
                $this->hash,
                $this->token
            );

            if($this->confirmed){
                $user->confirmSignUp();
            }
        }

        if($this->network){
            $user->signUpByNetwork(
                $this->network,
                $this->identity
            );
        }

        return $user;
    }

}