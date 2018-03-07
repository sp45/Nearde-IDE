<?php
namespace ui;

use php\gui\layout\UXHBox;
use php\gui\designer\UXCodeAreaScrollPane;
use php\gui\UXRichTextArea;

class BuildLog 
{
    /**
     * @var UXRichTextArea
     */
    private $textAria;
    /**
     * @var UXCodeAreaScrollPane
     */
    private $textAriaScrol;
    
    private $split;
    
    private $line = 0;
    
    public function __construct($split = null) 
    {
        $this->textAria = new UXRichTextArea;
        $this->textAria->classes->add("console");
        $this->textAriaScrol = new UXCodeAreaScrollPane($this->textAria);
        $this->textAriaScrol->anchors = ["top" => 1, "bottom" => 1, "left" => 1, "right" => 1];
        
        if ($split)
            $this->split = $split;
    }
    
    public function print($text, $color = "#333")
    {
        $this->line++;
        $this->textAria->appendText($text . "\n", "-fx-fill : " . $color);
        $this->textAriaScrol->scrollY = $this->line * 25;
    }
    
    public function makeUI(){
        $hbox = new UXHBox();
        $hbox->add($this->textAriaScrol);
        return $this->textAriaScrol;
    }
    
    public function hide()
    {
        if ($this->split)
            $this->split->items->remove($this->makeUI());
    }
}