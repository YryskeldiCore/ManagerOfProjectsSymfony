<?php


namespace App\Tests\Unit\Model\User\Entity\User\Network;

use App\Model\User\Entity\User\Network;
use App\Tests\Builder\User\UserBuilder;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{

    public function testSuccess():void
    {
        $user = (new UserBuilder())->viaEmail()->confirmed()->build();

        $user->signUpByNetwork(
            $network = 'facebook',
            $identity = '123123'
        );

        self::assertTrue($user->isActive());

        self::assertCount(1, $networks = $user->getNetworks());
        self::assertInstanceOf(Network::class, $first = reset($networks));
        self::assertEquals($network, $first->getNetwork());
        self::assertEquals($identity, $first->getIdentity());
    }

}