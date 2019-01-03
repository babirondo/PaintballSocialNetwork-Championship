<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
//set_time_limit(10);


require('vendor/autoload.php');


class testPlayers extends PHPUnit\Framework\TestCase
{
    protected $client;

    protected function setUp()
    {

        $conf['timeout'] = 5;
        $conf['connect_timeout'] = 5;
        $conf['read_timeout'] = 5;
        $this->client = new GuzzleHttp\Client(   $conf );

        require_once("include/globais.php");

        $this->Globais = new raiz\Globais();
    }

    public function OpenConf(){

      $configuracoes_externas = file_get_contents('include/config.json');
      $config_parsed = json_decode($configuracoes_externas,true);
      return $config_parsed;
    }

      public function SaveConf($conf){

           $fp = fopen('include/config.json', "w");
           if (fwrite($fp, json_encode($conf,true)))
                $sucesso = 1;
           else
                $sucesso = 0;
           fclose($fp);

          return $sucesso;
      }


    public function testGet_HealthCheck()
    {
        $response = $this->client->request('GET', $this->Globais->healthcheck
            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'connect_timeout' => 10 // Connection timeout
            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump(  $jsonRetorno );
        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"]);
    }





    public function testPOST_criarTorneios()
    {


        $idjogador = 10;

        $champ = "testCHAMP".rand(500,8500);

        $JSON = json_decode( "  {\"sigla\":\"$champ\",\"championship\":\"$champ\",\"foto\":null} " , true);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        $response = $this->client->request('POST', strtr($this->Globais->NovoCampeonato, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //var_dump($jsonRetorno);

        $Conf = $this->OpenConf();
        $Conf["idchampionship"] = $jsonRetorno["idChampionship"];

        if ($this->SaveConf($Conf) == 0){
          echo " Nao foipossivel salvar o arqvuio de conf";
          exit;
        }

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }


    public function testPUT_AlterarTorneio()
    {


        $idtorneio = 11;
        $Conf = $this->OpenConf();

        $champ = "testCHAMP-alterado".rand(500,8500);

        $JSON = json_decode( "  {\"sigla\":\"TST\",\"championship\":\"$champ\",\"foto\":null} " , true);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtorneio" =>   $Conf["idchampionship"]  );

        //var_dump($JSON);
        //var_dump(strtr($this->Globais->NovoCampeonatoAlterar, $trans));

        $response = $this->client->request('PUT', strtr($this->Globais->NovoCampeonatoAlterar, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);


        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }


    public function testPOST_newEvento()
    {

        $Conf = $this->OpenConf();
        $idtorneio =  $Conf["idchampionship"];

        $champ = "testEVENT".rand(500,8500);

        $JSON = json_decode( "  {\"evento\":\"$champ\",\"foto\":null} " , true);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtorneio" => $idtorneio );

        //var_dump($JSON);var_dump(strtr($this->Globais->NovaEtapa, $trans));

        $response = $this->client->request('POST', strtr($this->Globais->NovaEtapa, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
      //  var_dump($jsonRetorno);
        $Conf["idEvento"] = $jsonRetorno["idEvento"];

        if ($this->SaveConf($Conf) == 0){
          echo " Nao foipossivel salvar o arqvuio de conf";
          exit;
        }


        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }




    public function testPUT_AlterarEvento()
    {
        $Conf = $this->OpenConf();

        $idtorneio =$Conf["idchampionship"];
        $idetapa =   $Conf["idEvento"];

        $champ = "testEVENT".rand(500,8500);

        $JSON = json_decode( "  {\"evento\":\"$champ\",\"foto\":null} " , true);
        if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtorneio" => $idtorneio , ":idetapa" => $idetapa);

//        var_dump($JSON);var_dump(strtr($this->Globais->AlterarEtapa, $trans));


        $response = $this->client->request('PUT', strtr($this->Globais->AlterarEtapa, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
        //  var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }




    public function testGET_EventosTorneios()
    {

        set_time_limit(10);
        $idjogador = 10;

        $time = "testAAA".rand(500,8500);

    //        $JSON = json_decode( " {\"time\":\"$time\",\"treino\":{\"Quarta\":\"Quarta\"},\"nivelcompeticao\":\"D2\",\"procurando\":{\"BackCenter\":\"BackCenter\"},\"localtreino\":\"dublin\",\"foto\":{\"name\":\"\",\"type\":\"\",\"tmp_name\":\"\",\"error\":4,\"size\":0}} " , true);
        //if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        //var_dump(strtr($this->Globais->getEventos, $trans));
        $response = $this->client->request('GET', strtr($this->Globais->getEventos, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);


        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }





    public function testGET_getEventosfromChampionship()
    {
      $Conf = $this->OpenConf();

      $idtorneio =$Conf["idchampionship"];
      $idetapa =   $Conf["idEvento"];

        //$champ = "testCHAMP-alterado".rand(500,8500);

        //$JSON = json_decode( "  {\"siglka\":\"TST\",\"championship\":\"$champ\",\"foto\":null} " , true);
        //if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtorneio" => $idtorneio );
        $response = $this->client->request('GET', strtr($this->Globais->CampeonatoEtapas, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);
      //  var_dump($jsonRetorno);

        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }




    public function testGET_Torneio()
    {

        $Conf = $this->OpenConf();

        $idtorneio =$Conf["idchampionship"];
        $idetapa =   $Conf["idEvento"];

      //  $champ = "testCHAMP".rand(500,8500);

//        $JSON = json_decode( "  {\"$champ\":\"TST\",\"championship\":\"$champ\",\"foto\":null} " , true);
        //if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idtorneio" => $idtorneio );
        $response = $this->client->request('GET', strtr($this->Globais->getCampeonato, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);


        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }




    public function testGET_getTorneios()
    {

      $Conf = $this->OpenConf();

      $idtorneio =$Conf["idchampionship"];
      $idetapa =   $Conf["idEvento"];

        $idjogador = 10;

        //$time = "testAAA".rand(500,8500);

        //$JSON = json_decode( " {\"time\":\"$time\",\"treino\":{\"Quarta\":\"Quarta\"},\"nivelcompeticao\":\"D2\",\"procurando\":{\"BackCenter\":\"BackCenter\"},\"localtreino\":\"dublin\",\"foto\":{\"name\":\"\",\"type\":\"\",\"tmp_name\":\"\",\"error\":4,\"size\":0}} " , true);
        //if ($JSON == NULL ) die(" JSON erro de formacao");

        $trans = null;$trans = array(":idjogadorlogado" => $idjogador );
        $response = $this->client->request('GET', strtr($this->Globais->Campeonatos, $trans)

            , array(
                'headers' => array('Content-type' => 'application/x-www-form-urlencoded'),
                'timeout' => 10, // Response timeout
                'form_params' => $JSON,
                'connect_timeout' => 10 // Connection timeout


            )
        );
        $jsonRetorno = json_decode($response->getBody()->getContents(), 1);


        $this->assertEquals('SUCESSO', $jsonRetorno["resultado"] );
    }


    /*
*/
}
