<?php
/* Hosting module -> $Id: hosting.class.php,v 1.1 2007/04/16 05:36:02 mbelos Exp $ */

require_once( $AppUI->getSystemClass ('dp' ) );
require_once( $AppUI->getLibraryClass( 'PEAR/Date' ) );
require_once( $AppUI->getModuleClass( 'companies' ) );

/**
 * The Project Class
 */
class CHosting extends CDpObject {
	var $domain_id = null;
	var $company_id = null;
	var $domain_name = null;
	var $domain_expiry_date = null;
	var $domain_registrar = null;
	var $domain_status = null;
	var $domain_notes = null;
	
	var $hosting_id = null;
	var $hosting_package_name = null;
	var $hosting_expiry_date = null;
	var $hosting_status = null;
	var $hosting_notes = null;
	
	function CHosting() {
		$this->CDpObject( 'hosting', 'domain_id' );
	}
	
	function canDelete($message = null, $domain_id = null){
		return true;
	}
	
	function load($domain_id){
		$q = new DBQuery;
		$q->addTable('domains');
		$q->addQuery("domains.domain_id, company_id, domain_name, domain_expiry_date, domain_registrar, domain_status,
			domain_notes, hosting_id, hosting_package_name, hosting_expiry_date, hosting_status, hosting_notes");
		$q->addJoin('hosting', 'h', 'h.domain_id = domains.domain_id');
		$q->addWhere('domains.domain_id = '.$domain_id);
		$results = $q->exec();
		
		$row = db_fetch_assoc($results);
		
		$this->domain_id = $row['domains.domain_id'];
		$this->company_id = $row['company_id'];
		$this->domain_name = $row['domain_name'];
		$this->domain_expiry_date = $row['domain_expiry_date'];
		$this->domain_registrar = $row['domain_registrar'];
		$this->domain_status = $row['domain_status'];
		$this->domain_notes = $row['domain_notes'];
		
		$this->hosting_id = $row['hosting_id'];
		$this->hosting_package_name = $row['hosting_package_name'];
		$this->hosting_expiry_date = $row['hosting_expiry_date'];
		$this->hosting_status = $row['hosting_status'];
		$this->hosting_notes = $row['hosting_notes'];
	}
	
	function delete(){
		$q = new DBQuery;
		$q->setDelete('domains');
		$q->addWhere('domain_id='.$this->domain_id);
		$q->exec();
		$q->clear();
		
		$q->setDelete('hosting');
		$q->addWhere('domain_id='.$this->domain_id);
		$q->exec();
		$q->clear();
	}
	
	function store(){
		$q = new DBQuery;
		$q->addTable('domains');
		$q->addInsert('company_id', $this->company_id);
		$q->addInsert('domain_name', $this->domain_name);
		$q->addInsert('domain_expiry_date', $this->domain_expiry_date);
		$q->addInsert('domain_registrar', $this->domain_registrar);
		$q->addInsert('domain_status', $this->domain_status);
		$q->addInsert('domain_notes', $this->domain_notes);
		$q->exec();
		
		$domainID = db_insert_id();
		
		$q->clear();
		
		$q->addTable('hosting');
		$q->addInsert('domain_id', $domainID);
		$q->addInsert('hosting_package_name', $this->hosting_package_name);
		$q->addInsert('hosting_expiry_date', $this->hosting_expiry_date);
		$q->addInsert('hosting_status', $this->hosting_status);
		$q->addInsert('hosting_notes', $this->hosting_notes);
		$q->exec();
		$q->clear();
	}
}

function getCompanies(){
	// retrieve list of records
	// modified for speed
	// by Pablo Roca (pabloroca@mvps.org)
	// 16 August 2003
	// get the list of permitted companies
	global $AppUI, $buffer, $company, $company_id, $company_prefix, $deny, $department, $dept_ids, $dPconfig, $orderby, $orderdir, $projects, $tasks_critical, $tasks_problems, $tasks_sum, $tasks_summy;
	
	$obj = new CCompany();

	$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
	if(count($companies) == 0) $companies = array(0);	

	// get the list of permitted companies
	$companies = arrayMerge( array( '0'=>$AppUI->_('All') ), $companies );

	//get list of all departments, filtered by the list of permitted companies.
	$q  = new DBQuery;
	$q->clear();
	$q->addTable('companies');
	$q->addQuery('company_id, company_name, dep.*');
	$q->addJoin('departments', 'dep', 'companies.company_id = dep.dept_company');
	$q->addOrder('company_name,dept_parent,dept_name');
	$obj->setAllowedSQL($AppUI->user_id, $q);
	$rows = $q->loadList();

	//display the select list
	$buffer = '<select name="company_id" onChange="document.pickCompany.submit()" class="text">';
	$buffer .= '<option value="company_0" style="font-weight:bold;">'.$AppUI->_('All').'</option>'."\n";
	$company = '';
	foreach ($rows as $row) {
		if ($row["dept_parent"] == 0) {
			if($company!=$row['company_id']){
				$buffer .= '<option value="'.$company_prefix.$row['company_id'].'" style="font-weight:bold;"'.($company_id==$row['company_id']?'selected="selected"':'').'>'.$row['company_name'].'</option>'."\n";
				$company=$row['company_id'];
			}
			if($row["dept_parent"]!=null){
				showchilddept( $row );
				findchilddept( $rows, $row["dept_id"] );
			}
		}
	}
	$buffer .= '</select>';
}

//writes out a single <option> element for display of departments
function showchilddept( &$a, $level=1 ) {
	Global $buffer, $department;
	$s = '<option value="'.$a["dept_id"].'"'.(isset($department)&&$department==$a["dept_id"]?'selected="selected"':'').'>';

	for ($y=0; $y < $level; $y++) {
		if ($y+1 == $level) {
			$s .= '';
		} else {
			$s .= '&nbsp;&nbsp;';
		}
	}

	$s .= '&nbsp;&nbsp;'.$a["dept_name"]."</option>\n";
	$buffer .= $s;

//	echo $s;
}

//recursive function to display children departments.
function findchilddept( &$tarr, $parent, $level=1 ){
	$level = $level+1;
	$n = count( $tarr );
	for ($x=0; $x < $n; $x++) {
		if($tarr[$x]["dept_parent"] == $parent && $tarr[$x]["dept_parent"] != $tarr[$x]["dept_id"]){
			showchilddept( $tarr[$x], $level );
			findchilddept( $tarr, $tarr[$x]["dept_id"], $level);
		}
	}
}

function addDeptId($dataset, $parent){
	Global $dept_ids;
	foreach ($dataset as $data){
		if($data['dept_parent']==$parent){
			$dept_ids[] = $data['dept_id'];
			addDeptId($dataset, $data['dept_id']);
		}
	}
}
?>