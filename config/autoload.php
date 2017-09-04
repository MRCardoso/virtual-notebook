<?php
require ROOT.DS.'Library'.DS.'Marlon'.DS.'AutoLoad.php';

$autoLoad = new AutoLoad();
$autoLoad->setPath(ROOT);
$autoLoad->setExt('php');

spl_autoload_register([$autoLoad, 'loadCore']);