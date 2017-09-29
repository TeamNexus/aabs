<?php

function command_exists($cmd) {
    return !empty(shell_exec("which \"{$cmd}\" 2>/dev/null"));
}
