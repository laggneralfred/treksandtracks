<?php
/**
 * Compatibility shim for older All in One SEO Pack versions.
 *
 * The plugin bootstrap in this archive still requires this file, but the
 * restored tree already provides the metabox implementation in
 * general-metaboxes.php.
 */

if ( ! class_exists( 'aiosp_metaboxes' ) && file_exists( dirname( __FILE__ ) . '/general-metaboxes.php' ) ) {
	require_once dirname( __FILE__ ) . '/general-metaboxes.php';
}
