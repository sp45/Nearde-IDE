<?php
namespace nd\ui;

use nd;
use gui;
use std;

class NDTreeContextMenu extends UXContextMenu
{
    private $path;
    
    public function __construct(string $path, callable $callback)
    {
        parent::__construct();
        $this->createItems($path);
        $this->on('hide', function () use ($callback) {
            $callback();
        });
    }
    
    private function createItems($path)
    {
        $this->items->addAll([
            $this->createMenuItem("Создать", IDE::ico("newFile.png"), IDE::get()->getFileFormat()->getFileTemplats($path)),
            $this->createItem("Создать папку.", IDE::ico("folderAdd16.png"), function ($item) use ($path) {
                $name = UXDialog::input("Ввидите название новой папки.");
                
                if (fs::isDir($path))
                {
                    $path .= "/" . $name;
                } else {
                    $path = fs::parent($path) . "/" . $name;
                }
                
                if (!fs::makeDir(fs::abs($path)))
                {
                    UXDialog::show("Не удалось создать папку : " . fs::abs($path));
                }
            }),
            $this->createItem("Удалить.", IDE::ico("fileDelete16.png"), function ($item) use ($path) {
                if (fs::isFile($path))
                {
                    if (!fs::delete($path))
                    {
                        UXDialog::show("Не удалось файл : " . fs::abs($path));
                    }
                } else {
                    if (!FileUtils::delete($path))
                    {
                        UXDialog::show("Не удалось папку : " . fs::abs($path));
                    }
                }
            }),
            $this->createItem("Открыть.", IDE::ico("folder.png"), function ($item) use ($path) {
                new UXDesktop->open($path);
            }),
            $this->createItem("Скопировать путь.", IDE::ico("clipboard.png"), function ($item) use ($path) {
                UXClipboard::setText($path);
            })
        ]);
    }
    
    public static function createItem(string $name, UXImageView $img, callable $callback)
    {
        $item = new UXMenuItem($name);
        $item->graphic = $img;
        $item->on('action', function () use ($callback, $item) {
            $callback($item);
        });
        
        return $item;
    }
    
    public static function createMenuItem(string $name, UXImageView $img, array $items)
    {
        $menu = new UXMenu($name);
        $menu->graphic = $img;
        $menu->items->addAll($items);
        
        return $menu;
    }
}