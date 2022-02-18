<?php 
	// INCLUDES
	include_once("STutilsExtForm.php");
	include_once("STscripts.js");
	
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

	$strCLIENTE = getCliente(); //Nome do cliente usando o path da pasta
	
$objConn = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../../_tradeunion/_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - ".getTText("cad_novo_filiado",C_NONE));?></title>
<style type="text/css">
	.span_manual{
		float:right;
		background-image:url(../../_tradeunion/img/icon_document_pdf.png);
		background-repeat:no-repeat;
		background-position:right;
		height:15px;
		padding-top: 2px;
		padding-right:20px;
		margin-right:5px;
		cursor:pointer;
		font-size:10px;
		color:#009900;
		font-weight:600;
	}
</style>
<script>
<!--

function checkCPF(prCPF, prAviso) {
	if(prCPF != ""){
		var auxBoolean = false;
		var strChars   = "";
		for(auxCounter = 0; auxCounter < prCPF.length; auxCounter++){
			if(auxCounter > 0){
				strChars = prCPF.charAt([auxCounter]-1);
				if(strChars != prCPF.charAt([auxCounter])){
					auxBoolean = true;
				}
			} else{
				strChars = prCPF.charAt([auxCounter]);
			}
			//alert(prCPF.charAt([auxCounter]));
		}
		if(!auxBoolean) { if(prAviso){ alert("CPF Inválido"); } return(false); }
		
		var x = 0;
		var soma = 0;
		var dig1 = 0;
		var dig2 = 0;
		var texto = "";
		var strCPFaux = "";
		var len = prCPF.length;
		var strAux1, strAux2;
		
   	    if (len < 11) {	if (prAviso) alert("CPF Inválido"); return false; }
		
		strAux1 = prCPF.substring(0, 3);
		strAux2 = prCPF.substring(8, 11);
		
		//Se começa e termina com 999 é porque é um CPF de estrangeiro
		if ((strAux1 == "999") && (strAux2 == "999")) {	return true; }
		else {
			x = len -1;
			
			for (var i=0; i <= len - 3; i++) {
				y = prCPF.substring(i,i+1);
				soma = soma + ( y * x);
				x = x - 1;
				texto = texto + y;
			}
			
			dig1 = 11 - (soma % 11);
			if (dig1 == 10) dig1=0 ;
			if (dig1 == 11) dig1=0 ;
			strCPFaux = prCPF.substring(0,len - 2) + dig1 ;	
			x = 11; soma=0;
			
			for (var i=0; i <= len - 2; i++) { soma = soma + (strCPFaux.substring(i,i+1) * x); x = x - 1; }
			
			dig2 = 11 - (soma % 11);
			if (dig2 == 10) dig2=0;
			if (dig2 == 11) dig2=0;
			if ((dig1 + "" + dig2) == prCPF.substring(len,len-2)) { return true; }
			else { if (prAviso) alert("CPF Inválido"); return false; }
		}
	}	
	else return false;
}

