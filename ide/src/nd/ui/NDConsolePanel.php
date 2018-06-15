<?php

namespace nd\ui;


use nd\modules\IDE;
use nd\utils\NDProcess;
use php\gui\layout\UXHBox;
use php\gui\layout\UXPanel;
use php\gui\layout\UXVBox;
use php\gui\UXButton;
use php\gui\UXNode;

class NDConsolePanel extends UXPanel
{
    /**
     * @var NDConsole
     */
    private $console;

    /**
     * @var UXHbox
     */
    private $box;

    /**
     * @var UXVBox
     */
    private $nodeBox;

    /**
     * @var UXButton
     */
    private $killButton;

    /**
     * NDConsolePanel constructor.
     * @param string $dir
     * @param NDProcess|null $process
     * @throws \php\lang\IllegalArgumentException
     */
    public function __construct(string $dir, NDProcess $process = null)
    {
        parent::__construct();
        $this->borderWidth = 0;

        $this->console = new NDConsole($dir);
        $this->nodeBox = new UXVBox;
        $this->nodeBox->maxWidth = $this->nodeBox->minWidth = $this->nodeBox->width = 37;
        $this->nodeBox->paddingTop = 10;

        $this->box = new UXHBox([
            $this->nodeBox, $this->console
        ]);

        $this->box->anchors = [
            'top' => true, 'bottom' => 1, 'left' => true, 'right' => true
        ];

        $resize = function ($old, $new) {
            if ($old == $new) return;

            $this->console->size = [$this->size[0] - 37, $this->size[1]];
        };

        $this->observer('width')->addListener($resize);
        $this->observer('height')->addListener($resize);

        $this->add($this->box, true);

        $this->killButton = new UXButton("", IDE::ico('close16.png'));
        $this->killButton->classes->add('stop-btn');
        $this->killButton->on('click', function () {
            $process = $this->console->getProcess();
            $handle = $process->getProcessHandle();

            foreach ($handle->children() as $child)
                $child->destroy();

            if ($handle->destroy())
                $this->killButton->enabled = false;
        });
        $this->killButton->enabled = false;
        $this->add($this->killButton);

        $this->console->onProcessExecute = function () {
            $this->killButton->enabled = true;
        };

        if ($process)
            $this->runProcess($process);
        else $this->console->printUserAndDir();
    }

    /**
     * @param NDProcess $process
     */
    public function runProcess(NDProcess $process)
    {
        if (!$process->isStarted())
            $process->start();

        $this->killButton->enabled = true;

        $this->console->runProcess($process, function () {
            $this->killButton->enabled = false;
            $this->console->printUserAndDir();
        });
    }

    /**
     * @param UXNode $node
     * @param bool $toLayout
     */
    public function add(UXNode $node, bool $toLayout = false)
    {
        if ($toLayout)
        {
            parent::add($node);
            return;
        }

        $this->nodeBox->add($node);
    }

    /**
     * @param string $text
     * @param string $color
     */
    public function print(string $text, string $color = 'gray')
    {
        $this->console->addConsole($text, $color);
    }
}