<?php

namespace App\Controller;

use App\Entity\Meal;
use App\Entity\Product;
use App\Entity\Template;
use App\EventDispatcher\Events;
use App\EventDispatcher\ProductEvent;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
     * @return Response
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
     * @param Request $request
     *
     * @return RedirectResponse|Response
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
                $product->setTotals();
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
     *
     * @param Request $request
     *
     * @param Product $product
     *
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

            $product->setTotals();

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
     * @return RedirectResponse
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

    /**
     * @Route("/product/share/{id}", name="product_share")
     *
     * @param Product $product
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return RedirectResponse
     */
    public function shareProduct(Product $product, EventDispatcherInterface $dispatcher)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        if ($this->getUser() !== $product->getOwner()) {
            throw new AccessDeniedException();
        }

        if($product->getStatus() === Product::STATUS_SHARED)
        {
            $this->addFlash("error", "Product {$product->getName()} has been already shared.");
            return $this->redirectToRoute('product_details', ["id" => $product->getId()]);
        }

        $product->setStatus(Product::STATUS_SHARED);
        $publicProduct = clone $product;
        $publicProduct->setOwner(null);
        $publicProduct->setValue(100);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($publicProduct);
        $entityManager->flush();

        $dispatcher->dispatch(new ProductEvent($product), Events::PRODUCT_SHARE);
        //$this->get('event_dispatcher')->dispatch(new ProductEvent($product), Events::PRODUCT_SHARE);

        $this->addFlash("success", "Product {$publicProduct->getName()} has been shared.");

        return $this->redirectToRoute('product_details', ["id" => $product->getId()]);

    }
}
