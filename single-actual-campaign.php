<?php
/**
 * Template Variables for Campaigns.
 *
 * @package P4MT
 */

use Timber\Timber;

// Initializing variables.
$context = Timber::get_context();

/**
 * Post object.
 *
 * @var P4_Post $post
 * */
$post            = Timber::query_post( false, 'P4_Post' );
$context['post'] = $post;

if ( post_password_required( $post->ID ) ) {
	$context['login_url'] = wp_login_url();

	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
