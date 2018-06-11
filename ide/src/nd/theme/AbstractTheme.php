<?php
namespace nd\theme;


abstract class AbstractTheme
{
    abstract public function getName() : string;
    abstract public function getID() : string;
    abstract public function getAuthor() : string;
    abstract public function getCssFile() : string;
}