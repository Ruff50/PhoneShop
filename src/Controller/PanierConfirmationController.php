<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeDetails;
use App\Form\PanierConfirmType;
use App\Panier\PanierService;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PanierConfirmationController extends AbstractController
{
    #[Route('/panier/confirmation', name: 'panier_confirmation')]
    public function confirmation(Request $request, FormFactoryInterface $formFactory, RouterInterface $router, Security $security, PanierService $panier, ProductRepository $productRepository, SessionInterface $session, EntityManagerInterface $em): Response
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

        $panieritem = $panier->getDétailsPanier($session, $productRepository);
        $totalpanier = $panier->getTotal($session, $productRepository);


        if (count($panieritem) === 0) {
            $this->addFlash('warning', "Vous ne pouvez pas confirmer une commande avec un panier vide");
            return new RedirectResponse($router->generate('panier_show'));
        }

        /**@var Commande  */
        $commande = $form->getData();

        $commande->setUser($user);
        $commande->setDate(new \DateTime());
        $commande->setTotal($totalpanier);
        $commande->setStatus("PENDING");

        $em->persist($commande);

        foreach ($panieritem as $item) {
            $commandedetail = new CommandeDetails();
            $commandedetail->setCommande($commande);
            $commandedetail->setProduct($item['product']);
            $commandedetail->setProduitNom($item['product']->getNom());
            $commandedetail->setQte($item['quantity']);
            $commandedetail->setProduitPrix($item['product']->getPrix());
            $commandedetail->setTotal($item['product']->getPrix() * $item['quantity']);

            $em->persist($commandedetail);
        }


        $em->flush();
        $panier->empty($session);
        $this->addFlash('success', "Votre commande a bien été enregistrée");
        return new RedirectResponse($router->generate('commandes_index'));
    }
}
