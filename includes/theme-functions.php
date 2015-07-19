<?php
/**
 * Popup Theme Functions
 *
 * @package		POPMAKE
 * @subpackage  Functions
 * @copyright   Copyright (c) 2014, Daniel Iser
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since 1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



function popmake_get_default_popup_theme() {
	$default_theme = get_option('popmake_default_theme');
	if( FALSE === get_post_status( $default_theme ) ) {
		popmake_install_default_theme();
		$default_theme = get_option('popmake_default_theme');
	}
	return $default_theme;
}


function popmake_get_all_popup_themes() {
	$query = get_posts( array(
		'post_type' => 'popup_theme',
		'post_status' => 'publish',
		'posts_per_page' => -1
	) );
	return $query;
}


function popmake_get_popup_theme_meta( $group, $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	if ( ! $popup_theme_id ) {
		$popup_theme_id = get_the_ID();
	}

	$values = get_post_meta( $popup_theme_id, "popup_theme_{$group}", true );

	if ( ! $values ) {
		$values = apply_filters( "popmake_popup_theme_{$group}_defaults", array() );
	}
	else {
		$values = array_merge( popmake_get_popup_theme_meta_group( $group, $popup_theme_id ), $values );
	}

	if ( $key ) {

		// Check for dot notation key value.
		$test = uniqid();
		$value = popmake_resolve( $values, $key, $test );
		if ( $value == $test ) {

			$key = str_replace( '.', '_', $key );

			if ( ! isset( $values[ $key ] ) ) {
				$value = $default;
			}
			else {
				$value = $values[$key];
			}

		}

		return apply_filters( "popmake_get_popup_theme_{$group}_$key", $value, $popup_theme_id );
	}
	else {
		return apply_filters( "popmake_get_popup_theme_{$group}", $values, $popup_theme_id );
	}
}

/**
 * Returns the meta group of a theme or value if key is set.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a overlay meta for
 * @return mixed array|string of the popup overlay meta
	                                            */
function popmake_get_popup_theme_meta_group( $group, $popup_theme_id = NULL, $key = NULL ) {
	if(!$popup_theme_id) $popup_theme_id = get_the_ID();

	$post_meta = get_post_custom( $popup_theme_id );
	$group_values = $post_meta ? array() : apply_filters("popmake_popup_theme_{$group}_defaults", array());
	if($post_meta) {
		foreach($post_meta as $meta_key => $value) {
			if(strpos($meta_key, "popup_theme_{$group}_") !== false) {
				$new_key = str_replace("popup_theme_{$group}_", '', $meta_key);
				if(count($value) == 1)
					$group_values[$new_key] = $value[0];
				else
					$group_values[$new_key] = $value;
			}
		}

	}
	if($key) {
		$key = str_replace('.', '_', $key);
		if(!isset($group_values[$key])) {
			return false;
		}
		$value = $group_values[$key];
		return apply_filters( "popmake_get_popup_theme_{$group}_$key", $value, $popup_theme_id );
	}
	else {
		return apply_filters( "popmake_get_popup_theme_{$group}", $group_values, $popup_theme_id );
	}
}


/**
 * Returns the overlay meta of a theme.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a overlay meta for
 * @return mixed array|string of the popup overlay meta 
 */
function popmake_get_popup_theme_overlay( $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	return popmake_get_popup_theme_meta( 'overlay', $popup_theme_id, $key, $default );
}


/**
 * Returns the container meta of a theme.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a container meta for
 * @return mixed array|string of the popup container meta 
 */
function popmake_get_popup_theme_container( $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	return popmake_get_popup_theme_meta( 'container', $popup_theme_id, $key, $default );
}


/**
 * Returns the title meta of a theme.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a title meta for
 * @return mixed array|string of the popup title meta 
 */
function popmake_get_popup_theme_title( $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	return popmake_get_popup_theme_meta( 'title', $popup_theme_id, $key, $default );
}


/**
 * Returns the content meta of a theme.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a content meta for
 * @return mixed array|string of the popup content meta 
 */
function popmake_get_popup_theme_content( $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	return popmake_get_popup_theme_meta( 'content', $popup_theme_id, $key, $default );
}


/**
 * Returns the close meta of a theme.
 *
 * @since 1.0
 * @param int $popup_theme_id ID number of the popup to retrieve a close meta for
 * @return mixed array|string of the popup close meta 
 */
function popmake_get_popup_theme_close( $popup_theme_id = NULL, $key = NULL, $default = NULL ) {
	return popmake_get_popup_theme_meta( 'close', $popup_theme_id, $key, $default );
}




function popmake_get_popup_theme_data_attr( $popup_theme_id = 0 ) {
	$data_attr = array(
		'overlay' => popmake_get_popup_theme_overlay( $popup_theme_id ),
		'container' => popmake_get_popup_theme_container( $popup_theme_id ),
		'title' => popmake_get_popup_theme_title( $popup_theme_id ),
		'content' => popmake_get_popup_theme_content( $popup_theme_id ),
		'close' => popmake_get_popup_theme_close( $popup_theme_id ),
	);
	return apply_filters('popmake_get_popup_theme_data_attr', $data_attr, $popup_theme_id );
}


function popmake_get_popup_theme_default_meta() {
	$default_meta = array();
	$defaults = popmake_get_popup_theme_data_attr( 0 );
	foreach($defaults as $group => $fields) {
		$prefix = 'popup_theme_' . $group . '_';
		foreach($fields as $field => $value) {
			$default_meta[$prefix . $field] = $value;
		}
	}
	return $default_meta;
}

function popmake_get_popup_themes_data() {
	$themes = popmake_get_all_popup_themes();

	$popmake_themes = array();

	foreach( $themes as $theme ) {
		$popmake_themes[ $theme->ID ] = popmake_get_popup_theme_data_attr( $theme->ID );
	}

	wp_reset_postdata();

	return apply_filters( 'popmake_get_popup_themes_data', $popmake_themes );
}