<?php
include('../bbdd/facade.php');
include('../model.php');
use Ratchet\MessageComponentInterface;  
use Ratchet\ConnectionInterface;

class Game implements MessageComponentInterface {  
    public $clients;
    private $player1Conn;
    private $player1ID;
    private $player1Name;
    private $player1Score;
    private $player1Stage;
    private $player2Conn;
    private $player2ID;
    private $player2Name;
    private $player2Score;
    private $player2Stage;
    private $preguntas;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->player1Conn = null;
        $this->player2Conn = null;
        $this->player1Score = 0;
        $this->player2Score = 0;  
        $this->player1Stage = 0;
        $this->player2Stage = 0;
    }

    public function onOpen(ConnectionInterface $conn) {
    
        //Conectar el jugador
        if($this->player1Conn === null)
        {
        
            //Obtener el id del jugador que se conecta
            $query = $conn->WebSocket->request->getQuery()->toArray();
            $userID = $query['param'];
            
            //Obtener el nombre
            $result = Facade::getUserFromID($userID);
			$data = mysqli_fetch_array($result);
            $userName = $data["name"];
            
            $this->clients->attach($conn);
            echo "Nueva conexion! [ResourceID:{$conn->resourceId} | UserID:$userID | UserName:$userName ]\n";
            $this->player1Conn = $conn;
            $this->player1ID = $userID;
            $this->player1Name = $userName;
        }
        
        //Conectar el jugador y comenzar la partida
        else if($this->player2Conn === null)
        {
        
            //Obtener el id del jugador que se conecta
            $query = $conn->WebSocket->request->getQuery()->toArray();
            $userID = $query['param'];
            
            //Obtener el nombre
            $result = Facade::getUserFromID($userID);
			$data = mysqli_fetch_array($result);
            $userName = $data["name"];
            
            $this->clients->attach($conn);
            echo "Nueva conexion! [ResourceID:{$conn->resourceId} | UserID:$userID | UserName:$userName ]\n";
            $this->player2Conn = $conn;
            $this->player2ID = $userID;
            $this->player2Name = $userName;
            
            //Enviar el nombre de los jugadores
            $this->sendPlayers();
            
            //Generamos las preguntas que el jugador debe responder
            $this->preguntas = Model::getPreguntas(5, 'Informatica') or exit('Fallo al generar las preguntas');

            //Enviamos la primera pregunta
            $this->sendNewQuestion(0);
        }

        //Rechazar el jugador
        else
        {
            echo "Refused connection\n";
            $conn->close();
        }
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $data = json_decode($msg, true);
        //print_r($data);
        
        if($data['type'] == 'response'){
            $respuesta = $data['text'];
            $tiempo = $data['time'];
            $player = $data['player'];
            
            //Jugador 1
            if($player == $this->player1ID){
                echo "Player1: Sent response=$respuesta; Correct response=".$this->preguntas[$this->player1Stage]['c']."\n";
                if($respuesta == $this->preguntas[$this->player1Stage]['c']){
                    $this->sendResponse($this->player1Conn, "Respuesta Correcta! :)");
                    $this->player1Score = $this->player1Score + intval($tiempo);
                    echo "El jugador 1 suma $tiempo puntos\n";
                }else{
                    $this->sendResponse($this->player1Conn, "Respuesta Incorrecta :(");
                }
                $this->player1Stage = $this->player1Stage + 1;
                echo "Player 1 stage = ".$this->player1Stage."\n";
            }
            
            //Jugador 2
            else if($player == $this->player2ID){
                echo "Player2: Sent response=$respuesta; Correct response=".$this->preguntas[$this->player2Stage]['c']."\n";
                if($respuesta == $this->preguntas[$this->player2Stage]['c']){
                    $this->sendResponse($this->player2Conn, "Respuesta Correcta! :)");
                    $this->player2Score = $this->player2Score + intval($tiempo);
                    echo "El jugador 2 suma $tiempo puntos\n";
                }else{
                    $this->sendResponse($this->player2Conn, "Respuesta Incorrecta :(");
                }
                $this->player2Stage = $this->player2Stage + 1;
                echo "Player 2 stage = ".$this->player2Stage."\n";
            }
            
            //Actualizar la puntuacion a ambos usuarios
            $this->sendScore();
            
            //Enviar una pregunta nueva si ambos han respondido las mismas preguntas pero menos de 5
            if(($this->player1Stage == $this->player2Stage) && ($this->player1Stage < 5)){
                $this->sendNewQuestion($this->player1Stage);
            }
            
            //Terminar partida si ambos han respondido 5 preguntas
            if(($this->player1Stage >= 5) && ($this->player2Stage >= 5)){
                
                //Insertar puntuacion final en el ranking (registro de partidas)
                Facade::insertPartida(array('j1' => $this->player1ID, 'j2' => $this->player2ID, 'p1' => $this->player1Score, 'p2' => $this->player2Score));
                
                //Send final match message
                $this->sendMatchEnd();
                
                //End Connections
                $this->player1Conn->close();
                $this->player2Conn->close();
                
                //Reset variables
                $this->player1Conn = null;
                $this->player2Conn = null;
                $this->player1Score = 0;
                $this->player2Score = 0;  
                $this->player1Stage = 0;
                $this->player2Stage = 0;
            }

        }else if($data['type'] == 'disconnect'){
            $player = $data['player'];
            
            if($player == $this->player1ID){
                $this->player1Score = 0;
            }
            
            else if($player == $this->player2ID){
                $this->player2Score = 0;
            }
            
            //End match match message
            Facade::insertPartida(array('j1' => $this->player1ID, 'j2' => $this->player2ID, 'p1' => $this->player1Score, 'p2' => $this->player2Score));
            $this->sendMatchEnd();
            $this->player1Conn->close();
            $this->player2Conn->close();
            $this->player1Conn = null;
            $this->player2Conn = null;
            $this->player1Score = 0;
            $this->player2Score = 0;  
            $this->player1Stage = 0;
            $this->player2Stage = 0;
        }

    }
    
    private function sendResponse($playerConn, $message){
        $msg = array( 
            "type" => "responseUpdate",
            "message" => $message
        );
        $playerConn->send(json_encode($msg));
    }
    
    private function sendPlayers(){
        $msg1 = array( 
            "type" => "playerSet",
            "player1" => $this->player1Name,
            "player2" => $this->player2Name
        );
        
        $msg2 = array( 
            "type" => "playerSet",
            "player1" => $this->player2Name,
            "player2" => $this->player1Name
        );
        
        $this->player1Conn->send(json_encode($msg1));
        $this->player2Conn->send(json_encode($msg2));
        echo "Players names sent\n";
    }
    
    private function sendScore(){
        $msg1 = array( 
            "type" => "scoreUpdate",
            "player1" => $this->player1Score,
            "player2" => $this->player2Score
        );
        
        $msg2 = array( 
            "type" => "scoreUpdate",
            "player1" => $this->player2Score,
            "player2" => $this->player1Score
        );
        
        $this->player1Conn->send(json_encode($msg1));
        $this->player2Conn->send(json_encode($msg2));
    }
    
    
    private function sendMatchEnd() {
        
        $p1 = 0;
        $p2 = 0;
        
        if($this->player1Score > $this->player2Score){
            $p1 = 1;
        }else if($this->player1Score < $this->player2Score){
            $p2 = 1;
        }else{
            $p1 = 2;
            $p2 = 2;
        }
    
        $msg1 = array( 
            "type" => "endMatch",
            "data" => array (
                "win" => $p1,
                "score" => $this->player1Score
            )
        );
        
        $msg2 = array( 
            "type" => "endMatch",
            "data" => array (
                "win" => $p2,
                "score" => $this->player2Score
            )
        );
        
        $this->player1Conn->send(json_encode($msg1));
        $this->player2Conn->send(json_encode($msg2));
        echo "Final match message sent: score1=".$this->player1Score." score2=".$this->player2Score."\n";
    }
    
    private function sendNewQuestion($index) {
        $msg = array( 
            "type" => "newQuestion",
            "data" => array (
                "p" => $this->preguntas[$index]['p'],
                "r1" => $this->preguntas[$index]['r1'],
                "r2" => $this->preguntas[$index]['r2'],
                "r3" => $this->preguntas[$index]['r3'],
                "r4" => $this->preguntas[$index]['r4']
            )
        );
        
        $this->player1Conn->send(json_encode($msg));
        $this->player2Conn->send(json_encode($msg));
    }

    public function onClose(ConnectionInterface $conn) {
        // Detatch everything from everywhere
        $this->clients->detach($conn);
        echo "Connection closed\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        $conn->close();
    }
    
}

/*

    [1] => Array
        (
            [id] => 1
            [tema] => 1
            [p] => ¿Cuál de las siguientes unidades de información es mayor?
            [r1] => Petabyte
            [r2] => Zettabyte
            [r3] => Exabyte
            [r4] => Yottabyte
            [c] => 4
        )
*/

?>