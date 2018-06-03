<?php
namespace nd\modules;

use facade\Json;
use php\framework\Logger;
use php\gui\event\UXMouseEvent;
use php\gui\framework\AbstractModule;
use php\gui\layout\UXHBox;
use php\gui\layout\UXVBox;
use php\gui\UXImage;
use php\gui\UXImageView;
use php\gui\UXLabel;
use php\gui\UXListCell;
use php\gui\UXListView;
use script\storage\IniStorage;
use nd\utils\NDProcess;

use std, gui, framework, nd;

use nd\utils\FileUtils;
use php\lib\fs;


class IDE extends AbstractModule
{

    /**
     * return UXImageView
     */
    public static function ico($name)
    {
        return self::image("res://.data/img/" . $name);
    }
    
    /**
     * return UXImageView
     */
    public static function image($path)
    {
        if ($path instanceof UXImageView)
            return $path;
        if ($path instanceof UXImage)
            $image = new UXImageView($path);
        if (is_string($path))
            $image = new UXImageView(new UXImage($path));
        
        $image->smooth = true;
        
        return $image;
    }
    
    /**
     * @return ND
     */
    public static function get()
    {
        return $GLOBALS['ND'];
    }
    
    public static function upgradeListView(UXListView $listView)
    {
        $listView->setCellFactory(function(UXListCell $cell, $item) {
            if ($item) {              
                $titleName = new UXLabel($item[0]);
                $titleName->style = '-fx-font-weight: bold;';
             
                if ($item[1])
                {
                    $titleDescription = new UXLabel($item[1]);
                    $titleDescription->opacity = 0.7;
                }
                
                $cell->observer("width")->addListener(function ($old, $new) use ($titleDescription, $item) {
                    if ($old == $new) return;
                    if ($item[1])
                        $titleDescription->maxWidth = $new - 63;
                });
                
                if ($titleDescription)
                {
                    $title  = new UXVBox([$titleName, $titleDescription]);
                    $title->spacing = 0;
                }
                
                if ($titleDescription)
                    $line = new UXHBox([$item[2], $title]);
                else $line = new UXHBox([$item[2], $titleName]);
                $line->spacing = 7;
                $line->padding = 5;
                $line->on('click', function (UXMouseEvent $e) use ($item) {
                    if ($e->clickCount < 2) return;
                    $callback = $item[3];
                    if (!is_callable($callback)) return;
                    $callback();
                });
                $cell->text = null;
                $cell->graphic = $line;
            }
        });
    }
    
    /**
     * @return formManger
     */
    public static function getFormManger()
    {
        return self::get()->getFormManger();
    }
    
    /**
     * @return bool 
     */
    public static function isWin()
    {
        return Str::posIgnoreCase(System::getProperty('os.name'), 'WIN') > -1;
    }
    
    /**
     * @return string 
     */
    public static function treeDialog(string $text, string $path)
    {
        app()->hideSplash();
        return IDE::getFormManger()->getForm("TreeDialog")->open($text, $path);
    }
    
    /**
     * @return bool 
     */
    public static function inputDialog(string $text)
    {
        app()->hideSplash();
        return IDE::getFormManger()->getForm("InputDialog")->open($text);
    }
    
    /**
     * @return bool 
     */
    public static function confirmDialog(string $text)
    {
        app()->hideSplash();
        return IDE::getFormManger()->getForm("ConfirmDialog")->open($text);
    }
    
    public static function dialog(string $text)
    {
        app()->hideSplash();
        IDE::getFormManger()->getForm("Dialog")->open($text);
    }
    
    /**
     * @return bool 
     */
    public static function unpackDialog(string $zip, string $dir)
    {
        app()->hideSplash();
        return IDE::getFormManger()->getForm("ProgressDialog")->unpack($zip, $dir, false);
    }
    
    /**
     * @return bool 
     */
    public static function downloadDialog(string $url, string $to)
    {
        app()->hideSplash();
        return IDE::getFormManger()->getForm("ProgressDialog")->download($url, $to);
    }
    
    /**
     * @return NDProcess
     */
    public static function createProcess($shell, $path)
    {
        return new NDProcess($shell, $path);
    }


    public static function restart()
    {
        $path = substr($GLOBALS['argv'][0], '1');
        if (str::endsWith($path, '/lib/jphp-core.jar'))
        {
            Logger::error(IDE::get()->getName() . " Runing in another IDE.");
            exit(-1);
        }
        
        $p = new NDProcess("java -jar " . fs::name($path), fs::parent($path));
        $p->start();
        Logger::info(IDE::get()->getName() . " Restarting ...");
        exit(0);
    }
    
    /**
     * @return array 
     */
    public static function githubApiQueryGET(string $query, $data = null)
    {
        $res = app()->module("IDE")->githubClient->get($query, $data);
        Logger::info("Server code : " . $res->statusCode());
        return $res->body();
    }

    public static function cleanPluginsTemp()
    {
        FileUtils::delete("./plugins/temp/");
        fs::makeDir("./plugins/temp/");
    }

    /**
     * @param $element
     * @param $radius
     */
    public static function setBorderRadius($element, $radius)
    {
        // функция от TsSaltan
        $rect = new UXRectangle;
        $rect->width = $element->width;
        $rect->height = $element->height;
        $rect->arcWidth = $radius * 2;
        $rect->arcHeight = $radius * 2;
        $element->clip = $rect;
        $circledImage = $element->snapshot();
        $element->clip = NULL;
        $rect->free();
        $element->image = $circledImage;
    }

    /**
     * @param $file
     * @param string $mode
     * @return bool|void
     */
    public static function installPlugin($file, $mode = "windows")
    {
        $tempDir = fs::abs("./plugins/temp/" . substr(md5(Time::now()), 5));
        fs::makeDir($tempDir);
        if (!IDE::unpackDialog((string) $file, $tempDir)) return;
        
        if (!fs::exists($tempDir . "/.ndp"))
        {
            if ($mode == "windows")
                IDE::dialog("Архив не является плагином для " . IDE::get()->getName());
            else
                exit(-1);
            IDE::cleanPluginTemp();
            return;
        }
        
        $ini = new IniStorage(fs::abs($tempDir . "/.ndp"));
        
        $pluginData = $ini->toArray()[''];
        $pluginDir = fs::abs("./plugins/" . $pluginData['dir']);
        if (fs::exists($pluginDir)) {
            $oldIni = new IniStorage($pluginDir . '/.ndp');
            $oldVersion = $oldIni->toArray()['']['version'];
            if ($oldVersion != $pluginData['version'])
            {
                
                if ($mode != "windows") goto copyPlugin;
                
                if (IDE::confirmDialog("Заменить другой версиеяй (" . $oldVersion . " => ". $pluginData['version'] . ") ?"))
                {
                    FileUtils::delete($pluginDir);
                    goto copyPlugin;
                } else {
                    IDE::cleanPluginsTemp();
                    return;
                }
            } else {
                if ($mode == "windows") {
                    IDE::dialog("Данный плагин уже установлен");
                    IDE::cleanPluginsTemp();
                } else {
                    IDE::cleanPluginsTemp();
                    exit(-2);
                }
                
                return;
            }
        }
        
        copyPlugin:
        
        fs::makeDir($pluginDir);
        FileUtils::copy($tempDir, $pluginDir);
        IDE::cleanPluginsTemp();
        $json = Json::fromFile("./plugins/plugins.json");
        $json[strtoupper($pluginData['dir'])] = $pluginData;
        Json::toFile("./plugins/plugins.json", $json);
        
        return true;
    }
    
}
