<?php
/**
 * Interface for Settings Integration
 *
 * @package   PUM
 * @copyright Copyright (c) 2022, Code Atlantic LLC
 */

interface PUM_Interface_Integration_Settings extends PUM_Interface_Integration {

	/**
	 * @param array $fields
	 *
	 * @return array
	 */
	public function append_fields( $fields = [] );

}
