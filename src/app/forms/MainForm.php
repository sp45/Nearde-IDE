<?php
namespace app\forms;

use facade\Json;
use utils\Project;
use std, gui, framework, app;
use php\gui\event\UXEvent; 
use php\gui\event\UXWindowEvent; 


class MainForm extends AbstractForm
{

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        $this->Projectslist->setCellFactory(function(UXListCell $cell, $item) {
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
                $line->on('click', function (UXMouseEvent $e) use ($item) {
                
                    if ($e->clickCount < 2) return;
                    
                    $callback = $item[3];
                    
                    $callback();
                });
                $cell->text = null;
                $cell->graphic = $line;
                $cell->data("project", $item[3]);
            }
        }); 
        
        $json = Json::fromFile("./projects.json");
        foreach ($json as $project)
        {
            $platform = $this->getProjects()->getPlatform($project['platform']);
            if ($platform == null)
            {
                $this->Projectslist->items->add([
                    $project['name'],
                    "Несовместимый проект.",
                    new UXImageView(new UXImage("res://.data/img/question32.png")),
                    function (){
                        alert("Несовместимый проект.");
                    }
                ]);
                
                continue;
            }
            
            $type = $platform->getProjectType();
    
            if ($type != null) {
            
                $name = $type->getName();
            
                $img = new UXImageView(new UXImage($type->getIcon()));
            
                $this->Projectslist->items->add([$project['name'], $name, $img, function () use ($this, $project) {
                    $p = new \utils\Project();
                    $open = $p->open($project['src'], $project['name']);
                    if ($open == null)
                        alert("Не удалось открыть проект.");
                    else $this->hide();
                }]);
                
            } else {
                $this->Projectslist->items->add([
                    $project['name'],
                    "Несовместимый проект.",
                    new UXImageView(new UXImage("res://.data/img/question32.png")),
                    function (){
                        alert("Несовместимый проект.");
                    }
                ]);
            }
        }
    }



    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        app()->getForm("newProject")->show();
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->nrdChooser->execute();
        $path = $this->nrdChooser->file;
        if (!$path) return;
        
        $project = new \utils\Project();
        
        if (!$project->Open(fs::parent($path), fs::nameNoExt($path)))
        {
            alert("Невозможно открыть проект.");
        } else {
            $this->hide();
        }
    }

    /**
     * @event close 
     */
    function doClose(UXWindowEvent $e = null)
    {    
        die();
    }


}
