<?php
namespace app\forms;

use documentation\documentationView;
use editors\CodeEditor;
use php\gui\UXMenuBar;
use php\gui\UXTreeView;
use php\gui\designer\UXDirectoryTreeValue;
use php\gui\UXTreeItem;
use editors\FormEditor;
use php\lib\fs;
use php\gui\designer\UXDirectoryTreeView;
use php\gui\designer\UXDesignPane;
use php\gui\designer\UXSyntaxTextArea;
use php\gui\designer\UXCssCodeArea;
use php\gui\UXRichTextArea;
use Exception;
use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\designer\UXTextCodeArea;
use php\gui\designer\UXPhpCodeArea;
use facade\Json;
use std, gui, framework, app;
use php\gui\event\UXEvent; 


class project extends AbstractForm
{

    private $type;
    private $name;
    private $path;
    private $project;
    private $process;
    private $platform;
    /**
     * @var Process
     */
    private $processP;
    /**
     * @var Thread
     */
    private $thread;
    
    /**
     * @var UXTreeView
     */
    private $projectTree;
    
    /**
     * @var UXTabPane
     */
    private $projectTab;
    
    /**
     * @var UXSplitPane
     */
    private $split;
    
    /**
     * @var UXSplitPane
     */
    private $MainSplit;
    
    private $buildLog;
    /**
     * @var UXMenuBar
     */
    private $mainMenu;
    
    /**
     * @event showing 
     */
    function doShowing(UXWindowEvent $e = null)
    {    
        $this->title = $this->name . " - [" . $this->path . "] - Nearde IDE";
        
        $this->projectTree = new UXTreeView;
        $this->projectTree->rootVisible = false;
        $this->projectTree->on('click', function (UXMouseEvent $e) {
        
            if ($e->clickCount < 2) return;
            
            $path = $this->projectTree->focusedItem->value->id;
            
            foreach ($this->projectTab->tabs as $tab)
            {
                if ($tab->id == $path){
                    $this->projectTab->selectTab($tab);
                    return;
                }    
            }
            
            if (fs::isDir($path)) return;
            
            $editor = new CodeEditor(new File($path));
            
            if ($editor->makeUI() == null) 
            {
                if (uiConfirm("Nearde не может открыть файл этого типа. Открыть в системном редакторе ?"))
                    open($path);
                
                return;
            }
            
            $tab = new UXTab(fs::name($path));
            $tab->id = $path;
            $tab->content = $editor->makeUI();
            $tab->userData = $editor;
            $tab->graphic = new UXImageView($this->projectTree->focusedItem->value->graphic->image);
            
            $this->projectTab->tabs->add($tab);
            $this->projectTab->selectTab($tab);
        });
        
        $this->projectTab = new UXTabPane;
        
        $this->MainSplit = new UXSplitPane;
        $this->MainSplit->anchors = ["top" => 1, "bottom" => 1, "left" => 1, "right"=> 1];
        $this->MainSplit->orientation = "VERTICAL";
        
        $split = new UXSplitPane();
        $split->dividerPositions = [0.2,1];
        
        $split->anchors = ["top" => 1, "bottom" => 1, "left" => 1, "right"=> 1];
        $split->items->addAll([
            $this->projectTree,
            $this->projectTab
        ]);
        
        $this->split = $split;
        
        $this->MainSplit->items->add($this->split);
        
        $this->panel->add($this->MainSplit);
        
        $this->refreshTree(new File($this->path));
    }


    /**
     * @event keyDown-Ctrl+S 
     * @event button3.action 
     */
    function SaveProject()
    {    
        foreach ($this->projectTab->tabs as $tab)
        {
            $tab->userData->save();
        }
        
        $this->toast("Сохранено");
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {
        $this->MainSplit->items->removeByIndex(1);
        $log = new BuildLog($this->MainSplit);
        $this->buildLog = $log;
        $this->MainSplit->items->add($log->makeUI());
        $this->platform->getRunType()->onRun($log, $this->project, function () {
            $this->button->enabled = ! $this->buttonAlt->enabled = 0;
        });
        $this->button->enabled = ! $this->buttonAlt->enabled = 1;
    }

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->platform->getRunType()->onStop($this->project, function () {
            $this->buildLog->hide();
            $this->button->enabled = ! $this->buttonAlt->enabled = 0;
        });
    }

    /**
     * @event button4.action 
     */
    function doButton4Action(UXEvent $e = null)
    {    
        app()->getForm("newProject")->show();
        $this->hide();
    }

    /**
     * @event button5.action 
     */
    function doButton5Action(UXEvent $e = null)
    {    
        $this->project->build();
    }

    function OpenProject(\utils\Project $project)
    {
        $this->project = $project;
        $this->name = $this->project->getName();
        $this->path = $this->project->getDir();
        $this->type = $this->project->getType();
        $this->platform = $this->project->getPlatform();
        $this->show();
    }
    
    function refreshTree(File $file)
    {
        $this->projectTree->root = new UXTreeItem(new ItemValue($this->path, $this->name));
        $this->refreshTreeItem($file, $this->projectTree->root);
    }
    
    protected function refreshTreeItem(File $file, UXTreeItem $item) 
    {
        $files = $file->findFiles();
        
        foreach ($files as $file) {
            $add = true;
            $subItem = new UXTreeItem(new ItemValue($file->getPath(), $file->getName()));
            
            if ($file->isDirectory()) {
                if (!$GLOBALS['hide_directory'][strtolower(fs::name($file))]){
                    $subItem->graphic = new UXImageView(new UXImage("res://.data/img/folder16.png"));
                    $this->refreshTreeItem($file, $subItem);
                } else {
                    $add = false;
                }
            } else {
                switch (fs::ext($file))
                {
                    case ("gradle"):
                        $subItem->graphic = new UXImageView(new UXImage("res://.data/img/gradleFile16.png"));
                    break;
                    case ("php"):
                        $subItem->graphic = new UXImageView(new UXImage("res://.data/img/phpFile16.png"));
                    break;
                    case ("fxcss"):
                    case ("css"):
                        $subItem->graphic = new UXImageView(new UXImage("res://.data/img/cssFile16.png"));
                    break;
                    case ("nrd"):
                    case ("dnproject"):
                        $add = false;
                    break;
                    default: 
                        $subItem->graphic = new UXImageView(new UXImage("res://.data/img/file16.gif"));
                }
            }
            
            if ($add == true)
            {
                $subItem->value->graphic = $subItem->graphic;
                $item->children->add($subItem);
            } 
        }
    }
}
