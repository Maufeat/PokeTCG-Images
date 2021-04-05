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

    $card = new CardFactory();

    if (str_starts_with($_GET["data"], '{')) {
        $data = json_decode($_GET["data"]);
    } else {
        $data = json_decode(base64_decode($_GET["data"]));
    }

    if(isset($data->image))
        $card->setImage($data->image);
    if(isset($data->offsetX) && isset($data->offsetY))
        $card->setOffset($data->offsetX, $data->offsetY);
    if(isset($data->holoEffect))
        $card->setHolo("none");
    if(isset($data->damage))
        $card->setDamage($data->damage);

    $card->create();
    $card->show();

} else {
    $card = new CardFactory();
    $card->setImage("https://tcg.pokemon.com/assets/img/global/tcg-card-back.jpg");
    $card->setOffset(0.5, 0.5);
    $card->create();
}