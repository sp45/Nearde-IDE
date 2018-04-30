<?php
namespace nd\ui;

use nd;
use std;
use gui;

class NDTree extends UXTreeView
{
    public function refreshTree($file)
    {
        $root = new UXTreeItem(new NDTreeValue(fs::name($file), fs::abs($file)));
        $this->root = $root;
        $this->rootVisible = false;
        $this->refreshTreeItem($file, $root);
        $this->on('click', function (UXMouseEvent $e) use ($this) {
            if ($e->button != "SECONDARY") return;
            
            $item = $this->selectedItems[0];
            $path = $item->value->path;
            new NDTreeContextMenu($path, function () use ($this, $path, $item) {
                $this->refreshTreeItem($path, $item);
            })->showByNode($this, $e->x, $e->y);
        });
    }
    
    protected function refreshTreeItem($file, UXTreeItem $item) 
    {
        $item->children->clear();
        $files = File::of($file)->findFiles();
        
        foreach ($files as $file) {
            $subItem = new UXTreeItem(new NDTreeValue(fs::name($file), fs::abs($file)));
            
            if (fs::isDir($file)) {
                $subItem->graphic = IDE::ico("folder16.png");
                $this->refreshTreeItem($file, $subItem);
            } else {
                $subItem->graphic = IDE::get()->getFileFormat()->getIcon(fs::ext($file));
            }
            
            $item->children->add($subItem);
        }
    }
}