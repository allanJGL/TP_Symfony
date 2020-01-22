<?php

namespace App\Controller;

use App\Entity\Editor;
use App\Entity\Videogame;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = $this->getUser();

        $videogames = $this->getDoctrine()
            ->getRepository(Videogame::class)
            ->findAll();

        $editors = $this->getDoctrine()
            ->getRepository(Editor::class)
            ->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'videogames' => $videogames,
            'editors' => $editors,
            'user' => $user,
        ]);
    }
}
