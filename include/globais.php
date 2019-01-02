<?php
namespace raiz;
set_time_limit(2);
//error_reporting(E_ALL ^ E_DEPRECATED ^E_NOTICE);
class Globais{

    public $env;
    public $banco;
    public $localhost;
    public $username ;
    public $port ;
    public $password;
    public $db;

    function __construct( ){

        $this->env = "local";

        $servidor["UI"] = $servidor["frontend"] = "http://192.168.0.150:81";
        $servidor["autenticacao"] = "http://192.168.0.150:82";
        $servidor["campeonato"] = "http://192.168.0.150:84";
        $servidor["players"] = "http://192.168.0.150:83";

        $servidor["bancodados_campeonato"] = "localhost";
        $servidor["bancodados_players"] = "192.168.0.150";

        $this->verbose=1;

        switch($this->env){

            case("local");
                $this->banco = "Mongo";
                $this->localhost = $servidor["bancodados_campeonato"];
                $this->username = "postgres";
                $this->port = 27017;
                $this->password = "postgres";
                $this->db ="championship_local";
            break;

            case("prod");
                $this->localhost = "localhost";
                $this->username = "postgres";
                $this->password = "bruno";
                $this->db ="championship";
                break;

        }

        $this->Championship["Index"] = "championship2";
        $this->Championship["Type"]["campeonato"] = "campeonatos";
        $this->Championship["Id"] = "id";

        $this->healthcheck = $servidor["campeonato"]."/PaintballSocialNetwork-Championship/healthcheck/"; //UNIT TEST       
        $this->Campeonatos =                    $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/";


        $this->LogoutUI =                       $servidor["campeonato"]."/PaintballSocialNetwork/Logout/";

        //ROTAS de CAMPEONATO
        $this->Campeonatos =                    $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/";
        $this->NovoCampeonato =                 $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/";

        $this->getCampeonato =                  $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/";
        $this->NovoCampeonatoAlterar =          $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/";

        $this->CampeonatoEtapas =               $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/";
        $this->NovaEtapa =                      $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/";

        $this->getEtapa =                       $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/:idetapa/";
        $this->AlterarEtapa =                   $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/:idetapa/";

        $this->getCampeonatosEventos =          $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/Etapas/"; // TEST UNIT
        $this->getEventos =                     $servidor["campeonato"]."/PaintballSocialNetwork-Championship/Tournaments/Etapas/"; // TEST UNIT

    }
    Function ArrayMergeKeepKeys() {
        $arg_list = func_get_args();
        foreach((array)$arg_list as $arg){
            if (is_array ($arg) )
            {
                foreach((array)$arg as $K => $V){
                    $Zoo[$K]=$V;
                }
            }
        }
        return $Zoo;
    }

}
