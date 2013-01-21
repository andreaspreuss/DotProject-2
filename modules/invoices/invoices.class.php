<?php /* PROJECTS $Id: invoices.class.php,v 1.1.1.1 2004/04/01 16:08:45 aardvarkads Exp $ */
/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision: 1.1.1.1 $
*/

require_once( $AppUI->getSystemClass ('dp' ) );
require_once( $AppUI->getLibraryClass( 'PEAR/Date' ) );

/**
 * The Invoice Class
 */
class CInvoice extends CDpObject {
	var $invoice_id = NULL;
	var $invoice_company = NULL;
	var $invoice_grand_total = NULL;
	var $invoice_date = NULL;
	var $invoice_due = NULL;
	var $invoice_terms = NULL;
	var $invoice_status = NULL;

	function CInvoice() {
		$this->CDpObject( 'invoices', 'invoice_id' );
	}

	function store() {
		GLOBAL $AppUI;
		$msg = $this->check();
		if( $msg ) {
			return get_class( $this )."::store-check failed - $msg";
		}
		if( $this->invoice_id ) {
			$this->_action = 'updated';
			$ret = db_updateObject( 'invoices', $this, 'invoice_id', false );
		} else {
			$this->_action = 'added';
			$ret = db_insertObject( 'invoices', $this, 'invoice_id' );

		}
		if( !$ret ) {
			return get_class( $this )."::store failed <br />" . db_error();
		} else {
			return NULL;
		}
	}


	function delete() {
		$sql = "SELECT product_id FROM invoice_product WHERE product_invoice = $this->invoice_id";

		$res = db_exec( $sql );
		if (db_num_rows( $res )) {
			return "You cannot delete a invoice that has products associated with it.";
		} else{
			$sql = "DELETE FROM invoices WHERE invoice_id = $this->invoice_id";
			if (!db_exec( $sql )) {
				return db_error();
			} else {
				return NULL;
			}
		}
	}
}

/**
* CProduct Class
*/
class CProduct extends CDpObject {
	var $product_id = NULL;
	var $product_invoice = NULL;
	var $product_costcode = NULL;
	var $product_name = NULL;
	var $product_qty = NULL;
	var $product_price = NULL;

	function CProduct() {
		$this->CDpObject( 'invoice_product', 'product_id' );
	}
}
?>