<?php

namespace App\Controller;

use App\Helpers\CartManager;
use App\Helpers\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products")
     */
    public function index(SessionInterface $session): Response
    {
        $cartManager = new CartManager($session);
        $helper = new Helper($session);

        $products = Helper::getProduct();
 
        return $this->render('products/index.html', [
            'products' => $products,
            'cart_count' => $cartManager->cartCount(),
        ]);
    }
}
