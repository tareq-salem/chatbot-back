<?php

namespace App\Controller\AdminBot;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class AdminBotController extends AbstractController
{
    /**
     * @Route("/adminbot", name="adminbot")
     */
    public function adminBot()
    {
        
    }
}
