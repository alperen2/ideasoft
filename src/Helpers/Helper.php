<?php
namespace App\Helpers;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class Helper {
    
    private $session;

    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    static function getProduct($id = 0)
    {
        $package = new Package(new EmptyVersionStrategy());
        $json_file = $package->getUrl('products.json');
        $json_data = file_get_contents($json_file);
        $products = json_decode($json_data, true);

        if ($id) {
            return array_values(array_filter($products, function($product) use ($id) {
                return $product['id'] == $id;
            }))[0];

            
        }
        return $products;
    }

    static function setFlash($message)
    {
        $session = new Session();

        $session->getFlashBag()->add('message',$message);
    }
}