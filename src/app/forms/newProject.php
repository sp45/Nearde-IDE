<?php
namespace app\forms;

use app\modules\MainModule;
use php\compress\ZipFile;
use bundle\zip\ZipFileScript;
use facade\Json;
use std, gui, framework, app;
use php\gui\event\UXWindowEvent; 


class newProject extends AbstractForm
{

    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {    
        $this->projectsType->setCellFactory(function(UXListCell $cell, $item) {
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
                $cell->data("type", $item[3]);
            }
        }); 
        
        $platforms = $this->getProjects()->getAllPlatforms();
        
        $this->projectsType->items->clear();
        
        foreach ($platforms as $platform)
        {
            $project = $platform->getProjectType();
            
            $name = $project->getName();
            $description = $project->getDescription();
            $this->projectsType->items->add([ $name, $description, new UXImageView(new UXImage($project->getIcon())), $platform->getId() ]);    
        }
        
        $this->hidePreloader();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->hide();
    }

    /**
     * @event createButton.action 
     */
    function doCreateButtonAction(UXEvent $e = null)
    {    
        if ($this->projectsType->selectedItem != null)
        {
            if ($this->name->text != null)
            {
                if ($this->dir->text != null)
                {
                    $directory = new File($this->dir->text . "/" . $this->name->text);
                    $list = $directory->findFiles();
                    if ($list == [])
                    {
                        $this->showPreloader("Создание проекта.");
                        $typeID = $this->projectsType->selectedItem[3];
                        $platform = $this->getProjects()->getPlatform($typeID);
                        $type = $platform->getProjectType($typeID);
                        $projectSDK = fs::abs("./sdk/" . $type->getSdk());
                        if (fs::exists($projectSDK))
                        {
                            $zip = new ZipFile($projectSDK);
                            $zip->unpack($directory);
                            Json::toFile($this->dir->text . "/" . $this->name->text . "/" . $this->name->text .".nrd", [
                                "name" => $this->name->text,
                                "platform" => $typeID
                            ]);
                            $json = Json::fromFile("./projects.json");
                            $json[] = [
                                "name" => $this->name->text,
                                "platform" => $typeID,
                                "src"  => (string) $directory
                            ];
                            Json::toFile("./projects.json", $json);
                            new \utils\Project()->open($directory, $this->name->text);
                            app()->hideForm("MainForm");
                            $this->hide();
                        } else {
                            alert("Невозможно найти sdk для создания проекта.");
                            $this->hidePreloader();
                        } 
                    } else alert("Папка " . $this->dir->text . " не пустая.");
                } else alert("Выберете папку.");
            } else alert("Заполните имя проекта.");
        } else alert("Выберите тип проекта.");
    }

    /**
     * @event dirButton.action 
     */
    function doDirButtonAction(UXEvent $e = null)
    {    
        $json = Json::fromFile("./config.json");
        $this->projectChooser->execute();
        $this->dir->text = $this->projectChooser->file;
        $json['projects_path'] = $this->dir->text;
        Json::toFile("./config.json", $json);
    }

    /**
     * @event show 
     */
    function doShow(UXWindowEvent $e = null)
    {    
        if (fs::exists("./config.json"))
        {
            $json = Json::fromFile("./config.json");
            $this->dir->text = $json['projects_path'];
        } else {
            if (MainModule::getOS() == "windows")
            {
                $this->dir->text = "C:\\Users\\" . System::getProperty('user.name') . "\\NeardeProjects";
            } else {
                $this->dir->text = "/home/" . System::getProperty('user.name') . "/NeardeProjects";
            }
        }
    }
    
}
