<?php

namespace App\Controller\Botman;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Symfony\Flex\Response;

class BotmanController extends AbstractController
{
    /**
     * @Route("/botman", name="botman", methods={"POST"})
     */
    public function index()
    {
        return new \Symfony\Component\HttpFoundation\Response($this->detect_intent_texts("geekbot-efd25", ["Salut !"],""), 200, []);
    }

    /**
     * Returns the result of detect intent with texts as inputs.
     * Using the same `session_id` between requests allows continuation
     * of the conversation.
     */
    protected function detect_intent_texts($projectId, $texts, $sessionId, $languageCode = 'fr-FR')
    {
        // new session
        $sessionsClient = new SessionsClient();
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
        printf('Session path: %s' . PHP_EOL, $session);

        $data = [];

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
            $response = $sessionsClient->detectIntent($session, $queryInput);
            $queryResult = $response->getQueryResult();
            $queryText = $queryResult->getQueryText();
            $intent = $queryResult->getIntent();
            $displayName = $intent->getDisplayName();
            $confidence = $queryResult->getIntentDetectionConfidence();
            $fulfilmentText = $queryResult->getFulfillmentText();

            // output relevant info
            print(str_repeat("=", 20) . PHP_EOL);
            printf('Query text: %s' . PHP_EOL, $queryText);
            printf('Detected intent: %s (confidence: %f)' . PHP_EOL, $displayName,
                $confidence);
            print(PHP_EOL);
            printf('Fulfilment text: %s' . PHP_EOL, $fulfilmentText);

            array_push($data, $queryText, $intent, $fulfilmentText);
        }

        $sessionsClient->close();
        return "queryText : " . $data[0];
    }
}
