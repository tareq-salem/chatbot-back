<?php

namespace App\Controller\Botman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\BotmanService;
use App\Controller\Botman\IntentController;
use App\Controller\ProductController;
use App\Repository\ProductRepository;

class BotmanController extends AbstractController
{
    private $botmanService;
    private $intentController;
    private $productController;
    private $productRepository;

    public function  __construct(
        BotmanService $botmanService,
        IntentController $intentController,
        ProductController $productController,
        ProductRepository $productRepository)
    {
        $this->botmanService = $botmanService;
        $this->intentController = $intentController;
        $this->productController = $productController;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/botman", name="botman", methods={"POST"})
     */
    public function postRequestToBotman(
        BotmanService $botmanService,
        IntentController $intentController,
        Request $request,
        ProductController $productController,
        ProductRepository $productRepository)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $message = $data["message"];
        
        $botmanReturn = $botmanService->detect_intent_texts("geekbot-efd25", [$message], "");
        $response = $intentController->intentRouter($productController, $productRepository, $data, $botmanReturn);

        return new JsonResponse($response, 200, [], true);
    }


}
