<?php

namespace nd\ui;

use php\gui\layout\UXVBox;
use php\gui\layout\UXPane;
use php\gui\UXDndTabPane;
use php\gui\UXLabel;
use php\gui\UXTabPane;

class NDTabPane extends UXPane
{
    /**
     * @var UXTabPane
     */
    private $tabPane;

    /**
     * @var UXVBox
     */
    private $box;

    /**
     * NDTabPane constructor.
     * @param string $text
     * @throws \php\lang\IllegalArgumentException
     */
    public function __construct(string $text = 'Ничего нет')
    {
        parent::__construct();

        $this->tabPane = new UXTabPane;
        $this->add($this->tabPane);

        $this->box = new UXVBox([
            new NDSvgImage('M11.99,2C6.47,2,2,6.48,2,12s4.47,10,9.99,10C17.52,22,22,17.52,22,12S17.52,2,11.99,2z M12,20c-4.42,0-8-3.58-8-8
		s3.58-8,8-8s8,3.58,8,8S16.42,20,12,20z M15.5,11c0.83,0,1.5-0.67,1.5-1.5S16.33,8,15.5,8S14,8.67,14,9.5S14.67,11,15.5,11z
		 M8.5,11c0.83,0,1.5-0.67,1.5-1.5S9.33,8,8.5,8S7,8.67,7,9.5S7.67,11,8.5,11z M12,13.5c-2.03,0-3.8,1.11-4.75,2.75
		C7.06,16.58,7.31,17,7.69,17h8.62c0.38,0,0.63-0.42,0.44-0.75C15.8,14.61,14.03,13.5,12,13.5z', [50, 50]),
            new UXLabel($text)
        ]);

        $size = function ($old, $new) {
            if ($old == $new) return;

            $this->box->size     = $this->size;
            $this->tabPane->size = $this->size;
        };

        $this->observer('width')->addListener($size);
        $this->observer('height')->addListener($size);

        $this->box->alignment = 'CENTER';
        $this->box->spacing = 10;
        $this->add($this->box);
        $this->box->toBack();
    }

    /**
     * @return UXDndTabPane
     */
    public function getTabPane()
    {
        return $this->tabPane;
    }


}