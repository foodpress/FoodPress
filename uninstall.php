<?php
/**
 * foodpress Uninstall
 *
 * Uninstalling foodpress deletes everything.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	foodpress/Uninstaller
 * @version     1.3.7
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();

$fp_opt = get_option('fp_options_food_1');

// If settings set to not delete the foodpress settings
if(!empty($fp_opt['fp_do_not_delete_settings']) && $fp_opt['fp_do_not_delete_settings'] == 'yes') exit();

global $wpdb, $wp_roles;

// Delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE 
	option_name LIKE 'foodpress_%'
	OR option_name LIKE '%_fp_%'
	OR option_name LIKE '%_foodpress_%';");

// Remove the 'menu_order' column
$sql = "ALTER TABLE `{$wpdb->terms}` DROP COLUMN `menu_order`;";
$wpdb->query( $sql );