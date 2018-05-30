<?php
namespace nd\forms;

use Error;
use php\compress\ZipFile;
use std, gui, framework, nd;


class UpdateForm extends AbstarctIDEForm
{
    private $downloadLink;
    private $downloadFile;
    
    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->hide();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->showPreloader();
        IDE::downloadDialog($this->downloadLink, fs::abs("./"));
        $pid = UXApplication::getPid();
        $execND = 'java -jar updater.jar "' . fs::abs($this->downloadFile) . '" "'.fs::abs('./').'" '.$pid.' "java -jar Nearde.jar"';
        $process = new NDProcess($execND, './')->start();
        exit;
    }

    public function update(int $newVer, array $info)
    {
        app()->hideSplash();
        $this->labelAlt->text = "Новая версия : " . $newVer;
        $this->textArea->text = $info['body'];
        $this->downloadLink = $info['assets'][0]['browser_download_url'];
        $this->downloadFile = fs::abs("./") . '/' .fs::name($this->downloadLink);
        $this->showAndWait();
    }

}
