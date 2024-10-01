<?php
    
    include_once dirname(__FILE__) . '/menu.php';

    $isUserRegistered = false;

    // Read the data sent via POST from our Africa's Talking API
    $sessionId   = $_POST["sessionId"];
    $serviceCode = $_POST["serviceCode"];
    $phoneNumber = str_replace("+", "", $_POST["phoneNumber"]);
    $text        = $_POST["text"];

    $menu = new Menu();
    $text = $menu->middleware($text);
    //$text = $menu->goBack($text);

    //user has just started session and string is empty
    if($text == ""){
         $menu->mainMenu();

    }else{
        //string is not empty
        $textArray = explode("*", $text);
        switch($textArray[0]){
            case 1: //First level i.e. get tokens
                $menu->buyTokens($textArray, $phoneNumber);
                break;
           /* case 2:
                $menu->registerMenu($textArray, $phoneNumber);
                break;*/
            default:
                echo "CON Invalid choice. Please try again";
        }
    }


