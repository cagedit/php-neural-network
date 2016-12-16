<?php
use Layers\Layer;
use Logging\Logger;
class Neuron
{

    protected $value;
    protected $outputVal;
    protected $preValue;
    protected $outputWeights;
    protected $gradient;
    protected $myIndex;

    /** @var Layer */
    protected $layer;
    protected $eta = 0.15;
    protected $alpha = 0.5;


    public function __construct(int $numOutputs, int $myIndex)
    {
        foreach (range(0, $numOutputs) as $i) {
            $this->outputWeights[] = [
                'weight' => randFloat(),
                'deltaWeight' => 0
            ];
        }

        $this->myIndex = $myIndex;
    }

    public function setLayer(Layer $layer)
    {
        $this->layer = $layer;
    }

    public function setOutputVal(float $value)
    {
        $this->outputVal = $value;
    }

    public function getOutputVal()
    {
        return $this->outputVal;
    }

    public function feedForward(Layer $prevLayer)
    {
        $sum = 0.0;
        $prevNodes = $prevLayer->getNodes();

        for ($n = 0; $n < count($prevNodes); ++$n) {
            /** @var Neuron $prevNode */
            $prevNode = $prevNodes[$n];
            $sum += $prevNode->getOutputVal() * $prevNode->getOutputWeightFor($this->myIndex)['weight'];
        }

        if (!is_numeric($sum)) {
            throw new Exception(ErrorsEnum::NODE_OUTPUT_NOT_NUMERIC);
        }

        $this->setOutputVal(transferFnc($sum));
    }

    public function calcOutputGradients(float $targetVal)
    {
        Logger::debug("Calculating output gradients");

        $delta = $targetVal - $this->getOutputVal();

        if (!is_numeric($this->getOutputVal())) {
            throw new Exception(ErrorsEnum::NODE_OUTPUT_NOT_NUMERIC);
        }

        $this->gradient = $delta * transferDerivativeFnc($this->getOutputVal());
    }

    public function calcHiddenGradients(Layer $nextLayer)
    {
        $dow = $this->sumDOW($nextLayer);

        $this->gradient = $dow * transferDerivativeFnc($this->getOutputVal());
    }

    function sumDOW(Layer $nextLayer): float
    {
        $sum = 0.0;
        $nodes = $nextLayer->getNodes();

        for ($n = 0; $n < count($nodes) - 1; ++$n) {
            /** @var Neuron $nextNode */
            $nextNode = $nodes[$n];
            $sum += $this->outputWeights[$n]['weight'] * $nextNode->getGradient();
        }

        return $sum;

    }

    public function getGradient()
    {
        return $this->gradient;
    }

    public function updateInputWeights(Layer $prevLayer)
    {
        $nodes = $prevLayer->getNodes();
        for ($n = 0; $n < count($nodes); ++$n) {
            /** @var Neuron $node */
            $node = $nodes[$n];

            $oldDeltaWeight = $node->getOutputWeightFor($this->myIndex)['deltaWeight'];

            $newDeltaWeight =
                $this->eta
                * $node->getOutputVal()
                * $this->gradient
                + $this->alpha
                * $oldDeltaWeight;

            $node->updateOutputWeightsFor($this->myIndex, $newDeltaWeight);
        }
    }

    public function getOutputWeightFor($index)
    {

        return array_get($this->outputWeights, $index, ['weight' => 0, 'deltaWeight' => 0.0]);
    }

    public function getOutputWeights()
    {
        return $this->outputWeights;
    }

    public function updateOutputWeightsFor(int $nodeIndex, float $newDeltaWeight)
    {
        $weight = $this->outputWeights[$nodeIndex]['weight'] ?? 0.0;

        $this->outputWeights[$nodeIndex]['deltaWeight'] = $newDeltaWeight;
        $this->outputWeights[$nodeIndex]['weight'] = $weight + $newDeltaWeight;
    }
}
