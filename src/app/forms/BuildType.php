<?php
namespace app\forms;

use app\forms\project;
use utils\Project;
use php\gui\UXImage;
use php\gui\UXImageView;
use build\BuildType;
use php\gui\layout\UXHBox;
use php\gui\layout\UXVBox;
use php\gui\UXLabel;
use php\gui\UXListCell;
use php\gui\framework\AbstractForm;
use php\gui\event\UXWindowEvent; 

use gui;
use php\gui\event\UXEvent; 

class BuildType extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->listView->setCellFactory(function(UXListCell $cell, $item) {
            if ($item) {              
                $titleName = new UXLabel($item[0]);
                $titleName->style = '-fx-font-weight: bold;'; 
             
                $titleDescription = new UXLabel($item[1]);
                $titleDescription->style = '-fx-text-fill: gray;';
             
                $title  = new UXVBox([$titleName, $titleDescription]);
                $title->spacing = 0;
               
                $line = new UXHBox([$item[2], $title]);
                $line->spacing = 7;
                $line->padding = 5;
                $cell->text = null;
                $cell->graphic = $line;
                $cell->data("call", $item[3]);
                $cell->on('click', function (UXMouseEvent $e) use ($item) {
                    if ($e->clickCount < 2) return;
                    
                    $callback = $item[3];
                    
                    $callback();
                    
                    $this->hide();
                });
            }
        }); 
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->hide();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $item = $this->listView->selectedItems[0];
        if ($item == []) return;
        
        $callback = $item[3];          
        $callback();          
        $this->hide();
    }
    
    function addItem(BuildType $type, \utils\Project $project)
    {
        $this->listView->items->add([
            $type->getName(),
            $type->getDescription(),
            new UXImageView(new UXImage($type->getIcon())),
            function () use ($type, $project) {
                $type->build($project);
            }
        ]);
    }

}
