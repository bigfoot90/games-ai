<?php

namespace Games\Tris\Bot;

class SimulationEnd extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cannot go back more');
    }
}