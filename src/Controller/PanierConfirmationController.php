<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\PanierConfirmType;
use App\Panier\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PanierConfirmationController extends AbstractController
{
    #[Route('/panier/confirmation', name: 'panier_confirmation')]
    public function confirmation(Request $request, FormFactoryInterface $formFactory, RouterInterface $router, Security $security, PanierService $panier): Response
    {
        $form = $formFactory->create(PanierConfirmType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");
            return new RedirectResponse($router->generate('panier_show'));
        }

        $user = $security->getUser();

        if (!$user) {

            throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande");
        }
        /**@var Commande  */
        $commande = $form->getData();

        $commande->setUser($user);







        return $this->render('panier_confirmation/index.html.twig', [
            'controller_name' => 'PanierConfirmationController',
        ]);
    }
}
