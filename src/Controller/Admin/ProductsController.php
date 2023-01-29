<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/admin/products", name="admin_products_")
 */
class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }

    /**
     * @Route("/add", name="add")
     */
    public function add(Request $request, 
                        EntityManagerInterface $em, 
                        SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Products();

        $productForm = $this->createForm(ProductsFormType::class, $product);

        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
        
            $price = $product->getPrice() * 100;
            $product->setPrice($price);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product added successfully');

            return $this->redirectToRoute('admin_products_index');
        }


        // return $this->render('admin/products/add.html.twig', [
        //     'productForm' => $productForm->createView()
        // ]);

        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }
    
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(
                        Products $product, 
                        Request $request, 
                        EntityManagerInterface $em, 
                        SluggerInterface $slugger
                        ): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $price = $product->getPrice() / 100;
        $product->setPrice($price);

        $productForm = $this->createForm(ProductsFormType::class, $product);

        $productForm->handleRequest($request);

        if($productForm->isSubmitted() && $productForm->isValid()){
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
        
            $price = $product->getPrice() * 100;
            $product->setPrice($price);

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product edited successfully');

            return $this->redirectToRoute('admin_products_index');
        }

        return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));
    }
    
    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);
        return $this->render('admin/products/index.html.twig');
    }
}