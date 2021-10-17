<?php


namespace App\Model\User\Entity\User;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\FormTypeInterface;

class EmailType extends StringType
{
    public const NAME = 'user_user_email';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Email ? $value->getValue(): null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value)? new Email($value): null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}