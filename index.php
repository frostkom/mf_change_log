<?php
/*
Plugin Name: MF Change Log
Plugin URI:
Description:
Version: 1.4.9
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_change_log
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI:
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_change_log = new mf_change_log();

	add_action('cron_base', array($obj_change_log, 'cron_base'), mt_rand(1, 10));

	if(is_admin())
	{
		register_activation_hook(__FILE__, 'activate_change_log');

		add_action('init', array($obj_change_log, 'init'), 1);

		add_action('post_updated', array($obj_change_log, 'post_updated'), 10, 3);
		add_action('wp_trash_post', array($obj_change_log, 'wp_trash_post'));

		add_action('rwmb_meta_boxes', array($obj_change_log, 'rwmb_meta_boxes'));
	}

	load_plugin_textdomain('lang_change_log', false, dirname(plugin_basename(__FILE__))."/lang/");

	function activate_change_log()
	{
		require_plugin("meta-box/meta-box.php", "Meta Box");
	}
}