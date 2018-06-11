<?php
namespace nd;

abstract class Plugin 
{
    abstract public function getName();
    abstract public function getIcon();
    abstract public function getAuthor();
    abstract public function getVersion();
    abstract public function getDscription();

    abstract public function onIDEStarting();
}