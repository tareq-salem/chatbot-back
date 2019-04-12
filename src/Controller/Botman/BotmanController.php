<?php

namespace App\Controller\Botman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BotmanController extends AbstractController
{

    /**
     * @Route("/botman", name="botman", methods={"POST"})
     */
    public function index(Request $request)
    {
        $content = $request->getContent();
        $data = json_decode($content, true);
        $message = $data["message"];
        $content = $this->detect_intent_texts("geekbot-efd25", [$message], "");
        return new JsonResponse($content, 200, [], true);
    }

    /**
     * Returns the result of detect intent with texts as inputs.
     * Using the same `session_id` between requests allows continuation
     * of the conversation.
     */
    protected function detect_intent_texts($projectId, $texts, $sessionId, $languageCode = 'fr')
    {
        // new session
        $sessionsClient = new SessionsClient();
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
        // printf('Session path: %s' . PHP_EOL, $session);

        // $data = [];

        // query for each string in array
        foreach ($texts as $text) {
            // create text input
            $textInput = new TextInput();
            $textInput->setText($text);
            $textInput->setLanguageCode($languageCode);

            // create query input
            $queryInput = new QueryInput();
            $queryInput->setText($textInput);

            // get response and relevant info
            $response = json_decode($sessionsClient->detectIntent($session, $queryInput)->serializeToJsonString(), true);
            // $queryResult = $response->getQueryResult();
            // $parameters= json_decode($queryResult->getParameters()->serializeToJsonString(), true);
            // $queryText = $queryResult->getQueryText();
            // $intent = $queryResult->getIntent();
            // $displayName = $intent->getDisplayName();
            // $confidence = $queryResult->getIntentDetectionConfidence();
            // $fulfilmentText = $queryResult->getFulfillmentText();

            // $botData = [
            //     "response" => $response
                // "queryText" => $queryText,
                // "fulfilmentText" => $fulfilmentText,
                // "intention" => $displayName,
                // "confidence" => (string)$confidence,
                // "parameters" => $parameters,
            // ];

            // // output relevant info
            // printf('Moi : %s [Intention : %s (confidence: %f)]' . PHP_EOL, $queryText, $displayName, $confidence);
            // // printf('INTENTION : %s (confidence: %f)' . PHP_EOL, $displayName, $confidence);
            // printf('Bot : %s' . PHP_EOL, $fulfilmentText);
            // print(PHP_EOL);
            // print(str_repeat("=", 20) . PHP_EOL);
            // print(PHP_EOL);
            
            // array_push($data, $queryText, $intent, $fulfilmentText);
        }
        $sessionsClient->close();
        return json_encode(["response" => $response]);
    }
}
