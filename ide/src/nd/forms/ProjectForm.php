<?php
namespace nd\forms;

use facade\Json;
use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\UXFlatButton;
use php\gui\UXRichTextArea;
use php\gui\UXDndTabPane;
use nd\ui\NDTabPane;

use php\gui\UXSplitPane;
use php\gui\UXTab;

use nd\ui\NDTreeContextMenu;
use php\gui\UXMenuBar;
use php\gui\UXMenuItem;
use php\gui\UXMenu;
use nd\modules\IDE;
use php\gui\UXTabPane;
use php\lib\fs;
use php\gui\UXLabel;
use nd\utils\project;
use nd\ui\NDConsolePanel;

use nd\utils\NDProcess;

use std, gui, framework, nd;


class ProjectForm extends AbstarctIDEForm
{

    /**
     * @var project
     */
    private $project;
    
    /**
     * @var nd\ui\NDTree
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
     * @var NDTabPane
     */
    private $projectTabPane;
    
    /**
     * @var NDTabPane
     */
    private $consoleTabPane;
    
    /**
     * @var nd\projectTemplate
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
            NDTreeContextMenu::createItem("Новый проект", IDE::ico("newFile.png"), function () {
                $this->hide();
                IDE::getFormManger()->getForm('NewProject')->show();
            }),
            
            NDTreeContextMenu::createItem("Открыть проект", IDE::ico("folder.png"), function () {
                $this->hide();
                IDE::getFormManger()->getForm('OpenProject')->show();
            }),
            
            UXMenuItem::createSeparator(),
            
            NDTreeContextMenu::createItem("Закрыть проект", null, function () {
                $this->hide();
                IDE::getFormManger()->getForm('Main')->show();
            }),
            
            NDTreeContextMenu::createItem("Открыть папку проекта", IDE::ico("folder.png"), function () {
                open($this->project->getPath());
            }),
            
            UXMenuItem::createSeparator(),
            
            NDTreeContextMenu::createItem("Выход из IDE", null, function () {
                app()->shutdown();
            }),
        ]);
        $menuBar->menus->add($projectMenu);
        
        $runMenu = new UXMenu("Запуск");
        
        $runMenu->items->add(NDTreeContextMenu::createItem("Терминал", IDE::ico("terminal16.png"), function () {
            $this->showConsole("Терминал", IDE::ico("terminal16.png"));
        }));
        
        if ($this->template->getCommand("run"))
        {
            $runMenu->items->add(NDTreeContextMenu::createItem("Запуск проекта", IDE::ico("run16.png"), function () {
                $this->executeCommand($this->template->getCommand("run"), "Запустить проект", IDE::ico("run16.png"));
            }));
        } 
        
        if ($this->template->getCommand("build"))
        {
            $runMenu->items->add(NDTreeContextMenu::createItem("Собрка проекта", IDE::ico("compile16.png"), function () {
                $this->executeCommand($this->template->getCommand("build"), "Собрать проект", IDE::ico("compile16.png"));
            }));
        }
        
        if (fs::exists(fs::abs($this->project->getPath() . "/.nd/tasks.json")))
        {
            $json = Json::fromFile(fs::abs($this->project->getPath() . "/.nd/tasks.json"));
            foreach ($json as $task)
            {
                $runMenu->items->add(NDTreeContextMenu::createItem($task['name'], null, function () use ($task) {
                    $p = new NDProcess($task['shell'], $this->project->getPath());
                    $this->executeCommand($p->start(), $task['name']);
                }));
            }
        }
        
        $menuBar->menus->add($runMenu);
        
        foreach (IDE::get()->getProjectManger()->getGlobalMenus() as $menu)
            $menuBar->menus->add($menu);
        
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
        
        $this->projectTree = new \nd\ui\NDTree(function ($path) {
            // onAction
            if (fs::isFile($path))
            {
                $tab = new UXTab(fs::name($path));
                $tab->graphic  = IDE::get()->getFileFormat()->getIcon(fs::ext($path));
                $tab->content  = IDE::get()->getFileFormat()->getEditor($path); // get code editor
                $tab->userData = $path;
                $this->projectTabPane->getTabPane()->tabs->add($tab);
                $this->projectTabPane->getTabPane()->selectTab($tab);
            } else 
            {
                $this->projectTree->selectedItems[0]->expanded = !$this->projectTree->selectedItems[0]->expanded;
            }
        });
        $this->projectTree->refreshTree($this->project->getPath(), true);
        $this->projectSplit->items->add($this->projectTree);
        
        $this->projectTabPane = new NDTabPane;
        $this->projectSplit->items->add($this->projectTabPane);
        
        $this->consoleTabPane = new NDTabPane('Не открыт не один терминал');
        
        
        $this->mainSplit->items->add($this->projectSplit);
        $this->mainSplit->items->add($this->consoleTabPane);
        $this->mainSplit->dividerPositions = [
            1, 0
        ];
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
    
    private function executeCommand($obj, $tabTitle, $tabGraphic = null)
    {
        /** @var NDProcess $process */
        
        if (is_callable($obj)) {
            $process = $obj($this->project->getPath());
        } elseif ($obj instanceof \nd\utils\NDProcess)
            $process = $obj;
        
        if (!$process) return;
        
        $console = $this->showConsole($tabTitle, $tabGraphic);
        $console->print($process->getCommand() . "\n", '#6680e6');
        $console->runProcess($process);
    }
    
    /**
     * @return nd\ui\NDConsolePanel
     */
    public function showConsole($text, $graphic = null) : NDConsolePanel
    {
        $this->mainSplit->dividerPositions = [
            .7, .3
        ];
        $console = new NDConsolePanel($this->project->getPath());
        $tab = new UXTab($text);
        $tab->content = $console;
        $tab->graphic = $graphic;
        $this->consoleTabPane->getTabPane()->tabs->add($tab);
        $this->consoleTabPane->getTabPane()->selectTab($tab);

        return $console;
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
