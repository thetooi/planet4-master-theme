<?php

namespace P4\MasterTheme;

use RuntimeException;
use WP_CLI;

/**
 * Class Loader.
 * Loads all necessary classes for Planet4 Master Theme.
 */
final class Loader {
	/**
	 * A static instance of Loader.
	 *
	 * @var Loader $instance
	 */
	private static $instance;
	/**
	 * Indexed array of all the classes/services that are needed.
	 *
	 * @var array $services
	 */
	private $services;
	/**
	 * Indexed array of all the classes/services that are used by Planet4.
	 *
	 * @var array $default_services
	 */
	private $default_services;

	/**
	 * Singleton creational pattern.
	 * Makes sure there is only one instance at all times.
	 *
	 * @param array $services The Controller services to inject.
	 *
	 * @return Loader
	 */
	public static function get_instance( $services = [] ) : Loader {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self( $services );
		}
		return self::$instance;
	}

	/**
	 * Loader constructor.
	 *
	 * @param array $services The dependencies to inject.
	 */
	private function __construct( $services ) {
		$this->load_services( $services );
		$this->add_filters();
		$this->load_commands();
	}

	/**
	 * Inject dependencies.
	 *
	 * @param array $services The dependencies to inject.
	 */
	private function load_services( $services ) {

		$this->default_services = [
			CustomTaxonomy::class,
			PostCampaign::class,
			PostArchive::class,
			Settings::class,
			Features::class,
			PostReportController::class,
			Cookies::class,
			DevReport::class,
			MasterSite::class,
		];

		if ( is_admin() ) {
			global $pagenow;

			// Load P4 Control Panel only on Dashboard page.
			$this->default_services[] = ControlPanel::class;
			$this->default_services[] = ImageArchive\UiIntegration::class;
			$this->default_services[] = ImageArchive\Rest::class;

			// Load P4 Metaboxes only when adding/editing a new Page/Post/Campaign.
			if ( 'post-new.php' === $pagenow || 'post.php' === $pagenow ) {
				$this->default_services[] = MetaboxRegister::class;
				add_action(
					'cmb2_save_field_p4_campaign_name',
					[ MetaboxRegister::class, 'save_global_project_id' ],
					10,
					3
				);
			}

			// Load `Campaigns` class only when adding/editing a new tag.
			if ( 'edit-tags.php' === $pagenow || 'term.php' === $pagenow ) {
				$this->default_services[] = Campaigns::class;
			}

			// Load `CampaignExporter` class on admin campaign listing page and campaign export only.
			if ( 'campaign' === filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING ) || 'export_data' === filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING ) ) {
				$this->default_services[] = CampaignExporter::class;
			}

			// Load `CampaignImporter` class on admin campaign import only.
			// phpcs:disable
			if ( 'wordpress' === filter_input( INPUT_GET, 'import', FILTER_SANITIZE_STRING ) ) {
				// phpcs:enable
				$this->default_services[] = CampaignImporter::class;
			}
		}

		// Run Activator after theme switched to planet4-master-theme or a planet4 child theme.
		if ( get_option( 'theme_switched' ) ) {
			$this->default_services[] = Activator::class;
		}

		if ( wp_is_json_request() ) {
			$this->default_services[] = MetaboxRegister::class;
		}

		$services = array_merge( $services, $this->default_services );
		if ( $services ) {
			foreach ( $services as $service ) {
				$this->services[ $service ] = new $service();
			}
		}
	}

	/**
	 * Gets the loaded services.
	 *
	 * @return array The loaded services.
	 */
	public function get_services() : array {
		return $this->services;
	}

	/**
	 * Add some filters.
	 *
	 * @return void
	 */
	private function add_filters(): void {
		add_filter( 'pre_delete_post', [ $this, 'do_not_delete_autosave' ], 1, 3 );
	}

	/**
	 * Registers WP_CLI commands.
	 */
	public function load_commands() {
		if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
			return;
		}

		$activator_command = static function ( $args, $assoc_args ) {
			Activator::run();
		};
		WP_CLI::add_command( 'p4-run-activator', $activator_command );

		/**
		 * Put the CF API key into the options table, where the CF plugin uses it from.
		 */
		$put_cf_key_in_db = static function ( $args ) {
			$hostname = $args[0];
			if ( empty( $hostname ) ) {
				WP_CLI::error( 'Please specify the hostname.' );
			}

			if ( ! defined( 'CLOUDFLARE_API_KEY' ) || empty( CLOUDFLARE_API_KEY ) ) {
				WP_CLI::error( 'CLOUDFLARE_API_KEY constant' );
			}

			$domain_parts = explode( '.', $hostname );

			$root_domain = implode( '.', array_slice( $domain_parts, - 2 ) );
			update_option( 'cloudflare_api_key', CLOUDFLARE_API_KEY );
			update_option( 'automatic_platform_optimization', [ 'value' => 1 ] );
			update_option( 'cloudflare_cached_domain_name', $root_domain );
		};
		WP_CLI::add_command( 'p4-cf-key-in-db', $put_cf_key_in_db );
	}

	/**
	 * Due to a bug in WordPress core the "autosave revision" of a post is created and deleted all of the time.
	 * This is pretty pointless and makes it impractical to add any post meta to that revision.
	 * The logic was probably that some space could be saved it is can be determined that the autosave doesn't differ
	 * from the current post content. However that advantage doesn't weigh up to the overhead of deleting the record and
	 * inserting it again, each time burning through another id of the posts table.
	 *
	 * @see https://core.trac.wordpress.org/ticket/49532
	 *
	 * @param null $delete Whether to go forward with the delete (sic, see original filter where it is null initally, not used here).
	 * @param null $post Post object.
	 * @param null $force_delete Is true when post is not trashed but deleted permanently (always false for revisions but they are deleted anyway).
	 *
	 * @return bool|null If the filter returns anything else than null the post is not deleted.
	 */
	public function do_not_delete_autosave( $delete = null, $post = null, $force_delete = null ): ?bool {
		if (
			$force_delete
			|| ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			|| ( isset( $_GET['delete_all'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			|| ! preg_match( '/autosave-v\d+$/', $post->post_name ) ) {

			return null;
		}

		return false;
	}

	/**
	 * @param string $rel_path Relative path to the file.
	 * @return int timestamp of file creation
	 */
	public static function theme_file_ver( string $rel_path ): int {
		$filepath = trailingslashit( get_template_directory() ) . $rel_path;

		return self::get_timestamp( $filepath );
	}

	/**
	 * Get timestamp of a file.
	 *
	 * @param string $path The path of the file.
	 *
	 * @throws RuntimeException If the file doesn't exist, or filectime fails in some other way.
	 * @return int Timestamp of last file change.
	 */
	private static function get_timestamp( string $path ): int {
		$ctime = filectime( $path );

		if ( ! $ctime ) {
			throw new RuntimeException( "Tried to get file change time of {$path} but failed to." );
		}

		return $ctime;
	}

	/**
	 * Enqueue a style with a version based on the file change time.
	 *
	 * @param string $relative_path An existing css file.
	 */
	public static function enqueue_versioned_style( string $relative_path ): void {
		// Create a supposedly unique handle based on the path.
		$handle = str_replace( '/[^\w]/', '', $relative_path );

		$relative_path = '/' . ltrim( $relative_path, '/' );

		$version = self::get_timestamp( get_template_directory() . $relative_path );

		wp_enqueue_style(
			$handle,
			get_template_directory_uri() . $relative_path,
			[],
			$version
		);
	}

	/**
	 * Enqueue a script with a version based on the file change time.
	 *
	 * @param string $relative_path An existing js file.
	 * @param array  $deps Dependencies of the script.
	 * @param bool   $in_footer Whether the script should be loaded in the footer.
	 */
	public static function enqueue_versioned_script( string $relative_path, array $deps = [], $in_footer = false ): void {
		// Create a supposedly unique handle based on the path.
		$handle = str_replace( '/[^\w]/', '', $relative_path );

		$relative_path = '/' . ltrim( $relative_path, '/' );

		$version = self::get_timestamp( get_template_directory() . $relative_path );

		wp_enqueue_script(
			$handle,
			get_template_directory_uri() . $relative_path,
			$deps,
			$version,
			$in_footer
		);
	}
}
