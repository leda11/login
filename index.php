<?php
//
// PHASE: BOOTSTRAP
//
define('HANDY_INSTALL_PATH', dirname(__FILE__));
define('HANDY_SITE_PATH', HANDY_INSTALL_PATH . '/site');

require(HANDY_INSTALL_PATH.'/src/bootstrap.php');

$ha = CHandy::Instance();

//
// PHASE: FRONTCONTROLLER ROUTE
//
$ha->FrontControllerRoute();

//
// PHASE: THEME ENGINE RENDER
//
$ha->ThemeEngineRender();
