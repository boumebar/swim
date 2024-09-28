<?php


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function apilogin()
    {
        dd("login");
    }

    #[Route('/api/test', name: 'api_test')]
    public function apitest()
    {
        dd("test");
    }
}
