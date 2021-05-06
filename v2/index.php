<?php

require_once "../App.php";
require "../games.php";
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Referrer-Policy: no-referrer");


// App::getUniqueGenres($games);
if (!$_GET) {
    App::render_data($games);
    die();
}

App::main($games);

// App::shuffleData($games
