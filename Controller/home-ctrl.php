<?php
class home
{
    public function index()
    {
        CORE::VIEW("index", "Home");
    }
    public function argTest($args)
    {
        var_dump($args);
    }
}