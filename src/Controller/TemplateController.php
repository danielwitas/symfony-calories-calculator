<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Product;
use App\Entity\Template;
use App\Form\ProductType;
use App\Form\TemplateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TemplateController extends AbstractController
{
    /**
     * @Route("/template", name="template_index")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $templates = $entityManager
            ->getRepository(Template::class)
            ->findby(["owner" => $this->getUser()], ['id' => 'DESC']);

        return $this->render('template/index.html.twig', ['templates' => $templates]);
    }

    /**
     * @Route("template/create", name="template_create")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function createTemplate(Request $request) : Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $template = new Template();

        $form = $this->createForm(TemplateType::class, $template);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);

            if($form->isValid()) {

                $template->setCreatedAt(new \DateTime());
                $template->setOwner($this->getUser());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($template);
                $entityManager->flush();

                $this->addFlash("success", "Template {$template->getName()} has been created.");

                return $this->redirectToRoute('template_index');
            }

            $this->addFlash("error", "Oops! Something went wrong...");

        }

        return $this->render('template/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/template/delete/{id}", name="template_delete")
     *
     * @param Template $template
     */
    public function deleteTemplate(Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $template->getOwner()) {
            throw new AccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($template);
        $entityManager->flush();

        $this->addFlash("success", "Template {$template->getName()} has been deleted.");

        return $this->redirectToRoute('template_index');
    }

    /**
     * @Route("/template/edit/{id}", name="template_edit")
     * @param Request $request
     * @param Template $template
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function editTemplate(Request $request, Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $template->getOwner()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(TemplateType::class, $template);

        if($request->isMethod('post')) {

            $form->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($template);
            $entityManager->flush();

            $this->addFlash("success", "Template {$template->getName()} has been edited.");

            return $this->redirectToRoute('template_details', ['id' => $template->getId()]);
        }

        return $this->render('template/edit.html.twig',
            [
                'template' => $template,
                'form' => $form->createView()
            ]);


    }


    /**
     * @Route("/template/details/{id}", name="template_details")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @param Template $template
     */
    public function detailsTemplate(Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();

        $userProducts = $entityManager
            ->getRepository(Product::class)
            ->findby(["template" => null, "meal" => null, "owner" => $this->getUser()], ['id' => 'DESC']);

        $userMeals = $entityManager
            ->getRepository(Meal::class)
            ->findby(["owner" => $this->getUser()], ['id' => 'DESC']);



//        $deleteTemplateForm = $this->createFormBuilder()
//            ->setAction($this->generateUrl('template_delete', ['id' => $template->getId()]))
//            ->setMethod(Request::METHOD_DELETE)
//            ->add('submit', SubmitType::class, ['label' => 'Delete Template'])
//            ->getForm();
//
//        $deleteProductForm = $this->createFormBuilder()
//            ->setMethod(Request::METHOD_DELETE)
//            ->add('submit', SubmitType::class, ['label' => 'Delete Product'])
//            ->getForm();

        return $this->render('template/details.html.twig',
            [
                'template' => $template,
                'userProducts' => $userProducts,
                'userMeals' => $userMeals
//                'deleteProductForm' => $deleteProductForm->createView(),
//                'deleteTemplateForm' => $deleteTemplateForm->createView()
            ]
        );
    }

    /**
     * @Route("/template/add/{id}/{template_id}", name="template_addUserProduct")
     * @ParamConverter("template", class="App\Entity\Template", options={"id" = "template_id"})
     */
    public function addUserProduct(Product $product, Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $product = clone $product;
        $product->setTemplate($template);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        $this->addFlash("success", "Product {$product->getName()} has been added.");
        return $this->redirectToRoute('template_details', ['id' => $template->getId()]);
    }

    /**
     * @Route("/template/addProduct/{id}", name="template_add_product")
     */
    public function addProduct(Request $request, Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        if ($request->isMethod("post")) {
            $form->handleRequest($request);

            // $product = $form->getData();

            $product->setTemplate($template);
            $product->setOwner($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash("success", "Product {$product->getName()} has been added.");

            return $this->redirectToRoute('template_details', ['id' => $template->getId()]);
        }

        return $this->render("template/addProduct.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/template/addMeal/{id}/{template_id}", name="template_add_meal")
     * @ParamConverter("template", class="App\Entity\Template", options={"id" = "template_id"})
     */
    public function addMeal(Meal $meal, Template $template)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");


        foreach ($meal->getProducts() as $product)
        {

            $product = clone $product;

            $product->setMeal(null);
            $product->setTemplate($template);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
        }



        $this->addFlash("success", "Meal {$meal->getName()} has been added.");

        return $this->redirectToRoute('template_details', ['id' => $template->getId()]);
    }


}
