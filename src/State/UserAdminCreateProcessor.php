<?php

// src/State/UserAdminCreateProcessor.php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @implements ProcessorInterface<User>
 */
final class UserAdminCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof User && $data->getPlainPassword()) {
            $data->setPassword($this->passwordHasher->hashPassword($data, $data->getPlainPassword()));
        }

        return $data;
    }
}
