<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier/add/{id}', name: 'panier_add', requirements: ['id' => '\d+'])]
    public function add($id, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Le produit $id n\'existe pas');
        }
        $panier = $session->get('panier', []);

        if (array_key_exists($id, $panier)) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);


        $this->addFlash('success', 'Le produit a bien été ajouté au panier');


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
}
