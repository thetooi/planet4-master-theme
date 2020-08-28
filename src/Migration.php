<?php

namespace P4\MasterTheme;

/**
 * A migration that can be recorded.
 */
abstract class Migration {
	/**
	 * Get a unique identifier, achieved here by using the FQCN.
	 *
	 * @return string The unique identifier.
	 */
	public function get_id(): string {
		return get_called_class();
	}

	/**
	 * Perform the actual migration.
	 */
	abstract public function run(): void;
}
