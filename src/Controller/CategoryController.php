<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getNom())));
            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $category->setImage($filename);
            }
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('category_show');
        }
        $formView = $form->createView();

        return $this->render('category/create.html.twig', ['form' => $formView]);
    }


    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = $categoryRepository->find($id);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['image']->getData();
            if ($file) {
                $filename = $file->getClientOriginalName();
                $file->move(
                    $this->getParameter('images_directory'),
                    $filename
                );

                $category->setImage($filename);
            }

            $category->setSlug(strtolower($slugger->slug($category->getNom())));

            $em->flush();
        }


        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $formView
        ]);
    }
    #[Route('/admin/category/list', name: 'category_show')]
    public function show(EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->findAll();
        return $this->render('category/category_list.html.twig', [
            'listeCategory' => $category
        ]);
    }
    #[Route('/admin/category/{id}/delete', name: 'category_delete')]
    public function delete($id, CategoryRepository $categoryRepository, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id);
        $em->remove($category);
        $em->flush();

        return new JsonResponse(['success' => 'La catégorie numéro ' . $id . ' a bien été supprimée !']);
    }
}
