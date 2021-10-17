<?php
declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\ResetToken;
use Ramsey\Uuid\Uuid;

class ResetTokenizer
{
    private $interval;

    public function __construct(\DateInterval $interval)
    {
        $this->interval = $interval;
    }

    public function generate(): ResetToken
    {
        $resetToken = new ResetToken(
            Uuid::uuid4()->toString(),
            ((new \DateTimeImmutable())->setTimezone(new \DateTimeZone('Asia/Bishkek')))->add($this->interval)
        );

        return $resetToken;
    }
}