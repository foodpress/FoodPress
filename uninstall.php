<?php
/**
 * foodpress Uninstall
 *
 * Uninstalling foodpress deletes everything.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	foodpress/Uninstaller
 * @version     1.0.0
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();

global $wpdb, $wp_roles;

// Delete options
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'foodpress_%';");

// Remove the 'menu_order' column
$sql = "ALTER TABLE `{$wpdb->terms}` DROP COLUMN `menu_order`;";
$wpdb->query( $sql );