<?php
use packager\Event;
use php\lib\fs;
use packager\cli\Console;

function task_publish(Event $e)
{
    Tasks::runExternal('./nd-framework', 'publish', [], ...$e->flags());
    Tasks::runExternal('./designer', 'publish', [], ...$e->flags());
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
    Tasks::runExternal("./sandbox", "update");
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
 * @jppm-task start-sandbox
 * @jppm-description Start SandBox
 */
function task_startSandBox(Event $e)
{
    Tasks::runExternal('./sandbox', 'start', $e->args(), ...$e->flags());
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

/**
 * @jppm-task start
 */
function task_run(Event $e)
{
    Console::log('Using start-ide task ...');
    Tasks::runExternal('./', 'start-ide', $e->args(), ...$e->flags());
}

/**
 * @jppm-task build
 */
function task_build(Event $e)
{
    Console::log('Using build-ide task ...');
    Tasks::runExternal('./', 'build-ide', $e->args(), ...$e->flags());
}