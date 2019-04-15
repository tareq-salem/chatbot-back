<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    public function getAllProducts(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        $productList = [];
        foreach($products as $list)
        {
            $list = 
            [
                'id'=> $list->getId(),
                'name' => $list->getName(),
                'image' =>$list->getImage(),
                'description' => $list->getDescription()

            ];
            array_push($productList, $list);
        }
        
        return $productList;
    }

    public function getProduct(Request $request, ProductRepository $productRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = $productRepository->findOneBy($data);

        $data = $this->get('serializer')->serialize($product, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }

}
