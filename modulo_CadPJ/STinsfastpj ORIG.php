<?php 
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// RECEBE PARÂMETRO BANCO
	// $strSystem = request("var_db"); 
	
	// ABERTURA DE CONEXÃO COM BANCO
	$objConn   = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<title><?php echo(strtoupper(CFG_SYSTEM_NAME)." - ".getTText("cad_novo_filiado",C_NONE));?></title>
	<style type="text/css">
		.span_manual{
			float:right;
			background-image:url(../img/icon_document_pdf.png);
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
		function validaCampos(){
			// Esta função faz uma pré-validação via
			// js dos campos marcados com asterisco
			var strMSG  = "";
			// Tratamento contra campos vazios
			// GUARDA PARA DADOS DA EMPRESA
			// alert(document.getElementById('dbvar_str_data_fundacao').value);
			// alert(getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_fundacao').value,'ptb')));
			strMSG += (
					   (document.getElementById('dbvar_str_cnpj').value 				 == "")	|| 
					   (document.getElementById('dbvar_str_insc_est').value 			 == "") ||
					   (document.getElementById('dbvar_str_insc_mun').value 			 == "") ||
					   (checkCNPJ(document.getElementById('dbvar_str_cnpj').value,false) == false) 	||
					   (validateEmail(document.getElementById('dbvar_str_email').value,false) == false)	||
					   (document.getElementById('dbvar_str_razao_social').value 		 == "")	|| 
					   (document.getElementById('dbvar_str_nome_fantasia').value 	  	 == "")	|| 
					   (document.getElementById('var_num_cad_cnae_divisao__cod_cnae_secao').value == "") || 
					   (document.getElementById('var_num_cad_cnae_grupo__cod_cnae_divisao').value == "") ||
					   (document.getElementById('var_num_cad_cnae_classe__cod_cnae_grupo').value == "") ||
					   (document.getElementById('dbvar_str_email').value 				 == "") ||
					   (document.getElementById("dbvar_str_data_fundacao").value		 == "") ||
					   (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_fundacao').value,'ptb'))>0)
					   ) ? "\n\nDADOS DA EMPRESA:" : "";
			strMSG += (document.getElementById('dbvar_str_cnpj').value 					== "") ? "\nCNPJ" 				: "";
			strMSG += (document.getElementById('dbvar_str_insc_est').value 				== "") ? "\nInscrição Estadual"	: "";
			strMSG += (document.getElementById('dbvar_str_insc_mun').value 				== "") ? "\nInscrição Municipal": "";
			strMSG += (checkCNPJ(document.getElementById('dbvar_str_cnpj').value,false) == false) ? "\nCNPJ Inválido!" 	: "";
			strMSG += (document.getElementById('dbvar_str_razao_social').value 			== "") ? "\nRazão Social" 		: "";
			strMSG += (document.getElementById('dbvar_str_nome_fantasia').value 		== "") ? "\nNome Fantasia" 		: "";
			strMSG += (document.getElementById('var_num_cad_cnae_divisao__cod_cnae_secao').value 		== "") ? "\nCNAE Divisão" 		: "";
			strMSG += (document.getElementById('var_num_cad_cnae_grupo__cod_cnae_divisao').value 		== "") ? "\nCNAE Seção" 		: "";
			strMSG += (document.getElementById('var_num_cad_cnae_classe__cod_cnae_grupo').value 		== "") ? "\nCNAE Grupo" 		: "";
			strMSG += (document.getElementById('dbvar_str_email').value 				== "") ? "\nEmail" 				: "";
			strMSG += (document.getElementById('dbvar_str_data_fundacao').value 		== "") ? "\nData de Fundação"	: "";
			strMSG += (getDDiff(getNow(),getVDate(document.getElementById('dbvar_str_data_fundacao').value,'ptb'))>0) ? "\nData de Fundação Maior que Data Atual"	: "";
			strMSG += (validateEmail(document.getElementById('dbvar_str_email').value,false) == false) ? "\nEmail Inválido!" : "";
			// GUARDA PARA DOCUMENTO DIGITALIZADO	
			strMSG += (
					   (document.getElementById('dbvar_str_arquivo_1').value == "")
					   ) ? "\n\nDOCUMENTOS DIGITALIZADOS:" : "";
			strMSG += (document.getElementById('dbvar_str_arquivo_1').value 			== "") ? "\nDocumento Um (1)" 	: "" ;
			// GUARDA PARA ENDEREÇO PRINCIPAL
			strMSG += (
					   (document.getElementById('dbvar_str_cep').value 					== "") ||
					   (document.getElementById('dbvar_str_logradouro').value 			== "") ||
					   (document.getElementById('dbvar_str_numero').value 				== "") ||
					   (document.getElementById('dbvar_str_bairro').value 				== "") ||
					   (document.getElementById('dbvar_str_uf').value 					== "") ||
					   (document.getElementById('dbvar_str_telefone').value 			== "")
					   ) ? "\n\nENDEREÇO PRINCIPAL:" : "";
			strMSG += (document.getElementById('dbvar_str_cep').value 					== "") ? "\nCep" 				: "" ;
			strMSG += (document.getElementById('dbvar_str_logradouro').value 			== "") ? "\nLogradouro" 		: "" ;
			strMSG += (document.getElementById('dbvar_str_numero').value 				== "") ? "\nNúmero" 			: "" ;
			strMSG += (document.getElementById('dbvar_str_bairro').value 				== "") ? "\nBairro" 			: "" ;
			strMSG += (document.getElementById('dbvar_str_uf').value 					== "") ? "\nEstado / UF" 		: "" ;
			strMSG += (document.getElementById('dbvar_str_telefone').value 				== "") ? "\nTelefone Um (1)" 	: "" ;
			// GUARDA DADOS DE LOGIN
			strMSG += (
					   (document.getElementById('dbvar_str_usuario').value 				== "") ||
					   (document.getElementById('dbvar_str_senha').value 				== "") ||
					   (document.getElementById('dbvar_str_senha').value.length 		 <  6) ||
					   (document.getElementById('dbvar_str_senha_confirma').value 		== "") ||
					   (document.getElementById('dbvar_str_senha_confirma').value != document.getElementById('dbvar_str_senha').value)
					   ) ? "\n\nDADOS DE LOGIN:" : "";
			strMSG += (document.getElementById('dbvar_str_usuario').value 				== "") ? "\nUsuário" 			: "" ;
			strMSG += (document.getElementById('dbvar_str_senha').value 				== "") ? "\nSenha" 				: "" ;
			strMSG += (document.getElementById('dbvar_str_senha').value.length 			 <  6) ? "\nQuantidade de Caracteres da senha" 				: "" ;
			strMSG += (document.getElementById('dbvar_str_senha_confirma').value != document.getElementById('dbvar_str_senha').value) ? "\nSenhas não Conferem!" : "";
			strMSG += (document.getElementById('dbvar_str_senha_confirma').value 		== "") ? "\nConfirmação de Senha" : "" ;
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
		
		function STajaxDetailData(prSQL, prFuncao, prID, prFuncExtra) {
			var objAjax;
			var strReturnValue;

			objAjax = createAjax();

			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
							//alert(strReturnValue);
							switch (prFuncao) {
								case "ajaxMontaCombo":  ajaxMontaCombo(prID, strReturnValue);
										if(prFuncExtra != '') eval(prFuncExtra);
										break;
								case "ajaxMontaEdit":   ajaxMontaEdit(prID, strReturnValue);
										if(prFuncExtra != '') eval(prFuncExtra);
										break;
							}
					} else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/STreturndados.php?var_sql=" + prSQL+"&var_db=<?php echo(CFG_DB);?>",  true); 
			objAjax.send(null); 
		}
		
		function copiaCamposEndereco(){
			document.getElementById('dbvar_str_endcobr_logradouro_000').value 	= document.getElementById('dbvar_str_logradouro').value;
			document.getElementById('dbvar_str_endcobr_numero_000').value 		= document.getElementById('dbvar_str_numero').value;
			document.getElementById('dbvar_str_endcobr_complemento_000').value 	= document.getElementById('dbvar_str_complemento').value;
			document.getElementById('dbvar_str_endcobr_cidade_000').value 		= document.getElementById('dbvar_str_cidade').value;
			document.getElementById('dbvar_str_endcobr_bairro_000').value 		= document.getElementById('dbvar_str_bairro').value;
			document.getElementById('dbvar_str_endcobr_estado_000').value 		= document.getElementById('dbvar_str_uf').value;
			document.getElementById('dbvar_num_endcobr_cep_000').value 			= document.getElementById('dbvar_str_cep').value;
			document.getElementById('dbvar_str_endcobr_fone1_000').value 		= document.getElementById('dbvar_str_telefone').value;
			document.getElementById('dbvar_str_endcobr_fone2_000').value 		= document.getElementById('dbvar_str_telefone_2').value;
			document.getElementById('dbvar_str_endcobr_rotulo_000').focus();
		}
				
		// Melhorar usabilidade desta função, 
		// está engessada a este código somente			
		function ajaxCopiaEnderContabil(prValue){
			var strSQL = "SELECT cod_pj_contabil, razao_social, end_logradouro, end_cep, end_bairro, end_cidade, end_estado, end_numero, end_complemento, contato, email FROM cad_pj_contabil WHERE cod_pj_contabil = " + prValue;
			var objAjax; 
			var	strReturnValue;
			var arrDados;
			objAjax = createAjax();
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						//alert(strReturnValue);
						//alert(prSQL);
						// verifica se retornou dados
						if(strReturnValue.indexOf('|') != -1){
							arrDados = strReturnValue.split('|');
							document.formeditor_000.dbvar_num_cod_pj_contabil.value = arrDados[0]; //cod_pj_contabil
							document.formeditor_000.dbvar_str_endcobr_rotulo_000.value = arrDados[1]; //razao_social
							document.formeditor_000.dbvar_num_endcobr_cep_000.value = arrDados[2]; //cep
							document.formeditor_000.dbvar_str_endcobr_email_000.value = arrDados[3]; //email
							document.formeditor_000.dbvar_str_endcobr_contato_000.value = arrDados[4]; //contato
							document.formeditor_000.dbvar_str_endcobr_logradouro_000.value = arrDados[5]; //logradouro
							document.formeditor_000.dbvar_str_endcobr_numero_000.value = arrDados[6]; //numero
							document.formeditor_000.dbvar_str_endcobr_complemento_000.value = arrDados[7]; //complemento
							document.formeditor_000.dbvar_str_endcobr_bairro_000.value = arrDados[8]; //bairro
							document.formeditor_000.dbvar_str_endcobr_cidade_000.value = arrDados[9]; //cidade
							document.formeditor_000.dbvar_str_endcobr_estado_000.value = arrDados[10]; //estado
						}
					}
					else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/STreturndadoscontabil.php?var_sql=" + strSQL +"&var_db=<?php echo(CFG_DB);?>",  true); 
			objAjax.send(null);
		}
		
		// VERIFICA SE PJ JÁ EXISTE
		function ajaxVerificaPJ(){
			var objAjax;
			var strReturnValue;
			var strDB;
			var strSQL;
			// Tratamento BREVE, caso o nome de usuário 
			// esteja vazio ou nulo, retorno nulo
			if(document.getElementById('dbvar_str_cnpj').value == null || document.getElementById('dbvar_str_cnpj').value == ""){
				return(null);
			}
			// Seta o SQL, cria o AJAX
			strSQL  = "SELECT cod_pj, razao_social FROM cad_pj WHERE cnpj = '"+ document.getElementById('dbvar_str_cnpj').value +"'"; "SELECT cod_usuario FROM sys_usuario WHERE id_usuario = '"+ document.getElementById('dbvar_str_cnpj').value +"';";
			strDB   = "<?php echo(CFG_DB);?>"
			objAjax = createAjax();
			// Coloca LOADER
			document.getElementById('loader_empresa').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						//alert(strReturnValue);
						//alert(prSQL);
						// verifica se retornou dados
						if(strReturnValue.indexOf('|') != -1){
							document.getElementById('loader_empresa').innerHTML = "<br/><span style='color:red;'>(O CNPJ <em><b>"+ document.getElementById('dbvar_str_cnpj').value +"</b></em>&nbsp; JÁ ESTÁ CADASTRADO NO SISTEMA)</span>";
							// alert('Esta empresa já está CADASTRADA!');
						}
						setTimeout("document.getElementById('loader_empresa').innerHTML = ''",3000);
					}
					else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/returndadosexterna.php?var_sql="+strSQL+"&var_db="+strDB,true); 
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
			strDB   = "<?php echo(CFG_DB);?>"
			objAjax = createAjax();
			// Coloca LOADER
			document.getElementById('loader_usuario').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
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
			objAjax.open("GET", "../_ajax/returndadosexterna.php?var_sql=" + strSQL +"&var_db="+ strDB,true); 
			objAjax.send(null); 
		}
			
			
		-->
	  </script>

