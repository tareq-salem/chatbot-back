<?php
namespace App\Controller\Botman;

use App\Controller\ProductController;

class IntentController
{

    public function intentRouter($content) {
        $data = json_decode($content, true);
        $queryResult = $data["response"]["queryResult"];
        $intentName = $queryResult["intent"]["displayName"];

        $dbItems = array();

        switch($intentName) {
            case "intent.products":
                $products = new ProductController();
//                $dbItems = $products->
                break;
        }

        return $dbItems;
    }
}