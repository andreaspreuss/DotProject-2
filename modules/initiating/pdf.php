<?php
// chama a classe 'class.ezpdf.php' necessária para se gerar o documento
//include "lib/ezpdf/class.ezpdf.php"; 
$font_dir = DP_BASE_DIR.'/lib/ezpdf/fonts';
require($AppUI->getLibraryClass('ezpdf/class.ezpdf'));

$id=intval(dPgetParam($_GET, 'id', 0));

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('initiating');
$q->addWhere('initiating_id = ' . $id);

$obj = new CInitiating();

// load the record data
$obj = null;
if (!db_loadObject($q->prepare(), $obj) && $id > 0) {
	$AppUI->setMsg('Initiating');
	$AppUI->setMsg("invalidID", UI_MSG_ERROR, true);
	$AppUI->redirect();
}

$q = new DBQuery();
$q->addQuery('*');
$q->addTable('contacts');
$q->addWhere('contact_id = ' . $obj->initiating_manager);
$contact = $q->loadHash();

// instancia um novo documento com o nome de pdf
$pdf = new Cezpdf();

// seta a fonte que será usada para apresentar os dados
//essas fontes são aquelas dentro do diretório GeraPDF/fonts
//$pdf->selectFont('lib/ezpdf/Helvetica.afm'); 
$pdf->selectFont("$font_dir/Helvetica.afm");

// chama o método ezText e passa o texto que deverá ser apresentado no documento
//o numero após o texto se refere ao tamanho da fonte
$pdf->ezText("\n");
$pdf->ezText("<b>Termo de abertura de projeto</b>",18,array('justification'=>'center')); 
$pdf->ezText('');
$pdf->ezText('');
$pdf->ezText("<b>Titulo do Projeto: </b>" . $obj->initiating_title,16);
$pdf->ezText('');
$pdf->ezText("<b>Stakeholder: </b>" . $contact['contact_first_name'] . " " .  $contact['contact_last_name'],16);
$pdf->ezText('');
$pdf->ezText("<b>Justificativa: </b>" . $obj->initiating_justification,16);
$pdf->ezText('');
$pdf->ezText("<b>Objetivos: </b>" . $obj->initiating_objective,16);
$pdf->ezText('');
$pdf->ezText("<b>Resultados Esperados: </b>" . $obj->initiating_expected_result,16);
$pdf->ezText('');
$pdf->ezText("<b>Premissas: </b>" . $obj->initiating_premise,16);
$pdf->ezText('');
$pdf->ezText("<b>Restrições: </b>" . $obj->initiating_restrictions,16);
$pdf->ezText('');
$pdf->ezText("<b>Orçamento: </b>" . $obj->initiating_budget,16);
$pdf->ezText('');
$pdf->ezText("<b>Data de Início: </b>" . $obj->initiating_start_date,16);
$pdf->ezText('');
$pdf->ezText("<b>Data Final: </b>" . $obj->initiating_end_date,16);
$pdf->ezText('');
$pdf->ezText("<b>Marcos: </b>" . $obj->initiating_milestone,16);
$pdf->ezText('');
$pdf->ezText("<b>Critérios para Sucesso: </b>" . $obj->initiating_success,16);
$pdf->ezText('');
$pdf->ezText('');
$pdf->ezText("<b>Assinaturas</b>",16,array('justification'=>'center')); 

// gera o PDF
$pdf->ezStream(); 
?>