function validaCampos(){
	// Esta função faz uma pré-validação via
	// js dos campos marcados com asterisco
	var strMSG  = "";
	// Tratamento contra campos vazios
	// GUARDA PARA DADOS DA EMPRESA
	// alert(document.getElementById('dbvar_str_data_fundacao').value);
	// alert(getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_fundacao').value,'ptb')));
	strMSG += (
			   (document.getElementById('dbvar_str_cpf').value 				 == "")	||
			   //(checkCPF(document.getElementById('dbvar_str_cpf').id,false) != true) 	||
			   (document.getElementById('dbvar_str_nome').value 		 == "")	||
			   (document.getElementById('dbvar_str_email').value 				 == "")	||
			   (document.getElementById('dbvar_str_rg').value 				 == "")	||
			   (document.getElementById('dbvar_str_rg_orgao').value 				 == "")	||
			   (document.getElementById('dbvar_str_data_nasc').value 				 == "")	||
			   (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_nasc').value,'ptb'))>0) ||
			   (document.getElementById('dbvar_str_nacionalidade').value 				 == "")	||
			   (document.getElementById('dbvar_str_sexo').value 				 == "")	||
			   (document.getElementById('dbvar_str_img_logo').value 				 == "")	||
			   (document.getElementById('dbvar_str_estado_civil').value 				 == "")			   		   
			   ) ? "\n\nDADOS PESSOAIS:" : "";
	strMSG += (document.getElementById('dbvar_str_cpf').value 					== "") ? "\nCPF" 				: "";
	strMSG += (document.getElementById('dbvar_str_nome').value 				== "") ? "\nNome"	: "";
	strMSG += (document.getElementById('dbvar_str_email').value 				== "") ? "\nE-mail"	: "";
	strMSG += (document.getElementById('dbvar_str_rg').value 				== "") ? "\nRG"	: "";
	strMSG += (document.getElementById('dbvar_str_rg_orgao').value 				== "") ? "\nRG Orgão"	: "";
	strMSG += (document.getElementById('dbvar_str_data_nasc').value 				== "") ? "\nData Nascimento"	: "";
	strMSG += (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_nasc').value,'ptb'))>0) ? "\nData de Nascimento Maior que Data Atual"	: "";
	strMSG += (document.getElementById('dbvar_str_nacionalidade').value 				== "") ? "\nNacionalidade"	: "";
	strMSG += (document.getElementById('dbvar_str_sexo').value 				== "") ? "\nSexo"	: "";		
	strMSG += (document.getElementById('dbvar_str_img_logo').value 				== "") ? "\nFoto"	: "";			
	strMSG += (document.getElementById('dbvar_str_estado_civil').value 				== "") ? "\nEstado Civil"	: "";		
	
	strMSG += (
			   (document.getElementById('dbvar_str_cep').value 					== "") ||
			   (document.getElementById('dbvar_str_logradouro').value 			== "") ||
			   (document.getElementById('dbvar_str_numero').value 				== "") ||
			   (document.getElementById('dbvar_str_bairro').value 				== "") ||
			   (document.getElementById('dbvar_str_cidade').value 				== "") ||
			   (document.getElementById('dbvar_str_uf').value 					== "") ||
			   (document.getElementById('dbvar_str_telefone').value 			== "")
			   ) ? "\n\nENDEREÇO PRINCIPAL:" : "";
	strMSG += (document.getElementById('dbvar_str_cep').value 					== "") ? "\nCep" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_logradouro').value 			== "") ? "\nLogradouro" 		: "" ;
	strMSG += (document.getElementById('dbvar_str_numero').value 				== "") ? "\nNúmero" 			: "" ;
	strMSG += (document.getElementById('dbvar_str_bairro').value 				== "") ? "\nBairro" 			: "" ;
	strMSG += (document.getElementById('dbvar_str_cidade').value 				== "") ? "\nCidade" 			: "" ;
	strMSG += (document.getElementById('dbvar_str_uf').value 					== "") ? "\nEstado / UF" 		: "" ;
	strMSG += (document.getElementById('dbvar_str_telefone').value 				== "") ? "\nTelefone Um (1)" 	: "" ;
	
	
	strMSG += (
			   (document.getElementById('dbvar_str_senha').value 				== "") ||
			   (document.getElementById('dbvar_str_senha').value.length 		 <  6) ||
			   (document.getElementById('dbvar_str_senha_confirma').value 		== "") ||
			   (document.getElementById('dbvar_str_senha_confirma').value != document.getElementById('dbvar_str_senha').value)
			   ) ? "\n\nDADOS DE LOGIN:" : "";
	strMSG += (document.getElementById('dbvar_str_senha').value 				== "") ? "\nSenha" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_senha').value.length 			 <  6) ? "\nQuantidade de Caracteres da senha" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_senha_confirma').value != document.getElementById('dbvar_str_senha').value) ? "\nSenhas não Conferem!" : "";
	strMSG += (document.getElementById('dbvar_str_senha_confirma').value 		== "") ? "\nConfirmação de Senha" : "" ;
	
	strMSG += (
			   (document.getElementById('dbvar_str_autorizacao1').value 				!= "Sim") ||
			   (document.getElementById('dbvar_str_autorizacao2').value 				!= "Sim") 
			   ) ? "\n\nDADOS DE AUTORIZAÇÃO:" : "";
	strMSG += (document.getElementById('dbvar_str_autorizacao1').value 				!= "Sim") ? "\nVocê precisa concordar com as informações e obrigações do estatuto!" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_autorizacao2').value 				!= "Sim") ? "\nVocê precisa concordar com o compartilhamento de suas informações no sistema de busca da ABFM!" 				: "" ;

	
	
	strMSG += (
			   (document.getElementById('dbvar_str_curriculo_resumido').value 					== "") ||
			   (document.getElementById('dbvar_str_curriculo').value 			== "") ||
			   (document.getElementById('dbvar_str_diploma').value 				== "") 

			   ) ? "\n\nDADOS PROFISSIONAIS:" : "";
	strMSG += (document.getElementById('dbvar_str_curriculo_resumido').value 					== "") ? "\nCurrículo Resumido" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_curriculo').value 			== "") ? "\nArquivo Currículo" 		: "" ;
	strMSG += (document.getElementById('dbvar_str_diploma').value 				== "") ? "\nArquivo Comprovante ou Diploma" 			: "" ;

	
	
	
	strMSG += (
			   (document.getElementById('dbvar_str_num_socio1').value 			== "") ||			   
			   (document.getElementById('dbvar_str_declaracao_socio1').value 			== "") ||
   			   (document.getElementById('dbvar_str_categoria').value 					== "") 
			   ) ? "\n\nDADOS ASSOCIATIVOS:" : "";
	strMSG += (document.getElementById('dbvar_str_num_socio1').value 					== "") ? "\nNúmero Sócio Proponente 1" 				: "" ;
	strMSG += (document.getElementById('dbvar_str_declaracao_socio1').value 				== "") ? "\nArquivo Declaração Sócio Proponente 1" 			: "" ;
	strMSG += (document.getElementById('dbvar_str_categoria').value 				== "") ? "\nInforme uma Categoria" 			: "" ;	
	
	
	strMSG += (
			   (document.getElementById('dbvar_str_categoria').value 					!= "19") && 
   			   (
			   	(document.getElementById('dbvar_str_num_socio2').value 			== "") ||			   
			   	(document.getElementById('dbvar_str_declaracao_socio2').value 				== "") 
			   )
			   ) ? "\n\nDADOS ASSOCIATIVOS:" : "";
	strMSG += ((document.getElementById('dbvar_str_categoria').value 					!= "19") && (document.getElementById('dbvar_str_num_socio2').value != "") && (document.getElementById('dbvar_str_num_socio1').value == document.getElementById('dbvar_str_num_socio2').value)) ? "\nInforme um Sócio 2 diferente" 				: "" ;
	strMSG += ((document.getElementById('dbvar_str_categoria').value 					!= "19") && (document.getElementById('dbvar_str_num_socio2').value 			== "")) ? "\nNúmero Sócio Proponente 2" 		: "" ;
	strMSG += ((document.getElementById('dbvar_str_categoria').value 					!= "19") && (document.getElementById('dbvar_str_declaracao_socio2').value 				== "")) ? "\nArquivo Declaração Sócio Proponente 2" 			: "" ;	

	
	
	
	if(strMSG != ""){ alert('Os seguintes campos não foram preenchidos:'+strMSG); return(false); }
	else { return(true); }
}

