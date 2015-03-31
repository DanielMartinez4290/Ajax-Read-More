<?php
/*
Plugin Name: Ajax Read More
Author: Daniel Martinez
Description: Makes the Read More Link show the second part of the text without a page refresh.
Version:1.00
*/
 
global $dm_arm_needjs;
$dm_arm_needjs = false;
define( 'dm_ARM_VERSION', '1.0' );
add_action( 'template_redirect', 'dm_arm_add_js' ); 

function dm_arm_add_js() {
	wp_enqueue_script( 'dm_arm',
		plugin_dir_url( __FILE__ ).'js/script.js',
		array('jquery'), dm_ARM_VERSION, true 
	);
	$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';
	$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ) );
	wp_localize_script( 'dm_arm', 'dm_arm', $params ); 
}

add_action( 'wp_print_footer_scripts', 'dm_arm_footer_maybe_remove', 1 ); 
function dm_arm_footer_maybe_remove() {
	global $dm_arm_needjs; 
	if( !$dm_arm_needjs ) {
		wp_deregister_script( 'dm_arm' ); 
	}
}
add_action( 'the_post', 'dm_arm_check_readmore' ); 
function dm_arm_check_readmore( $post ) {
	if ( preg_match('/<!--more(.*?)?-->/', $post->post_content ) 
	&& !is_single() ) {
		global $dm_arm_needjs;
		$dm_arm_needjs = true; 
	}
}
add_action('wp_ajax_nopriv_dm_arm_ajax', 'dm_arm_ajax'); 
add_action('wp_ajax_dm_arm_ajax', 'dm_arm_ajax'); 
function dm_arm_ajax() {
	add_filter( 'the_content', 'dm_arm_get_2nd_half' );
	query_posts( 'p='.absint( $_REQUEST['post_id'] ) );
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		the_content(); 
	endwhile; else:
		echo "post not found :/"; 
	endif;
	wp_reset_query(); 
	die();
	}
function dm_arm_get_2nd_half( $content ) {
	$id = absint( $_REQUEST['post_id'] );
	$content = preg_replace( "!^.*<span id=\"more-$id\"></span>!s", '', $content ); 
	return $content;
}