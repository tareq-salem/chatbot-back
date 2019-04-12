<?php

namespace App\Controller\Botman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\BotmanService;
use App\Controller\Botman\IntentController;


class BotmanController extends AbstractController
{
    private $botmanService;
    private $intentController;

    public function  __construct(BotmanService $botmanService, IntentController $intentController)
    {
        $this->botmanService = $botmanService;
        $this->intentController = $intentController;
    }

    /**
     * @Route("/botman", name="botman", methods={"POST"})
     */
    public function postRequestToBotman(BotmanService $botmanService, IntentController $intentController, Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $message = $data["message"];
        $botmanReturn = $botmanService->detect_intent_texts("geekbot-efd25", [$message], "");
        $response = $intentController->intentRouter($botmanReturn);


        return new JsonResponse($response, 200, [], true);
    }


}
