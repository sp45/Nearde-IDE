<?php

use php\lib\fs;
use nd\ND;
use php\desktop\Runtime;
use php\gui\framework\Application;

$app = new Application();
try {
    $app->launch(function () {

        foreach (\php\io\File::of("./libs")->findFiles() as $lib)
        {
            if (fs::ext($lib) == "jar")
                Runtime::addJar(fs::abs($lib));
        }

        if (count($GLOBALS['argv']) == 1)
            $initType = "window";
        else {
            $initType = "console";
        }

        $GLOBALS['ND'] = new ND();
        $GLOBALS['ND']->init($initType);

    });
} catch (\php\lang\IllegalStateException $exception)
{
    exit(-1);
}
