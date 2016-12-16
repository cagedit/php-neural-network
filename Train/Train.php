<?php
namespace Train;

use Logging\Logger;
use Net, Exception, Neuron;
use Layers\Layer;

class Train implements TrainInterface
{
    const TRAIN_METHOD_XOR = 'xor';
    const TRAIN_METHOD_AND = 'and';
    const TRAIN_METHOD_OR = 'or';

    private $trainingData = [];

    /** @var Net */
    private $network;

    public function __construct($name)
    {
        switch($name) {
            case self::TRAIN_METHOD_XOR:
                $data = [
                    [1, 1, 0],
                    [1, 0, 1],
                    [0, 0, 0],
                    [0, 1, 1]
                ];
                break;
            case self::TRAIN_METHOD_OR:
                $data = [
                    [0, 1, 1],
                    [0, 0, 0],
                    [1, 1, 1],
                    [0, 1, 1]
                ];
                break;
            case self::TRAIN_METHOD_AND:
            default:
                $data = [
                    [0, 1, 0],
                    [1, 1, 1],
                    [1, 0, 0],
                    [0, 0, 0]
                ];
                break;
        }

        foreach (range(0, 1000) as $i) {
            $this->trainingData = array_merge($this->trainingData, $data);
        }

    }

    public function setNetwork(Net $net): TrainInterface
    {
        $this->network = $net;
        return $this;
    }

    public function getTopology(): array
    {
        return [2, 3, 1];
    }

    public function train(): TrainInterface
    {
        Logger::debug("Beginning to train the network!");

        $pass = 0;
        foreach ($this->trainingData as $index => $trainData) {
            println("Pass #" . ++$pass);
            $targetOutput = [array_pop($trainData)];

            Logger::debug("Target output: " . json_encode($targetOutput));
            Logger::debug("Input values: " . json_encode($trainData));

            $this->network->feedForward($trainData);
            $output = $this->network->getResults();

            Logger::debug("Actual Output: " . json_encode($output));

            $this->network->backProp($targetOutput);

            Logger::debug("Net current error: {$this->network->getError()}");
            Logger::debug("Net recent average error: {$this->network->getRecentAvgError()}");

            if ($index > 100 && $this->network->getRecentAvgError() < 0.01) {
                Logger::debug("Error has dropped to an acceptable level. Training is complete!");
                break;
            }
        }

        return $this;
    }



}