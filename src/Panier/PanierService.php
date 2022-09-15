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
        $panier = $session->get('panier', []);

        if (array_key_exists($id, $panier)) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);
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
