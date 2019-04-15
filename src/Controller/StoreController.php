<?php

namespace App\Controller;

use App\Entity\Store;
use App\Form\StoreType;
use App\Repository\StoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/store")
 */
class StoreController extends AbstractController
{
    /**
     * @Route("/", name="store_index", methods={"GET"})
     */
    public function index(StoreRepository $storeRepository): Response
    {
        
        $storeList = [];
        $stores =  $storeRepository->findAll(); 

        foreach($stores as $list)
        {
            $list = 
            [
                'name' => $list->getName(),
                'image' =>$list->getAddress(),
              
            ];

            array_push($storeList, $list);
        }
        
        
        $data = $this->get('serializer')->serialize($storeList, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }
    

    /**
     * @Route("/show", name="store_show", methods={"GET"})
     */
    public function show(Request $request, StoreRepository $storeRepository): Response
    {
        $data = json_decode($request->getContent(), true);

        $store = $storeRepository->findOneBy($data);
        $storeInfo = 
        [
            'nom' => $store->getName(),
            'adresse' => $store->getAddress()
        ];

        $data = $this->get('serializer')->serialize($storeInfo, 'json');
        
        return new JsonResponse($data, 200, [], true);
    }

}
