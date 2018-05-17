<?php
namespace nd\ui;

use framework;
use nd;
use std;
use gui;

class NDTree extends UXTreeView
{
    private $onAction;
    private $readOnly = false;
    private $path;
    
    public function __construct($onAction = null)
    {
        parent::__construct();
        $this->onAction = $onAction;
    }
    
    public function setReadOnly(bool $read)
    {
        $this->readOnly = $read;
    }
    
    public function refreshTree($path, bool $rootV = false)
    {
        $this->path = $path;
        $root = new UXTreeItem(new NDTreeValue(fs::name($this->path), fs::abs($this->path)));
        $root->graphic = IDE::ico("nd16.png"); // critical bug !!!! был
        $root->expanded = true;
        $this->root = $root;
        $this->rootVisible = $rootV;
        $this->refreshTreeItem($this->path, $root);
        if (!$this->readOnly)
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
                //$this->refreshTreeItem($path, $item);
                // уже не надо так как дерево само обновляется
            })->showByNode($this, $e->x, $e->y);
        });
        
        new Thread(function () {
            $files = File::of($this->path)->findFiles();
                    
            while (true)
            {
                $curent = File::of($this->path)->findFiles();
                if ($files != $curent)
                {
                    $this->refreshTreeItem($this->path, $this->root);
                    $files = $curent;
                }
                sleep(1);
            }
        })->start();
    }
    
    protected function refreshTreeItem($file, UXTreeItem $item) 
    {
        $item->children->clear();
        $files = File::of($file)->findFiles();
        
        foreach ($files as $file) {
            $subItem = new UXTreeItem(new NDTreeValue(fs::name($file), fs::abs($file)));
            if (fs::isDir($file)) {
                new Thread(function () use ($file, $subItem) {
                    $files = File::of($file)->findFiles();
                    
                    while (true)
                    {
                        $curent = File::of($file)->findFiles();
                        if ($files != $curent)
                        {
                            $this->refreshTreeItem($file, $subItem);
                            $files = $curent;
                        }
                        sleep(1);
                    }
                })->start();
                $subItem->graphic = IDE::ico("folder16.png");
                $this->refreshTreeItem($file, $subItem);
            } else {
                $subItem->graphic = IDE::get()->getFileFormat()->getIcon(fs::ext($file));
            }
            
            $item->children->add($subItem);
        }
    }
}