<?php
namespace App\Helpers;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartManager
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getCart()
    {
        $cart_json = $this->session->get('cart');

        return json_decode($cart_json, true) ?? [];
    }

    public function addCart($product)
    {
        $product_id = $product['id'];

        $cart = $this->getCart() ?? [];

        $hasProduct = $this->hasProductInCart($product_id);

        if ($hasProduct) {
            $hasProduct['quantity'] += $product['quantity'];
            $hasProduct['price'] += $product['quantity'] * $product['price'];
            $cart[$product_id] = $hasProduct;
        } else {
            $cart[$product_id] = $product;
        }

        $this->session->set('cart', json_encode($cart));
    }

    public function hasProductInCart($product_id)
    {
        $cart = $this->getCart();
        $filtered = array_values(
            array_filter($cart, function ($product) use ($product_id) {
                return $product['id'] === $product_id;
            })
        );

        return $filtered[0] ?? false;
    }

    public function cartCount()
    {
        $cart = $this->getCart();
        return count($cart);
    }
    public function updateQuantity($product_id, $quantity)
    {
        $product = Helper::getProduct($product_id);
        $productInCart = $this->hasProductInCart((int) $product_id);

        $productInCart['quantity'] = $quantity;

        $cart = $this->getCart();
        

        $cart[$product_id] = $productInCart;

        $cart[$product_id]['price'] = $quantity * $product['price'];

        $this->session->set('cart', json_encode($cart));
    }

    public function cartTotal()
    {
        $cart = $this->getCart();
        
        $prices = array_map(function($c){
            return $c['price'];
        }, $cart);

        return array_sum($prices);
    }

    public function calculateTax() {
        $total = $this->cartTotal();

        return $total * 0.18;
    }

    public function calculateShipCost() {
        $total = $this->cartTotal();

        if ($total >= 100) return 0;
        
        return 10;
    }

    public function checkDiscountCode ($code) {
       return $code === 'IDEASOFT';
    }

    public function calculateDiscount($code)
    {
        if ($this->checkDiscountCode($code)) {
            return $this->cartTotal() * 0.2;
        };
    }
}
