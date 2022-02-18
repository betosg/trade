<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodPJ = request("var_cod_pj");
	$intCPF   = request("var_cpf");
	
	// Verificação de ACESSO
	// Carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// Verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_FAST");
	
	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// SQL
	try {
		$strSQL = "
			SELECT
				  cad_pf.cod_pf
				, cad_pf.nome
				, cad_pf.apelido
				, cad_pf.rg
				, cad_pf.cpf
				, cad_pf.sexo
				, cad_pf.foto
				, cad_pf.matricula
				, cad_pf.obs
			FROM cad_pf 
			WHERE cad_pf.cpf = '".$intCPF."'";
		//die($strSQL);
		$objResult  = $objConn->query($strSQL);
		$objRS	    = $objResult->fetch();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// IMPORTANTE: Busca o PRODUTO de CARD
	// CORRENTE PARA inserção de PEDIDO - garantindo
	// a cascata para PEDIDO, TITULO e LANÇAMENTO
	try {
		$strSQL = "
				SELECT
					 prd_produto.cod_produto
					,prd_produto.rotulo
					,prd_produto.valor
					,prd_produto.descricao
					,prd_produto.dt_ini_val_produto
					,prd_produto.dt_fim_val_produto
					,prd_produto.tipo
				FROM prd_produto
				WHERE CURRENT_DATE BETWEEN prd_produto.dt_ini_val_produto AND prd_produto.dt_fim_val_produto 
				AND prd_produto.tipo = 'card'
				AND	prd_produto.dtt_inativo IS NULL
				ORDER BY prd_produto.sys_dtt_ins DESC, prd_produto.valor DESC ";
		$objResultProd = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Caso nenhum Produto encontrado, então 
	// exception avisando que produto não cadastrado
	if($objResultProd->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("produto_homo_validade_off",C_NONE),"","erro",1);
		die();
	} else{
		// Fetch dos dados do produto válido corrente
		$objRSProd = $objResultProd->fetch();
		// Coletando dados do produto
		$intCodProduto = getValue($objRSProd,"cod_produto");
		$strRotuloProd = getValue($objRSProd,"rotulo");
		$strDescProd   = getValue($objRSProd,"descricao");
		$dblVlrProduto = getValue($objRSProd,"valor");
		$dblVlrProduto = MoedaToFloat($dblVlrProduto);
		$dtIniValidade = getValue($objRSProd,"dt_ini_val_produto");
		$dtFimValidade = getValue($objRSProd,"dt_fim_val_produto");
		$strTipoProd   = getValue($objRSProd,"tipo");
	}
		
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// Calcula a DATA DE VENCIMENTO
	$strTIPO = "card";
	$intQtdeDiasVctoPadrao = getVarEntidade($objConn,"pedido_qtde_dias_vcto_padrao");
	$intQtdeDiasVctoPadrao = ($intQtdeDiasVctoPadrao == "") ? 0 : $intQtdeDiasVctoPadrao;
	$intQtdeDiasVctoPadrao = (($strTIPO == "homo") || ($strTIPO == "card")) ? "2" : $intQtdeDiasVctoPadrao;
	$dtVcto = dateAdd("d", $intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	if(getWeekDay($dtVcto) == "sabado"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 3;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}elseif(getWeekDay($dtVcto) == "domingo"){
		$intQtdeDiasVctoPadrao = $intQtdeDiasVctoPadrao + 2;
		$dtVcto = dateAdd("d",$intQtdeDiasVctoPadrao, date("Y-m-d"), false);
	}
	$dtVcto = dDate(CFG_LANG, $dtVcto, false);
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css"></style>
		<script type="text/javascript">
			var strLocation = null;
			function changeDisplay(prIDOne, prIDTwo){
				document.getElementById(prIDOne).style.display = 'none';
				document.getElementById(prIDTwo).style.display = 'block';
			}
			
			function ajaxBuscaPF(prCPF){
				// VERIFICA SE PF JÁ EXISTE
				var objAjax;
				var strReturnValue;
				var strDB;
				var strSQL;
				
				// Tratamento BREVE, caso o nome de usuário 
				// esteja vazio ou nulo, retorno nulo
				if(prCPF == null || prCPF == ""){
					return(null);
				}
				// Seta o SQL, cria o AJAX
				strSQL  = "SELECT cpf FROM cad_pf WHERE cpf = '"+ prCPF +"';";
				objAjax = createAjax();
				// Coloca LOADER
				document.getElementById('loader_ajax_cpf').innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' width='13' />";
				objAjax.onreadystatechange = function(){
					if(objAjax.readyState == 4) {
						if(objAjax.status == 200) {
							strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
							// alert(strReturnValue);
							// alert(prSQL);
							// imgstatus_fechado.gif
							// icon_wrong.gif
							// verifica se retornou dados
							if(strReturnValue.indexOf('|') != -1){
								document.getElementById('loader_ajax_cpf').innerHTML = "<span style='color:red;'>(CPF <em><b>"+ prCPF +"</b></em>&nbsp; JÁ ESTÁ CADASTRADO)</span>";
								document.getElementById('var_pf_cpf').value = "";
							}
							setTimeout("document.getElementById('loader_ajax_cpf').innerHTML = ''",3000);
						}
						else {
							alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET", "../_ajax/returndados.php?var_sql=" + strSQL,true); 
				objAjax.send(null); 
			}
			
			function validateFormFields(){
				// Verifica se campos obrigatórios estão em branco
				var strErrMSG;
				var auxBool;
				strErrMSG = "";
				
				<?php if(getValue($objRS,"cod_pf") == ""){?>
				// Consistência de Dados do Colaborador [PF]
				if(
					(document.form_insert.var_pf_rg.value == "")||
					(document.form_insert.var_pf_nome.value == "")||
					(document.form_insert.var_pf_apelido.value == "")||
					(document.form_insert.var_pf_sexo.value == "")
				){ strErrMSG += "\n\nDADOS DA PESSOA FÍSICA:" }
				strErrMSG += (document.form_insert.var_pf_rg.value   == "") ? "\nRG do Colaborador" : "";
				strErrMSG += (document.form_insert.var_pf_nome.value == "") ? "\nNome do Colaborador" : "";
				strErrMSG += (document.form_insert.var_pf_apelido.value == "") ? "\nNome Credencial" : "";
				strErrMSG += (document.form_insert.var_pf_sexo.value == "") ? "\nSexo do Colaborador" : "";
				<?php }?>
				
				if(document.form_insert.var_vaga_admissao.value == ""){ strErrMSG += "\n\nDADOS DA VAGA:" }
				strErrMSG += (document.form_insert.var_vaga_admissao.value == "") ? "\nData de Admissão" : "";
						
				if(document.getElementById("var_produto").value 		== ""){ strErrMSG += "\n\nDADOS PRODUTO/PEDIDO:"; }
				if(document.getElementById("var_produto").value 		== ""){ strErrMSG += "\nProduto"; }
				
				// CASO A OPÇÃO PARA GERAÇÃO DE TÍTULO ESTEJA MARCADA
				// ENTÃO DEVE TRATAR OS CAMPOS OBRIGATÓRIOS C/PAGAR RECEBER
				if(document.getElementById("var_opcao_gerar_titulo").checked == true){
					if(
						(document.getElementById("var_valor").value 			== "")||
						(document.getElementById("var_dt_vcto").value 			== "")||
						(document.getElementById("var_cod_conta").value 		== "")||
						(document.getElementById("var_cod_plano_conta").value 	== "")||
						(document.getElementById("var_cod_centro_custo").value	== "")||
						(document.getElementById("var_historico").value 		== "")||
						(document.getElementById("var_cod_cfg_boleto").value 	== "")
					  ){ strErrMSG += "\n\nDADOS TÍTULO:"; }
					if(document.getElementById("var_valor").value 			 == ""){ strErrMSG += "\nValor do Título"; }
					if(document.getElementById("var_dt_vcto").value 		 == ""){ strErrMSG += "\nData de Vencimento"; }
					if(document.getElementById("var_cod_conta").value 		 == ""){ strErrMSG += "\nConta"; }
					if(document.getElementById("var_cod_plano_conta").value  == ""){ strErrMSG += "\nPlano de Contas"; }
					if(document.getElementById("var_cod_centro_custo").value == ""){ strErrMSG += "\nCentro de Custo"; }
					if(document.getElementById("var_historico").value 		 == ""){ strErrMSG += "\nHistórico"; }
					if(document.getElementById("var_cod_cfg_boleto").value 	 == ""){ strErrMSG += "\nTipo de Boleto"; }
				}
				
				if(strErrMSG != ""){
					strErrMSG = "Informe os campos obrigatórios abaixo:\n" + strErrMSG;
					alert(strErrMSG);
					return(null);
				} else{
					submeterForm();
				}
			}
			function ok(){ 
				strLocation = "STviewpfs.php?var_chavereg=<?php echo($intCodPJ);?>";
				validateFormFields();
			}
			function cancelar(){ 
				document.location.href = "STverifycpf.php?var_chavereg=<?php echo($intCodPJ);?>&var_flag_inserir=INS_CARD";
			}
			function submeterForm(){ 
				document.form_insert.DEFAULT_LOCATION.value = strLocation;
				document.form_insert.submit();	
			}
			// FUNÇÃO QUE GERENCIA OS COLLAPSE/DISPLAY DA TABELA DE TITULOS
		function showTable(){
			if(document.getElementById('table_titulo').style.display == 'block'){ 
				document.getElementById('table_titulo').style.display = 'none'; 
			} else{
				document.getElementById('table_titulo').style.display = 'block';
			}
			resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
		}
		
		// FUNÇÃO QUE LOCALIZA VIA AJAX OS DADOS DO PRODUTO
		function changePROD(){
			if(document.getElementById("var_produto") 		== null){ return(null); }
			if(document.getElementById("var_produto").value == null){ return(null); }
			if(document.getElementById("var_produto").value == ""  ){ return(null); }
			var objAjax;
			var strReturnValue;
			var strSQL = "SELECT prd_produto.valor, prd_produto.descricao FROM prd_produto WHERE cod_produto = "+document.getElementById("var_produto").value;
			document.getElementById("ajax_loader").innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' />";
			objAjax = createAjax();
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						arrReturn = strReturnValue.split("|");
						if((arrReturn[0] != "")||(arrReturn[0] != null)){ document.getElementById("prod_valor").innerHTML = FloatToMoeda(arrReturn[0]); document.getElementById("var_valor").value = FloatToMoeda(arrReturn[0]); }
						if((arrReturn[1] != "")||(arrReturn[1] != null)){ document.getElementById("prod_descr").innerHTML = arrReturn[1]; }
						if((arrReturn[1] != "")||(arrReturn[1] != null)){ document.getElementById("var_descricao_prod").value = arrReturn[1]; }
						<?php if(getValue($objRS,"cod_pf") != ""){?>
						document.getElementById("var_historico").value = arrReturn[1] + ' ('+ document.getElementById('var_nome_colaborador').value + ')' ;
						<?php } else{?>
						document.getElementById("var_historico").value = arrReturn[1] + ' ('+ document.getElementById('var_pf_nome').value + ')' ;
						<?php }?>
						document.getElementById("ajax_loader").innerHTML = "";
						// FAÇA ALGO!
					} else {
						alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/returndados.php?var_sql=" + strSQL,  true); 
			objAjax.send(null); 
		}
		
		function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
			strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
			AbreJanelaPAGE(strLink, "570", "270");
		}
		</script>
	</head>
<body style="margin:10px;background-color:#FFFFFF;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("725","",getTText("credencial",C_TOUPPER)." - (".getTText("inserir_fast",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="form_insert" action="STinscolabcardexec.php" method="post">
	<input type="hidden" name="DEFAULT_LOCATION" value="" />
	<input type="hidden" name="var_tipo_pf" value="<?php echo((getValue($objRS,"cod_pf") != "") ? "OLD_PF" : "NEW_PF");?>" />
	<input type="hidden" name="var_cod_pf" value="<?php echo(getValue($objRS,"cod_pf"));?>" />
	<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ);?>" />
	<input type="hidden" name="var_descricao_prod" id="var_descricao_prod" value="" />
	<input type="hidden" id="var_nome_colaborador" value="<?php echo(getValue($objRS,"nome"));?>" />
	<input type="hidden" name="var_tipo_doc" value="BOLETO">
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="705" bgcolor="#FFFFFF" class="table_master" style="border:1px solid #BBB;">
		<tr><td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td></tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 75px 10px 75px;">
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<!-- DIALOG INSERT -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pf",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					
					<?php if(getValue($objRS,"cod_pf") != ""){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_pf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"cod_pf"));?>
							<?php }?>
						</td>
					</tr>
					<?php }?>
					<?php if(getValue($objRS,"cod_pf") != ""){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("matricula",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"matricula"));?>
							<?php }?>
						</td>
					</tr>
					<?php }?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"cpf"));?>
								<input type="hidden" name="var_pf_cpf" value="<?php echo(getValue($objRS,"cpf"));?>" />
							<?php } else{?>
								<?php echo($intCPF);?>
								<input type="hidden" name="var_pf_cpf" value="<?php echo($intCPF);?>" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("rg",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"rg"));?>
							<?php } else{?>
								<input type="text" name="var_pf_rg" id="var_pf_rg" size="20" value="" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"nome"));?>
							<?php } else{?>
								<input type="text" name="var_pf_nome" id="var_pf_nome" size="50" value="" onBlur="document.getElementById('var_historico').value = document.getElementById('var_descricao_prod').value + ' ('+this.value+')';" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("nome_credencial",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"apelido"));?>
							<?php } else{?>
								<input type="text" name="var_pf_apelido" id="var_pf_apelido" size="30" maxlength="20" value="" />
								<br /><span class="comment_med">(Este é o nome que aparecerá na credencial, abrevie caso seja necessário)</span>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("sexo",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"sexo"));?>
							<?php } else{?>
							<select name="var_pf_sexo" style="width:40px;" >
								<option value="M"><?php echo(getTText("M",C_TOUPPER))?></option>
								<option value="F"><?php echo(getTText("F",C_TOUPPER))?></option>
							</select>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if(getValue($objRS,"cod_pf") != ""){?>
								<?php echo(getValue($objRS,"obs"));?>
							<?php } else{?>
							<textarea name="var_pf_obs" id="var_pf_obs" rows="5" cols="60"></textarea>
							<?php }?>
						</td>
					</tr>
					<?php if(getValue($objRS,"cod_pf") == ""){?>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td width="1%" align="right" valign="top" nowrap><strong><?php echo(getTText("foto",C_UCWORDS));?>:</strong></td>
						<td>
							<table cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td>
									<input type="text" name="var_foto" id="var_foto" value="" size="50" readonly="true" title="Foto">
									<span class="comment_med">Clique <span style="font-weight:bold;cursor:pointer;" onClick="document.getElementById('var_foto').value='';"><u>aqui</u></span> para limpar o campo foto</span>
								</td>
								<td nowrap align="left" width="99%" valign="top">
									<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('form_insert','var_foto','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/','','');">
								</td>
							</tr>
							</table>
						</td>
					</tr>
					<?php } else{?>
					<tr bgcolor="<?php getLineColor($strColor);?>">
						<td width="1%" align="right" valign="top" nowrap><strong><?php echo(getTText("foto",C_UCWORDS));?>:</strong></td>
						<td><img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"))?>/upload/fotospf/<?php echo(getValue($objRS,"foto"));?>"  height="90"></td>
					</tr>
					<?php }?>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_da_vaga",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<select name="var_vaga_categoria" style="width:120px;" >
								<option value=""></option>
								<option value="GERAL"  selected="selected" ><?php echo(getTText("GERAL",C_TOUPPER))?></option>
								<option value="ESPECIAL"><?php echo(getTText("ESPECIAL",C_TOUPPER))?></option>
								<option value="PLENO"   ><?php echo(getTText("PLENO",C_TOUPPER))?></option>
							</select>	
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<select name="var_vaga_tipo" style="width:120px;" >
								<option value=""></option>
								<option value="AUTONOMO"    ><?php echo(getTText("AUTONOMO",C_TOUPPER))?></option>
								<option value="AVULSO"      ><?php echo(getTText("AVULSO",C_TOUPPER))?></option>
								<option value="TEMPORARIO"  ><?php echo(getTText("TEMPORÁRIO",C_TOUPPER))?></option>
								<option value="EMPREGADO" selected="selected"><?php echo(getTText("EMPREGADO",C_TOUPPER))?></option>
								<option value="ESTAGIO"     ><?php echo(getTText("ESTAGIÁRIO",C_TOUPPER))?></option>
							</select>	
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("funcao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_funcao" size="50" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("departamento",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_departamento" size="50" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("admissao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_vaga_admissao" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" /><span class="comment_peq"><?php echo(getTText("formato_data",C_NONE));?></span></td>					
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs_vaga",C_UCWORDS));?>:</strong></td>
						<td align="left"><textarea name="var_vaga_obs" rows="5" cols="60"></textarea></td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					
					<!-- DADOS PEDIDO/PRODUTO -->
					<tr bgcolor="#FFFFFF">
						<td width="23%" align="right">&nbsp;</td>
						<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pedido_produto",C_TOUPPER));?></strong></td>
					</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("produto",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<select name="var_produto" id="var_produto" style="width:180px;" onChange="changePROD();">
								<option value="" selected="selected"></option>
								<?php echo(montaCombo($objConn,"SELECT UPPER(prd_produto.rotulo) AS rotulo, prd_produto.cod_produto FROM prd_produto WHERE prd_produto.tipo = 'card' AND prd_produto.dtt_inativo IS NULL AND CURRENT_DATE < dt_fim_val_produto ORDER BY valor, dt_fim_val_produto DESC","cod_produto","rotulo",""));?>
							</select>
							&nbsp;<span id="ajax_loader"></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("descricao",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><span id="prod_descr">-</span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("valor",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><span id="prod_valor">-</span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("obs_pedido",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><textarea name="var_obs_pedido" id="var_obs_pedido" rows="6" cols="60"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><input type="checkbox" name="var_opcao_gerar_titulo" id="var_opcao_gerar_titulo" value="TRUE" class="inputclean" onClick="showTable();"/></td>
						<td width="77%" align="left">&nbsp;<?php echo(getTText("opcao_pagar_ato",C_NONE));?></td>
					</tr>
					<tr><td colspan="2" height="10">&nbsp;</td></tr>
					
					<tr>
						<td colspan="2">
						<table cellpadding="4" cellspacing="0" border="0" width="100%" id="table_titulo" style="display:none;">
							<tr bgcolor="#FFFFFF">
								<td width="23%" align="right">&nbsp;</td>
								<td width="77%" align="left" class="destaque_gde"><strong><?php echo(getTText("dados_titulo",C_TOUPPER));?></strong></td>
							</tr>
							<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo_documento",C_NONE));?>:</strong></td>
								<td width="77%" align="left">BOLETO</td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("valor",C_NONE));?>:</strong></td>
								<td width="77%" align="left"><input name="var_valor" id="var_valor" value="" size="10" maxlength="10" onKeyPress="return validateFloatKeyNew(this,event);" /></td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("dt_vcto",C_NONE));?>:</strong></td>
								<td width="77%" align="left"><input type="text" name="var_dt_vcto" id="var_dt_vcto" value="<?php echo($dtVcto);?>" size="12" maxlength="10" onKeyUp="FormataInputData(this);" onKeyPress="return validateNumKey(event);" /></td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("conta",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<select name="var_cod_conta" id="var_cod_conta" size="1" style="width:180px;">
										<?php echo(montaCombo($objConn,"SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_conta","nome","",""));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("plano_conta",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<select name="var_cod_plano_conta" id="var_cod_plano_conta" size="1" style="width:240px;">
										<?php echo(montaCombo($objConn,"SELECT cod_plano_conta, cod_reduzido || ' ' || nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome","cod_plano_conta","rotulo",259,""));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("centro_custo",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<select name="var_cod_centro_custo" id="var_cod_centro_custo" size="1" style="width:160px;">
										<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_centro_custo","nome","",""));?>
									</select>
								</td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("historico",C_NONE));?>:</strong></td>
								<td width="77%" align="left"><input name="var_historico" id="var_historico" value="" size="60" maxlength="200" /></td>
							</tr>
							<!--tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("num_lcto",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<input name="var_num_lcto" id="var_num_lcto" value="<?php echo(getValue($objRS,"cod_pf").getValue($objRS,"cod_pj"))?>" size="15" maxlength="30" />
									<br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>	
								</td>
							</tr>
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("num_documento",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<input name="var_num_documento" id="var_num_documento" value="<?php echo(str_replace(" ","",(str_replace(":","",(str_replace("-","",now()))))));?>" size="15" maxlength="30" />
									<br /><span class="comment_med">Será usado caso marque a opção gerar título já quitado</span>	
								</td>
							</tr-->
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong><?php echo(getTText("obs",C_NONE));?>:</strong></td>
								<td width="77%" align="left"><textarea name="var_obs" id="var_obs" cols="60" rows="5"></textarea></td>
							</tr>				
							<tr bgcolor="<?php echo(getLineColor($strColor));?>">
								<td width="23%" align="right" valign="top"><strong>*<?php echo(getTText("boleto",C_NONE));?>:</strong></td>
								<td width="77%" align="left">
									<select name="var_cod_cfg_boleto" id="var_cod_cfg_boleto" size="1" style="width:160px;">
										<?php echo(montaCombo($objConn,"SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao","cod_cfg_boleto","descricao","",""));?>
									</select>
								</td>
							</tr>
							
							<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
							<tr><td></td><td align="left" valign="top" class="destaque_gde"><strong>ENVIO DE EMAIL</strong></td></tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<!-- ------------------------------------------ -->
							<tr bgcolor="<?php echo(CL_CORLINHA_2);?>">
								<td align="right" valign="top"><strong><?php echo(getTText("enviar_email_com_boleto_qm",C_NONE));?></strong></td>
								<td align="left"  valign="top">
									<input type="radio" name="var_opcao_enviar_email" id="var_opcao_enviar_email_1" value="S" class="inputclean" <?php echo((getValue($objRS,"email_pj") == "") ? 'disabled="true"' : 'checked="checked"')?> /><?php echo(getTText("sim",C_NONE))?><br/>
									<input type="radio" name="var_opcao_enviar_email" id="var_opcao_enviar_email_2" value="N" class="inputclean" <?php echo((getValue($objRS,"email_pj") == "") ? 'disabled="true" checked="checked"' : '')?> /><?php echo(getTText("nao",C_NONE))?>
									<span class="comment_peq">
									<?php 
										if(getValue($objRS,"email_pj") != ""){
											echo("<br/>".getTText("sistema_enviara_emails_para",C_NONE).":");
											echo((getValue($objRS,"email_pj") != "") ? "<br/>&bull;&nbsp;".getValue($objRS,"email_pj") : "");
										} else{
											echo("<br/>".getTText("nenhum_email_cad_para_pj",C_NONE));
										}
									?>
									</span>
								</td>
							</tr>
							<!-- ------------------------------------------ -->
							<tr><td colspan="2" height="5" bgcolor="#FFFFFF">&nbsp;</td></tr>
							<tr><td colspan="2" height="10"></tr>
						</table>
						</td>
					</tr>			
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td>
									<td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td>
									<td width="35%" align="right">
										<button onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button>
										<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>			
			</td>
		</tr>
	</table>
</form>
<?php athEndFloatingBox();?>
</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>