<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class ProductsController extends AbstractController
{
    /**
     * @Route("/", name="products")
     */
    public function index(): Response
    {
 
        $package = new Package(new EmptyVersionStrategy());
        $json_file = $package->getUrl('products.json');
        $json_data = file_get_contents($json_file);

        return $this->render('products/index.html', [
            'products' => json_decode($json_data, true),
        ]);
    }
}
