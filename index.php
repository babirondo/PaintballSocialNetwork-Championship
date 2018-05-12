<?php
namespace raiz;
error_reporting(E_ALL ^ E_DEPRECATED);


use Slim\Views\PhpRenderer;

include "vendor/autoload.php";


$app = new \Slim\App(['settings' => ['displayErrorDetails' => true]]);

$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("./templates");



$app->get('/healthcheck/', function ($request, $response, $args)  use ($app )   {
    require_once("healthcheck/healthcheck.php");

    $HealthCheck = new HealthCheck();

    $retorno = $HealthCheck->check($response, $request->getParsedBody() );
    return $retorno;
}  );


$app->get('/Tournaments/{idtorneio:[0-9]+}/', function ($request, $response, $args)  use ($app )   {  // TEST UNIT
    require_once("include/class_championship.php");

    $cChampionship = new Championship();
    $retorno = $cChampionship->getChampionships($request, $response, $args , null);

    return $retorno;
}  );
$app->put('/Tournaments/{idtorneio:[0-9]+}/', function ($request, $response, $args)  use ($app )   {  // TEST UNIT
    require_once("include/class_championship.php");

    $cChampionship = new Championship();
    $retorno = $cChampionship->AlterarChampionship($request, $response, $args ,  $request->getParsedBody() );

    return $retorno;
}  );



$app->get('/Tournaments/', function ($request, $response, $args)  use ($app )   {  // TEST UNIT
    require_once("include/class_championship.php");

    $cChampionship = new Championship();
    $retorno = $cChampionship->getChampionships($request, $response, $args , null);

    return $retorno;
}  );

$app->post('/Tournaments/', function ($request, $response, $args)  use ($app )   {  // TEST UNIT
    require_once("include/class_championship.php");

    $cChampionship = new Championship();
    $retorno = $cChampionship->CreateChampionships($request, $response, $args , $request->getParsedBody() );

    return $retorno;
}  );


$app->any('/Tournaments/Etapas/', function ($request, $response, $args)  use ($app )   { // TEST UNIT
    require_once("include/class_events.php");

    $cEvents = new Events();
    $retorno = $cEvents->getEvents($request, $response, $args  , $request->getParsedBody()  );

    return $retorno;
}  );

$app->get('/Tournaments/{idtorneio:[0-9]+}/Etapas/', function ($request, $response, $args)  use ($app )   { // TEST UNIT
    require_once("include/class_events.php");

    $cEvents = new Events();
    $retorno = $cEvents->getEvents($request, $response, $args , null);

    return $retorno;
}  );
$app->post('/Tournaments/{idtorneio:[0-9]+}/Etapas/', function ($request, $response, $args)  use ($app )   {// TEST UNIT
    require_once("include/class_events.php");

    $cEvents = new Events();
    $retorno = $cEvents->CriarEvento($request, $response, $args , $request->getParsedBody() );

    return $retorno;
}  );
$app->get('/Tournaments/{idtorneio:[0-9]+}/Etapas/{idevento:[0-9]+}/', function ($request, $response, $args)  use ($app )   { // TEST UNIT
    require_once("include/class_events.php");

    $cEvents = new Events();
    $retorno = $cEvents->getEvents($request, $response, $args , $request->getParsedBody() );

    return $retorno;
}  );
$app->put('/Tournaments/{idtorneio:[0-9]+}/Etapas/{idevento:[0-9]+}/', function ($request, $response, $args)  use ($app )   { // TEST UNIT
    require_once("include/class_events.php");

    $cEvents = new Events();
    $retorno = $cEvents->AlterarEvento($request, $response, $args , $request->getParsedBody() );

    return $retorno;
}  );


$app->run();

