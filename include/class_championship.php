<?php
namespace raiz;
use Elasticsearch\ClientBuilder;
set_time_limit( 2 );

class Championship{
    function __construct( ){
        include "vendor/autoload.php";

        require("include/class_db.php");
        $this->con = new db();
        $this->con->conecta();

        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->ElasticSearch = ClientBuilder::create()->build();

    }


    function AlterarChampionship(  $request, $response, $args,   $jsonRAW){

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

        //var_dump($jsonRAW); exit;
        // salvando no elasticsearch
        $params["index"] = $this->Globais->Championship["Index"];
        $params["type"] = $this->Globais->Championship["Type"];
        $params["id"] = $args["idtorneio"];
        $params["body"]["doc"] = $jsonRAW;
        $params["body"]["upsert"]["counter"] = 1;

        //var_dump($params); exit;
        $respostaElasticSearch = $this->ElasticSearch->update($params);
        //var_dump($respostaElasticSearch); exit;

        $sql = "UPDATE championship SET
                      championship ='".$jsonRAW['championship']."',
                      sigla ='".$jsonRAW['sigla']."'
                      WHERE id =  '".$args['idtorneio']."'";
        $this->con->executa($sql);

        if ( $this->con->res == 1 ){

            $data =   array(	"resultado" =>  "SUCESSO" );
            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "Impossible to edit Championship - $mensagem_retorno");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }


    function DeleteChampionshipsElastic(  $request, $response, $args,   $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        //var_dump($jsonRAW); exit;
        // salvando no elasticsearch
        $params["index"] = $this->Globais->Championship["Index"];
        $params["type"] = $this->Globais->Championship["Type"];
        $params["id"] = $args["idtorneio"];


        //var_dump($params); exit;
        $respostaElasticSearch = $this->ElasticSearch->delete($params);
        //var_dump($respostaElasticSearch["result"]); exit;
        $mensagem_retorno .= $respostaElasticSearch["result"];

        $sql = "DELETE FROM championship WHERE id = '".$args["idtorneio"]."'";
        $this->con->executa($sql);

        if ( $this->con->res == 1 ){

            $data =   array(	"resultado" =>  "SUCESSO" );
            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "Impossible to delete this Tournament - $mensagem_retorno");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }


    function CreateChampionships(  $request, $response, $args,   $jsonRAW){

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

        //var_dump($jsonRAW); exit;
        // salvando no elasticsearch
        $params = [
            'index' => $this->Globais->Championship["Index"],
            'type' => $this->Globais->Championship["Type"],

            'body' => $jsonRAW
        ];
        $respostaElasticSearch = $this->ElasticSearch->index($params);
       // var_dump($respostaElasticSearch); exit;

        $sql = "INSERT INTO championship (championship, sigla)
                VALUES('".$jsonRAW['championship']."',  '".$jsonRAW["sigla"]."')";
        $this->con->executa($sql);

        if ( $this->con->res == 1 ){

            $data =   array(	"resultado" =>  "SUCESSO" );
            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "Impossible to create a new Championship - $mensagem_retorno");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }

    function getChampionships (  $request, $response, $args ){
        die("use elastic serach");

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        if ($args["idtorneio"]) $filtros[] = " id = '".$args["idtorneio"]."'";
        if ($jsonRAW["nome"]) $filtros[] = " nome ilike '%".$jsonRAW["nome"]."%'";

        $sql = "SELECT *
                FROM championship cha  ".((is_array($filtros))?" WHERE ".implode( " or ",$filtros) :"") ;
        $this->con->executa($sql);

        if ( $this->con->nrw > 0 ){
            $contador = 0;

            $data =   array(	"resultado" =>  "SUCESSO" );

            while ($this->con->navega(0)){
                $contador++;
                $data["CHAMPIONSHIP"][$this->con->dados["id"]]["sigla"] = $this->con->dados["sigla"];
                $data["CHAMPIONSHIP"][$this->con->dados["id"]]["championship"] = $this->con->dados["championship"];

            }

            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "No tournaments has been registered ");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }

    }
    function getChampionshipsElastic (  $request, $response, $args, $jsonRAW ){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        $filtros=array();
        $params = array();
        if (is_array($filtros)){
            $params["index"] =  $this->Globais->Championship["Index"];
            $params["type"] =  $this->Globais->Championship["Type"];
        }
        if ($args["idtorneio"]){
            //$filtros["query"]["terms"]["_id"] = $jsonRAW["idtorneio"];
            //$filtros["_source"] = false;
            $filtros["body"]["query"]["match"]["_id"] = $args["idtorneio"];
        }




        //$filtros = null;

        $params = $this->Globais->ArrayMergeKeepKeys($params,$filtros);
        //var_dump($params); exit;

        $respostaElasticSearch = $this->ElasticSearch->search($params);
        //var_dump($respostaElasticSearch); exit;

        return $response->withStatus(200)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($respostaElasticSearch);
       /*
          $sql = "SELECT *
                FROM championship cha  ".((is_array($filtros))?" WHERE ".implode( " or ",$filtros) :"") ;
        $this->con->executa($sql);

        if ( $this->con->nrw > 0 ){
            $contador = 0;

            $data =   array(	"resultado" =>  "SUCESSO" );

            while ($this->con->navega(0)){
                $contador++;
                $data["CHAMPIONSHIP"][$this->con->dados["id"]]["sigla"] = $this->con->dados["sigla"];
                $data["CHAMPIONSHIP"][$this->con->dados["id"]]["championship"] = $this->con->dados["championship"];

            }

            return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');
        }
        else {

            // nao encontrado
            $data =    array(	"resultado" =>  "ERRO",
                "erro" => "No tournaments has been registered ");

            return $response->withStatus(200)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);



        }
        */

    }

}
