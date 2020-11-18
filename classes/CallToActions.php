<?php
/**
 * Call To Action collector.
 *
 * @since       1.14
 * @package     PUM
 * @copyright   Copyright (c) 2020, Code Atlantic LLC
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PUM_CallToActions
 *
 * This class maintains a global set of all registered PUM calls to action.
 */
class PUM_CallToActions {

	/**
	 * Static Instance
	 *
	 * @var PUM_CallToActions
	 */
	private static $instance;

	/**
	 * Holds array of registered $ctas.
	 *
	 * @var array
	 */
	private $calltoactions = [];

	/**
	 * Main PUM_Shortcodes Instance
	 *
	 * @return PUM_Shortcodes
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PUM_Shortcodes ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get all registered call to actions.
	 *
	 * @return array Array of conditions.
	 */
	public function get_all() {
		if ( ! isset( $this->calltoactions ) ) {
			$this->register_all();
		}

		return $this->calltoactions;
	}

	/**
	 * Registers call to actions.
	 *
	 * @return void
	 */
	public function register_all() {
		$ctas = apply_filters(
			'pum_registered_ctas',
			[
				new PUM_CallToAction_Link(),
			]
		);

		foreach ( $ctas as $callToAction ) {
			$this->add( $callToAction );
		}
	}

	/**
	 * Add a call to action object to the collection.
	 *
	 * @param PUM_Interface_CallToAction $callToAction Instance of a call to action.
	 */
	public function add( PUM_Interface_CallToAction $callToAction ) {
		$this->calltoactions[ $callToAction->key() ] = $callToAction;
	}

	/**
	 * Get cta by key.
	 *
	 * @param string $key Key of the cta to retrieve.
	 *
	 * @return bool|PUM_Interface_CallToAction
	 */
	public function get( $key ) {
		return isset( $this->calltoactions[ $key ] ) ? $this->calltoactions[ $key ] : false;
	}

}