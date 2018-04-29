<?php
namespace nd\forms;

use std, gui, framework, nd;


class SandBoxForm extends AbstractForm
{
    
    /**
     * @var NDTree
     */
    private $tree;
    
    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->tree = new NDTree();
        $this->tree->x = 8; 
        $this->tree->y = 48;
        $this->tree->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        $this->tree->refreshTree(fs::abs("./"));
        $this->tabPane->tabs->toArray()[0]->content->add($this->tree);
        
        IDE::upgradeListView($this->listView); 
    }


    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->listView->items->add([
            'Android',
            'Возможность писать приложения под android',
            IDE::ico("android.png"),
            function () {
                alert("Но кто сказал что android будет ?");
            }
        ]);
    }

}
