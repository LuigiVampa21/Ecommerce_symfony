<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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
    public function add(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Products();

        $productForm = $this->createForm(ProductsFormType::class, $product);

        // return $this->render('admin/products/add.html.twig', [
        //     'productForm' => $productForm->createView()
        // ]);

        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }
    
    /**
     * @Route("/edit/{id}", name="edit")
     */
    public function edit(Products $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);
        return $this->render('admin/products/index.html.twig');
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