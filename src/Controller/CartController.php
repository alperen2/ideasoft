<?php

namespace App\Controller;

use App\Helpers\CartManager;
use App\Helpers\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(SessionInterface $sessionInterface, Request $request): Response
    {

        $code = $request->query->get('code') ?? false;
        
        $cartManager = new CartManager($sessionInterface);
        $cart = $cartManager->getCart();
        $cartWithProduct = array_map(function ($c){
            return array_merge(Helper::getProduct($c['id']), $c);
        }, $cart);

        $discount = $cartManager->calculateDiscount($code);
        $cartTotal = $cartManager->cartTotal();

        return $this->render('cart/index.html', [
            'cart' => $cartWithProduct,
            'cart_count' => $cartManager->cartCount(),
            'total' => $cartTotal,
            'tax' => $cartManager->calculateTax(),
            'shipCost' => $cartManager->calculateShipCost(),
            'discount' => $discount,
            'paymentAmount' => $cartTotal - $discount,
        ]);
    }

    /**
     * @Route("/cart/add/{product_id<\d+>}", name="cart_add", methods={"post"})
     */
    public function add(int $product_id, Request $request, SessionInterface $sessionInterface)
    {
        $cartManager = new CartManager($sessionInterface);

        $product = Helper::getProduct($product_id);
        
        $quantity = $request->request->get("quantity");
        $price = $product['price'] * $quantity;

        $cartManager->addCart([
            "id" => $product_id,
            "quantity" => $quantity,
            "price" => $price,
        ]);
        
        Helper::setFlash("Ürün sepete eklendi");
        return $this->redirectToRoute('products');
    }

     /**
     * @Route("/cart/empty", name="cart_empty")
     */
    public function destroy(SessionInterface $session)
    {
        $session->clear();
        Helper::setFlash("Sepet boşaltıldı");
        return $this->redirectToRoute('products');
    }

    /**
     * @Route("/cart/update/{product_id<\d+>}", name="cart_update", methods={"post"})
     */
    public function update($product_id, SessionInterface $session, Request $request) {
        $cartManager = new CartManager($session);
        $quantity = $request->request->get('quantity');
        $cartManager->updateQuantity($product_id, $quantity);

        return $this->redirectToRoute('cart');
    }
}
