<?php

class mf_change_log
{
	var $post_type = 'mf_status_change';
	var $meta_prefix = 'mf_cl_';

	function __construct()
	{
		//$this->post_type = 'mf_status_change';
		//$this->meta_prefix = 'mf_cl_';
	}

	function cron_base()
	{
		global $wpdb;

		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			// Clean up left overs
			##############################
			$result = $wpdb->get_results($wpdb->prepare("SELECT posts_changes.ID FROM ".$wpdb->prefix."posts AS posts_changes LEFT JOIN ".$wpdb->prefix."posts AS post_pages ON posts_changes.post_parent = post_pages.ID WHERE posts_changes.post_type = %s AND post_pages.ID IS null", $this->post_type));

			foreach($result as $r)
			{
				$wpdb->get_results($wpdb->prepare("DELETE FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $r->ID));

				//do_log("Remove: ".$wpdb->prepare("DELETE FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $r->ID));
			}
			##############################

			// Clean up old data
			##############################
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_type = %s AND post_date < DATE_SUB(NOW(), INTERVAL 1 YEAR)", $this->post_type));

			foreach($result as $r)
			{
				$wpdb->get_results($wpdb->prepare("DELETE FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $r->ID));

				//do_log("Remove: ".$wpdb->prepare("DELETE FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = '%d'", $this->post_type, $r->ID));
			}
			##############################
		}

		$obj_cron->end();
	}

	function init()
	{
		$args = array(
			'labels' => array(),
			'public' => false,
			'show_ui' => false,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
		);

		register_post_type($this->post_type, $args);
	}

	function post_updated($post_id, $post_after, $post_before)
	{
		$arr_exclude = array('mf_log', $this->post_type, 'attachment', 'nav_menu_item', 'revision', 'template');

		if(isset($post_after) && in_array($post_after->post_type, $arr_exclude))
		{
			return false;
		}

		else if(in_array(get_post_type($post_id), $arr_exclude))
		{
			return false;
		}

		else
		{
			$post_status = $post_status_before = $post_title = "";

			if(isset($post_after))
			{
				$post_status = $post_after->post_status;

				if(isset($post_before))
				{
					$post_status_before = $post_before->post_status;

					$post_title .= $post_status_before." -> ";
				}

				$post_title .= $post_status;
			}

			else
			{
				$post_status = get_post_status($post_id);

				$post_title .= $post_status;
			}

			$post_title .= " (".date("Y-m-d H:i:s").")";

			if($post_status != '' && $post_status != $post_status_before)
			{
				$post_data = array(
					'post_type' => $this->post_type,
					'post_parent' => $post_id,
					'post_status' => $post_status,
					'comment_status' => $post_status_before,
					'post_title' => $post_title,
				);

				wp_insert_post($post_data);
			}
		}
	}

	function wp_trash_post($post_id)
	{
		global $wpdb;

		if(get_post_type($post_id) != $this->post_type)
		{
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_type = %s AND post_parent = '%d'", $this->post_type, $post_id));

			foreach($result as $r)
			{
				wp_trash_post($r->ID);
			}
		}
	}

	function meta_status_changes()
	{
		global $wpdb, $post;

		$out = "";

		$post_id = $post->ID;

		if($post_id > 0)
		{
			$arr_statuses = array(
				'auto-draft' => __("Auto-Draft", 'lang_change_log'),
				'draft' => __("Draft"),
				'future' => __("Scheduled", 'lang_change_log'),
				'pending' => __("Pending", 'lang_change_log'),
				'publish' => __("Published", 'lang_change_log'),
				'trash' => __("Trash", 'lang_change_log'),
			);

			$result = $wpdb->get_results($wpdb->prepare("SELECT post_author, post_date, post_status, comment_status FROM ".$wpdb->posts." WHERE post_type = %s AND post_parent = '%d' ORDER BY post_date DESC", $this->post_type, $post_id));

			if($wpdb->num_rows > 0)
			{
				$out .= "<ul class='post-revisions'>";

					foreach($result as $r)
					{
						$user_avatar = get_avatar($r->post_author, 24);
						$user_name = get_user_info(array('id' => $r->post_author));

						$post_status_before = (isset($arr_statuses[$r->comment_status]) ? $arr_statuses[$r->comment_status] : '');
						$post_status = (isset($arr_statuses[$r->post_status]) ? $arr_statuses[$r->post_status] : '');

						$post_date = $r->post_date;

						$out .= "<li>".$user_avatar." ".$user_name.", ".$post_status_before." -> ".$post_status." (".format_date($post_date).")</li>";
					}

				$out .= "</ul>";
			}
		}

		return $out;
	}

	function rwmb_meta_boxes($meta_boxes)
	{
		if(IS_ADMIN)
		{
			$meta_boxes[] = array(
				'id' => $this->meta_prefix.'status_changes',
				'title' => __("Status Changes", 'lang_change_log'),
				'post_types' => array('page'),
				//'context' => 'side',
				'priority' => 'low',
				'fields' => array(
					array(
						'id' => $this->meta_prefix.'status_changes',
						'type' => 'custom_html',
						'callback' => array($this, 'meta_status_changes'),
					),
				)
			);
		}

		return $meta_boxes;
	}
}