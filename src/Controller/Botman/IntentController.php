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
            case "04A_ListeProduits":
                $data["response"]["data"] = $productController->getAllproducts($productRepository);
                break;
            case "04Abis_ListProduitsByCategorie":
                $categories = $queryResult["parameters"]["Category"];
                $products =  $productController->getProductsByCategory($categories, $productRepository);
                $data["response"]["data"] = $products;
                break;
            case "05_getCategories":
                $data["response"]["data"] = $productController->getCategories($productRepository);
                break;
        }

        $response = json_encode($data);

        return $response;
    }
}