<?php
namespace raiz;
set_time_limit(2);
//error_reporting(E_ALL ^ E_DEPRECATED ^E_NOTICE);
class Globais{

    public $env;
    public $banco;

    function __construct( ){

        $this->banco = $this->env = "prod";
        $servidor["UI"] = $servidor["frontend"] = "http://34.247.245.249";
        $servidor["autenticacao"] = "http://34.242.188.167";
        $servidor["players"] = "http://54.171.155.88";
        $servidor["campeonato"] = "http://34.242.140.31";

        $this->verbose=1;

        switch($this->banco){

            case("local");
                $this->localhost = "localhost";
                $this->username = "postgres";
                $this->password = "bruno";
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
