<?php
namespace nd\forms;

use facade\Json;
use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\UXRichTextArea;
use std, gui, framework, nd;


class ProjectForm extends AbstractForm
{
    /**
     * @var project
     */
    private $project;
    
    /**
     * @var NDTree
     */
    private $projectTree;
    
    /**
     * @var UXSplitPane
     */
    private $projectSplit;
    
    /**
     * @var UXSplitPane
     */
    private $mainSplit;
    
    /**
     * @var UXTabPane
     */
    private $projectTabPane;
    
    /**
     * @var ProjectTemplate
     */
    private $template;
    
    public function openProject(project $project)
    {
        $this->project = $project;
        $this->template = $this->project->getTemplate();
        $this->initUI();
        $this->show();
    }
    
    private function initUI()
    {
        $this->title = $this->project->getName() . " - [" . fs::abs($this->project->getPath()) . "] - " . IDE::get()->getName() . " " . IDE::get()->getVersion();
        
        $menuBar = new UXMenuBar;
        $menuBar->anchors = [
            "left" => 1, "right" => 1
        ];
        
        $projectMenu = new UXMenu("Проект");
        $projectMenu->items->addAll([
            NDTreeContextMenu::createItem("Новый проект.", IDE::ico("newFile.png"), function () {
                $this->hide();
                IDE::getFormManger()->getForm('NewProject')->show();
            }),
            
            NDTreeContextMenu::createItem("Открыть проект.", IDE::ico("folder.png"), function () {
                $this->hide();
                IDE::getFormManger()->getForm('OpenProject')->show();
            }),
            
            UXMenuItem::createSeparator(),
            
            NDTreeContextMenu::createItem("Закрыть проект.", IDE::ico("close16.png"), function () {
                $this->hide();
                IDE::getFormManger()->getForm('Main')->show();
            }),
            
            NDTreeContextMenu::createItem("Открыть папку проекта.", IDE::ico("folder.png"), function () {
                open($this->project->getPath());
            }),
            
            UXMenuItem::createSeparator(),
            
            NDTreeContextMenu::createItem("Выход из IDE.", IDE::ico("close16.png"), function () {
                app()->shutdown();
            }),
        ]);
        $menuBar->menus->add($projectMenu);
        
        $runMenu = new UXMenu("Запуск");
        
        if ($this->template->getCommand("run"))
        {
            $runMenu->items->add(NDTreeContextMenu::createItem("Запустить проект.", IDE::ico("run16.png"), function () {
                $this->executeCommand($this->template->getCommand("run"));
            }));
        } 
        
        if ($this->template->getCommand("build"))
        {
            $runMenu->items->add(NDTreeContextMenu::createItem("Собрать проект.", IDE::ico("build16.png"), function () {
                $this->executeCommand($this->template->getCommand("build"));
            }));
        }
        
        if (fs::exists(fs::abs($this->project->getPath() . "/.nd/tasks.json")))
        {
            $json = Json::fromFile(fs::abs($this->project->getPath() . "/.nd/tasks.json"));
            foreach ($json as $task)
            {
                $runMenu->items->add(NDTreeContextMenu::createItem($task['name'], IDE::ico("bat16.png"), function () use ($task) {
                    
                    if (IDE::isWin()) $prefix = "cmd.exe /c";
                    else $prefix = "bash";
                    
                    $this->executeCommand(new Process(explode(" ", $prefix . " " . $task['shell']), $this->project->getPath())->start());
                }));
            }
        }
        
        $menuBar->menus->add($runMenu);
        $this->add($menuBar);
        
        $this->mainSplit = new UXSplitPane;
        $this->mainSplit->orientation = "VERTICAL";
        $this->mainSplit->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        
        $this->projectSplit = new UXSplitPane;
        $this->projectSplit->dividerPositions = [
            .4, .6
        ];
        
        $this->projectTree = new NDTree(function ($path) {
            // onAction
            if (fs::isFile($path))
            {
                $tab = new UXTab(fs::name($path));
                $tab->graphic  = IDE::get()->getFileFormat()->getIcon(fs::ext($path));
                $tab->content  = new NDCode(File::of($path), IDE::get()->getFileFormat()->getLang4ext(fs::ext($path)));
                $tab->userData = $path;
                $this->projectTabPane->tabs->add($tab);
                $this->projectTabPane->selectTab($tab);
            } else 
            {
                $this->projectTree->selectedItems[0]->expanded = true;
            }
        });
        $this->projectTree->refreshTree($this->project->getPath(), true);
        $this->projectSplit->items->add($this->projectTree);
        
        $this->projectTabPane = new UXTabPane;
        $this->projectSplit->items->add($this->projectTabPane);
        
        $this->mainSplit->items->add($this->projectSplit);
        $this->panel->add($this->mainSplit);
        
        foreach (IDE::get()->getProjectManger()->getGlobalGunters() as $globalGunter)
        {
            $this->makeGunter($globalGunter);
        }
        
        foreach ($this->template->getGunters() as $gunter)
        {
            $this->makeGunter($gunter);
        }
        
        $templateImg = IDE::image($this->template->getIcon());
        $templateImg->size = [ 16, 16 ];
        $this->hboxAlt->add($templateImg);
        $this->hboxAlt->add(new UXLabel($this->template->getName())); 
    }
    
    private function executeCommand($obj)
    {
        if (is_callable($obj)) {
            $process = $obj($this->project->getPath());
        } elseif ($obj instanceof Process)
            $process = $obj;
        
        if (!$process) return;
        
        $this->mainSplit->items->removeByIndex(1);
        $this->mainSplit->dividerPositions = [
            .7, .3
        ];
        
        $textArea = new UXRichTextArea;
        $textArea->padding = 8;
        new Thread(function() use ($textArea, $process) {
            $process->getInput()->eachLine(function($line) use ($textArea) {
                uiLater(function() use ($line, $textArea) {
                    $textArea->appendText($line . "\n", '-fx-fill: gray;');
                    $textArea->selectLine();
                });
            });

            $process->getError()->eachLine(function($line) use ($textArea) {
                uiLater(function() use ($line, $textArea) {
                    $textArea->appendText($line . "\n", '-fx-fill: red;');
                    $textArea->selectLine();
                });
            });
            
            $exitValue = $process->getExitValue();
            
            uiLater(function () use ($exitValue, $textArea) {
                $textArea->appendText("> exit code: " . $exitValue . "\n", '-fx-fill: #BBBBFF;');
                $textArea->selectLine();
            });
        })->start();
        
        $this->mainSplit->items->add(new UXCodeAreaScrollPane($textArea));
    }
    
    private function makeGunter(array $data)
    {
        $gunterNode = new UXFlatButton($data['text']);
        $gunterNode->graphic = $data['image'];
        $gunterNode->tooltipText = $data['name'];
        $gunterNode->on('click', function () use ($data) {
            $data['callable']($this->project->getPath());
        });
        $this->hbox->add($gunterNode);
    }
}
