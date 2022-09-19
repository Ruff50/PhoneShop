<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use Doctrine\ORM\Mapping\OrderBy;
use App\Repository\ProductRepository;

use App\Repository\CategoryRepository;

use Doctrine\ORM\EntityManagerInterface;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name= "product_category" , priority=-1)
     */

    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas');
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name= "product_show",priority=-1)
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
    public function edit($id, ProductRepository $productRepository, SluggerInterface $slugger, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response

    {

        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }
        $form = $this->createForm(ProductType::class);

        $form->setData($product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();

            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $product->setImage($filename);
            }

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
            $product = $form->getData();

            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $product->setImage($filename);
            }

            $product->setSlug(strtolower($slugger->slug($product->getNom())));

            $em->persist($product);
            $em->flush();
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'form' => $formView
        ]);
    }

    /**
     * @Route("admin/product/all", name="product_all")
     */
    public function productsAll(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $limit = 5;
        $page = (int)$request->query->get("page", 1);

        $products = $em->getRepository('App\Entity\Product')->createQueryBuilder('p')
            ->orderBy('p.id')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();;

        $total = $em->getRepository('App\Entity\Product')->createQueryBuilder('p')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        if ($products) {
            return $this->render('product/product_list.html.twig', [
                'listeProduits' => $products,
                'limit' => $limit,
                'total' => $total,
                'page' => $page,
            ]);
        }
    }

    #[Route('/admin/product/{id}/delete', name: 'product_delete')]
    public function delete($id, ProductRepository $productRepository, EntityManagerInterface $em): Response
    {
        $product = $productRepository->find($id);
        $em->remove($product);
        $em->flush();

        return new JsonResponse(['success' => 'Le produit numéro ' . $id . ' a bien été supprimé !']);
    }
}
