<?php
namespace nd\forms;

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
     * @var UXTabPane
     */
    private $projectTabPane;
    
    public function openProject(project $project)
    {
        $this->project = $project;
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
        $menuBar->menus->add($projectMenu);
        $this->add($menuBar);
        
        $this->projectSplit = new UXSplitPane;
        $this->projectSplit->anchors = [
            "top" => 1, "bottom" => 1, "left" => 1, "right" => 1
        ];
        $this->projectSplit->dividerPositions = [
            .4, .6
        ];
        
        $this->projectTree = new NDTree(function ($path) {
            // onAction
            $tab = new UXTab(fs::name($path));
            $tab->graphic = IDE::get()->getFileFormat()->getIcon(fs::ext($path));
            $tab->content = new NDCode(Stream::getContents($path), IDE::get()->getFileFormat()->getLang4ext(fs::ext($path)));
            $this->projectTabPane->tabs->add($tab);
            $this->projectTabPane->selectTab($tab);
        });
        $this->projectTree->refreshTree($this->project->getPath());
        $this->projectSplit->items->add($this->projectTree);
        
        $this->projectTabPane = new UXTabPane;
        $this->projectSplit->items->add($this->projectTabPane);
        
        $this->panel->add($this->projectSplit);
    }
}
