<?php
namespace raiz;
use MongoDB;
set_time_limit( 2 );
class Events{
    function __construct( ){
        require("include/class_db.php");
        $this->con = new db();
        $this->con->conecta();

        $this->Mongo = new db();
        $this->Mongo  = $this->Mongo->conecta("Mongo");

        require_once("include/globais.php");
        $this->Globais = new Globais();
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

        $jsonRAW["idevento"] = $args["idevento"];
        $args["idevento"] = null;

        $data = $this->getEvents( $request, $response, $args,   $jsonRAW );
        $data =  json_decode(json_encode($data), true);

        //var_dump($data["eventos"]); exit;

        foreach ($data as $linha => $eventos){

            if ($linha == "_id")
                $novo_array[$linha] =   ((array) new MongoDB\BSON\ObjectID( $eventos['$oid']  ))["oid"];

            else
                $novo_array[$linha] =     $eventos  ;

            if (is_array($eventos)){

                foreach ($eventos as $linhaEvento => $evento) {

                    if (is_array($evento)){
                        foreach ($evento as $idcampo => $campo) {



                            if ($idcampo == "_id") {

                             //   echo "-".$campo;
                                //$novo_array[$linha][$linhaEvento][$idcampo] =  new MongoDB\BSON\ObjectID (  $campo['$oid'] );//
                                $novo_array[$linha][$linhaEvento][$idcampo] =  new MongoDB\BSON\ObjectID (  $campo['$oid'] );//

//                                if ($novo_array[$linha][$linhaEvento][$idcampo]['$oid'] == $jsonRAW["idevento"])
                                if ($novo_array[$linha][$linhaEvento][$idcampo] == $jsonRAW["idevento"])
                                    $idEvento = $linhaEvento;

                            }
                            else
                                $novo_array[$linha][$linhaEvento][$idcampo] =  $campo  ;
                        }

                    }

                }
            }
        }


    //    $novo_array = $data;
        $jsonRAW_bkp = $jsonRAW;
        $jsonRAW["_id"] =  new MongoDB\BSON\ObjectID(  $jsonRAW["idevento"])        ;
        unset($jsonRAW["idevento"]);

        $novo_array["eventos"][$idEvento] = $jsonRAW;

        unset($novo_array["_id"]);

        //var_dump($novo_array);exit;

        $filtros=array();
        $params = array();

        if ($args["idtorneio"]){
            $filtros['_id']  =  new MongoDB\BSON\ObjectID( $args["idtorneio"]  );//
        }
        if ($jsonRAW["idevento"]){
            $filtros["eventos._id"]  =     new MongoDB\BSON\ObjectID( $jsonRAW_bkp["idevento"] )  ;//
        }

        $options = array( 'upsert' => true, 'multi' => false ); //
        $param =   array(  '$set' =>   $novo_array );

        $bd = $this->Globais->Championship["Index"];
        $table = $this->Globais->Championship["Type"]["campeonato"];

        $conectadoTabela = $this->Mongo->$bd->$table;

       // var_dump($jsonRAW_update);exit;

        $resultMongo = $conectadoTabela->updateOne($filtros, $param, $options) ;

        $data =   array(	"resultado" =>  "SUCESSO" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');



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
        $etapaID = new MongoDB\BSON\ObjectID(   );



        $filtros=array();
        $params = array();
        if (is_array($filtros)){
            $params["index"] =  $this->Globais->Championship["Index"];
            $params["type"] =  $this->Globais->Championship["Type"]["campeonato"];
        }
        if ($args["idtorneio"]){
            //$filtros["query"]["terms"]["_id"] = $jsonRAW["idtorneio"];
            //$filtros["_source"] = false;
            $filtros["_id"]  =   new MongoDB\BSON\ObjectID( $args["idtorneio"] )  ;
        }

        $bd = $this->Globais->Championship["Index"];
        $table = $this->Globais->Championship["Type"]["campeonato"];

        $conectadoTabela = $this->Mongo->$bd->$table;

        $resultMongo = $conectadoTabela->find( $filtros )  ;
        $dados_ja_armazenados  = iterator_to_array($resultMongo)[0];
        ini_set("xdebug.overload_var_dump", "off");

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        //$dados_ja_armazenados["eventos"][] =  $jsonRAW ;

        $jsonRAW_2 =$jsonRAW;
        $jsonRAW_2["_id"] = $etapaID ;

        $dados_ja_armazenados["eventos"][]  =  (object) $jsonRAW_2;
        //$dados_ja_armazenados["eventos"]["$etapaID"]["_id"] = $etapaID ;

        //var_dump( $dados_ja_armazenados["eventos"] );exit;
        $conectadoTabela = $this->Mongo->$bd->$table;

        $filter = array( "_id" =>  new MongoDB\BSON\ObjectID( $args["idtorneio"] )     );
        $options = array( 'upsert' => true, 'multi' => false ); //
        $param =   array(  '$set' => $dados_ja_armazenados );
        $mensagem = "ID evento criado ".$etapaID;

        $resultMongo = $conectadoTabela->updateOne($filter, $param, $options) ;


        $data =   array(	"resultado" =>  "SUCESSO" );
        $data["mensagem"] = $mensagem;
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');



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


        $filtros=array();
        $params = array();

        if (is_array($filtros)){
            //$params["index"] =  $this->Globais->Championship["Index"];
            //$params["type"] =  $this->Globais->Championship["Type"]["campeonato"];
        }
        if ($args["idtorneio"]){
            $filtros['_id']  =  new MongoDB\BSON\ObjectID( $args["idtorneio"]  );//
        }
        if ($args["idevento"]){
            $filtros["eventos._id"]  =     new MongoDB\BSON\ObjectID( $args["idevento"] )  ;//
            $params["projection"]['eventos.$']  =  true;//
            $params["projection"]["sigla"]  =  true;//
            $params["projection"]["championship"]  =  true;//
            $params["projection"]["foto"]  =  true;//
        }
        if ($jsonRAW["idevento"]){
            $filtros["eventos._id"]  =     new MongoDB\BSON\ObjectID( $jsonRAW["idevento"] )  ;//
        }

        ini_set("xdebug.overload_var_dump", "off");

        $bd = $this->Globais->Championship["Index"];
        $table = $this->Globais->Championship["Type"]["campeonato"];

        $conectadoTabela = $this->Mongo->$bd->$table;

        $ops = $filtros;

        //var_dump($ops);var_dump($params);exit;
        $resultMongo = $conectadoTabela->find($ops , $params )  ; //$params

        $dados_ja_armazenados = $resultMongo->toArray()[0] ;
        //var_dump($dados_ja_armazenados);var_dump($ops);var_dump($params);exit;

        if (($dados_ja_armazenados) != false){
        //    var_dump($dados_ja_armazenados);var_dump($ops);var_dump($params);exit;
            return $dados_ja_armazenados;
        }
        else
            return false;

    }

    function getEventsAPI (  $request, $response, $args , $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }

        $dados_ja_armazenados = $this->getEvents( $request, $response, $args , $jsonRAW );
        //var_dump($data);exit;

        if ( ($dados_ja_armazenados) != false){
            $dados_ja_armazenados = (array) $dados_ja_armazenados;
            $dados_ja_armazenados["resultado"] = "SUCESSO";

            return $response->withJson($dados_ja_armazenados, 200)->withHeader('Content-Type', 'application/json');
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
