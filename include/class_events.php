<?php
namespace raiz;
use MongoDB;

error_reporting(E_ALL ^ E_NOTICE);

class Events{
    function __construct( ){

        include "vendor/autoload.php";

        require_once("include/globais.php");
        $this->Globais = new Globais();

        $this->con = new \babirondo\classbd\db();



        $this->con->conecta($this->Globais->banco ,
                              $this->Globais->localhost,
                              $this->Globais->db,
                              $this->Globais->username,
                              $this->Globais->password,
                              $this->Globais->port);

        $this->con->MongoDB = $this->Globais->Championship["Index"];
        $this->con->MongoTable = $this->Globais->Championship["Type"]["campeonato"];
    }

    function AlterarEvento(  $request, $response, $args,   $jsonRAW){

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

	//TODO:  completa bagunca esse metodo alterar

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

//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;

       // var_dump($jsonRAW_update);exit;

        $resultMongo = $this->con->MongoUpdateOne($filtros, $param, $options) ;

        $data =   array(	"resultado" =>  "SUCESSO" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');



    }


    function CriarEvento(  $request, $response, $args,   $jsonRAW){
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

//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;

        $resultMongo = $this->con->MongoFind( $filtros )  ;
        $dados_ja_armazenados  = $resultMongo[0];
//        ini_set("xdebug.overload_var_dump", "off");

//XXXXXXXXXXXXXXXXXXXXXXXXXXXXX

        //$dados_ja_armazenados["eventos"][] =  $jsonRAW ;

        $jsonRAW_2 =$jsonRAW;
        $jsonRAW_2["_id"] = $etapaID ;

        $dados_ja_armazenados["eventos"][]  =  (object) $jsonRAW_2;
        //$dados_ja_armazenados["eventos"]["$etapaID"]["_id"] = $etapaID ;

        //var_dump( $dados_ja_armazenados["eventos"] );exit;
//        $conectadoTabela = $this->Mongo->$bd->$table;

        $filter = array( "_id" =>  new MongoDB\BSON\ObjectID( $args["idtorneio"] )     );
        $options = array( 'upsert' => true, 'multi' => false ); //
        $param =   array(  '$set' => $dados_ja_armazenados );
        $mensagem = "ID evento criado ".$etapaID;

        $resultMongo = $this->con->MongoUpdateOne($filter, $param, $options) ;


        $data =   array(	"resultado" =>  "SUCESSO" );
        $data["mensagem"] = $mensagem;
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');




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
	      $dados_ja_armazenados = $this->con->MongoFind($filtros, $params);
        //$dados_ja_armazenados = $resultMongo[0] ;

	      return $dados_ja_armazenados;
    }


    function DeleteEvento (  $request, $response, $args , $jsonRAW){

        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        ini_set("xdebug.overload_var_dump", "off");

        $filtros=array();
        $params = array();

        if ($args["idtorneio"]){
            $filtros["_id"]  =     new MongoDB\BSON\ObjectID( $args["idtorneio"]  );//
        }
        if ($args["idevento"]){
          //  $filtros ['eventos._id']  =  new MongoDB\BSON\ObjectID(   $args["idevento"]  );//     $args["idevento"] ;
            $where["eventos"]['_id'] = new MongoDB\BSON\ObjectID(   $args["idevento"]  );
            //$options["projection"]['eventos.$']  =  true;//

        }
        $options['$pull'] =  $where;

//        $bd = $this->Globais->Championship["Index"];
//        $table = $this->Globais->Championship["Type"]["campeonato"];

//        $conectadoTabela = $this->Mongo->$bd->$table;

        if ($args["idevento"]){
            //$options["projection"]['eventos.$']  =  true;//
        }

        //var_dump($filtros);var_dump($options);
        $resultMongo = $this->con->MongoUpdateOne ( $filtros, $options ) ;
        //var_dump($resultMongo);exit;

        $data =   array(	"resultado" =>  "SUCESSO" );
        return $response->withJson($data, 200)->withHeader('Content-Type', 'application/json');


    }

    function getEventsAPI (  $request, $response, $args , $jsonRAW){
        /*
        if (!$this->con->conectado){
            $data =   array(	"resultado" =>  "ERRO",
                "erro" => "nao conectado - ".$this->con->erro );
            return $response->withStatus(500)
                ->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withJson($data);
        }
        */

        $dados_ja_armazenados["hits"] = $this->getEvents( $request, $response, $args , $jsonRAW );
        $torneios = array();
        $eventos = array();


        foreach ($dados_ja_armazenados["hits"] as $idretorno => $retorno){
            foreach ($retorno["eventos"] as $idretorno2 => $retorno2){

              $chave =  $retorno2['_id'];
              $eventos[ "$chave"]  =  $retorno2;
              $eventos[ "$chave"]["idcampeonato"]  =  $retorno['_id'];
              $eventos[ "$chave"]['combo']  =  $retorno["championship"]. " - ".$retorno2["evento"];
            }
        }


        foreach ($dados_ja_armazenados["hits"] as $idretorno => $retorno){
              $chave =  $retorno['_id'];
              $torneios[ "$chave"]  =  $retorno;
              $torneios[ "$chave"]['_id']  =  $chave;
        }


        if ( ($dados_ja_armazenados) != false){
            $dados_ja_armazenados = (array) $dados_ja_armazenados;

            $dados_ja_armazenados["campeonatos"] = $torneios;
            $dados_ja_armazenados["eventos"] = $eventos;
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
