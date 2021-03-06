<?php
namespace raiz;
use  MongoDB;

error_reporting(E_ALL ^ E_NOTICE);
class Championship{
    function __construct( ){
        include "vendor/autoload.php";

        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->con = new \babirondo\classbd\db();


        //var_dump($this->Globais); exit;

        $this->con->conecta($this->Globais->banco ,
                              $this->Globais->localhost,
                              $this->Globais->db,
                              $this->Globais->username,
                              $this->Globais->password,
                              $this->Globais->port);

        $this->con->MongoDB = $this->Globais->Championship["Index"];
        $this->con->MongoTable = $this->Globais->Championship["Type"]["campeonato"];
    }


    function AlterarChampionship(  $request, $response, $args,   $jsonRAW){
      /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */

        IF (!is_array ($jsonRAW)  ) {
            $data =  array(	"resultado" =>  "ERRO",
                "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;
        //var_dump(  $conectadoTabela );exit;
        $filter = array( "_id" =>  new MongoDB\BSON\ObjectID( $args["idtorneio"] )     );
//        $options = array( 'upsert' => true, 'multi' => false ); //
        $param =   array(  '$set' => $jsonRAW );

        //var_dump($filter); var_dump($param); var_dump($jsonRAW);


        $resultMongo = $this->con->MongoUpdateOne($filter, $param,  $jsonRAW) ;

        $data =   array(	"resultado" =>  "SUCESSO" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');


        /*
        //var_dump($jsonRAW); exit;
        // salvando no elasticsearch
        $params["index"] = $this->Globais->Championship["Index"];
        $params["type"] = $this->Globais->Championship["Type"]["campeonato"];
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
        */

    }


    function DeleteChampionshipsElastic(  $request, $response, $args,   $jsonRAW){
      /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */



//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;
        //var_dump(  $conectadoTabela );exit;

        $resultMongo = $this->con->MongoDeleteOne   (
            array("_id" => new MongoDB\BSON\ObjectID( $args["idtorneio"] ) )

        );

        $data =   array(	"resultado" =>  "SUCESSO" );

        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');

/*
        //var_dump($jsonRAW); exit;
        // salvando no elasticsearch
        $params["index"] = $this->Globais->Championship["Index"];
        $params["type"] = $this->Globais->Championship["Type"]["campeonato"];
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
        */

    }


    function CreateChampionships(  $request, $response, $args,   $jsonRAW){

        /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */

        IF (!is_array ($jsonRAW)  ) {
            $data =  array(	"resultado" =>  "ERRO",
                "erro" => "JSON zuado - ".var_export($jsonRAW, true) );

            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }



// salvando no MongoDB

//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;

        $resultMongo = $this->con->MongoInsertOne( $jsonRAW );
        //var_dump(  $resultMongo );exit;

        $idMongo = $resultMongo->getInsertedId();


        $data["msg"] = "Inserted with Object ID '{$idMongo}'";
        $data["idChampionship"] = "{$idMongo}";

        //var_dump($jsonRAW); exit;
// salvando no elasticsearch
/*
        $params = [
            'index' => $this->Globais->Championship["Index"],
            'type' => $this->Globais->Championship["Type"]["campeonato"],
            'id' => $idMongo,
            'body' => $jsonRAW
        ];
        $respostaElasticSearch = $this->ElasticSearch->index($params);
        */
       // var_dump($respostaElasticSearch); exit;

// salvando no Postgresql
        /*
        $sql = "INSERT INTO championship (id, championship, sigla)
                VALUES('$idMongo','".$jsonRAW['championship']."',  '".$jsonRAW["sigla"]."')";
        $this->con->executa($sql);
        */

        if ( $idMongo ){

            $data["resultado"] = "SUCESSO" ;
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
        die("use Mongo");

        /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */

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

      /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */
        $filtros=array();
        $params = array();

        if ($args["idtorneio"]){
            $filtros["_id"]  =   new MongoDB\BSON\ObjectID( $args["idtorneio"] )  ;
        }

        $resultMongo =  $this->con->MongoFind($filtros );
//	$resultMongo =  iterator_to_array(  $resultMongo) ;

        $data["hits"] = $resultMongo;
        $data["resultado"] = "SUCESSO";

        /*

        $params = $this->Globais->ArrayMergeKeepKeys($params,$filtros);
        //var_dump($params); exit;

        $respostaElasticSearch = $this->ElasticSearch->search($params);
        //var_dump($respostaElasticSearch); exit;
*/
        return $response->withStatus(200)
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($data);
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
