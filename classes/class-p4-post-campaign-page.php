<?php
/**
 * P4 Post Campaign Template Settings
 *
 * @package P4MT
 */

if ( ! class_exists( 'P4_Post_Campaign_Page' ) ) {
	/**
	 * A page inside a campaign. Note that this type might become obsolete after we moved all campaign specific settings
	 * to its own "campaign" type. Previously this post type was called campaign and we kept the post type identifier to
	 * avoid having to migrate existing content.
	 */
	class P4_Post_Campaign_Page {

		/**
		 * Post Type
		 */
		const POST_TYPE = 'campaign';


		/**
		 * Taxonomy_Image constructor.
		 */
		public function __construct() {
			$this->hooks();
		}

		/**
		 * Class hooks.
		 */
		private function hooks() {
			add_action( 'init', [ $this, 'register_campaign_pages_cpt' ] );
		}

		/**
		 * Register campaigns cpt
		 */
		public function register_campaign_pages_cpt() {

			$labels = array(
				'name'               => _x( 'Campaign pages', 'post type general name', 'planet4-master-theme-backend' ),
				'singular_name'      => _x( 'Campaign page', 'post type singular name', 'planet4-master-theme-backend' ),
				'menu_name'          => _x( 'Campaign pages', 'admin menu', 'planet4-master-theme-backend' ),
				'name_admin_bar'     => _x( 'Campaign page', 'add new on admin bar', 'planet4-master-theme-backend' ),
				'add_new_item'       => __( 'Add New Campaign page', 'planet4-master-theme-backend' ),
				'new_item'           => __( 'New Campaign page', 'planet4-master-theme-backend' ),
				'edit_item'          => __( 'Edit Campaign page', 'planet4-master-theme-backend' ),
				'view_item'          => __( 'View Campaign page', 'planet4-master-theme-backend' ),
				'all_items'          => __( 'All Campaigns pages', 'planet4-master-theme-backend' ),
				'search_items'       => __( 'Search Campaigns pages', 'planet4-master-theme-backend' ),
				'parent_item_colon'  => __( 'Parent Campaign pages:', 'planet4-master-theme-backend' ),
				'not_found'          => __( 'No campaigns pages found.', 'planet4-master-theme-backend' ),
				'not_found_in_trash' => __( 'No campaigns pages found in Trash.', 'planet4-master-theme-backend' ),
			);

			$args = array(
				'labels'             => $labels,
				'description'        => __( 'Campaigns', 'planet4-master-theme-backend' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => [ 'slug' => 'campaign' ],
				'capability_type'    => [ 'campaign', 'campaigns' ],
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon'          => 'dashicons-megaphone',
				'show_in_rest'       => true,
				'supports'           => [
					'title',
					'editor',
					'author',
					'thumbnail',
					'excerpt',
					'revisions',
					// Required to expose meta fields in the REST API.
					'custom-fields',
				],
			);

			register_post_type( self::POST_TYPE, $args );

			// The id of the P4_Post_Campaign this page is a part of.
			register_post_meta(
				self::POST_TYPE,
				'campaign_id',
				[
					'type' => 'string',
					'single'       => true,
					'show_in_rest' => true
				]
			);
		}
	}
}
