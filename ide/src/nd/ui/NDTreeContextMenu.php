<?php
namespace nd\ui;

use nd;
use gui;
use std;

use php\gui\UXContextMenu;
use nd\modules\IDE;
use php\lib\fs;
use php\gui\UXDesktop;
use php\gui\UXClipboard;
use php\gui\UXMenu;
use php\gui\UXMenuItem;
use nd\utils\FileUtils;

class NDTreeContextMenu extends UXContextMenu
{
    private $path;

    /**
     * NDTreeContextMenu constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct();
        $this->createItems($path);
    }

    /**
     * @param $path
     */
    private function createItems($path)
    {
        $this->items->addAll([
            $this->createMenuItem("Создать", IDE::ico("newFile.png"), IDE::get()->getFileFormat()->getFileTemplats($path)),
            $this->createItem("Создать папку", IDE::ico("folderAdd16.png"), function ($item) use ($path) {
                $name = IDE::inputDialog("Ввидите название новой папки.");
                
                if (fs::isDir($path))
                {
                    $path .= "/" . $name;
                } else {
                    $path = fs::parent($path) . "/" . $name;
                }
                
                if (!fs::makeDir(fs::abs($path)))
                {
                    IDE::dialog("Не удалось создать папку : " . fs::abs($path));
                }
            }),
            $this->createItem("Удалить", IDE::ico("fileDelete16.png"), function ($item) use ($path) {
                if (fs::isFile($path))
                {
                    if (!fs::delete($path))
                    {
                        IDE::dialog("Не удалось файл : " . fs::abs($path));
                    }
                } else {
                    if (!FileUtils::delete($path))
                    {
                        IDE::dialog("Не удалось папку : " . fs::abs($path));
                    }
                }
            }),
            $this->createItem("Показать в папке", IDE::ico("folder.png"), function ($item) use ($path) {
                $d = new UXDesktop;
                $d->open($path);
            }),
            $this->createItem("Скопировать путь", IDE::ico("clipboard.png"), function ($item) use ($path) {
                UXClipboard::setText($path);
            })
        ]);
    }
    
    public static function createItem(string $name, $img, callable $callback)
    {
        $item = new UXMenuItem($name);
        $item->graphic = $img;
        $item->on('action', function () use ($callback, $item) {
            $callback($item);
        });
        
        return $item;
    }
    
    public static function createMenuItem(string $name, $img, array $items)
    {
        $menu = new UXMenu($name);
        $menu->graphic = $img;
        $menu->items->addAll($items);
        
        return $menu;
    }
}