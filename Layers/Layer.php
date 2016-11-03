<?php
namespace Layers;

use Logging\Logger;
use Neuron, Net, Traits\IdTrait;

class Layer
{
    use IdTrait;

    private $network;
    private $nodes;
    private $type;

    public function __construct(Net $network, int $nodeCount)
    {
        $this->network = $network;


        Logger::debug("Node count for {$this->getType()} is {$nodeCount}");

        for ($j = 0; $j <= $nodeCount; ++$j) {
            $neuron = new Neuron($nodeCount, $j);
            $this->nodes[] = $neuron;
            $neuron->setLayer($this);
            $neuron->setOutputVal(1.0);
        }
    }

    public function setOutputVals(array $values)
    {

        /** @var Neuron $node */
        foreach ($this->nodes as $index => $node) {
            $node->setOutputVal($values[$index]);
        }
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    protected function setup(string $type)
    {
        $this->setId();

        if (is_null($this->type)) {
            $this->type = $type;
        }

        Logger::debug("Setting up a new {$this->type} layer.");

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }


    public function feedForward(Layer $prevLayer)
    {
        $nodes = $this->nodes;

        if ($this->getType() !== LayerTypeEnum::OUTPUT) {
            // Remove bias
            array_pop($nodes);
        }

        /** @var Neuron $node */
        foreach ($nodes as $node) {
            $node->feedForward($prevLayer);
        }
    }

    public function getOutputVals(): array
    {
        $output = [];
        $nodes = $this->getNodes();

        // Remove bias
        array_pop($nodes);

        /** @var Neuron $node */
        foreach ($nodes as $node) {
            $output[] = $node->getOutputVal();
        }

        return $output;
    }

    public function getOutputWeights()
    {
        $weights = [];
        foreach ($this->getNodes() as $node) {
            $weights[] = $node->getOutputWeights();
        }
        return $weights;
    }


}
