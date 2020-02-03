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

// Get the cmb2 custom fields data.
$meta              = $post->custom;

if ( $meta['campaign_id'] ) {
	$theme_meta = get_post_meta( $meta['campaign_id'] );
} else {
	$theme_meta = $meta;
}
$theme_meta = array_map( 'maybe_unserialize', $meta );

$campaign_template = $theme_meta['campaign_page_template'] ?? $theme_meta['_campaign_page_template'] ?? null;

if ( $campaign_template ) {
	$context['custom_body_classes'] = 'brown-bg theme-' . $campaign_template;
}

// Save custom style settings.
$custom_styles = [];

$custom_styles['nav_type']            = $theme_meta['campaign_nav_type'];
$custom_styles['nav_border']          = $theme_meta['campaign_nav_border'];
$custom_styles['campaign_logo_color'] = $theme_meta['campaign_logo_color'] ?? 'light';
$custom_styles['campaign_logo']       = $theme_meta['campaign_logo'] ?? null;

// Set GTM Data Layer values.
$post->set_data_layer();
$data_layer = $post->get_data_layer();

$context['campaign_template']           = $campaign_template;
$context['post']                        = $post;
$context['header_title']                = is_front_page() ? ( $meta['p4_title'] ?? '' ) : ( $meta['p4_title'] ?? $post->title );
$context['header_subtitle']             = $meta['p4_subtitle'] ?? '';
$context['header_description']          = wpautop( $meta['p4_description'] ?? '' );
$context['header_button_title']         = $meta['p4_button_title'] ?? '';
$context['header_button_link']          = $meta['p4_button_link'] ?? '';
$context['header_button_link_checkbox'] = $meta['p4_button_link_checkbox'] ?? '';
$context['hide_page_title_checkbox']    = $meta['p4_hide_page_title_checkbox'] ?? '';
$context['social_accounts']             = $post->get_social_accounts( $context['footer_social_menu'] );
$context['page_category']               = $data_layer['page_category'];
$context['post_tags']                   = implode( ', ', $post->tags() );

$background_image_id                = get_post_meta( get_the_ID(), 'background_image_id', 1 );
$context['background_image']        = wp_get_attachment_url( $background_image_id );
$context['background_image_srcset'] = wp_get_attachment_image_srcset( $background_image_id, 'full' );
$context['og_title']                = $post->get_og_title();
$context['og_description']          = $post->get_og_description();
$context['og_image_data']           = $post->get_og_image();
$context['custom_styles']           = $custom_styles;
$context['css_vars']                = P4_Post_Campaign::css_vars( $theme_meta );

// P4 Campaign/dataLayer fields.
$context['cf_campaign_name'] = $meta['p4_campaign_name'] ?? '';
$context['cf_basket_name']   = $meta['p4_basket_name'] ?? '';
$context['cf_scope']         = $meta['p4_scope'] ?? '';
$context['cf_department']    = $meta['p4_department'] ?? '';

// Social footer link overrides.
$context['social_overrides'] = [];

foreach ( range( 1, 5 ) as $i ) {
	$footer_item_key = 'campaign_footer_item' . $i;

	if ( isset( $meta[ $footer_item_key ] ) ) {
		$campaign_footer_item = maybe_unserialize( $meta[ $footer_item_key ] );
		if ( $campaign_footer_item['url'] && $campaign_footer_item['icon'] ) {
			$context['social_overrides'][ $i ]['url']  = $campaign_footer_item['url'];
			$context['social_overrides'][ $i ]['icon'] = $campaign_footer_item['icon'];
		}
	}
}

if ( post_password_required( $post->ID ) ) {
	$context['login_url'] = wp_login_url();

	Timber::render( 'single-password.twig', $context );
} else {
	Timber::render( array( 'single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig' ), $context );
}
