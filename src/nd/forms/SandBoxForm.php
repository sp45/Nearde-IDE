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
        $this->tree->anchors = $this->panel->anchors;
        $this->panel->add($this->tree);
        
        IDE::upgradeListView($this->listView);
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        if (!fs::exists($this->edit->text))
        {
            alert("Not found");
            return;
        } 
        
        $this->tree->refreshTree(File::of($this->edit->text));
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
