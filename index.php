<?php
/*
Plugin Name: MF Change Log
Plugin URI: 
Description: 
Version: 1.3.7
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_change_log
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: 
*/

include_once("include/classes.php");
include_once("include/functions.php");

$obj_change_log = new mf_change_log();

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_change_log');

	add_action('init', 'init_change_log', 1);

	add_action('admin_init', array($obj_change_log, 'admin_init'), 0);

	add_action('post_updated', 'post_updated_change_log', 10, 3);

	add_action('rwmb_meta_boxes', 'meta_boxes_change_log');
}

load_plugin_textdomain('lang_change_log', false, dirname(plugin_basename(__FILE__))."/lang/");

function activate_change_log()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");
}