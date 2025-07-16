<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once APPPATH . 'libraries/pdf/App_pdf.php';

/**
 *  Delivery pdf
 */
class Delivery_reconcile_pdf extends App_pdf {
	protected $delivery;

	/**
	 * construct
	 * @param object
	 */
	public function __construct($delivery) {

		$delivery = hooks()->apply_filters('request_html_pdf_data', $delivery);
		$GLOBALS['Delivery_reconcile_pdf'] = $delivery;

		parent::__construct();

		$this->delivery = $delivery;

		$this->SetTitle('Stock Reconciliation');

		# Don't remove these lines - important for the PDF layout
		$this->delivery = $this->fix_editor_html($this->delivery);
	}

	/**
	 * prepare
	 * @return
	 */
	public function prepare() {
		$this->set_view_vars('delivery', $this->delivery);

		return $this->build();
	}

	/**
	 * type
	 * @return
	 */
	protected function type() {
		return 'delivery';
	}

	/**
	 * file path
	 * @return
	 */
	protected function file_path() {
		$customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_requestpdf.php';
		$actualPath = APP_MODULES_PATH . '/warehouse/views/stock_reconciliation/deliverypdf.php';

		if (file_exists($customPath)) {
			$actualPath = $customPath;
		}

		return $actualPath;
	}
}