<?php
namespace nd\external;

use nd;
use facade\Json;
use php\gui\UXTabPane;
use php\gui\UXTab;
use nd\modules\IDE;
use php\gui\UXLabel;
use php\gui\layout\UXVBox;

class ProjectEditor extends UXTabPane
{
    private $json;
    
    public function open(string $path)
    {
        parent::__construct();
        $this->json = Json::fromFile($path);
        $this->side = "BOTTOM";
        $this->tabClosingPolicy = "UNAVAILABLE";
        
        $codeTab = new UXTab("Исходный код");
        $codeTab->graphic = IDE::ico("newFile.png");
        $codeTab->content = IDE::get()->getFileFormat()->getCodeEditor($path);
        $this->tabs->addAll([
            $this->guiTab(), $codeTab
        ]);
    }
    
    private function guiTab() : UXTab
    {
        $tab = new UXTab("Проект");
        $tab->graphic = IDE::ico("nd16.png");
        $tab->content = new UXVBox([IDE::ico("plugin32.png"), new UXLabel("Скоро...")]);
        $tab->content->alignment = "CENTER";
        return $tab;
    } 
}