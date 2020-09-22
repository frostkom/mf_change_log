<?php

class mf_change_log
{
	function __construct(){}

	function admin_init()
	{
		if(!is_plugin_active("mf_base/index.php"))
		{
			deactivate_plugins(str_replace("include/classes.php", "index.php", plugin_basename(__FILE__)));
		}
	}
}