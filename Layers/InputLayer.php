<?php
namespace Layers;

use Net;

class InputLayer extends Layer
{
    public function __construct(Net $network, int $nodeCount)
    {
        $this->setup(LayerTypeEnum::INPUT);
        parent::__construct($network, $nodeCount);
    }
}
