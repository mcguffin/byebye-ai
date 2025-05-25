<?php

namespace ByebyeAI;
use ByebyeAI\Core;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'include/autoload.php';

Core\Core::instance( __DIR__ . DIRECTORY_SEPARATOR . 'index.php' );
Core\Plugin::uninstall();
