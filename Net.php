<?php

use Logging\Logger;
use Layers\Layer;
use Layers\HiddenLayer;
use Layers\InputLayer;
use Layers\OutputLayer;


class Net
{
    private $layers;

    /** @var InputLayer */
    public $inputLayer;

    /** @var HiddenLayer */
    public $hiddenLayer;

    /** @var OutputLayer */
    public $outputLayer;

    /** @var float */
    private $error = 0.0;

    /** @var float */
    private $recentAvgError = 0.0;

    /** @var float */
    private static $recentAvgSmoothingFactor = 100.0;

    /** @var bool */
    private $isSetup = false;

    public function setup(array $topology)
    {
        $lastIndex = count($topology) - 1;

        foreach ($topology as $index => $layerTopology) {
            if ($index === 0) {
                $this->layers[] = $layer = $this->inputLayer = new InputLayer($this, $layerTopology);
            } elseif ($index === $lastIndex) {
                $this->layers[] = $layer = $this->outputLayer = new OutputLayer($this, $layerTopology);
            } else {
                $this->layers[] = $layer = $this->hiddenLayer = new HiddenLayer($this, $layerTopology);
            }
        }

        $this->isSetup = true;

        Logger::debug("Total number of layers created: " . count($this->layers));
    }

    public function feedForward(array $inputVals)
    {
        $this->assertSetup();

        Logger::all("Setting input values for feed forward: " . json_encode($inputVals));

        assert(
            count($inputVals) === count($this->inputLayer->getNodes()) - 1,
            "The number of inputs should be the same as number of nodes in input layer"
        );

        $inputVals[] = 1.0;
        $this->inputLayer->setOutputVals($inputVals);

        /** @var Layer $currLayer */
        foreach ($this->layers as $i => $currLayer) {
            if ($i === 0) {
                continue;
            }

            /** @var Layer $prevLayer */
            $prevLayer = $this->layers[$i - 1];

            $currLayer->feedForward($prevLayer);
        }
    }

    public function backProp(array $targetVals)
    {
        $this->assertSetup();

        //
        // error

        // Calculate overall net error (RMS of output neuron errors)

        $outputNodes = $this->outputLayer->getNodes();

        $this->error = 0.0;

        for ($n = 0; $n < count($outputNodes) - 1; ++$n) {
            /** @var Neuron $outputNode */
            $outputNode = $outputNodes[$n];
            $delta = $targetVals[$n] - $outputNode->getOutputVal();
            $this->error += $delta * $delta;
        }

        $this->error /= (count($outputNodes) - 1);
        $this->error = sqrt($this->error);


        // Implement a recent average measurement

        $this->recentAvgError =
            ($this->recentAvgError * self::$recentAvgSmoothingFactor + $this->error)
            / (self::$recentAvgSmoothingFactor + 1.0);

        // Calculate output layer gradients
        for ($n = 0; $n < count($outputNodes) - 1; ++$n) {
            /** @var Neuron $outputNode */
            $outputNode = $outputNodes[$n];
            $outputNode->calcOutputGradients($targetVals[$n]);
        }



        // Calculate hidden layer gradients
        $this->calcHiddenGradients();

        // For all layers from outputs to first hidden layer,
        // update connection weights
        $this->updateInputWeights();
    }

    public function updateInputWeights()
    {
        $this->assertSetup();

        for ($i = count($this->layers) - 1; $i > 0; --$i) {

            /** @var Layer $currLayer */
            $currLayer = $this->layers[$i];

            $currNodes = $currLayer->getNodes();

            /** @var Layer $prevLayer */
            $prevLayer = $this->layers[$i - 1];

            for ($n = 0; $n < count($currNodes) - 1; ++$n) {
                /** @var Neuron $currNode */
                $currNode = $currNodes[$n];
                $currNode->updateInputWeights($prevLayer);
            }
        }
    }

    /**
     * Calculate hidden gradients.
     * @todo: In order to be dynamic, this needs to go through ALL hidden
     * @todo: layers and not just assume 1 hidden layer.
     */
    public function calcHiddenGradients()
    {
        for ($i = count($this->layers) - 2; $i > 0; --$i) {
            $hiddenLayer = $this->layers[$i];
            $hiddenNodes = $hiddenLayer->getNodes();
            $nextLayer = $this->layers[$i + 1];


            for ($n = 0; $n < count($hiddenNodes); ++$n) {
                /** @var Neuron $hiddenNode */
                $hiddenNode = $hiddenNodes[$n];
                $hiddenNode->calcHiddenGradients($nextLayer);
            }
        }

    }

    public function getError()
    {
        return $this->error;
    }

    public function getRecentAvgError()
    {
        return $this->recentAvgError;
    }

    public function getResults()
    {
        return $this->outputLayer->getOutputVals();
    }

    public function getLayers()
    {
        return $this->layers;
    }

    private function assertSetup()
    {
        if (!$this->isSetup) {
            throw new Exception(ErrorsEnum::NETWORK_NOT_SETUP);
        }
    }
}
