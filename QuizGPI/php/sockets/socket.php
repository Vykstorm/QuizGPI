<?php

require 'vendor/autoload.php';  
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require 'game.php';

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Game()
        )
    ),
    8081
);


$server->run();
