<?php

use App\Helpers\CartManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

$session = new SessionInterface;
$cartManager = new CartManager($session);


$container->loadFromExtension('twig', [
    'globals' => [
        'cart_count' => $cartManager->cartCount(),
    ],
]);