function ok(){
	if(validaCampos()){

		document.getElementById('forminsert').submit();
	} else{
		return(false);
	}
}

function cancelar(){
	window.history.back();
}


		

// VERIFICA SE PJ JÁ EXISTE
function ajaxVerificaPF(){
	var objAjax;
	var strReturnValue;
	var strDB;
	var strSQL;
	// Tratamento BREVE, caso o nome de usuário 
	// esteja vazio ou nulo, retorno nulo
	if(document.getElementById('dbvar_str_cpf').value == null || document.getElementById('dbvar_str_cpf').value == ""){
		return(null);
	}
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cod_pf, nome FROM cad_pf WHERE cpf = '"+ document.getElementById('dbvar_str_cpf').value +"'"; "SELECT cod_usuario FROM sys_usuario WHERE id_usuario = '"+ document.getElementById('dbvar_str_cpf').value +"';";
	strDB   = "<?php echo($strDB);?>"
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_empresa').innerHTML = "<img src='../../_tradeunion/img/icon_ajax_loader.gif' border='0' width='13' />";
	document.getElementById('user_cpf').innerHTML = document.getElementById('dbvar_str_cpf').value;
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				//alert(strReturnValue);
				//alert(prSQL);
				// verifica se retornou dados
				if(strReturnValue.indexOf('|') != -1){
					document.getElementById('loader_empresa').innerHTML = "<br/><span style='color:red;'>(O CPF <em><b>"+ document.getElementById('dbvar_str_cpf').value +"</b></em>&nbsp; JÁ ESTÁ CADASTRADO NO SISTEMA)</span>";
					document.getElementById('dbvar_str_cpf').value = "";
					// alert('Esta empresa já está CADASTRADA!');
				}
				setTimeout("document.getElementById('loader_empresa').innerHTML = ''",3000);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../../_tradeunion/_ajax/returndadosexterna.php?var_sql="+strSQL+"&var_db="+strDB,true); 
	objAjax.send(null); 
}



