<?php

namespace P4\MasterTheme;

use ArrayIterator;

class MigrationList extends ArrayIterator
{
	private $migrationsDir = __DIR__ . '/Migrations';
	private $migrationsNs  = __NAMESPACE__ . '\\Migrations';
	private $migrationsAbstract = Migration::class;

	public function __construct()
	{
		foreach ( glob( $this->migrationsDir . '/M*.php' ) as $file ) {
			$class_name = $this->migrationsNs . '\\' . pathinfo($file, \PATHINFO_FILENAME);
			
			if ( ! class_exists( $class_name ) 
				|| ! in_array( $this->migrationsAbstract, class_implements( $class_name ) ) 
			) {
				continue;
			}

			$this->append( $class_name );
		}
	}
}
