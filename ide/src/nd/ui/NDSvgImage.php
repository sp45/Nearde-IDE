<?php

namespace nd\ui;

use php\gui\paint\UXColor;
use php\gui\text\UXFont;
use php\gui\UXFlatButton;

class NDSvgImage extends UXFlatButton
{
    public function __construct($svgPath, array $size = [24, 24], string $color = 'gray')
    {
        parent::__construct();
        $this->font = new UXFont(0, 'system');
        $this->size = $size;
        $this->color = $this->backgroundColor = $this->hoverColor = UXColor::of($color);
        $this->style = '-fx-shape: "'.$svgPath.'"';
    }
}