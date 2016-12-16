<?php
require_once(__DIR__ . '/autoload.php');

use Logging\LogLevelEnum;
use Logging\Logger;
use Train\Train;
use Layers\Layer;
use Train\TrainNumbers;

ini_set('memory_limit', '2048M');

Logger::setLevel(LogLevelEnum::DEBUG);

$network = new Net;

set_exception_handler(function(Exception $e) use($network) {
    println($e->getMessage());


    /** @var Layer $layer */
    foreach ($network->getLayers() as $layer) {
        println("Describing layer type: {$layer->getType()}");

        /** @var Neuron $node */
        foreach ($layer->getNodes() as $node) {
            println("Node as value of: {$node->getOutputVal()}", 1);
        }
    }

});

/*
 * Training phase
 */

//$train = new Train('xor');

$train = new TrainNumbers;
$topology = $train->getTopology();

$network->setup($topology);

$train
    ->setNetwork($network)
    ->train();


///*
// * Verify phase
// */
//println("######### Test 1 #########");
//$network->feedForward([1, 1]);

//$results = json_encode($network->getResults());
//Logger::debug("The results of verifying are: {$results}");
//
//
//println("######### Test 2 #########");
//$network->feedForward([1, 1]);
////
//$results = json_encode($network->getResults());
//Logger::debug("The results of verifying are: {$results}");


Logger::all("Completed");
throw new Exception("fuck.");

//(new TrainNumbers())->train();

