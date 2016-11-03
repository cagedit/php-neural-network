<?php
namespace Layers;

use Net;

class OutputLayer extends Layer
{
    public function __construct(Net $net, int $numNodes)
    {
        $this->setup(LayerTypeEnum::OUTPUT);
        parent::__construct($net, $numNodes);
    }
}
