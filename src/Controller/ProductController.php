<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route('/{slug}', name: 'product_category', priority=-1)
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw $this->createNotFoundException('La catégorie demandée n\'existe pas');
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route('/{category_slug}/{slug}', name: 'product_show')
     */

    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);
        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
    /**
     * @Route("admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, SluggerInterface $slugger, Request $request, EntityManagerInterface $em): Response

    {

        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }
        $form = $this->createForm(ProductType::class);


        // $form->setData($product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $product->setNom($data['nom']);
            $product->setDesignation($data['designation']);
            $product->setPrix($data['prix']);
            $product->setTva($data['tva']);
            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $product->setImage($filename);
            }
            $product->setQte($data['quantite']);
            $product->setCategory($data['category_id']);
            $product->setSlug(strtolower($slugger->slug($product->getNom())));


            $em->flush();
        }

        $formView = $form->createView();


        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $formView
        ]);
    }


    /**
     * @Route("admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProductType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $product = new Product();
            $product->setNom($data['nom']);
            $product->setDesignation($data['designation']);
            $product->setPrix($data['prix']);
            $product->setTva($data['tva']);
            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $product->setImage($filename);
            }
            $product->setQte($data['quantite']);
            $product->setCategory($data['category_id']);
            $product->setSlug(strtolower($slugger->slug($product->getNom())));

            $em->persist($product);
            $em->flush();
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'form' => $formView
        ]);
    }
}
