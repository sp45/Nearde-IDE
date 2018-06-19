<?php
namespace php\framework;


use php\gui\layout\UXAnchorPane;
use php\gui\UXNode;

class ArrayToLayout
{
    private $data;

    /**
     * @var UXAnchorPane
     */
    private $pane;

    private $externalClasses;

    /**
     * ArrayToLayout constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function exportClasses(array $data)
    {
        foreach ($data as $name => $class)
        {
            if (class_exists($class, true))
                $this->externalClasses[$name] = $class;
        }

        return $this;
    }

    public function parse()
    {
        $this->pane = new UXAnchorPane;

        foreach ($this->data as $id => $options)
        {
            $class = $options['class'];

            if (class_exists($class))
                $node = new $class;

            if ($this->externalClasses[$class])
                $node = new $this->externalClasses[$class];

            if (!$node) continue;
            if (!($node instanceof UXNode)) continue;

            $node->id = $id;

            foreach ($options as $option => $value)
            {
                if ($option == 'class') continue;

                $node->{$option} = $value;
            }

            $this->pane->add($node);
        }

        return $this;
    }

    /**
     * @param array $size
     * @return UXAnchorPane
     */
    public function get(array $size)
    {
        if ($this->pane)
            $this->pane->size = $size;
        return $this->pane;
    }
}