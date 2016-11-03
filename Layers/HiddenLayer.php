<?php
namespace Layers;

use Net;

class HiddenLayer extends Layer
{
    public function __construct(Net $network, int $nodeCount)
    {
        $this->setup(LayerTypeEnum::HIDDEN);
        parent::__construct($network, $nodeCount);
    }
}
