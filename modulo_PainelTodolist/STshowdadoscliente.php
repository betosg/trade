<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");

	// REQUESTS
	$intCodDado  = request("var_chavereg");		// CODIGO
	$strTipo	 = request("var_tipo");	 		// TIPO DE CLIENTE
	$strLocation = request("var_location");
	
	if(($intCodDado == "") || ($strTipo == "")){ die(); }
	
	// ABRE OBJETO DE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// SWITCH NO TIPO DE CLIENTE
	switch(strtolower($strTipo)){
		case "cad_pf":
			$strSQL = "
				SELECT 
					  cad_pf.cod_pf
					, cad_pf.nome
					, cad_pf.data_nasc
					, cad_pf.sexo
					, cad_pf.cpf
					, cad_pf.rg
					, cad_pf.cnh
					, cad_pf.email
					, cad_pf.email_extra
					, cad_pf.endprin_cep
					, cad_pf.endprin_logradouro
					, cad_pf.endprin_numero
					, cad_pf.endprin_complemento
					, cad_pf.endprin_bairro
					, cad_pf.endprin_cidade
					, cad_pf.endprin_estado
					, cad_pf.endprin_pais
					, cad_pf.endprin_fone1
					, cad_pf.endprin_fone2
					, cad_pf.apelido
					, cad_pf.nome_pai
					, cad_pf.nome_mae
					, cad_pf.estado_civil
					, cad_pf.instrucao
					, cad_pf.nacionalidade
					, cad_pf.naturalidade
					, cad_pf.pis
					, cad_pf.ctps
					, cad_pf.obs
					, cad_pf.titulo_eleitoral
					, cad_pf.data_falec
				FROM cad_pf
				WHERE cod_pf = ".$intCodDado;
		break;
		
		case "cad_pj":
			$strSQL = "
				SELECT
					  cad_pj.cod_pj
					, cad_pj.razao_social
					, cad_pj.nome_fantasia
					, cad_pj.nome_comercial
					, cad_pj.cnpj
					, cad_pj.insc_est
					, cad_pj.insc_munic
					, cad_pj.email
					, cad_pj.email_extra
					, cad_pj.website
					, cad_pj.contato
					, cad_pj.endprin_cep
					, cad_pj.endprin_logradouro
					, cad_pj.endprin_numero
					, cad_pj.endprin_complemento
					, cad_pj.endprin_bairro
					, cad_pj.endprin_cidade
					, cad_pj.endprin_estado
					, cad_pj.endprin_pais
					, cad_pj.endprin_fone1
					, cad_pj.endprin_fone2
					, cad_pj.num_funcionarios
					, cad_pj.dtt_fundacao
					, cad_pj.capital
					, cad_pj.obs
				FROM cad_pj 
				WHERE cod_pj = ".$intCodDado;
		break;
		
		case "cad_pj_fornec":
			$strSQL = "
				  cad_pj_fornec.cod_pj_fornec
				, cad_pj_fornec.razao_social
				, cad_pj_fornec.nome_fantasia
				, cad_pj_fornec.nome_comercial
				, cad_pj_fornec.cnpj
				, cad_pj_fornec.insc_est
				, cad_pj_fornec.insc_munic
				, cad_pj_fornec.email
				, cad_pj_fornec.email_extra
				, cad_pj_fornec.website
				, cad_pj_fornec.contato
				, cad_pj_fornec.end_cep
				, cad_pj_fornec.end_logradouro
				, cad_pj_fornec.end_numero
				, cad_pj_fornec.end_complemento
				, cad_pj_fornec.end_bairro
				, cad_pj_fornec.end_cidade
				, cad_pj_fornec.end_estado
				, cad_pj_fornec.end_pais
				, cad_pj_fornec.end_fone1
				, cad_pj_fornec.end_fone2
				, cad_pj_fornec.obs
			FROM cad_pj_fornec
			WHERE cod_pj_fornec = ".$intCodDado;
		break;
		
		default:break;
	}
	
	// EXECUTA SQL
	$objConn->beginTransaction();
	try{
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// VARIVÁVEL PARA COLORIR LINHA
	$strColor = CL_CORLINHA_1;
	
	// FUNÇÃO PARA COLORIR LINHAS
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
			.table_master{
				background-color:#FFFFFF;
				border-top:   1px solid #CCC;
				border-right: 1px solid #CCC;
				border-bottom:1px solid #CCC;
				border-left:  1px solid #CCC;
				padding-bottom: 5px;
			}
			.td_no_resp{ 
				font-size:11px; 
				font-weight:bold; 
				color:#C9C9C9; 
				text-align:center; 
				border:1px solid #E9E9E9;
				padding:5px 5px 0px 5px;
			}
			.td_resp{ border:1px solid #E9E9E9; padding:5px 0px 2px 10px; }
			.td_resp_cabec{ font-size:11px; font-weight:bold; color:#CCC;}
			.td_resp_conte{ padding:6px 0px 2px 20px; }
			.td_text_resp { border:2px dashed #E9E9E9; padding:4px 9px 4px 9px; }
			
			#img_drop_dt_ini{ cursor:pointer; display:none }
						
			#img_drop_dt_fim{ cursor:pointer; display:none }
			
			#lst_dt_ini{ width:250px;height:100px;overflow:scroll;display:none }
		</style>
		<script type="text/javascript">
			var strLocation = null;
			function ok() {
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					window.close();
				<?php }?>
			}

			function cancelar() {
				<?php if($strLocation != ""){?>
					document.location.href = "<?php echo($strLocation);?>";
				<?php } else{?>
					window.close();
				<?php }?>
			}
		</script>
	</head>
<body style="margin:10px;background-color:#FFFFFF;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("710","",getTText("dados_do_cliente",C_UCWORDS)." - (".getTText("visualizacao",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<table cellpadding="0" cellspacing="0" border="0" height="315" width="690" bgcolor="#FFFFFF" class="table_master">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td>
	</tr>
	<tr>
		<td align="left" valign="top" style="padding:10px 70px 10px 70px;">
			<table cellspacing="2" cellpadding="4" border="0" width="100%">
			<!-- DADOS -->
			<?php if(strtolower($strTipo) == "cad_pf"){?>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_pessoa_fisica",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_pf",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"cod_pf"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("apelido",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"apelido"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("data_nasc",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"data_nasc"),false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("sexo",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"sexo")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("estado_civil",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"estado_civil")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("naturalidade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"naturalidade")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nacionalidade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"nacionalidade")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"cpf")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("rg",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"rg"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cnh",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"cnh"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("ctps",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"ctps"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("pis",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"pis"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("titulo_eleitoral",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"titulo_eleitoral"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_mae",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_mae"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_pai",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_pai"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("instrucao",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"instrucao"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email_extra",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email_extra"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_cep",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_cep"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_logradouro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_logradouro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_numero",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_numero"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_complemento",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_complemento"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_bairro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_bairro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_cidade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_cidade"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_estado",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_estado"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_pais",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_pais"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_fone1",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_fone1"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_fone2",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_fone2"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"obs"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("data_falec",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"data_falec"),false));?></td>
				</tr>
			<?php } ?>
				
			<?php if(strtolower($strTipo) == "cad_pj"){?>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_pessoa_juridica",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_pj",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"cod_pj"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("razao_social",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"razao_social"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_fantasia",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_fantasia"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_comercial",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_comercial"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cnpj",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"cnpj")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("insc_est",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"insc_est")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("insc_munic",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"insc_munic")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right" valign="top"><strong><?php echo(getTText("dtt_fundacao",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_fundacao"),false));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("num_funcionarios",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"num_funcionarios")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("capital",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"capital")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email_extra",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email_extra"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("website",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"website"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("contato",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"contato"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_cep",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_cep"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_logradouro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_logradouro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_numero",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_numero"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_complemento",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_complemento"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_bairro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_bairro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_cidade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_cidade"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_estado",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_estado"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_pais",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_pais"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_fone1",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_fone1"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("endprin_fone2",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"endprin_fone2"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"obs"));?></td>
				</tr>
			<?php } ?>
				
			<?php if(strtolower($strTipo) == "cad_pj_fornec"){ ?>
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde">
						<strong><?php echo(getTText("dados_da_pessoa_juridica_fornec",C_TOUPPER));?></strong>
					</td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cod_pj_fornec",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"cod_pj_fornec"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("razao_social",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"razao_social"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_fantasia",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_fantasia"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("nome_comercial",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"nome_comercial"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("cnpj",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"cnpj")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("insc_est",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"insc_est")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("insc_munic",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(strtoupper(getValue($objRS,"insc_munic")));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("email_extra",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"email_extra"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("website",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"website"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("contato",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"contato"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_cep",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_cep"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_logradouro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_logradouro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_numero",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_numero"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_complemento",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_complemento"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_bairro",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_bairro"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_cidade",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_cidade"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_estado",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_estado"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_pais",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_pais"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_fone1",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_fone1"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("end_fone2",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"end_fone2"));?></td>
				</tr>
				<tr bgcolor="<?php echo(getLineColor($strColor));?>">
					<td align="right"><strong>*<?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
					<td align="left"><?php echo(getValue($objRS,"obs"));?></td>
				</tr>
			<?php }?>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
				<tr>
					<td colspan="2">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<!--td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td><td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td-->
								<td width="35%" align="right">
									<button onClick="ok();"><?php echo(getTText("ok",C_NONE));?></button>
									<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>			
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
</html>