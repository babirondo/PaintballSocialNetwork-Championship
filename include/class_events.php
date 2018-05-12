<?php
namespace raiz;
set_time_limit( 2 );
class Events{
    function __construct( ){
        require("include/class_db.php");
        $this->con = new db();
        $this->con->conecta();
    }

    function AlterarEvento(  $request, $response, $args,   $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        IF (!is_array ($jsonRAW)  ) {
            $data =  array(	"resultado" =>  "ERRO",
                "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        $sql = "UPDATE events SET 
                      sigla = '".$jsonRAW["sigla"]."',
                      event = '".$jsonRAW["evento"]."'
                      
                      WHERE id = '".$args["idevento"]."'";
        $this->con->executa($sql);

        if ( $this->con->res == 1 ){

            $data =   array(	"resultado" =>  "SUCESSO" );
            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "Impossible to create new event - $mensagem_retorno");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }


    function CriarEvento(  $request, $response, $args,   $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        IF (!is_array ($jsonRAW)  ) {
            $data =  array(	"resultado" =>  "ERRO",
                "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        $sql = "INSERT INTO events (idchampionship, event, sigla)
                VALUES('".$args['idtorneio']."','".$jsonRAW['evento']."',  '".$jsonRAW["sigla"]."')";
        $this->con->executa($sql);

        if ( $this->con->res == 1 ){

            $data =   array(	"resultado" =>  "SUCESSO" );
            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "Impossible to create new event - $mensagem_retorno");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }

    function getEvents (  $request, $response, $args , $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        if ($args["idtorneio"]) $filtros[] = " e.idchampionship = '".$args["idtorneio"]."'";
        if ($args["idevento"]) $filtros[] = " e.id = '".$args["idevento"]."'";
        if ($jsonRAW["ideventos"]) $filtros[] = " e.id IN (".implode(" ,",$jsonRAW["ideventos"]) .")";


        $sql = "SELECT e.*, c.championship, c.sigla champsigla
                FROM events e
                  INNER JOIN championship c ON (c.id = e.idchampionship) 
                ".((is_array($filtros))?" WHERE ".implode( " and ",$filtros) :"") . "
                 ORDER BY c.championship, e.event
                 " ;
        //echo $sql;exit;
        $this->con->executa($sql);

        if ( $this->con->nrw > 0 ){
            $contador = 0;

            $data =   array(	"resultado" =>  "SUCESSO" );

            while ($this->con->navega(0)){
                $contador++;
                $data["EVENTs"][$this->con->dados["id"]]["sigla"] = $this->con->dados["sigla"];
                $data["EVENTs"][$this->con->dados["id"]]["evento"] = $this->con->dados["event"];
                $data["EVENTs"][$this->con->dados["id"]]["championship"] = $this->con->dados["championship"];
                $data["EVENTs"][$this->con->dados["id"]]["combo"] = $this->con->dados["champsigla"].": ".$this->con->dados["event"];

            }

            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "SUCESSO",
                "erro" => "No Event has been registered for this Championship");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }


}
