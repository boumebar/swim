<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;

final class MeProvider implements ProviderInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?User
    {
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        // Si aucun utilisateur n'est connecté, retourner null
        if (!$user instanceof User) {
            return null;
        }

        // Retourner l'utilisateur connecté
        return $user;
    }
}
