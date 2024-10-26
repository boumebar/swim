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

        // Hashage du mot de passe uniquement si le mot de passe est défini
        if ($data->getPassword() !== null) {
            $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
            $data->setPassword($hashedPassword);
        }
        dd($data);

        // Enregistrement de l'utilisateur
        $this->processor->process($data, $operation, $uriVariables, $context);

        return $data;
    }
}
