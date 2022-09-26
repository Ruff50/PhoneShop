<?php

namespace App\Panier;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    protected function savePanier(array $panier, SessionInterface $session)
    {
        $session->set('panier', $panier);
    }
    public function empty(SessionInterface $session)
    {
        $this->savePanier([], $session);
    }
    public function add(int $id, SessionInterface $session)

    {
        //1) Retrouver le panier dans la session (sous forme de tableau)
        // 2) S'il n'existe pas encore, alors prendre un tableau vide
        $panier = $session->get('panier', []);
        // 3) voir si le produit ($id) existe dèjà dans le tableau
        if (array_key_exists($id, $panier)) {
            $panier[$id]++;
            // 4) si c'est le cas, simplement augmenter la quantité
        } else {
            $panier[$id] = 1;
            // 5) Sinon, ajouter le produit avec la quantité 1
        }
        $session->set('panier', $panier);
        // 6) Enregistrer le tableau mis à jour dans la session
    }

    public function remove(int $id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (array_key_exists($id, $panier)) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
    }

    public function decrement(int $id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);
        if (!array_key_exists($id, $panier)) {
            return;
        }
        if ($panier[$id] > 1) {
            $panier[$id]--;
        } else {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
    }

    public function getTotal(SessionInterface $session, ProductRepository $productRepository): float
    {
        $total = 0;
        foreach ($session->get('panier', []) as $id => $quantity) {
            $product = $productRepository->find($id);
            $total += $product->getPrix() * $quantity;
        }

        return $total;
    }

    public function getDétailsPanier(SessionInterface $session, ProductRepository $productRepository): array
    {
        $details = [];

        foreach ($session->get('panier', []) as $id => $quantity) {
            $product = $productRepository->find($id);
            $details[] = [
                'product' => $product,
                'quantity' => $quantity
            ];
        }
        return $details;
    }
}
