<?php

namespace P4\MasterTheme;

class MigrationLog
{
	/**
	 * The WP option key.
	 */
	private const OPTION_KEY = 'planet4_migrations';

	/**
	 * @var array[] An entry for each done migration.
	 */
	private $done_migrations;
	
	/**
	 * Get the log from the WP options.
	 *
	 * @return static The log.
	 */
	public static function from_wp_options(): self {
		$done_migrations = get_option( self::OPTION_KEY, [] );

		$log = new self();
		$log->done_migrations = $done_migrations;

		return $log;
	}
	
	public function already_ran( Migration $migration ): bool {
		foreach ( $this->done_migrations as $done ) {
			if ( $done['id'] === $migration->get_id() ) {
				return true;
			}
		}

		return false;
	}

	public function register( Migration $migration ): void {
		$entry = [
			'id'   => $migration->get_id(),
			'date' => time(),
		];

		$this->done_migrations[] = $entry;
	}

	public function persist(): void {
		add_option( self::OPTION_KEY, $this->done_migrations );
	}
}
