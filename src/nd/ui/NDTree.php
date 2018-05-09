<?php
namespace nd\ui;

use nd;
use std;
use gui;

class NDTree extends UXTreeView
{
    private $onAction;
    
    public function __construct($onAction = null)
    {
        parent::__construct();
        $this->onAction = $onAction;
    }
    
    public function refreshTree($file, bool $rootV = false)
    {
        $root = new UXTreeItem(new NDTreeValue(fs::name($file), fs::abs($file)));
        $root->graphic = IDE::ico("./nd16.png");
        $root->expanded = true;
        $this->root = $root;
        $this->rootVisible = $rootV;
        $this->refreshTreeItem($file, $root);
        $this->on('click', function (UXMouseEvent $e) use ($this) {
            if ($e->button != "SECONDARY") {
                
                if ($e->clickCount >= 2 && is_callable($this->onAction) && $this->selectedItems[0] != null)
                {
                    $callBack = $this->onAction;
                    $callBack($this->selectedItems[0]->value->path);
                }
                return;
            }
            
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