<?php

namespace App\Controller;

use App\Panier\PanierService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier/add/{id}', name: 'panier_add', requirements: ['id' => '\d+'])]
    public function add($id, SessionInterface $session, PanierService $panierService, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit $id n\'existe pas');
        }
        $panierService->add($id, $session);

        $this->addFlash('success', 'Le produit a bien été ajouté au panier');

        if ($request->query->get('returnToPanier')) {
            return $this->redirectToRoute('panier_show');
        }

        return  $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),

        ]);
    }

    /**
     * @Route("/panier/show", name="panier_show")
     */
    public function show(SessionInterface $session, ProductRepository $productRepository): Response
    {

        $details = [];
        $total = 0;
        foreach ($session->get('panier', []) as $id => $quantity) {
            $product = $productRepository->find($id);
            $details[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
            $total += $product->getPrix() * $quantity;
        }
        return $this->render('panier/index.html.twig', [
            'details' => $details,
            'total' => $total
        ]);
    }
    /**
     * @Route("/panier/delete/{id}", name="panier_delete", requirements={"id" : "\d+"})
     */
    public function delete($id, ProductRepository $productRepository, SessionInterface $session, PanierService $panierService): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit $id n\'existe pas');
        }
        $panierService->remove($id, $session);

        $this->addFlash('success', 'Le produit a bien été supprimé du panier');
        return $this->redirectToRoute("panier_show");
    }
    /**
     * @Route("/panier/decrement/{id}", name="panier_decrement", requirements={"id" : "\d+"})
     */
    public function decrement($id, SessionInterface $session, PanierService $panierService, ProductRepository $productRepository): Response
    {

        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit $id n\'existe pas');
        }
        $panierService->decrement($id, $session);

        $this->addFlash('success', 'Le produit a bien été décrémenté du panier');
        return $this->redirectToRoute("panier_show");
    }
}
