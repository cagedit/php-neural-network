<?php

class InstanceTracker
{
    private static $id = 0;

    public static function getNext()
    {
        return self::$id++;
    }
}