</head>
<body bgcolor="#F5F5F5"  background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
	<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="center" valign="middle">
		<!--span class='span_manual' onClick=\"alert('Em manutenção. Aguarde.');\" title='".getTText("download_manual_explicativo",C_NONE)."'>Manual Explicativo</span-->
		<?php athBeginFloatingBox("720","","<span style='font-weight:bold;'>".strtoupper(CFG_SYSTEM_NAME)." | ".ucwords(str_replace(CFG_SYSTEM_NAME."_","",getsession(CFG_SYSTEM_NAME."_dir_cliente")))." - (".getTText("cad_novo_filiado",C_NONE).")",CL_CORBAR_GLASS_1); ?>
			<table width="700" bgcolor="#FFFFFF" border="0" cellspacing="0" cellpadding="0" style="border:1px #A6A6A6 solid; -moz-opacity:1.5 !important; z-index:100;">
			<form name="formeditor_000" id="forminsert" action="STinsfastpjexec.php" method="post" enctype="multipart/form-data">
				<input type="hidden" id="dbvar_num_cod_pj_contabil" name="dbvar_num_cod_pj_contabil" value="<?php echo(request("dbvar_num_cod_pj_contabil"));?>" />
				<tr>
					<td align="center" valign="top" style="padding:0px 80px 0px 80px;"> 
						<table width="100%" cellpadding="3" cellspacing="0">
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							<tr><td colspan="2"><strong><?php echo(getTText("preencha_cad_abaixo_corretamente",C_NONE));?></strong>:</td></tr>
							<tr>
								<td></td>
								<td align="left" valign="bottom" height="40" class="destaque_gde"><strong><?php echo(getTText("dados_empresa",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="25%" align="right" valign="top"><b>*<?php echo(getTText("cnpj",C_NONE));?>:</b></td>
								<td width="75%">
									<input type="text" name="dbvar_str_cnpj" id="dbvar_str_cnpj" size="20" maxlength="14" value="<?php echo request('dbvar_str_cnpj') ?>" onBlur="checkCNPJ(this.value,true);ajaxVerificaPJ();" onKeyPress="return validateNumKey(event);">
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_cnpj",C_NONE));?></span>
									&nbsp;<span id="loader_empresa"></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("inscricao_estadual",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_insc_est" id="dbvar_str_insc_est" size="25" value="<?php echo (request('dbvar_str_insc_est')); ?>" style="text-transform:uppercase">
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_inscricao_estadual",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("inscricao_municipal",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_insc_mun" id="dbvar_str_insc_mun" size="25" value="<?php echo (request('dbvar_str_insc_mun')); ?>" style="text-transform:uppercase">
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_inscricao_estadual",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("razao_social",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_razao_social" id="dbvar_str_razao_social" size="60" maxlength="120" value="<?php echo request('dbvar_str_razao_social'); ?>"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("nome_fantasia",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_nome_fantasia" id="dbvar_str_nome_fantasia" size="60" maxlength="120" value="<?php echo requestQueryString('dbvar_str_nome_fantasia'); ?>"><a href="#" onClick="javascript:document.getElementById('dbvar_str_nome_fantasia').value = document.getElementById('dbvar_str_razao_social').value;"><img src='../../_tradeunion/img/icon_back.gif' border='0' hspace="4" alt='Clique aqui para copiar conteúdo da Razão Social' title='Clique aqui para copiar conteúdo da Razão Social'></a>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("cnae_secao",C_NONE));?>:</b></td>
								<td>
									<select name="var_num_cad_cnae_divisao__cod_cnae_secao" id="var_num_cad_cnae_divisao__cod_cnae_secao" style="width:380px;" onChange="limpaSelect('var_num_cad_cnae_classe__cod_cnae_grupo');limpaSelect('var_num_cad_cnae_classe__cod_cnae_classe');STajaxDetailData((this.value != '') ? 'SELECT cod_cnae_divisao, cod_digi_divisao||\' - \'||nome as nome FROM cad_cnae_divisao WHERE cod_cnae_secao = ' + this.value : '','ajaxMontaCombo','var_num_cad_cnae_grupo__cod_cnae_divisao','','');">
										<option value=""></option>
										<?php echo(montaCombo($objConn,"SELECT cod_cnae_secao, cod_digi_secao_cnae||' - '||nome as nome FROM cad_cnae_secao ORDER BY nome","cod_cnae_secao","nome",request('var_num_cad_cnae_divisao__cod_cnae_secao')));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("cnae_divisao",C_NONE));?>:</b></td>
								<td>
									<select name="var_num_cad_cnae_grupo__cod_cnae_divisao" id="var_num_cad_cnae_grupo__cod_cnae_divisao" style="width:380px;" onChange="limpaSelect('var_num_cad_cnae_classe__cod_cnae_classe');STajaxDetailData((this.value != '') ? 'SELECT cod_cnae_grupo, cod_digi_grupo||\' - \'||nome as nome FROM cad_cnae_grupo WHERE cod_cnae_divisao = ' + this.value : '','ajaxMontaCombo','var_num_cad_cnae_classe__cod_cnae_grupo','','');">
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("cnae_grupo",C_NONE));?>:</b></td>
								<td>
									<select name="var_num_cad_cnae_classe__cod_cnae_grupo" id="var_num_cad_cnae_classe__cod_cnae_grupo" style="width:380px;" onChange="STajaxDetailData((this.value != '') ? 'SELECT cod_cnae_classe, cod_digi_classe||\' - \'||nome as nome FROM cad_cnae_classe WHERE cod_cnae_grupo = ' + this.value : '','ajaxMontaCombo','var_num_cad_cnae_classe__cod_cnae_classe','','');">
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("cnae_classe",C_NONE));?>:</b></td>
								<td>
									<select name="var_num_cad_cnae_classe__cod_cnae_classe" id="var_num_cad_cnae_classe__cod_cnae_classe" style="width:380px;" onChange="STajaxDetailData((this.value != '') ? 'SELECT cod_cnae_subclasse, cod_digi_subclasse||\' - \'||nome as nome FROM cad_cnae_subclasse WHERE cod_cnae_classe = ' + this.value : '' ,'ajaxMontaCombo','var_num_cad_cnae_subclasse__cod_cnae_subclasse','','');">
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("cnae_subclasse",C_NONE));?>:</b></td>
								<td>
									<select name="var_num_cad_cnae_subclasse__cod_cnae_subclasse" id="var_num_cad_cnae_subclasse__cod_cnae_subclasse" style="width:380px;">
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("categoria_empresa",C_NONE));?>:</b></td>
								<td>
									<select name="dbvar_str_categoria" style="width:210px;">
										<option value="CONTRIBUINTE"    ><?php echo(getTText("contribuinte",C_TOUPPER));?></option>
										<option value="NAO_CONTRIBUINTE"><?php echo(getTText("nao_contribuinte",C_TOUPPER));?></option>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("capital_social",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_capital" id="dbvar_str_capital" size="20" value="<?php echo request('dbvar_str_capital'); ?>"  onkeypress="return validateFloatKeyNew(this,event);" /></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("data_fundacao",C_UCWORDS));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_data_fundacao" id="dbvar_str_data_fundacao" size="15" maxlength="10" value="<?php echo request('dbvar_str_data_fundacao'); ?>" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);"/>
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_data",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("email",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_email" id="dbvar_str_email" size="40" value="<?php echo request('dbvar_str_email'); ?>"  onBlur="validateEmail(this.value,true)"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("email_extra",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_email_2" id="dbvar_str_email_2" size="40" value="<?php echo request('dbvar_str_email_2');?>" onBlur="validateEmail(this.value,true);"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("website",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_website" id="dbvar_str_website" size="40" value="<?php echo request('dbvar_str_website'); ?>"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("contato",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_contato" id="dbvar_str_contato" size="40" value="<?php echo request('dbvar_str_contato'); ?>"></td>
							</tr>
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" class="destaque_gde"><strong><?php echo(getTText("documentos_digitalizados",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td></td>
								<td height="10" valign="top">
									<span class="comment_peq">
									<?php echo(getTText("obs_cad_arquivo_login",C_NONE));?>
									</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("documento_1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_arquivo_1" id="dbvar_str_arquivo_1" size="40" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_arquivo_1','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/','','');">
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("documento_2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_arquivo_2" id="dbvar_str_arquivo_2" size="40" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_arquivo_2','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/','','');">
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("documento_3",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_arquivo_3" id="dbvar_str_arquivo_3" size="40" readonly="readonly">
									&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor_000','dbvar_str_arquivo_3','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspj/','','');">
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
									<input type="text" name="dbvar_str_cep" id="dbvar_str_cep" size="10" maxlength="8" value="<?php echo request('dbvar_str_cep'); ?>" onKeyPress="return validateNumKey(event)">
									&nbsp;<span><img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="ajaxBuscaCEP('dbvar_str_cep','dbvar_str_logradouro','dbvar_str_bairro','dbvar_str_cidade','dbvar_str_uf','dbvar_str_numero','loader_cep');" style="cursor:pointer" /></span>
									&nbsp;<span id="loader_cep"></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("logradouro",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_logradouro" id="dbvar_str_logradouro" size="45" value="<?php echo request('dbvar_str_logradouro'); ?>">
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_logradouro",C_NONE));?></span>
								</td>	
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("num_complemento",C_NONE));?></b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="15%"><input type="text" name="dbvar_str_numero" id="dbvar_str_numero" size="5" maxlength="6" value="<?php echo request('dbvar_str_numero'); ?>"></td>
									<td width="20%"><input type="text" name="dbvar_str_complemento" id="dbvar_str_complemento" size="10" maxlength="" value="<?php echo request('dbvar_str_complemento'); ?>"></td>
									<td width="65%"><b>*<?php echo(getTText("bairro",C_NONE));?>:</b><input type="text" name="dbvar_str_bairro" id="dbvar_str_bairro" size="25" value="<?php echo request('dbvar_str_bairro'); ?>"></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>"F>
								<td align="right"><b>*<?php echo(getTText("cidade",C_NONE));?>:</b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><input type="text" name="dbvar_str_cidade" id="dbvar_str_cidade" size="30" value="<?php echo request('dbvar_str_cidade'); ?>"></td>
									<td>
										<b>*<?php echo(getTText("uf",C_NONE));?>:</b>
										<select name="dbvar_str_uf" id="dbvar_str_uf" style="width: 45px;">
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
									<input type="text" name="dbvar_str_telefone" id="dbvar_str_telefone" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" size="20" maxlength="12" value="<?php echo request('dbvar_str_telefone'); ?>">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("telefone_2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_telefone_2" id="dbvar_str_telefone_2" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" size="20" maxlength="12" value="<?php echo request('dbvar_str_telefone_2'); ?>">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr><td colspan="2" height="10">&nbsp;</td></tr>		
							
							
							
							<tr>
								<td></td>
								<td align="left" valign="bottom" class="destaque_gde"><strong><?php echo(getTText("endereco_para_cobranca",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td></td>
								<td height="10" valign="top">
								<span class="comment_peq"><?php echo(getTText("obs_endereco_cobranca",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td></td>
								<td height="10"  valign="top">
								<span class="comment_peq"><?php echo(getTText("obs_endereco_cobranca_2",C_NONE));?> <span onClick="Javascript:copiaCamposEndereco();" style="cursor:pointer;font-weight:bold;color:#777;"><u>aqui</u></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("cep",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_num_endcobr_cep_000" id="dbvar_num_endcobr_cep_000" size="10" maxlength="8" value="<?php echo request('dbvar_num_endcobr_cep_000'); ?>" onKeyPress="return validateNumKey(event)">
									&nbsp;<span><img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick=":ajaxBuscaCEP('dbvar_num_endcobr_cep_000','dbvar_str_endcobr_logradouro_000','dbvar_str_endcobr_bairro_000','dbvar_str_endcobr_cidade_000','dbvar_str_endcobr_estado_000','dbvar_str_endcobr_numero_000','loader_cep_cobr');" style="cursor:pointer" /></span>
									&nbsp;<span id="loader_cep_cobr"></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right" ><b><?php echo(getTText("rotulo_entrega",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_endcobr_rotulo_000" id="dbvar_str_endcobr_rotulo_000" size="34" value="<?php echo request('dbvar_str_endcobr_rotulo_000'); ?>">
									&nbsp;<span><img src="../img/icon_view_wizard.gif" alt="Localizar Empresas de Contabilidade" title="Localizar Empresas de Contabilidade" onClick="javascript:AbreJanelaPAGE('../modulo_CadPJContabil/STresultaslw.php?var_db=<?php echo(CFG_DB);?>',800,600);" style="cursor:pointer;"/></span>
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_rotulo_entrega",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("email",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_endcobr_email_000" id="dbvar_str_endcobr_email_000" size="40" value="<?php echo request('dbvar_str_endcobr_email_000'); ?>"  onBlur="verifyEmail(this,this.value)"></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("contato",C_NONE));?>:</b></td>
								<td><input type="text" name="dbvar_str_endcobr_contato_000" id="dbvar_str_endcobr_contato_000" size="30" value="<?php echo request('dbvar_str_endcobr_contato_000'); ?>" /></td>
							</tr>
							<tr>
								<td align="right"><b><?php echo(getTText("logradouro",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_endcobr_logradouro_000" id="dbvar_str_endcobr_logradouro_000" size="45" value="<?php echo request('dbvar_str_endcobr_logradouro_000'); ?>">
									&nbsp;<span class="comment_peq"><?php echo(getTText("obs_logradouro",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("num_complemento",C_NONE));?></b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td width="15%"><input type="text" name="dbvar_str_endcobr_numero_000" id="dbvar_str_endcobr_numero_000" size="5" maxlength="6" value="<?php echo request('dbvar_str_endcobr_numero_000'); ?>"></td>
									<td width="20%"><input type="text" name="dbvar_str_endcobr_complemento_000" id="dbvar_str_endcobr_complemento_000" size="10" maxlength="" value="<?php echo request('dbvar_str_endcobr_complemento_000'); ?>"></td>
									<td width="65%"><b><?php echo(getTText("bairro",C_NONE));?>:</b>
									  <input type="text" name="dbvar_str_endcobr_bairro_000" id="dbvar_str_endcobr_bairro_000" size="25" value="<?php echo request('dbvar_str_endcobr_bairro_000'); ?>"></td>
								</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("cidade",C_NONE));?>:</b></td>
								<td>
								<table cellpadding="0" cellspacing="0" border="0">
								<tr>
									<td><input type="text" name="dbvar_str_endcobr_cidade_000" id="dbvar_str_endcobr_cidade_000" size="30" value="<?php echo request('dbvar_str_endcobr_cidade_000'); ?>"></td>
									<td>
										<b><?php echo(getTText("uf",C_NONE));?>:</b>
										<select name="dbvar_str_endcobr_estado_000" id="dbvar_str_endcobr_estado_000" style="width: 45px;">
										<?php $strUFRequestCob = request('dbvar_str_endcobr_estado_000'); $strUFRequestCob = ($strUFRequestCob == "") ? "SP" : $strUFRequestCob; echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado",$strUFRequestCob)); ?>
										</select>
										<b><?php echo(getTText("brasil",C_NONE));?></b>
										<input type="hidden" id="dbvar_str_endcobr_pais_000" name="dbvar_str_endcobr_pais_000" value="BRASIL" >
									</td>
								</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("telefone_1",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_endcobr_fone1_000" id="dbvar_str_endcobr_fone1_000" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" size="20" maxlength="12" value="<?php echo request('dbvar_str_endcobr_fone1_000'); ?>">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b><?php echo(getTText("telefone_2",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_endcobr_fone2_000" id="dbvar_str_endcobr_fone2_000" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" size="20" maxlength="12" value="<?php echo request('dbvar_str_endcobr_fone2_000');?>">
									&nbsp;<span class="comment_med"><?php echo(getTText("obs_formato_telefone",C_NONE));?></span>
								</td>
							</tr>
							<tr><td colspan="2" height="10">&nbsp;</td></tr>	
							
							

							<tr>
								<td></td>
								<td valign="bottom" class="destaque_gde"><strong><?php echo(getTText("dados_para_login",C_TOUPPER));?></strong></td>
							</tr>							
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td align="right"><b>*<?php echo(getTText("usuario",C_NONE));?>:</b></td>
								<td>
									<input type="text" name="dbvar_str_usuario" id="dbvar_str_usuario" size="15" maxlength="15" value="<?php echo request('dbvar_str_usuario'); ?>">
									&nbsp;<span class="comment_med" style="color:#777;font-weight:bold;cursor:pointer" onClick="ajaxVerificaUSER();"><u><?php echo(getTText("clique_disponibilidade",C_NONE));?></u></span>
									&nbsp;<span id="loader_usuario"></span>
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
										<td width="10%"><img src="../img/mensagem_info.gif" border="0"></td>
										<td width="60%"><?php echo(getTText("info_cad_nova_empresa",C_NONE));?></td>
										<td width="30%" colspan="2" align="right" style="padding-bottom:10px;">
											<button onClick="ok();"><?php echo(getTText("ok",C_UCWORDS));?></button>	
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