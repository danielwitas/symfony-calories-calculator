<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Product;
use App\Entity\Template;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $entityManager = $this->getDoctrine()->getManager();
        $products = $entityManager
            ->getRepository(Product::class)
            ->findby(["template" => null, "meal" => null, "owner" => $this->getUser()], ['id' => 'DESC']);


        return $this->render('product/index.html.twig', ['products' => $products]);
    }

    /**
     * @Route("/product/details/{id}", name="product_details")
     *
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function detailsProduct(Product $product)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('product_delete', ['id' => $product->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->add('submit', SubmitType::class, ['label' => 'Delete'])
            ->getForm();

        return $this->render('product/details.html.twig',
            [
                'product' => $product,
                'deleteForm' => $deleteForm->createView()
            ]);
    }

    /**
     * @Route("/product/add", name="product_add")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProduct(Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        if ($request->isMethod("post")) {
            $form->handleRequest($request);
            if ($form->isValid()) {

                // $product = $form->getData();
                $product->setOwner($this->getUser());
                $product->setTotalCalories($product->getCalories() * $product->getValue() / 100);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash("success", "Product {$product->getName()} has been added.");

                return $this->redirectToRoute('product_index');

            }

            $this->addFlash("error", "Oops! Something went wrong...");

        }

        return $this->render("product/add.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function editProduct(Request $request, Product $product)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $product->getOwner()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(ProductType::class, $product);

        if ($request->isMethod('post')) {
            $form->handleRequest($request);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash("success", "Product {$product->getName()} has been edited.");

            if ($product->getTemplate() !== null) {

                return $this->redirectToRoute("template_details", ['id' => $product->getTemplate()->getId()]);

            }

            if ($product->getMeal() !== null) {

                return $this->redirectToRoute("meal_details", ['id' => $product->getMeal()->getId()]);

            }

            return $this->redirectToRoute('product_details', ["id" => $product->getId()]);
        }

        return $this->render("product/edit.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete", methods={"DELETE", "GET"})
     *
     * @param Product $product
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteProduct(Product $product)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $product->getOwner()) {
            throw new AccessDeniedException();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash("success", "Product {$product->getName()} has been deleted.");

        if ($product->getTemplate() !== null) {

            return $this->redirectToRoute("template_details", ['id' => $product->getTemplate()->getId()]);

        }

        if ($product->getMeal() !== null) {

            return $this->redirectToRoute("meal_details", ['id' => $product->getMeal()->getId()]);

        }

        return $this->redirectToRoute("product_index");
    }
}
