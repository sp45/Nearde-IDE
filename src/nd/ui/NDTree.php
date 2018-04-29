<?php
namespace nd\ui;

use nd;
use std;
use gui;

class NDTree extends UXTreeView
{
    function refreshTree($file)
    {
        $this->root = new NDTreeItem(fs::name($file), fs::abs($file));
        $this->rootVisible = false;
        $this->refreshTreeItem($file, $this->root);
    }
    
    protected function refreshTreeItem($file, UXTreeItem $item) 
    {
        $files = File::of($file)->findFiles();
        
        foreach ($files as $file) {
            $subItem = new NDTreeItem(fs::name($file), fs::abs($file));
            
            if (fs::isDir($file)) {
                $subItem->graphic = IDE::ico("folder16.png");
                $this->refreshTreeItem($file, $subItem);
            } else {
                $subItem->graphic = $GLOBALS['ND']->getFileFormat()->getIcon(fs::ext($file));
            }
            
            $item->children->add($subItem);
        }
    }
}