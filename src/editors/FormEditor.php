<?php
namespace editors;

use php\gui\UXButton;
use php\gui\designer\UXDesignPane;
use php\gui\layout\UXAnchorPane;
use php\gui\UXLoader;
use php\gui\layout\UXFlowPane;
use php\io\Stream;
use php\io\File;
use php\xml\XmlProcessor;
use php\gui\layout\UXPanel;
use php\gui\designer\UXDesigner;

class FormEditor 
{
    /**
     * @var UXDesigner
     */
    private $designer;
    
    /**
     * @var UXDesignPane
     */
    private $DesignPane;
    
    /**
     * @var UXPanel
     */
    private $panel;
    
    public function __construct($file)
    {
        $this->panel = new UXPanel;
        $this->panel->borderWidth = 0;
        
        $this->DesignPane = new UXDesignPane();
        $this->designer = new UXDesigner($this->panel);
        
        $this->panel->add($this->DesignPane);
        $this->parse($file);
    }
    
    public function save()
    {
        
    }
    
    public function parse($file)
    {
        $Loader = new UXLoader();
        $node = $Loader->load(Stream::of($file));
        
        $this->DesignPane->add($node);
    }
    
    public function makeUI()
    {
        return $this->panel;
    }
}