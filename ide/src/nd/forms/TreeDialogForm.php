<?php
namespace nd\forms;

use nd\ui\NDTree;
use php\gui\event\UXEvent;
use std, gui, framework, nd;


class TreeDialogForm extends AbstarctIDEForm
{
    /**
     * @var NDTree
     */
    private $tree;
    
    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {
        $this->hide();
    }


    public function open(string $text, string $path)
    {    
        $this->label->text = $text;
        $this->tree = new NDTree;
        $this->tree->setReadOnly(true);
        $this->tree->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        
        $this->tree->refreshTree($path);
        $this->panel->add($this->tree);
        $this->showAndWait();
        
        if ($this->tree->selectedItems[0])
            return $this->tree->selectedItems[0]->value->path;
        else return null;
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->hide();
    }

}
