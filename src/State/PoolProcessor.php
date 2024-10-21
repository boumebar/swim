<?php
// src/State/PoolProcessor.php

namespace App\State;

use App\Entity\Pool;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<Pool>
 */
final class PoolProcessor implements ProcessorInterface
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
        // Si l'entité n'est pas une piscine, on ne fait rien
        if (!$data instanceof Pool) {
            return null;
        }

        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        if ($user) {
            $data->setOwner($user);
        }

        // Vérifiez si l'entité est déjà persistée
        if ($data->getId() !== null) {
            // Appeler la méthode pour mettre à jour updatedAt uniquement lors de la mise à jour
            $data->update();
        }

        // Appeler le processeur de persistance par défaut pour sauvegarder l'entité
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
