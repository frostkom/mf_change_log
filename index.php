<?php
/*
Plugin Name: MF Change Log
Plugin URI: 
Description: 
Version: 1.4.0
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_change_log
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: 
*/

include_once("include/classes.php");

$obj_change_log = new mf_change_log();

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_change_log');

	add_action('init', array($obj_change_log, 'init'), 1);

	add_action('admin_init', array($obj_change_log, 'admin_init'), 0);

	add_action('post_updated', array($obj_change_log, 'post_updated'), 10, 3);

	add_action('rwmb_meta_boxes', array($obj_change_log, 'rwmb_meta_boxes'));
}

load_plugin_textdomain('lang_change_log', false, dirname(plugin_basename(__FILE__))."/lang/");

function activate_change_log()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");
}