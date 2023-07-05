<?php

declare(strict_types=1);

namespace App\Model\User\UseCase\Role;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public string $role;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
