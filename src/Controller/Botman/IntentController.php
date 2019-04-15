<?php
namespace App\Controller\Botman;

use App\Controller\ProductController;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IntentController extends AbstractController
{
    private $productController;
    private $productRepository;

    public function  __construct(ProductController $productController, ProductRepository $productRepository)
    {
        $this->productController = $productController;
        $this->productRepository = $productRepository;
    }

    public function intentRouter(ProductController $productController, ProductRepository $productRepository, $request, $content) {
        $data = json_decode($content, true);
        $queryResult = $data["response"]["queryResult"];
        $intentName = $queryResult["intent"]["displayName"];

        $response = null;

        switch($intentName) {
            case "intent.test.produit":
                $data["response"]["data"] = $productController->getAllproducts($productRepository);
                break;
        }

        $response = json_encode($data);

        return $response;
    }
}