function ajaxVerificaSocio1(){
	var objAjax;
	var strReturnValue;
	var strDB;
	var strSQL;
	// Tratamento BREVE, caso o nome de usuário 
	// esteja vazio ou nulo, retorno nulo
	if(document.getElementById('dbvar_str_num_socio1').value == null || document.getElementById('dbvar_str_num_socio1').value == ""){
		return(null);
	} 
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cod_pf, nome FROM cad_pf WHERE old_entidade = '"+ document.getElementById('dbvar_str_num_socio1').value +"' and dtt_inativo is null LIMIT 1"; 
	strDB   = "<?php echo($strDB);?>"
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_socio1').innerHTML = "<img src='../../_tradeunion/img/icon_ajax_loader.gif' border='0' width='13' />";

	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				var arrResult = strReturnValue.split("|")
				//alert(strReturnValue);
				//alert(prSQL);
				// verifica se retornou dados
				if(strReturnValue != "") {
					document.getElementById('nome_socio1').innerHTML = arrResult[1];
				} else {
					document.getElementById('loader_socio1').innerHTML = "<br/><span style='color:red;'>(Sócio não localizado - Núm. ABFM: <em><b>"+ document.getElementById('dbvar_str_num_socio1').value +"</b></em></span>";
					document.getElementById('dbvar_str_num_socio1').value = "";
					document.getElementById('nome_socio1').innerHTML = "";
					// alert('Esta empresa já está CADASTRADA!');
				}
				
				setTimeout("document.getElementById('loader_socio1').innerHTML = ''",100);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../../_tradeunion/_ajax/returndadosexterna.php?var_sql="+strSQL+"&var_db="+strDB,true); 
	objAjax.send(null); 
}
		
function ajaxVerificaSocio2(){
	var objAjax;
	var strReturnValue;
	var strDB;
	var strSQL;
	// Tratamento BREVE, caso o nome de usuário 
	// esteja vazio ou nulo, retorno nulo
	if(document.getElementById('dbvar_str_num_socio2').value == null || document.getElementById('dbvar_str_num_socio2').value == ""){
		return(null);
	} 
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cod_pf, nome FROM cad_pf WHERE old_entidade = '"+ document.getElementById('dbvar_str_num_socio2').value +"' and dtt_inativo is null LIMIT 1"; 
	strDB   = "<?php echo($strDB);?>"
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_socio2').innerHTML = "<img src='../../_tradeunion/img/icon_ajax_loader.gif' border='0' width='13' />";

	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				var arrResult = strReturnValue.split("|")
				//alert(strReturnValue);
				//alert(prSQL);
				// verifica se retornou dados
				if(strReturnValue != "") {
					document.getElementById('nome_socio2').innerHTML = arrResult[1];
				} else {
					document.getElementById('loader_socio2').innerHTML = "<br/><span style='color:red;'>(Sócio não localizado - Núm. ABFM: <em><b>"+ document.getElementById('dbvar_str_num_socio2').value +"</b></em></span>";
					document.getElementById('dbvar_str_num_socio2').value = "";
					document.getElementById('nome_socio2').innerHTML = "";
					// alert('Esta empresa já está CADASTRADA!');
				}
				
				setTimeout("document.getElementById('loader_socio2').innerHTML = ''",100);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../../_tradeunion/_ajax/returndadosexterna.php?var_sql="+strSQL+"&var_db="+strDB,true); 
	objAjax.send(null); 
}


		
// VERIFICA SE USUARIO JÁ EXISTE
function ajaxVerificaUSER(){
	var objAjax;
	var strReturnValue;
	var strDB;
	var strSQL;
	// Tratamento BREVE, caso o nome de usuário 
	// esteja vazio ou nulo, retorno nulo
	if(document.getElementById('dbvar_str_usuario').value == null || document.getElementById('dbvar_str_usuario').value == ""){
		return(null);
	}
	// Seta o SQL, cria o AJAX
	strSQL  = "SELECT cod_usuario FROM sys_usuario WHERE id_usuario = '"+ document.getElementById('dbvar_str_usuario').value +"';";
	strDB   = "<?php echo($strDB);?>"
	objAjax = createAjax();
	// Coloca LOADER
	document.getElementById('loader_usuario').innerHTML = "<img src='../../_tradeunion/img/icon_ajax_loader.gif' border='0' width='13' />";
	objAjax.onreadystatechange = function() {
		if(objAjax.readyState == 4) {
			if(objAjax.status == 200) {
				strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
				// alert(strReturnValue);
				// alert(prSQL);
				// imgstatus_fechado.gif
				// icon_wrong.gif
				// verifica se retornou dados
				if(strReturnValue.indexOf('|') != -1){
					document.getElementById('loader_usuario').innerHTML = "<span style='color:red;'>(<em><b>"+ document.getElementById('dbvar_str_usuario').value +"</b></em>&nbsp; NÃO ESTÁ DISPONÍVEL)</span>";
				} else{
					document.getElementById('loader_usuario').innerHTML = "<span style='color:green;'>(<em><b>"+ document.getElementById('dbvar_str_usuario').value +"</b></em>&nbsp; está DISPONÍVEL)</span>";
				}
				setTimeout("document.getElementById('loader_usuario').innerHTML = ''",3000);
			}
			else {
				alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
			}
		}
	}
	objAjax.open("GET", "../../_tradeunion/_ajax/returndadosexterna.php?var_sql=" + strSQL +"&var_db="+ strDB,true); 
	objAjax.send(null); 
}

