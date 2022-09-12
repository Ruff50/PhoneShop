<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class CommandesController extends AbstractController
{
    #[Route('/commandes', name: 'commandes_index')]
    public function index(Security $security, RouterInterface $router, Environment $twig): Response
    {
        /**@var User */
        $user = $security->getUser();
        if (!$user) {

            throw new AccessDeniedException("Vous devez être connecté pour accéder aux commandes ");
        }

        $html = $twig->render('commandes/index.html.twig', [
            'commandes' => $user->getCommandes()
        ]);
        return new Response($html);
    }
}
