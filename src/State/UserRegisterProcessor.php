<?php
// src/State/UserRegisterProcessor.php

namespace App\State;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @implements ProcessorInterface<User>
 */
final class UserRegisterProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $processor,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

        if (!$data instanceof User) {
            return null;
        }

        dd($data);
        // Hashage du mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
        $data->setPassword($hashedPassword);

        // Enregistrement de l'utilisateur
        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
