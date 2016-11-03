<?php namespace Traits;
use InstanceTracker;
trait IdTrait
{
    private $id;
    public function setId()
    {
        $this->id = InstanceTracker::getNext();
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}