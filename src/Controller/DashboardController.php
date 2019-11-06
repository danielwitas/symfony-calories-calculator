<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserStats;
use App\Form\ProductType;
use App\Form\UserStatsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard_index")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $userStats = $entityManager->getRepository(UserStats::class)->findOneBy(['owner' => $this->getUser()]);

        return $this->render('dashboard/index.html.twig', ['userStats' => $userStats, 'user' => $this->getUser()]);
    }

    /**
     * @Route("/dashboard/avarage/{limit}", name="dashboard_avarage")
     */
    public function avarage($limit)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $avarage = $entityManager->getRepository(Product::class)->getAvarage($this->getUser(), $limit);

        return $this->render('dashboard/avarage.html.twig', [
            'avarage' => $avarage,
            'limit' => $limit
        ]);
    }

    /**
     * @Route("/dashboard/edit", name="dashboard_edit")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();

        $userStats = $entityManager->getRepository(UserStats::class)->findOneBy(['owner' => $this->getUser()]);
        if($userStats == null) {
            $userStats = new UserStats();
        }

        $form = $this->createForm(UserStatsType::class, $userStats);

        if ($request->isMethod('post'))
        {
            $form->handleRequest($request);

            $userStats->setOwner($this->getUser());


            $entityManager->persist($userStats);
            $entityManager->flush();

            $this->addFlash("success", "Your stats have been updated");
        }

        return $this->render('dashboard/edit.html.twig', ['form' => $form->createView()]);
    }
}
