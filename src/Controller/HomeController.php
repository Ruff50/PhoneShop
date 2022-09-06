<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Mapping\OrderBy;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);
        return $this->render('home.html.twig', [
            'products' => $products,
        ]);
    }
}
