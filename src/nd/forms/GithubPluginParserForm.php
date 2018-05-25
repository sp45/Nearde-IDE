<?php
namespace nd\forms;

use std, gui, framework, nd;


class GithubPluginParserForm extends AbstarctIDEForm
{
    
    private $repos = [
        'Venity' => '/orgs/VenityStudio/repos',
        'MWStudio' => '/orgs/MWStudio/repos',
    ];

    /**
     * @event buttonAlt.action 
     */
    function doButtonAltAction(UXEvent $e = null)
    {    
        $this->parseRepos();
    }
    
    private $file = "NaN";
    
    public function parse()
    {
        IDE::upgradeListView($this->listView);
        $this->showAndWait();
        
        return $this->file;
    }
    
    private function parseRepos()
    {
        $this->image->visible = !$this->labelAlt->visible = $this->listView->visible = true;
        $this->listView->items->clear();
        
        $repos = IDE::githubApiQueryGET($this->repos[$this->combobox->value]);
        if (!$repos['message'] && $repos)
            foreach ($repos as $repo)
            {
                $avatar = IDE::image($repo['owner']['avatar_url']);
                $avatar->size = [32, 32];
                IDE::setBorderRadius($avatar, 50);
                $release = IDE::githubApiQueryGET(trim(str_replace("{/id}", " ", substr($repo['releases_url'], 22))));
                if (fs::ext($release[0]['assets'][0]['name']) == "ndplugin")
                {
                    $this->listView->items->add([
                        $repo['name'],
                        null,
                        $avatar,
                        function () {
                            $this->download();
                        },
                        $release[0]['assets'][0]['name'],
                        $release[0]['assets'][0]['browser_download_url'],
                        $release
                    ]);
                }
            }
    }
    
    private function download()
    {
        if (!$this->listView->selectedItem) {
            IDE::dialog("Выберете репозиторий.");
            return;
        }
        
        if (IDE::downloadDialog($this->listView->selectedItem[5], "./plugins/temp/"))
        {
            $this->file = fs::abs("./plugins/temp/" . $this->listView->selectedItem[4]);
            $this->hide();
        }
    }

    /**
     * @event button3.action 
     */
    function doButton3Action(UXEvent $e = null)
    {
        $this->file = "NaN";
        $this->hide();
    }

    /**
     * @event button.action 
     */
    function doButtonAction(UXEvent $e = null)
    {    
        $this->download();
    }
}
