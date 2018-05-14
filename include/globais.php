<?php
namespace raiz;
set_time_limit(2);
//error_reporting(E_ALL ^ E_DEPRECATED ^E_NOTICE);
class Globais{

    public $env;
    public $banco;

    function __construct( ){

        if ( $_SERVER["PATH"] == "/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin")
            $this->banco = $this->env = "prod";
        else{
            $this->banco= "local";
            $this->env = "local";
        }
        switch($this->env){

            case("local");
                $servidor= "http://localhost:81";
                $this->verbose=1;
                break;

            case("prod");
                $servidor= "http://pb.mundivox.rio";
                $this->verbose=1;
                break;

        }
        switch($this->banco){

            case("local");
                $this->localhost = "localhost";
                $this->username = "postgres";
                $this->password = "bruno";
                $this->db ="championship_local";
                break;

            case("prod");
                $this->localhost = "pb.mundivox.rio";
                $this->username = "pb";
                $this->password = "Rodr1gues";
                $this->db ="championship";
                break;

        }

        //ROTAS de CAMPEONATO
        $this->Campeonatos =                    $servidor."/PaintballSocialNetwork-Championship/Tournaments/";
        $this->NovoCampeonato =                 $servidor."/PaintballSocialNetwork-Championship/Tournaments/";

        $this->getCampeonato =                  $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/";
        $this->NovoCampeonatoAlterar =          $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/";

        $this->CampeonatoEtapas =               $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/";
        $this->NovaEtapa =                      $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/";

        $this->getEtapa =                       $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/:idetapa/";
        $this->AlterarEtapa =                   $servidor."/PaintballSocialNetwork-Championship/Tournaments/:idtorneio/Etapas/:idetapa/";

        $this->getCampeonatosEventos =          $servidor."/PaintballSocialNetwork-Championship/Tournaments/Etapas/"; // TEST UNIT
        $this->getEventos =                     $servidor."/PaintballSocialNetwork-Championship/Tournaments/Etapas/"; // TEST UNIT

    }

}
