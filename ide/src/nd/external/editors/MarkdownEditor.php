<?php
namespace nd\external\editors;


use markdown\Markdown;
use nd\modules\IDE;
use php\gui\UXSplitPane;
use php\gui\UXWebView;
use php\io\Stream;

class MarkdownEditor extends UXSplitPane
{
    /**
     * @var UXWebView
     */
    private $browser;

    public function open($path)
    {
        $editor = IDE::get()->getFileFormat()->getCodeEditor($path);
        $this->browser = new UXWebView;

        $render = function ($file) {
            $md = new Markdown();

            $content  = "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
            $content .= "<style>".Stream::getContents("res://nd/external/editors/markdown.css")."</style>";
            $content .= "<article class=\"markdown-body\">";
            $content .= $md->render(Stream::getContents($file));
            $content .= "</article>";

            $this->browser->engine->loadContent($content);
        };
        $editor->onSave = $render;
        $render($path);

        $this->items->addAll([
            $editor, $this->browser
        ]);

        $this->dividerPositions = [ .5, .5 ];
    }
}