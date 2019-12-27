<?php
/**
 * P4 Post Campaign Template Settings
 *
 * @package P4MT
 */

if ( ! class_exists( 'P4_Post_Campaign' ) ) {
	/**
	 * Class P4_Post_Campaign
	 */
	class P4_Post_Campaign {

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
			add_action( 'init', [ $this, 'register_campaigns_cpt' ] );
			add_action( 'cmb2_admin_init', [ $this, 'register_campaigns_metaboxes' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
			add_action( 'cmb2_render_sidebar_link', [ $this, 'cmb2_render_sidebar_link_field_callback' ], 10, 5 );
			add_action( 'cmb2_render_footer_icon_link', [ $this, 'cmb2_render_footer_icon_link_field_callback' ], 10, 5 );
		}

		/**
		 * Return a list of the available campaign themes
		 *
		 * @return array
		 */
		private function campaign_themes(): array {
			return [
				[
					'value' => '',
					'label' => __( 'Default', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'antarctic',
					'label' => __( 'Antarctic', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'arctic',
					'label' => __( 'Arctic', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'climate',
					'label' => __( 'Climate Emergency', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'forest',
					'label' => __( 'Forest', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'oceans',
					'label' => __( 'Oceans', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'oil',
					'label' => __( 'Oil', 'planet4-master-theme-backend' ),
				],
				[
					'value' => 'plastic',
					'label' => __( 'Plastics', 'planet4-master-theme-backend' ),
				],
			];
		}

		/**
		 * Register campaigns cpt
		 */
		public function register_campaigns_cpt() {

			$labels = array(
				'name'               => _x( 'Campaigns', 'post type general name', 'planet4-master-theme-backend' ),
				'singular_name'      => _x( 'Campaign', 'post type singular name', 'planet4-master-theme-backend' ),
				'menu_name'          => _x( 'Campaigns', 'admin menu', 'planet4-master-theme-backend' ),
				'name_admin_bar'     => _x( 'Campaign', 'add new on admin bar', 'planet4-master-theme-backend' ),
				'add_new'            => _x( 'Add New', 'campaign', 'planet4-master-theme-backend' ),
				'add_new_item'       => __( 'Add New Campaign', 'planet4-master-theme-backend' ),
				'new_item'           => __( 'New Campaign', 'planet4-master-theme-backend' ),
				'edit_item'          => __( 'Edit Campaign', 'planet4-master-theme-backend' ),
				'view_item'          => __( 'View Campaign', 'planet4-master-theme-backend' ),
				'all_items'          => __( 'All Campaigns', 'planet4-master-theme-backend' ),
				'search_items'       => __( 'Search Campaigns', 'planet4-master-theme-backend' ),
				'parent_item_colon'  => __( 'Parent Campaigns:', 'planet4-master-theme-backend' ),
				'not_found'          => __( 'No campaigns found.', 'planet4-master-theme-backend' ),
				'not_found_in_trash' => __( 'No campaigns found in Trash.', 'planet4-master-theme-backend' ),
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
					// Required to expose fields in the REST API.
					'custom-fields',
				],
			);

			register_post_type( self::POST_TYPE, $args );

			$campaign_fields = [
				self::campaign_field(
					'campaign_page_template',
					null,
					$this->campaign_themes(),
					[ 'default' => '' ]
				),
				self::campaign_field(
					'campaign_logo',
					null,
					[
						[
							'value' => 'campaign',
							'label' => __( 'Campaign', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'greenpeace',
							'label' => __( 'Greenpeace', 'planet4-master-theme-backend' ),
						],
					],
					[ 'default' => 'campaign' ]
				),
				self::campaign_field(
					'campaign_logo_color',
					null,
					[
						[
							'value' => 'light',
							'label' => __( 'Light', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'dark',
							'label' => __( 'Dark', 'planet4-master-theme-backend' ),
						],
					],
					[ 'default' => 'light' ]
				),
				self::campaign_field(
					'campaign_nav_type',
					null,
					[
						[
							'value' => 'planet4',
							'label' => __( 'Planet 4 Navigation', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'minimal',
							'label' => __( 'Minimal Navigation', 'planet4-master-theme-backend' ),
						],
					],
					[ 'default' => 'planet4' ]
				),
				self::campaign_field(
					'campaign_nav_color',
					null,
					[
						[ 'color' => '#FFFFFF' ],
						[ 'color' => '#E5E5E5' ],
						[ 'color' => '#66CC00' ],
						[ 'color' => '#32CA89' ],
						[ 'color' => '#1BB6D6' ],
						[ 'color' => '#22938D' ],
						[ 'color' => '#186A70' ],
						[ 'color' => '#043029' ],
						[ 'color' => '#093944' ],
						[ 'color' => '#042233' ],
						[ 'color' => '#1A1A1A' ],
						[ 'color' => '#1B4A1B' ],
					],
					[ 'default' => '#FFFFFF' ]
				),
				self::campaign_field(
					'campaign_nav_border',
					'campaign_page_template',
					[
						'climate' => [
							[
								'value' => 'none',
								'label',
								'label' => __( 'No border', 'planet4-master-theme-backend' ),
							],
							[
								'value' => 'border',
								'label',
								'label' => __( 'White bottom border', 'planet4-master-theme-backend' ),
							],
						],
					],
					[ 'default' => 'none' ]
				),
				self::campaign_field(
					'campaign_header_color',
					null,
					[
						[ 'color' => '#000000' ],
						[ 'color' => '#E5E5E5' ],
						[ 'color' => '#32CA89' ],
						[ 'color' => '#1BB6D6' ],
						[ 'color' => '#22938D' ],
						[ 'color' => '#186A70' ],
						[ 'color' => '#043029' ],
						[ 'color' => '#093944' ],
						[ 'color' => '#042233' ],
						[ 'color' => '#1A1A1A' ],
					],
					[ 'default' => '#000000' ]
				),
				self::campaign_field(
					'campaign_primary_color',
					null,
					[
						[ 'color' => '#ffd204' ],
						[ 'color' => '#66cc00' ],
						[ 'color' => '#6ed961' ],
						[ 'color' => '#21cbca' ],
						[ 'color' => '#ee562d' ],
						[ 'color' => '#7a1805' ],
						[ 'color' => '#2077bf' ],
						[ 'color' => '#1B4A1B' ],
					]
				),
				self::campaign_field(
					'campaign_secondary_color',
					null,
					[ [ 'color' => '#042233' ], [ 'color' => '#093944' ], [ 'color' => '#074365' ] ]
				),
				self::campaign_field(
					'campaign_header_primary',
					null,
					[
						[
							'value' => '',
							'label' => __( 'Campaign default', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Anton',
							'label' => __( 'Anton', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Jost',
							'label' => __( 'Jost', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Montserrat',
							'label' => __( 'Montserrat Bold', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Montserrat_Light',
							'label' => __( 'Montserrat Light', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Sanctuary',
							'label' => __( 'Sanctuary', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Kanit',
							'label' => __( 'Kanit Extra Bold', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'Save the Arctic',
							'label' => __( 'Save the Arctic', 'planet4-master-theme-backend' ),
						],
					]
				),
				self::campaign_field(
					'campaign_header_secondary',
					null,
					[
						[
							'value' => 'monsterrat_semi',
							'label' => __( 'Montserrat Semi Bold', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'kanit_semi',
							'label' => __( 'Kanit Semi Bold', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'open_sans',
							'label' => __( 'Open Sans', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'open_sans_shadows',
							'label' => __( 'Open Sans Shadows', 'planet4-master-theme-backend' ),
						],
					]
				),
				self::campaign_field(
					'campaign_body_font',
					null,
					[
						[
							'value' => 'lora',
							'label' => __( 'Serif', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'roboto',
							'label' => __( 'Sans Serif', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'campaign',
							'label' => __( 'Campaign default', 'planet4-master-theme-backend' ),
						],
					],
					[ 'default' => 'lora' ]
				),
				self::campaign_field(
					'campaign_footer_theme',
					null,
					[
						[
							'value' => 'default',
							'label' => __( 'Default', 'planet4-master-theme-backend' ),
						],
						[
							'value' => 'white',
							'label' => __( 'White', 'planet4-master-theme-backend' ),
						],
					],
					[ 'default' => 'default' ]
				),
				self::campaign_field(
					'footer_links_color',
					'campaign_footer_theme',
					[
						'white' => [
							[ 'color' => '#ffd204' ],
							[ 'color' => '#66cc00' ],
							[ 'color' => '#6ed961' ],
							[ 'color' => '#21cbca' ],
							[ 'color' => '#ee562d' ],
							[ 'color' => '#7a1805' ],
							[ 'color' => '#2077bf' ],
							[ 'color' => '#1B4A1B' ],
						],
					]
				),
			];

			add_action(
				'rest_api_init',
				static function () use ( $campaign_fields ) {
					register_rest_route(
						'planet4/v1',
						'/campaign_fields',
						[
							'method'   => 'GET',
							'callback' => static function () use ( $campaign_fields ) {

								return $campaign_fields;
							},
						]
					);
				}
			);
		}

		/**
		 * Register Color Picker Metabox for navigation
		 */
		public function register_campaigns_metaboxes() {
			$cmb = new_cmb2_box(
				[
					'id'           => 'campaign_nav_settings_mb',
					'title'        => __( 'Page Design', 'planet4-master-theme-backend' ),
					'object_types' => [
						'campaign',
					],
					'context'      => 'normal',
					'priority'     => 'high',
					'show_names'   => true, // Show field names on the left.
				]
			);

			$cmb->add_field(
				[
					'id'   => 'new_sidebar_link',
					'type' => 'sidebar_link',
				]
			);

			$cmb->add_field(
				[
					'name' => __( 'Footer item 1', 'planet4-master-theme-backend' ),
					'id'   => 'campaign_footer_item1',
					'type' => 'footer_icon_link',
				]
			);

			$cmb->add_field(
				[
					'name' => __( 'Footer item 2', 'planet4-master-theme-backend' ),
					'id'   => 'campaign_footer_item2',
					'type' => 'footer_icon_link',
				]
			);

			$cmb->add_field(
				[
					'name' => __( 'Footer item 3', 'planet4-master-theme-backend' ),
					'id'   => 'campaign_footer_item3',
					'type' => 'footer_icon_link',
				]
			);

			$cmb->add_field(
				[
					'name' => __( 'Footer item 4', 'planet4-master-theme-backend' ),
					'id'   => 'campaign_footer_item4',
					'type' => 'footer_icon_link',
				]
			);

			$cmb->add_field(
				[
					'name' => __( 'Footer item 5', 'planet4-master-theme-backend' ),
					'id'   => 'campaign_footer_item5',
					'type' => 'footer_icon_link',
				]
			);
		}

		/**
		 * Load assets.
		 */
		public function enqueue_admin_assets() {
			wp_register_style( 'cmb-style', get_template_directory_uri() . '/admin/css/campaign.css' );
			wp_enqueue_style( 'cmb-style' );
		}

		/**
		 * CMB2 custom field(sidebar_link) callback function.
		 *
		 * @param array $field The CMB2 field array.
		 * @param array $value The CMB2 field Value.
		 * @param array $object_id The id of the object.
		 * @param array $object_type The type of object.
		 * @param array $field_type Instance of the `cmb2_Meta_Box_types` object.
		 */
		public function cmb2_render_sidebar_link_field_callback(
			$field,
			$value,
			$object_id,
			$object_type,
			$field_type
		) {
			?>
			<a
				href="#" onclick="openSidebar()"
				id="new_sidebar_link">
				<?php
					echo __( 'Design settings moved to a new sidebar.', 'planet4-master-theme-backend' )
				?>
			</a>
			<script>
				function openSidebar() {
					let sidebarButton = document.querySelector( '.edit-post-pinned-plugins button[aria-expanded=false]' );
					if ( sidebarButton ) {
						sidebarButton.click();
					}
				}
			</script>
			<?php
		}

		/**
		 * CMB2 custom field(footer_icon_link) callback function.
		 *
		 * @param array $field The CMB2 field array.
		 * @param array $value The CMB2 field Value.
		 * @param array $object_id The id of the object.
		 * @param array $object_type The type of object.
		 * @param array $field_type Instance of the `cmb2_Meta_Box_types` object.
		 */
		public function cmb2_render_footer_icon_link_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
			$value = wp_parse_args(
				$value,
				[
					'url'  => '',
					'icon' => '',
				]
			);
			?>
			<div class="alignleft">
			<?php
				echo wp_kses(
					$field_type->input(
						array(
							'class'       => 'cmb-type-text-medium',
							'name'        => esc_attr( $field_type->_name( '[url]' ) ),
							'id'          => esc_attr( $field_type->_id( '_url' ) ),
							'type'        => 'text',
							'value'       => esc_url( $value['url'] ),
							'placeholder' => __( 'Footer item link', 'planet4-master-theme-backend' ),
						)
					),
					[
						'input' => [
							'class'       => [],
							'placeholder' => [],
							'name'        => [],
							'id'          => [],
							'type'        => [],
							'value'       => [],
							'data-hash'   => [],
						],
					]
				);
			?>
			</div>
			<div class="alignleft">
			<?php
				echo wp_kses(
					$field_type->input(
						array(
							'class'       => 'cmb-type-text-medium',
							'name'        => esc_attr( $field_type->_name( '[icon]' ) ),
							'id'          => esc_attr( $field_type->_id( '_icon' ) ),
							'type'        => 'text',
							'value'       => $value['icon'],
							'placeholder' => __( 'Footer icon name', 'planet4-master-theme-backend' ),
						)
					),
					[
						'input' => [
							'class'       => [],
							'placeholder' => [],
							'name'        => [],
							'id'          => [],
							'type'        => [],
							'value'       => [],
							'data-hash'   => [],
						],
					]
				);
			?>
			</div>
			<div class="alignleft"> <?php esc_html_e( 'In the “Footer icon name” field add the name of the icon you want from the', 'planet4-master-theme-backend' ); ?> <a target="_blank" href="https://github.com/greenpeace/planet4-styleguide/tree/master/src/icons"><?php esc_html_e( 'list of icons in the CSS styleguide', 'planet4-master-theme-backend' ); ?></a>. e.g. twitter-square</div>
			<?php
		}

		/**
		 * Register a key as a post_meta and return a definition of the field to be used in the API endpoint.
		 *
		 * @param string      $meta_key Identifier the post_meta field will be registered with.
		 * @param string|null $depends_on Restrict allowed values based on the value of another field.
		 * @param array       $options The allowed values for this field.
		 * @param array       $args Arguments which are passed on to register_post_meta.
		 *
		 * @return array A description of the field.
		 */
		private static function campaign_field(
			string $meta_key,
			?string $depends_on,
			array $options,
			array $args = []
		): array {
			$args = array_merge(
				[
					'show_in_rest' => true,
					'type'         => 'string',
					'single'       => true,
				],
				$args
			);
			register_post_meta( self::POST_TYPE, $meta_key, $args );

			return [
				'key'       => $meta_key,
				'dependsOn' => $depends_on,
				'options'   => $options,
				'default'   => $args['default'] ?? null,
			];
		}

		/**
		 * Determine the css variables for a certain post.
		 *
		 * @param object $post The post for which to determine the css variables.
		 *
		 * @return array The variables.
		 */
		public static function css_vars( $post ): array {
			$campaign_template = $post->campaign_page_template ?? $post->custom['_campaign_page_template'];

			// Set specific CSS for Montserrat.
			$special_weight_fonts = [
				'Montserrat'       => '900',
				'Montserrat_Light' => '500',
			];
			$header_primary_font  = 'Montserrat_Light' === $post->campaign_header_primary ? 'Montserrat' : $post->campaign_header_primary;

			$campaigns_font_map = [
				'default'   => 'lora',
				'antarctic' => 'sanctuary',
				'arctic'    => 'Save the Arctic',
				'climate'   => 'Jost',
				'forest'    => 'Kanit',
				'oceans'    => 'Montserrat',
				'oil'       => 'Anton',
				'plastic'   => 'Montserrat',
			];

			$campaign_font = $campaigns_font_map[ $campaign_template ?: 'default' ];

			if ( 'campaign' === $post->campaign_body_font ) {
				$body_font = $campaign_font;
			} else {
				$body_font = $post->campaign_body_font;
			}
			$footer_theme = $post->campaign_footer_theme ?? null;

			if ( 'white' === $footer_theme ) {
				$default_footer_links_color = $post->campaign_nav_color ?? '#1A1A1A';
				$footer_links_color         = $post->footer_links_color ?? $default_footer_links_color;
				$footer_color               = '#FFFFFF';
			} else {
				$footer_links_color = 'light' === $post->campaign_logo_color ? '#FFFFFF' : '#1A1A1A';
				$footer_color       = $post->campaign_nav_color ?? null;
			}

			$passive_button_colors_map = [
				'#ffd204' => '#ffe467',
				'#66cc00' => '#66cc00',
				'#6ed961' => '#a7e021',
				'#21cbca' => '#77ebe0',
				'#ee562d' => '#f36d3a',
				'#7a1805' => '#a01604',
				'#2077bf' => '#2077bf',
				'#1b4a1b' => '#1b4a1b',
			];

			return [
				'nav-color'            => $post->campaign_nav_color ?? null,
				'footer-color'         => $footer_color,
				'footer-links-color'   => $footer_links_color,
				'header-color'         => $post->campaign_header_color,
				'header-primary-font'  => $header_primary_font,
				'header-font-weight'   => $special_weight_fonts[ $post->campaign_header_primary ] ?? 400,
				'body-font'            => $body_font,
				'passive-button-color' => $post->campaign_primary_color
					? $passive_button_colors_map[ strtolower( $post->campaign_primary_color ) ]
					: null,
				'primary-color'        => $post->campaign_primary_color,
				'secondary-color'      => $post->campaign_secondary_color,
			];
		}
	}
}
