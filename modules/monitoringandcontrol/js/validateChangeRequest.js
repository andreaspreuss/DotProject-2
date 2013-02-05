function validateChangeRequest(){	

	if(document.form_ata.impact.value==""){
		document.form_ata.impact.focus();
		alert('Fill the field');
		return false;	
	}
	if(document.form_ata.status.value==0){
		document.form_ata.status.focus();	
		alert('Fill the field');
		return false;	
	}		
	if(document.form_ata.description.value==""){
		document.form_ata.description.focus();
		alert('Fill the field');
		return false;	
	}
	if(document.form_ata.cause.value==""){
		document.form_ata.cause.focus();
		alert('Fill the field');
		return false;	
	}
	if(document.form_ata.acao_corretiva.value==""){
		document.form_ata.acao_corretiva.focus();
		alert('Fill the field');
		return false;	
	}	
	if(document.form_ata.user.value=="Selecione..."){
		document.form_ata.user.focus();
		alert('Fill the field');
		return false;	
	}
	if(document.form_ata.date_limit.value==""){
		document.form_ata.date_limit.focus();
		alert('Fill the field');
		return false;	
	}
	document.form_ata.submit();
} 