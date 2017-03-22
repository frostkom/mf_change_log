<?php
/*
Plugin Name: MF Change Log
Plugin URI: 
Description: 
Version: 1.2.4
Author: Martin Fors
Author URI: http://frostkom.se
Text Domain: lang_change_log
Domain Path: /lang

GitHub Plugin URI: 
*/

include_once("include/functions.php");

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_change_log');

	add_action('post_updated', 'post_updated_change_log', 10, 3);
	//add_action('before_delete_post', 'before_delete_post_change_log');

	add_action('rwmb_meta_boxes', 'meta_boxes_change_log');
}

load_plugin_textdomain('lang_change_log', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_change_log()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");
}