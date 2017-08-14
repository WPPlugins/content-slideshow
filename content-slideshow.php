<?php
/**
 * Plugin Name: Content Slideshow
 * Plugin URI: http://celloexpressions.com/plugins/content-slideshow
 * Description: Creates an automatic slideshow that randomly cycles through all of your site's image content. Includes full-screen, widget, and shortcode versions.
 * Version: 2.1
 * Author: Nick Halsey
 * Author URI: http://celloexpressions.com/
 * Tags: Slideshow, Pictures, Media, Media Library, Automatic, Widget, Shortcode
 * License: GPL
 * Text Domain: content-slideshow

=====================================================================================
Copyright (C) 2016 Nick Halsey

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WordPress; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
=====================================================================================
*/

function content_slideshow_load_translations() {
	load_plugin_textdomain( 'content-slideshow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'content_slideshow_load_translations' );

function content_slideshow_template_redirect() {
	global $wp;
	if ( 'slideshow' === $wp->request ) {
		content_slideshow_takeover_page();
	}
}
add_action( 'template_redirect', 'content_slideshow_template_redirect' );

function content_slideshow_takeover_page() {
	global $wp_query;

	// If we have a 404 status, ie, WordPress doesn't have anything at this URL, override it.
	if ( $wp_query->is_404 ) {
		$wp_query->is_404 = false;
		$wp_query->is_archive = true;
		// Change the header to 200 OK.
		header( "HTTP/1.1 200 OK" );
	}

	// Load slideshow page.
	include( 'slideshow.php' );

	// Stop execution.
	exit;
}

// Load the widget and shortcode wrappers.
include( 'slideshow-widget-shortcode.php' );