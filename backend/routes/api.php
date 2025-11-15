<?php

$routePath = __DIR__ . '/groups';

foreach (scandir($routePath) as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        require $routePath . '/' . $file;
    }
}
