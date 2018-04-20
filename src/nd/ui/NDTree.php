<?php
namespace nd\ui;

use nd;
use std;
use gui;

class NDTree extends UXTreeView
{
    function refreshTree(File $file)
    {
        $this->root = new NDTreeItem(fs::name($file), $file->getAbsolutePath());
        $this->refreshTreeItem($file, $this->root);
    }
    
    protected function refreshTreeItem(File $file, UXTreeItem $item) 
    {
        $files = $file->findFiles();
        
        foreach ($files as $file) {
            $subItem = new NDTreeItem($file->getName(), $file->getAbsolutePath());
            
            if ($file->isDirectory()) {
                $subItem->graphic = IDE::ico("folder.png");
                $this->refreshTreeItem($file, $subItem);
            } else {
                $subItem->graphic = $GLOBALS['ND']->getFileFormat()->getIcon(fs::ext($file));
            }
            
            $item->children->add($subItem);
        }
    }
}