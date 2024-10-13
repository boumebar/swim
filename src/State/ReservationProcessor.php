<?php
// src/State/PoolProcessor.php

namespace App\State;

use App\Entity\Reservation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<Pool>
 */
final class ReservationProcessor implements ProcessorInterface
{
    private ProcessorInterface $persistProcessor;
    private Security $security;

    public function __construct(
        Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $persistProcessor
    ) {
        $this->security = $security;
        $this->persistProcessor = $persistProcessor;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!$data instanceof Reservation) {
            return null;
        }
        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        if ($user) {
            $data->setLoueur($user);
        }

        // Appeler le processeur de persistance par défaut pour sauvegarder l'entité
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
