<?php

/**
 * Class Thim_System_Status.
 *
 * @since 0.8.5
 */
class Thim_Main_Dashboard extends Thim_Admin_Sub_Page {
	/**
	 * @var string
	 *
	 * @since 0.8.5
	 */
	public $key_page = 'dashboard';

	/**
	 * Thim_System_Status constructor.
	 *
	 * @since 0.8.5
	 */
	protected function __construct() {
		parent::__construct();

		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 0.8.5
	 */
	private function init_hooks() {
		add_filter( 'thim_dashboard_sub_pages', array( $this, 'add_sub_page' ) );
	}

	/**
	 * Add sub page.
	 *
	 * @since 0.8.5
	 *
	 * @param $sub_pages
	 *
	 * @return mixed
	 */
	public function add_sub_page( $sub_pages ) {
		$sub_pages['dashboard'] = array(
			'title' => __( 'Dashboard', 'thim-core' ),
		);

		return $sub_pages;
	}
}