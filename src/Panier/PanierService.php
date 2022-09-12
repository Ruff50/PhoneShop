<?php

namespace App\Panier;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{

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
}
