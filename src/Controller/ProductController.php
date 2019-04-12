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
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $productList = [];
        foreach($products as $list)
        {
            $list = 
            [
                'name' => $list->getName(),
                'image' =>$list->getImage(),
                'description' => $list->getDescription()

            ];
            array_push($productList, $list);
        }
        

        $data = $this->get('serializer')->serialize($productList, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }

    /**
     * @Route("/show", name="product_show", methods={"GET"})
     */
    public function show(Request $request, ProductRepository $productRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = $productRepository->findOneBy($data);

        $data = $this->get('serializer')->serialize($product, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }

}
