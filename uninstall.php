<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   woo-simple-tickets
 * @author    vimes1984 <churchill.c.j@gmail.com>
 * @license   GPL-2.0+
 * @link      http://buildawebdoctor.com
 * @copyright 5-7-2015 BAWD
 */

// If uninstall, not called from WordPress, then exit
if (!defined("WP_UNINSTALL_PLUGIN")) {
	exit;
}

// TODO: Define uninstall functionality here