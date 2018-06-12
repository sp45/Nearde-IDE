<?php
namespace nd\ui;

use framework;
use nd;
use php\io\IOException;
use std;
use gui;

use php\lang\Thread;
use php\io\File;
use php\lib\fs;
use nd\modules\IDE;
use php\gui\event\UXMouseEvent;

use php\gui\UXTreeView;
use php\gui\UXTreeItem;

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
        $root->graphic = IDE::ico("nd16.png");
        $root->expanded = true;
        $this->root = $root;
        $this->rootVisible = $rootV;
        $this->refreshTreeItem($this->path, $root);
        if (!$this->readOnly)
        $this->on('click', function (UXMouseEvent $e) {
            if ($e->button != "SECONDARY") {
                
                if ($e->clickCount >= 2 && is_callable($this->onAction) && $this->selectedItems[0] != null)
                    call_user_func($this->onAction, $this->selectedItems[0]->value->path);

                return;
            }
            
            $item = $this->selectedItems[0];
            $path = $item->value->path;
            $c = new NDTreeContextMenu($path);
            $c->showByNode($this, $e->x, $e->y);
        });
        
        $t = new Thread(function () {
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
        });
        $t->start();
    }
    
    protected function refreshTreeItem($file, UXTreeItem $item) 
    {
        $item->children->clear();
        try {
            $files = File::of($file)->findFiles();
        } catch (IOException $e) {
            return;
        }

        foreach ($files as $file) {
            $subItem = new UXTreeItem(new NDTreeValue(fs::name($file), fs::abs($file)));
            if (fs::isDir($file)) {
                $t = new Thread(function () use ($file, $subItem) {
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
                });
                $t->start();
                $subItem->graphic = IDE::ico("folder16.png");
                $this->refreshTreeItem($file, $subItem);
            } else {
                $subItem->graphic = IDE::get()->getFileFormat()->getIcon(fs::ext($file));
            }
            
            $item->children->add($subItem);
        }
    }
}