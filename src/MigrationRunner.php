<?php

namespace P4\MasterTheme;

class MigrationRunner
{
	public function run(MigrationList $list, MigrationLog $log)
	{
		foreach ( $list as $migration ) {
			if ( $log->already_ran( $migration ) ) {
				continue;
			}

			$migration->run();
			$log->register( $migration );
		}

		$log->persist();
	}
}
