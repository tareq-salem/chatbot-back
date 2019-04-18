<?php

namespace App\Service;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class BotmanService
{
    /**
     * Returns the result of detect intent with texts as inputs.
     * Using the same `session_id` between requests allows continuation
     * of the conversation.
     */
    public function detect_intent_texts($projectId, $texts, $sessionId, $languageCode = 'fr')
    {
        // new session
        $sessionsClient = new SessionsClient();
        $idSession = $sessionId ?: uniqid();
        $session = $sessionsClient->sessionName($projectId, $idSession);

        $response = null;

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

            // array_push($data, $queryText, $intent, $fulfilmentText);
        }
        //$sessionsClient->close();
        return json_encode(["response" => $response, "session_id" => $idSession]);
    }
}