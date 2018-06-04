<?php
use packager\Event;
use php\lib\fs;
use packager\cli\Console;

function task_publish(Event $e)
{
    Tasks::runExternal('./nd-framework', 'publish', [], ...$e->flags());
    Tasks::runExternal('./designer', 'publish', [], ...$e->flags());
    Tasks::runExternal('./gui-tabs-ext', 'publish', [], ...$e->flags());
}

/**
 * @jppm-task prepare-ide
 * @jppm-description Prepare Nearde IDE
 * @param Event $e
 */
function task_prepareIde(Event $e)
{
    Tasks::run('publish', [], 'yes');
    Tasks::runExternal("./ide", "update");
}

/**
 * @jppm-task start-ide
 * @jppm-description Start Nearde IDE
 */
function task_startIde(Event $e)
{
    Tasks::runExternal('./ide', 'start', $e->args(), ...$e->flags());
}

/**
 * @jppm-task build-ide
 * @jppm-description Build Nearde IDE
 */
function task_buildIde(Event $e)
{
    Tasks::runExternal('./ide', 'build', $e->args(), ...$e->flags());

    Console::log('-> Copy files to build ...');

    Tasks::copy('./ide/libs', './ide/build/libs');
    Tasks::copy('./ide/plugins', './ide/build/plugins');

    fs::rename('./ide/build/Nearde-last.jar', 'Nearde.jar');

    Console::log('--> done!');
}