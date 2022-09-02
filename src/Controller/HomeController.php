<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\SolBaseStock;
use App\Entity\Inventaire;
use App\Entity\InventaireSolBaseStock;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Mapping\OrderBy;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage()
    {
        return $this->render('home.html.twig');
    }
}
