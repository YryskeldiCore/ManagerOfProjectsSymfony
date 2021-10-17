<?php


namespace App\Model\User\Service;


use http\Exception\RuntimeException;

class PasswordHasher
{
    public function hash(string $hash): string
    {
        $hash = password_hash($hash, PASSWORD_ARGON2I);

        if($hash === false){
            throw new RuntimeException('Unable to generate hash'); // In rare case u can check in docs php
        }

        return $hash;
    }

    public function validate(string $password, string $hash):bool
    {
        return password_verify($password, $hash);
    }
}