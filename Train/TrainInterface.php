<?php
namespace Train;

use Net;

interface TrainInterface
{
    public function train(): TrainInterface;
    public function setNetwork(Net $network): TrainInterface;
    public function getTopology(): array;
}