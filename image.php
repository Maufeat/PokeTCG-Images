<?php

use PokeTCG\CardFactory;

require "vendor/autoload.php";
require "poketcg/CardFactory.php";
require "poketcg/HoloFilter.php";

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if(isset($_GET["data"])){
    $imagine = new Imagine\Gd\Imagine();

    if (str_starts_with($_GET["data"], '{')) {
        $data = json_decode($_GET["data"]);
    } else {
        $data = json_decode(base64_decode($_GET["data"]));
    }

    $card = new CardFactory();
    $card->setImage($data->image);
    $card->setOffset($data->offsetX - 0.5, $data->offsetY);
    $card->setHolo("sparkle");
    $card->create();
    $card->show();

} else {
    $card = new CardFactory();
    $card->setImage("https://tcg.pokemon.com/assets/img/global/tcg-card-back.jpg");
    $card->setOffset(0.5, 0.5);
    $card->create();
}