-->

function setRegiaoAtuacao(campo) {	
  if (campo.checked == true) {
	  campo.value = 1;
  }
  if (campo.checked == false) {
  	  campo.value = 0;
  }
}

</script>
</head>
<body bgcolor="#F5F5F5" background="../../_tradeunion/img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_collapsed.jpg">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
		<?php athBeginFloatingBox("720","","<span class='span_manual' onClick=\"AbreJanelaPAGE('../../".$strCLIENTE."/manuais/manual_cadastro_filiada.pdf',700,500);\" title='".getTText("download_manual_explicativo",C_NONE)."'></span><span style='font-weight:bold;'>".strtoupper(CFG_SYSTEM_NAME)." | ".strtoupper(ucwords(str_replace(CFG_SYSTEM_NAME."_","",$strDB)))." - (".getTText("cad_novo_filiado",C_NONE).")",CL_CORBAR_GLASS_1); ?>
			<table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
			<form name="formeditor_000" id="forminsert" action="STcadNovoSocioexec.php" method="post" enctype="multipart/form-data">
				<input type="hidden" id="var_db" name="var_db" value="<?php echo($strDB);?>" />

                <!--//-->
                <!--//-->

				<tr>
					<td align="center" valign="top" style="padding:0px 80px 0px 80px;"> 
						<table width="100%" cellpadding="3" cellspacing="0">
                        <!-- <tr><td colspan="2" align="right" ><img src="https://tradeunion.proevento.com.br/_tradeunion/img/LogoMarca_ABFM.gif" /></td></tr> //-->
                        
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" height="40" class="destaque_gde"><strong><?php echo(getTText("dados_empresa",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                             <tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="25%" align="right" valign="top"><b>*<?php echo(getTText("cpf",C_NONE));?>:</b></td>
								<td width="75%">
									<input type="text" name="dbvar_str_cpf" id="dbvar_str_cpf" style="width:90px;" maxlength="11" value="<?php echo request('dbvar_str_cpf') ?>" onBlur="javascript:if(!checkCPF(this.value, true)) this.value='';ajaxVerificaPF();" onKeyPress="return validateNumKey(event);">
									&nbsp;<span id="loader_empresa"></span>
								</td>
							</tr>
                              <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("email",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_email" id="dbvar_str_email" maxlength="255" value="<?php echo request('dbvar_str_email'); ?>"  onBlur="javascript:if(!validateEmail(this.value,true)) this.value='';" style="width:280px;"></td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nome",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_nome" id="dbvar_str_nome" maxlength="120" value="<?php echo request('dbvar_str_nome'); ?>" style="width:280px;"></td>
							</tr>							
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("img_foto",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_img_logo" id="dbvar_str_img_logo" size="40" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_img_logo','/abfm/upload/fotospf/','','');">
									<br><span class="comment_med">A foto deve ser em formato 3x4 (vertical) com resolução de 300 DPI</span>
								</td>
							</tr>
                           
                          
							
							
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("rg",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_rg" id="dbvar_str_rg" value="<?php echo (request('dbvar_str_rg')); ?>" style="text-transform:uppercase; width:120px;">
									&nbsp;*Orgão Emissor: <input type="text" name="dbvar_str_rg_orgao" id="dbvar_str_rg_orgao" value="<?php echo (request('dbvar_str_rg_orgao')); ?>" style="text-transform:uppercase; width:50px;">
								</td>
							</tr>
							
                            
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("data_nasc",C_UCWORDS));?>:</b></td>
								<td>
                                <input type="text" name="dbvar_str_data_nasc" id="dbvar_str_data_nasc" maxlength="10" value="<?php echo request('dbvar_str_data_nasc'); ?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" style="width:70px;" />
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nacionalidade",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_nacionalidade" id="dbvar_str_nacionalidade" maxlength="255" value="<?php echo request('dbvar_str_nacionalidade'); ?>"  style="width:200px;"></td>
							</tr>
                            <tr>
									<td align="right"><b>*<?php echo(getTText("genero",C_UCWORDS));?>:</b></td>
									<td>										
										<select name="dbvar_str_sexo" id="dbvar_str_sexo" style="width:100px;">
                                          <option value="" selected>Selecione</option>
										  <option value="F">Feminino</option>
  										  <option value="M">Masculino</option>
                                          <option value="I">Prefiro não informar</option>
										</select>                                        
                                       
                                        
									</td>
							</tr>
                             
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("estado_civil",C_UCWORDS));?>:</b></td>
									<td>										
										<select name="dbvar_str_estado_civil" id="dbvar_str_estado_civil" style="width:100px;">
                                          <option value="" selected>Selecione</option>                                        
										  <option value="Casado(a)">Casado(a)</option>
  										  <option value="Solteiro(a)">Solteiro(a)</option>
                                          <option value="Divorciado(a)">Divorciado(a)</option>
   										  <option value="Viúvo(a)">Viúvo(a)</option>
										</select>                                        
                                        
									</td>
							</tr>  
                            <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"><b>*<?php echo(getTText("regiao_atuacao",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td></td>
									<td>							
                                    <table width="100%">
                                    	<tr valign="top">
                                        <td  width="50%">
                                        <table>
                                    	<tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_nordeste" onclick="setRegiaoAtuacao(this)">
											</td>
                                            <td align="left">Nordeste</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_norte" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Norte</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_centro_oeste" onclick="setRegiaoAtuacao(this)">
											</td>
											<td align="left">Centro-oeste</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_sudeste" onclick="setRegiaoAtuacao(this)">
											</td>
											<td align="left">Sudeste</td>	</tr>
                                        </table>
                                        </td>
                                        <td  width="50%">
                                        <table>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_sul" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Sul</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_exterior" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Não atuo no Brasil</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_regiao_outro" onclick="setRegiaoAtuacao(this)">
											</td>

											<td align="left">Outro</td>	</tr>                
                                        </table>
                                        </td>
                                        </tr>                    
                                          
                                        </table>
									</td>
							</tr>  
                            <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"></td>
                            </tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
                            	<td></td>
								<td align="left"><b>*<?php echo(getTText("area_atuacao",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td></td>
									<td width="">				
                                    <table>
                                    	<tr valign="top">
                                        <td>
                                        <table width="100%">
                                    	<tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_radioterapia" onclick="setRegiaoAtuacao(this)">
											</td>
                                            <td  align="left">Radioterapia</td>											
										</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_radiodiagnostico" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Radiodiagnóstico</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_medicina_nuclear" onclick="setRegiaoAtuacao(this)">
											</td>
											<td  align="left">Medicina Nuclear</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_protecao" onclick="setRegiaoAtuacao(this)">
											</td>
											<td  align="left">Proteção Radiológica</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_ens_superior" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Ensino Superior</td>	</tr>
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_manut_com_rep" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Manutenção, Comércio e Representação</td>	
                                         </tr> 
                                        </table>
                                        </td>
                                        <td>
                                        <table width="100%">
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_ens_medio" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Ensino Médio</td>	</tr>      
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_orgao" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Órgão Regulatório</td>	</tr>   
                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_industria" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Indústria</td>	</tr>   
                                                                                  <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_pesquisa" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Pesquisa</td>	</tr>

                                        <tr bgcolor="#FFFFFF">
											<td align="left">
												<input type="checkbox" value=""  name="dbvar_atuacao_outro" onclick="setRegiaoAtuacao(this)">
											</td>

											<td  align="left">Outro</td>	</tr>                                 
                                          </table>
                                          </td>
                                          </tr>
                                        </table>				
                                        
									</td>
							</tr>  
                            
                            
                            
                            
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" class="destaque_gde"><strong><?php echo(getTText("endereco_principal",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr>
								<td></td>
								<td height="10" bgcolor="#FFFFFF" valign="top"><span class="comment_peq"><?php echo(getTText("este_endereco_usado_boleto_titulo",C_NONE));?></span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("cep",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_cep" id="dbvar_str_cep" maxlength="8" value="<?php echo request('dbvar_str_cep'); ?>" onKeyPress="return validateNumKey(event)" style="width:80px;">
									&nbsp;<span><img src="../../_tradeunion/img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="ajaxBuscaCEPLocal('dbvar_str_cep','dbvar_str_logradouro','dbvar_str_bairro','dbvar_str_cidade','dbvar_str_uf','dbvar_str_numero','loader_cep');" style="cursor:pointer" /></span>
									&nbsp;<span id="loader_cep"></span>
						        </td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("logradouro",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_logradouro" id="dbvar_str_logradouro" maxlength="255" value="<?php echo request('dbvar_str_logradouro'); ?>" style="width:280px;" /></span>
								</td>	
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("num_complemento",C_NONE));?></b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="30%"><input type="number" name="dbvar_str_numero" id="dbvar_str_numero" maxlength="20" value="<?php echo request('dbvar_str_numero'); ?>" style="width:80px;"></td>
									<td width="50%"><input type="text" name="dbvar_str_complemento" id="dbvar_str_complemento" maxlength="50" value="<?php echo request('dbvar_str_complemento'); ?>" style="width:90px;"></td>
								</tr>                                
								</table>
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("bairro",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_bairro" id="dbvar_str_bairro" maxlength="30" value="<?php echo request('dbvar_str_bairro'); ?>" style="width:150px;"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>"F>
								<td align="right"><b>*<?php echo(getTText("cidade",C_NONE));?>:</b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><input type="text" name="dbvar_str_cidade" id="dbvar_str_cidade" maxlength="30" value="<?php echo request('dbvar_str_cidade'); ?>" style="width:150px;"></td>
									<td>
										<b>*<?php echo(getTText("uf",C_NONE));?>:</b>
										<select name="dbvar_str_uf" id="dbvar_str_uf" style="width:45px;">
                                          <option value="" selected>UF</option>                                        
										<?php $strUFRequest = request('dbvar_str_uf'); $strUFRequest = ($strUFRequest == "") ? "SP" : $strUFRequest ; echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado",$strUFRequest)); ?>
										</select>
										<b><?php echo(getTText("brasil",C_TOUPPER));?></b>
										<input type="hidden" id="dbvar_str_pais" name="dbvar_str_pais" value="BRASIL" >
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("telefone_1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_telefone" id="dbvar_str_telefone" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" maxlength="12" value="<?php echo request('dbvar_str_telefone'); ?>" style="width:80px;">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("telefone_2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_telefone_2" id="dbvar_str_telefone_2" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" maxlength="12" value="<?php echo request('dbvar_str_telefone_2'); ?>" style="width:80px;">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            
                            <tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_filiacao",C_TOUPPER));?></strong></td>
							</tr>
                            <tr>
									<td align="right"></td>
									<td>*<?php echo(getTText("texto_categoria",C_UCWORDS));?></td>
                            </tr>        
                            <tr>
									<td align="right"><b>*<?php echo(getTText("categoria",C_UCWORDS));?>:</b></td>
									<td>										
										<select name="dbvar_str_categoria" id="dbvar_str_categoria" style="width:100px;">
                                          <option value="" selected>Selecione</option>
  										  <option value="19">Aspirante</option>
										  <option value="22">Efetivo</option>
                                          <option value="13">Adjunto</option>
										</select> 
									</td>
							</tr>		
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            
                            <tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("socios_proponentes",C_TOUPPER));?></strong></td>

							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"></td>
								<td>
                                <span class="comment_med">
                                <strong><br />Clique <u><font color="#0033FF"><a href="../upload/imgdin/modelo_declaracao_socios_proponentes.docx" download="modelo_declaracao_socios_proponentes.docx">AQUI</a></font></u> para download do MODELO de Declaração de Sócio Proponente</strong>
                                <br /><br />
                                <li><strong>Aspirante</strong> - Indique 1 sócio proponente;
                                <li><strong>Efetivo ou Adjunto</strong> - indique 2 sócios proponentes.
                                
                                </span>
								</td>
							</tr>
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("numero_socio1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_num_socio1" id="dbvar_str_num_socio1" size="15" maxlength="20" value="" onBlur="ajaxVerificaSocio1();" onKeyPress="return validateNumKey(event);">
                                    &nbsp;<span id="loader_socio1"></span>
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nome_socio1",C_NONE));?>:</b></td>
								<td><span id="nome_socio1"></span>
								</td>	
							</tr>
                            
                            <tr valign="top" bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("declaracao_socio1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_declaracao_socio1" id="dbvar_str_declaracao_socio1" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_declaracao_socio1','/abfm/upload/docspf/','','');">									
								</td>
							</tr>
                            
                             <tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("numero_socio2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_num_socio2" id="dbvar_str_num_socio2" size="15" maxlength="20" value="" onBlur="ajaxVerificaSocio2();" onKeyPress="return validateNumKey(event);">
                                    &nbsp;<span id="loader_socio2"></span>									
								</td>
							</tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nome_socio2",C_NONE));?>:</b></td>
								<td><span id="nome_socio2"></span>
								</td>	
							</tr>
                            <tr valign="top" bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("declaracao_socio2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_declaracao_socio2" id="dbvar_str_declaracao_socio2" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_declaracao_socio2','/abfm/upload/docspf/','','');">									
								</td>
							</tr>
                          
                            					
                            
                            
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            
                            <tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_curriculo",C_TOUPPER));?></strong></td>
							</tr>		
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("curriculo",C_NONE));?>:</b></td>
								<td><br><span class="comment_med"><strong>As informações aqui apresentadas são de responsabilidade do candidato!</strong></span><br /><br />
									<textarea rows="10" cols="80" name="dbvar_str_curriculo_resumido" id="dbvar_str_curriculo_resumido" value="" maxlength="2000"></textarea>
                                    <br><span class="comment_med">Este texto será apresentado no sistema de busca entre os sócios. Até 2.000 caracteres.</span>
								</td>
							</tr>
												
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                            <tr valign="top" bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("curriculo_completo",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_curriculo" id="dbvar_str_curriculo" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_curriculo','/abfm/upload/docspf/','','');">									
								</td>
							</tr>
                            <tr valign="top" bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*Comprov./Diploma:</b></td>
								<td>
									<input type="text" name="dbvar_str_diploma" id="dbvar_str_diploma" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="UPLOAD" class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_diploma','/abfm/upload/docspf/','','');">
									<br><span class="comment_med">Comprovante de Matrícula (para aspirantes) ou Diploma de Graduação.</span>
								</td>
							</tr>
                            <tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
                            
                            <tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_autorizacoes",C_TOUPPER));?></strong></td>
							</tr>
                            <tr>
									<td>&nbsp;</td>
                                    <td><b>*<?php echo(getTText("texto_autorizacao1",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td>&nbsp;</td>
									<td>										
										<select name="dbvar_str_autorizacao1" id="dbvar_str_autorizacao1" style="width:100px;">
                                          <option value="" selected>Selecione</option>
										  <option value="Sim">Sim</option>
  										  <option value="Nao">Não</option>
										</select> 
									</td>
							</tr>
                            <tr>
									<td>&nbsp;</td>
                                    <td><b>*<?php echo(getTText("texto_autorizacao2",C_UCWORDS));?>:</b></td>
                            </tr>
                            <tr>
                            		<td>&nbsp;</td>	
                                    <td>									
										<select name="dbvar_str_autorizacao2" id="dbvar_str_autorizacao2" style="width:100px;">
                                          <option value="" selected>Selecione</option>
										  <option value="Sim">Sim</option>
  										  <option value="Nao">Não</option>
										</select> 
									</td>
							</tr>
                             
                            
							<tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
							<tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_para_login",C_TOUPPER));?></strong></td>
							</tr>							
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                            <tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>Usuário:</b></td>
								<td><span id="user_cpf">(CPF)</span>
									
									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("senha",C_NONE));?>:</b></td>
								<td>
									<input type="password" name="dbvar_str_senha" id="dbvar_str_senha" size="15" maxlength="20" value="">
									&nbsp;<span class="comment_med">(Mínimo 6 Caracteres)</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("repita_senha",C_NONE));?>:</b></td>
								<td>
									<input type="password" name="dbvar_str_senha_confirma" id="dbvar_str_senha_confirma" size="15" maxlength="20" value="">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_repita_senha",C_NONE));?></span>
								</td>
							</tr>
							<tr><td height="20" colspan="2">&nbsp;</td></tr>
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>
							<tr><td height="10" colspan="2"></td></tr>
							<tr> 
								<td colspan="2">
									<table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-bottom:5px;">
									<tr>
										<td width="10%"><img src="../../_tradeunion/img/mensagem_info.gif" border="0"></td>
										<td width="60%"><?php echo(getTText("info_cad_nova_empresa",C_NONE));?></td>
										<td width="30%" colspan="2" align="right" style="padding-bottom:10px;">
											<button onClick="ok();return false;"><?php echo(getTText("ok",C_UCWORDS));?></button>	
											<button onClick="cancelar();return false;"><?php echo( getTText("cancelar",C_UCWORDS));?></button>
										</td>
									</tr>
									</table>
								</td>
							</tr>
                            
                             
                            
						</table>
					</td>
				</tr>
				</form>
			</table>
			<?php athEndFloatingBox(); ?>
			</td>
		</tr>
	</table>	
</body>
</html>
<?php $objConn = NULL; ?>