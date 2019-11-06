<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class WallController extends AbstractController
{
    /**
     * @Route("/wall", name="wall")
     */
    public function index()
    {

        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $news = $entityManager
            ->getRepository(News::class)
            ->findAll();



        return $this->render('wall/index.html.twig', ['news' => $news]);
    }
}
