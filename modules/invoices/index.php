<?php  /* INVOICES $Id: index.php,v 1.1.1.1 2004/04/01 16:08:45 aardvarkads Exp $ */
$AppUI->savePlace();

// load the companies class to retrieved denied companies
require_once( $AppUI->getModuleClass( 'companies' ) );

// retrieve any state parameters
if (isset( $_GET['tab'] )) {
	$AppUI->setState( 'InvIdxTab', $_GET['tab'] );
}
$tab = $AppUI->getState( 'InvIdxTab' ) !== NULL ? $AppUI->getState( 'InvIdxTab' ) : 0;
$active = intval( !$AppUI->getState( 'InvIdxTab' ) );

if (isset( $_GET['orderby'] )) {
	$AppUI->setState( 'InvIdxOrderBy', $_GET['orderby'] );
}
$orderby = $AppUI->getState( 'InvIdxOrderBy' ) ? $AppUI->getState( 'InvIdxOrderBy' ) : 'invoice_id desc';

// get any records denied from viewing
$obj = new CInvoice();
$deny = $obj->getDeniedRecords( $AppUI->user_id );

// retrieve list of records
$sql = "
SELECT
        invoice_id, invoice_status,
	invoice_date, invoice_due,
	invoice_terms,
	invoice_company, company_name, invoice_status,
	user_username,
        SUM(t1.product_price*t1.product_qty) as invoice_grand_total
FROM permissions,invoices
LEFT JOIN companies ON company_id = invoices.invoice_company
LEFT JOIN users ON invoices.invoice_owner = users.user_id
LEFT JOIN invoice_product t1 ON invoices.invoice_id = t1.product_invoice
WHERE permission_user = $AppUI->user_id
	AND permission_value <> 0
	AND (
		(permission_grant_on = 'all')
		OR (permission_grant_on = 'invoices' AND permission_item = -1)
		OR (permission_grant_on = 'invoices' AND permission_item = invoice_id)
		)"
.(count($deny) > 0 ? "\nAND invoice_id NOT IN (" . implode( ',', $deny ) . ')' : '')
.($company_id ? "\nAND invoice_company = $company_id" : '')
."
GROUP BY invoice_id
ORDER BY $orderby
";

$invoices = db_loadList( $sql );

// get the list of permitted companies
$obj = new CCompany();
$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
$companies = arrayMerge( array( '0'=>$AppUI->_('All') ), $companies );

// setup the title block
$titleBlock = new CTitleBlock( 'Invoices', 'applet3-48.png', $m, "$m.$a" );
$titleBlock->addCell( $AppUI->_('Company') . ':' );
$titleBlock->addCell(
	arraySelect( $companies, 'company_id', 'onChange="document.pickCompany.submit()" class="text"', $company_id ), '',
	'<form action="?m=invoices" method="post" name="pickCompany">', '</form>'
);
$titleBlock->addCell();
if ($canEdit) {
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new invoice').'">', '',
		'<form action="?m=invoices&a=addedit" method="post">', '</form>'
	);
}
$titleBlock->show();

// tabbed information boxes
$tabBox = new CTabBox( "?m=invoices&orderby=$orderby", "{$AppUI->cfg['root_dir']}/modules/invoices/", $tab );
$tabBox->add( 'vw_idx_open', 'Open Invoices' );
$tabBox->add( 'vw_idx_paid'  , 'Paid Invoices' );
$tabBox->show();
?>
