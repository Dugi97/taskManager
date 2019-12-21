<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;


class RegistrationService
{
    public $container;
    public $entityManager;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

}