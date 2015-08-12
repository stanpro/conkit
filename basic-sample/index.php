<?
////// This sets path to the FW (traling slash is mandatory)
define('CORE','../core/');

////// Includes FW code
include(CORE.'core.php');

////// Application wide configuration
////// It override FW-wide settings in core/config.php
////// Custom configuration can be added here also

core::config('class-prefix','');
core::config('class-suffix','.class');

////// Launch the action
core::start();
