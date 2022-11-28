<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(Request $request): Response
    {

        return $this->render('main/index.html.twig', [
            'name' => 'CNN Media Company',
            'description' => 'The Cable News Network is a multinational cable news channel headquartered in Atlanta, Georgia, U.S. Founded in 1980 by American media proprietor Ted Turner and Reese Schonfeld as a 24-hour cable news',
        ]);
    }
}
