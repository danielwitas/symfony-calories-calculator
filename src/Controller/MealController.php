<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Product;
use App\Form\MealType;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MealController extends AbstractController
{
    /**
     * @Route("/meal", name="meal_index")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $meals = $entityManager
            ->getRepository(Meal::class)
            ->findby(["owner" => $this->getUser()], ['id' => 'DESC']);

        return $this->render('meal/index.html.twig', ['meals' => $meals]);
    }

    /**
     * @Route("/meal/create", name="meal_create")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function createMeal(Request $request) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $meal = new Meal();

        $form = $this->createForm(MealType::class, $meal);

        if($request->isMethod('post')) {
            $form->handleRequest($request);

            if($form->isValid()) {
                $meal
                    ->setCreatedAt(new \DateTime())
                    ->setOwner($this->getUser());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($meal);
                $entityManager->flush();

                $this->addFlash("success", "Meal {$meal->getName()} has been created.");

                return $this->redirectToRoute('meal_index');
            }
            $this->addFlash("error", "Oops! Something went wrong...");
        }

        return $this->render('meal/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/meal/delete/{id}", name="meal_delete")
     *
     * @param Meal $meal
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteMeal(Meal $meal)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $meal->getOwner()) {
            throw new AccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($meal);
        $entityManager->flush();

        $this->addFlash("success", "Meal {$meal->getName()} has been deleted.");

        return $this->redirectToRoute('meal_index');

    }

    /**
     * @Route("/meal/edit/{id}", name="meal_edit")
     *
     * @param Request $request
     *
     * @param Meal $meal
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editMeal(Request $request, Meal $meal)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $meal->getOwner()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(MealType::class, $meal);

        if($request->isMethod('post')) {

            $form->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($meal);
            $entityManager->flush();

            $this->addFlash("success", "Meal {$meal->getName()} has been edited.");

            return $this->redirectToRoute('meal_details', ['id' => $meal->getId()]);
        }

        return $this->render('meal/edit.html.twig',
            [
                'meal' => $meal,
                'form' => $form->createView()
            ]);
    }

    /**
     * @Route("/meal/details/{id}", name="meal_details")
     */
    public function detailsMeal(Meal $meal)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $userProducts = $entityManager
            ->getRepository(Product::class)
            ->findby(["template" => null, "meal" => null, "owner" => $this->getUser()], ['id' => 'DESC']);

        return $this->render('meal/details.html.twig', ['meal' => $meal, 'userProducts' => $userProducts]);
    }

    /**
     * @Route("/meal/addProduct/{id}", name="meal_add_product")
     *
     * @param Request $request
     *
     * @param Meal $meal
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function addProduct(Request $request, Meal $meal)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        if ($request->isMethod("post")) {
            $form->handleRequest($request);

            // $product = $form->getData();

            $product
                ->setMeal($meal)
                ->setOwner($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash("success", "Product {$product->getName()} has been added.");

            return $this->redirectToRoute('meal_details', ['id' => $meal->getId()]);
        }

        return $this->render("meal/addProduct.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/meal/add/{id}/{meal_id}", name="meal_addUserProduct")
     *
     * @ParamConverter("meal", class="App\Entity\Meal", options={"id" = "meal_id"})
     *
     * @param Product $product
     *
     * @param Meal $meal
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addUserProduct(Product $product, Meal $meal)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $product = clone $product;
        $product->setMeal($meal);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash("success", "Product {$product->getName()} has been added.");

        return $this->redirectToRoute('meal_details', ['id' => $meal->getId()]);
    }
}
