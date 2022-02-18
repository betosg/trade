<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");	
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");	
	
	// REQUESTS
	$intCNPJ 	 = request("var_cnpj"); // CNPJ
	$intCPF	  	 = request("var_cpf");	// CPF
	$flagPJ		 = "";					// FLAG PARA INSERÇÃO OU NÃO DE PJ
	$flagPF		 = "";					// FLAG PARA INSERÇÃO OU NÃO DE PF
	$flagRE		 = "";					// FLAG PARA INSERÇÃO OU NÃO DE RELAÇÃO
	$strLocation = request("DEFAULT_LOCATION");
	
	// TRATAMENTO CASO CAMPOS VAZIOS
	if(($intCNPJ == "") || ($intCPF == "")){
		mensagem("err_sql_titulo","err_sql_desc",getTText("documentos_nao_enviados",C_NONE),"","aviso",1);
		die();
	}
	
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	//verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");
	
	// ABERTURA DE CONEXÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
	
	// SQL BUSCA DADOS CONFORME CNPJ e CPF ENVIADOS
	try {
		$objConn->beginTransaction();
		
		// BUSCA DADOS DA PJ
		$strSQL = "
			SELECT 
				  cad_pj.cod_pj
				, cad_pj.razao_social
				, cad_pj.cnpj
				, cad_pj.matricula
				, cad_pj.nome_fantasia
				, cad_pj.endprin_cep
				, cad_pj.endprin_logradouro
				, cad_pj.endprin_numero
				, cad_pj.endprin_complemento
				, cad_pj.endprin_bairro
				, cad_pj.endprin_cidade
				, cad_pj.endprin_estado
				, cad_pj.endprin_pais
				, cad_cnae_grupo.cod_digi_grupo||' - '||nome AS cnae
			FROM cad_pj
			LEFT JOIN cad_cnae_grupo ON (cad_cnae_grupo.cod_cnae_grupo = cad_pj.cod_cnae_n3)
			WHERE cad_pj.cnpj = '".$intCNPJ."'";	
		$objResultPJ = $objConn->query($strSQL);
		$objRSPJ	 = $objResultPJ->fetch();
		
		// BUSCA DADOS DA PF
		$strSQL = "
			SELECT
				  cad_pf.cod_pf
				, cad_pf.nome
				, cad_pf.cpf
				, cad_pf.rg
				, cad_pf.matricula
				, cad_pf.foto
				, cad_pf.sexo
				, cad_pf.obs
			FROM cad_pf	
			WHERE cad_pf.cpf = '".$intCPF."'";
		$objResultPF = $objConn->query($strSQL);
		$objRSPF  	 = $objResultPF->fetch();
		
		// TRATAMENTO DAS FLAGS - TRUE = JÁ EXISTE, FALSE = NÃO EXISTE
		$flagPJ = (getValue($objRSPJ,"cod_pj") != "") ? TRUE : FALSE;
		$flagPF = (getValue($objRSPF,"cod_pf") != "") ? TRUE : FALSE;
		
		// COM BASE NAS FLAGS, VERIFICA RELAÇÃO TAMBÉM
		if($flagPJ && $flagPF){
			$strSQL = "
				SELECT
					  relac_pj_pf.cod_pj_pf
					, relac_pj_pf.funcao
					, relac_pj_pf.departamento
					, relac_pj_pf.tipo
					, relac_pj_pf.categoria
					, relac_pj_pf.dt_admissao
					, relac_pj_pf.dt_demissao 
					, relac_pj_pf.obs
				FROM relac_pj_pf 
				INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj)
				INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf)
				WHERE relac_pj_pf.dt_demissao IS NULL
				AND cad_pf.cod_pf = ".getValue($objRSPF,"cod_pf")."
				AND cad_pj.cod_pj = ".getValue($objRSPJ,"cod_pj");
			$objResultRE = $objConn->query($strSQL);
			$objRSRE     = $objResultRE->fetch();
			$flagRE 	 = (getValue($objRSRE,"cod_pj_pf") != "") ? TRUE : FALSE;
		} else{
			// MARCA FLAG PARA INSERIR
			// UMA NOVA RELAÇÃO PARA PJ
			$flagRE = FALSE;
		}
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// Função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
	// IMPORTANTE: Busca o PRODUTO de HOMOLOGAÇÃO
	// CORRENTE PARA inserção de PEDIDO - garantindo
	// a cascata para PEDIDO, TITULO e LANÇAMENTO
	try {
		$strSQL = "	SELECT
						  prd_produto.cod_produto
						, prd_produto.rotulo
						, prd_produto.valor
						, prd_produto.descricao
						, prd_produto.dt_ini_val_produto
						, prd_produto.dt_fim_val_produto
						, prd_produto.tipo
					FROM  prd_produto
					WHERE CURRENT_DATE BETWEEN prd_produto.dt_ini_val_produto AND prd_produto.dt_fim_val_produto 
					AND prd_produto.tipo = 'homo'
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
		$dblVlrProduto = FloatToMoeda($dblVlrProduto);
		$dtIniValidade = getValue($objRSProd,"dt_ini_val_produto");
		$dtFimValidade = getValue($objRSProd,"dt_fim_val_produto");
		$strTipoProd   = getValue($objRSProd,"tipo");
	}
	
	$intCodContaBanco = getVarEntidade($objConn,"pedido_homo_cod_conta_banco"); // COLETA CONTA PADRÃO PARA HOMOLOGAÇÕES
	$intCodCFGBoleto  = getVarEntidade($objConn,"cod_cfg_boleto_padrao");       // COLETA BANCO PADRÃO PARA HOMOLOGAÇÕES
	
	// Calcula a DATA DE VENCIMENTO
	$strTIPO = "homo";
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
<script type="text/javascript">

var intQtdeClick = 0;
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
	
	<?php if(!$flagPJ){?>
	// Consistência de Dados da Pessoa Jurídica [PJ]
	if(
		(document.form_insert.var_pj_cnpj.value == "")||
		(document.form_insert.var_pj_razao_social.value == "")||
		(document.form_insert.var_pj_nome_fantasia.value == "")||
		(document.form_insert.var_pj_cnae_grupo.value == "")||
		(document.form_insert.var_pj_endprin_cep.value == "")||
		(document.form_insert.var_pj_endprin_logradouro.value == "")||
		(document.form_insert.var_pj_endprin_numero.value == "")||
		(document.form_insert.var_pj_endprin_bairro.value == "")||
		(document.form_insert.var_pj_endprin_cidade.value == "")||
		(document.form_insert.var_pj_endprin_estado.value == "")||
		(document.form_insert.var_pj_endprin_pais.value == "")
	){ strErrMSG += "\n\nDADOS DA PESSOA JURÍDICA:" }
	strErrMSG += (document.form_insert.var_pj_cnpj.value == "") ? "\nCNPJ da Empresa" : "";
	strErrMSG += (document.form_insert.var_pj_razao_social.value == "") ? "\nRazão Social" : "";
	strErrMSG += (document.form_insert.var_pj_nome_fantasia.value == "") ? "\nNome Fantasia" : "";
	strErrMSG += (document.form_insert.var_pj_cnae_grupo.value == "") ? "\nCnae GRUPO" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_cep.value == "") ? "\nCep" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_logradouro.value == "") ? "\nLogradouro" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_numero.value == "") ? "\nNúmero" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_bairro.value == "") ? "\nBairro" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_cidade.value == "") ? "\nCidade" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_estado.value == "") ? "\nEstado" : "";
	strErrMSG += (document.form_insert.var_pj_endprin_pais.value == "") ? "\nPaís" : "";
	<?php }?>
	
	<?php if(!$flagPF){?>
	// Consistência de Dados do Colaborador [PF]
	if(
		(document.form_insert.var_pf_nome.value == "")||
		(document.form_insert.var_pf_sexo.value == "")
	){ strErrMSG += "\n\nDADOS DA PESSOA FÍSICA:" }
	/*strErrMSG += (document.form_insert.var_pf_rg.value   == "") ? "\nRG do Colaborador" : "";*/
	strErrMSG += (document.form_insert.var_pf_nome.value == "") ? "\nNome do Colaborador" : "";
	strErrMSG += (document.form_insert.var_pf_sexo.value == "") ? "\nSexo do Colaborador" : "";
	<?php }?>
	
	<?php if(!$flagRE){?>
	if(document.form_insert.var_vaga_admissao.value == ""){ strErrMSG += "\n\nDADOS DA VAGA:" }
	strErrMSG += (document.form_insert.var_vaga_admissao.value == "") ? "\nData de Admissão" : "";
	<?php }?>
	
	// Consistência de Dados da HOMOLOGAÇÃO
	if(document.form_insert.var_homo_data.value == ""){ strErrMSG += "\n\nDADOS DA HOMOLOGAÇÃO:" }
	strErrMSG += (document.form_insert.var_homo_data.value == "") ? "\nData da Homologação" : "";
	
	if(
		((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (MoedaToFloat(document.form_insert.var_tit_valor.value) == 0))||
		((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (document.form_insert.var_tit_dt_pgto.value == ""))||
		((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (getDDiff(getNow(),getVDate(document.form_insert.var_tit_dt_pgto.value,'ptb'))>0))||
		((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (getDDiff(getVDate(document.form_insert.var_homo_data.value,'ptb'),getVDate(document.form_insert.var_tit_dt_pgto.value,'ptb'))>0))||
		(document.form_insert.var_tit_valor.value 			== "")||
		(document.form_insert.var_tit_dt_vcto.value 		== "")||
		(document.form_insert.var_tit_centro_custo.value 	== "")||
		(document.form_insert.var_tit_conta.value 			== "")||
		(document.form_insert.var_tit_historico.value 		== "")||
		(document.form_insert.var_tit_boleto.value 		== "")
	){ strErrMSG += "\n\nDADOS DO TÍTULO:" }
	
	if(document.form_insert.var_tit_valor.value != ""){
		// alert(MoedaToFloat(document.form_insert.var_tit_valor.value));
		// alert(getCheckedValue(document.form_insert.var_tit_opcao_gerar));
		strErrMSG += ((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (MoedaToFloat(document.form_insert.var_tit_valor.value) == 0)) ? "\nTítulo não pode ter valor zerado, caso queira emití-lo já quitado" : "";
	}
	strErrMSG += (document.form_insert.var_tit_valor.value 			== "") ? "\nValor do Título" : "";
	strErrMSG += (document.form_insert.var_tit_dt_vcto.value 		== "") ? "\nData de Vencimento" : "";
	strErrMSG += ((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (document.form_insert.var_tit_dt_pgto.value == "")) ? "\nData de Pagamento não Pode ser Vazia, Caso Queria Emitir Título já Quitado" : ""; 
	strErrMSG += ((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (getDDiff(getNow(),getVDate(document.form_insert.var_tit_dt_pgto.value,'ptb'))>0)) ? "\nData de Pagamento Maior que Data Atual" : "";
	strErrMSG += ((getCheckedValue(document.form_insert.var_tit_opcao_gerar) == "TIT_NEW") && (getDDiff(getVDate(document.form_insert.var_homo_data.value,'ptb'),getVDate(document.form_insert.var_tit_dt_pgto.value,'ptb'))>0)) ? "\nData de Pagamento Maior que Data de Homologação" : "";
	strErrMSG += (document.form_insert.var_tit_centro_custo.value 	== "") ? "\nCentro de Custo" : "";
	strErrMSG += (document.form_insert.var_tit_conta.value 			== "") ? "\nConta Banco" : "";
	strErrMSG += (document.form_insert.var_tit_historico.value 		== "") ? "\nHistórico do Título" : "";
	strErrMSG += (document.form_insert.var_tit_boleto.value 		== "") ? "\nModelo de Boleto" : "";
	
	if(strErrMSG != ""){
		strErrMSG = "Informe os campos obrigatórios abaixo:\n" + strErrMSG;
		alert(strErrMSG);
		return(null);
	} else{
		//Deixei o botão invisível para evitar que seja clicado mais de uma vez. By Lumertz 29.05.2013
		document.getElementById('but_ok').style.display = 'none';
		submeterForm();
	}
}
function ok(){ 
	strLocation = "../modulo_SdHomologacao/";
	validateFormFields();			
}
function cancelar(){ 
	document.location.href = "STverifydocs.php";
}
function submeterForm(){ 
	document.form_insert.DEFAULT_LOCATION.value = strLocation;
	document.form_insert.submit();	
}
</script>
</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<!-- body style="margin:10px;background-color:#FFFFFF;" -->
<!-- USO -->
<center>
<?php athBeginFloatingBox("725","",getTText("homologacao",C_TOUPPER)." - (".getTText("inserir_fast",C_NONE).")",CL_CORBAR_GLASS_1); ?>
<form name="form_insert" action="STverifydocsprocess.php" method="post">
	<input type="hidden" name="var_flag_insert_pf" value="<?php echo(($flagPF) ? "TRUE" : "FALSE");?>" />
	<input type="hidden" name="var_flag_insert_pj" value="<?php echo(($flagPJ) ? "TRUE" : "FALSE");?>" />
	<input type="hidden" name="var_flag_insert_re" value="<?php echo(($flagRE) ? "TRUE" : "FALSE");?>" />
	
	<input type="hidden" name="var_descricao_prod" id="var_descricao_prod" value="<?php echo($strDescProd);?>" />
	<input type="hidden" name="var_cod_produto"    id="var_cod_produto"    value="<?php echo($intCodProduto);?>" />
	
	<input type="hidden" name="DEFAULT_LOCATION" value="" />
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="705" bgcolor="#FFFFFF" class="table_master" style="border:1px solid #BBB;">
		<tr><td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("rotulo_dialog",C_NONE));?>:</strong></td></tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 75px 10px 75px;">
				<table cellspacing="2" cellpadding="4" border="0" width="100%">
					<!-- DADOS PJ -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pj",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<?php if($flagPJ){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_pj",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRSPJ,"cod_pj"));?><input type="hidden" name="var_cod_pj" value="<?php echo(getValue($objRSPJ,"cod_pj"));?>" /></td>
					</tr>
					<?php }?>
					<?php if($flagPJ){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("matricula",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRSPJ,"matricula"));?></td>
					</tr>
					<?php }?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cnpj",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"cnpj"));?>
								<input type="hidden" name="var_pj_cnpj" value="<?php echo(getValue($objRSPJ,"cnpj"));?>" />
							<?php } else{?>
								<?php echo($intCNPJ);?>
								<input type="hidden" name="var_pj_cnpj" value="<?php echo($intCNPJ);?>" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("razao_social",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"razao_social"));?>
							<?php } else{?>
								<input type="text" name="var_pj_razao_social" id="var_pj_razao_social" size="50" maxlength="100" value="" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("nome_fantasia",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"nome_fantasia"));?>
							<?php } else{?>
								<input type="text" name="var_pj_nome_fantasia" id="var_pj_nome_fantasia" size="30" maxlength="22" value="" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cnae_grupo",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"cnae"));?>
							<?php } else{?>
								<select name="var_pj_cnae_grupo" id="var_pj_cnae_grupo" style="width:250px;">
									<?php echo(montaCombo($objConn,"SELECT cod_cnae_grupo, cad_cnae_grupo.cod_digi_grupo||' - '||nome AS cnae FROM cad_cnae_grupo","cod_cnae_grupo","cnae","2278"));?>
								</select>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cep",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_cep"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_cep" id="var_pj_endprin_cep" size="10" maxlength="8" onKeyPress="return validateNumKey(event);"/>
								&nbsp;<span><img src="../img/icon_zoom_disabled.gif" border="0" style="cursor:pointer" onClick="ajaxBuscaCEP('var_pj_endprin_cep','var_pj_endprin_logradouro','var_pj_endprin_bairro','var_pj_endprin_cidade','var_pj_endprin_estado','var_pj_endprin_numero','loader_cep');" /></span>
								&nbsp;<span id="loader_cep"></span>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("logradouro",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_logradouro"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_logradouro" id="var_pj_endprin_logradouro" size="40" maxlength="100" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("numero",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_numero"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_numero" id="var_pj_endprin_numero" size="5" maxlength="10" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("complemento",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_complemento"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_complemento" id="var_pj_endprin_complemento" size="10" maxlength="20" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("bairro",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_bairro"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_bairro" id="var_pj_endprin_bairro" size="30" maxlength="100" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("cidade",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_cidade"));?>
							<?php } else{?>
								<input type="text" name="var_pj_endprin_cidade" id="var_pj_endprin_cidade" size="30" maxlength="100" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("estado",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPJ){?>
								<?php echo(getValue($objRSPJ,"endprin_estado")." ".getValue($objRSPJ,"endprin_pais"));?>
							<?php } else{?>
								<select name="var_pj_endprin_estado" id="var_pj_endprin_estado" style="width:50px;">
									<?php echo(montaCombo($objConn,"SELECT sigla_estado, sigla_estado FROM lc_estado","sigla_estado","sigla_estado",""));?>
								</select>
								&nbsp;<?php echo("BRASIL");?><input type="hidden" name="var_pj_endprin_pais" id="var_pj_endprin_pais" value="BRASIL" />
							<?php }?>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					
					<!-- DADOS PF -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_pf",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<?php if($flagPF){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("cod_pf",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php echo(getValue($objRSPF,"cod_pf"));?>
							<input type="hidden" name="var_cod_pf" value="<?php echo(getValue($objRSPF,"cod_pf"));?>" />
						</td>
					</tr>
					<?php }?>
					<?php if($flagPF){?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("matricula",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left"><?php echo(getValue($objRSPF,"matricula"));?></td>
					</tr>
					<?php }?>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("cpf",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPF){?>
								<?php echo(getValue($objRSPF,"cpf"));?>
								<input type="hidden" name="var_pf_cpf" value="<?php echo(getValue($objRSPF,"cpf"));?>" />
							<?php } else{?>
								<?php echo($intCPF);?>
								<input type="hidden" name="var_pf_cpf" value="<?php echo($intCPF);?>" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("rg",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPF){?>
								<?php echo(getValue($objRSPF,"rg"));?>
							<?php } else{?>
								<input type="text" name="var_pf_rg" id="var_pf_rg" size="20" value="" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("nome",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPF){?>
								<?php echo(getValue($objRSPF,"nome"));?>
							<?php } else{?>
								<input type="text" name="var_pf_nome" id="var_pf_nome" size="50" value="" onBlur="document.getElementById('var_tit_historico').value = document.getElementById('var_descricao_prod').value + ' ('+this.value+')';" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top">*<strong><?php echo(getTText("sexo",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPF){?>
								<?php echo(getValue($objRSPF,"sexo"));?>
							<?php } else{?>
							<select name="var_pf_sexo" id="var_pf_sexo" style="width:40px;" >
								<option value="M"><?php echo(getTText("M",C_TOUPPER))?></option>
								<option value="F"><?php echo(getTText("F",C_TOUPPER))?></option>
							</select>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" width="23%" valign="top"><strong><?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
						<td align="left"  width="77%" valign="top">
							<?php if($flagPF){?>
								<?php echo(getValue($objRSPF,"obs"));?>
							<?php } else{?>
							<textarea name="var_pf_obs" id="var_pf_obs" rows="5" cols="60"></textarea>
							<?php }?>
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<!-- DADOS DA VAGA -->
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_da_vaga",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("categoria",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php if($flagRE){?>
								<?php echo(getValue($objRSRE,"categoria"));?>
								<input type="hidden" name="var_cod_pj_pf" value="<?php echo(getValue($objRSRE,"cod_pj_pf"));?>" />
							<?php } else{?>
								<input type="hidden" name="var_cod_pj_pf" value="<?php echo(getValue($objRSRE,"cod_pj_pf"));?>" />
								<select name="var_vaga_categoria" style="width:120px;" >
									<option value=""></option>
									<option value="GERAL" selected="selected"><?php echo(getTText("GERAL",C_TOUPPER))?></option>
									<option value="ESPECIAL"><?php echo(getTText("ESPECIAL",C_TOUPPER))?></option>
									<option value="PLENO"   ><?php echo(getTText("PLENO",C_TOUPPER))?></option>
								</select>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="23%" align="right" valign="top"><strong><?php echo(getTText("tipo",C_UCWORDS));?>:</strong></td>
						<td width="77%" align="left">
							<?php if($flagRE){?>
								<?php echo(getValue($objRSRE,"tipo"));?>
							<?php } else{?>
								<select name="var_vaga_tipo" id="var_vaga_tipo" style="width:120px;" >
									<option value=""></option>
									<option value="AUTONOMO"><?php echo(getTText("AUTONOMO",C_TOUPPER))?></option>
									<option value="AVULSO"><?php echo(getTText("AVULSO",C_TOUPPER))?></option>
									<option value="TEMPORARIO"><?php echo(getTText("TEMPORÁRIO",C_TOUPPER))?></option>
									<option value="EMPREGADO" selected="selected"><?php echo(getTText("EMPREGADO",C_TOUPPER))?></option>
									<option value="ESTAGIO"><?php echo(getTText("ESTAGIÁRIO",C_TOUPPER))?></option>
								</select>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("funcao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<?php if($flagRE){?>
								<?php echo(getValue($objRSRE,"funcao"));?>
							<?php } else{?>
								<input type="text" name="var_vaga_funcao" id="var_vaga_funcao" size="50" maxlength="100" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("departamento",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<?php if($flagRE){?>
								<?php echo(getValue($objRSRE,"departamento"));?>
							<?php } else{?>
								<input type="text" name="var_vaga_departamento" id="var_vaga_departamento" size="50" maxlength="100" />
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("admissao",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<?php if($flagRE){?>
								<?php echo(dDate(CFG_LANG,getValue($objRSRE,"dt_admissao"),false));?>
							<?php } else{?>
								<input type="text" name="var_vaga_admissao" id="var_vaga_admissao" size="14" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" />
								<span class="comment_peq"><?php echo(getTText("formato_data",C_NONE));?></span>
							<?php }?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs_vaga",C_UCWORDS));?>:</strong></td>
						<td align="left">
							<?php if($flagRE){?>
								<?php echo(getValue($objRSRE,"obs"));?>
							<?php } else{?>
								<textarea name="var_vaga_obs" id="var_vaga_obs" rows="5" cols="60"></textarea>
							<?php }?>
						</td>		
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_homologacao",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs_homo",C_UCWORDS));?>:</strong></td>
						<td align="left"><textarea name="var_homo_obs" rows="5" cols="60"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top">*<strong><?php echo(getTText("data_homo",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_homo_data" size="14" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" /><span class="comment_peq"><?php echo(getTText("formato_data",C_NONE));?></span></td>					
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					
					
					<tr><td></td><td align="left" class="destaque_gde"><strong><?php echo(getTText("dados_titulo",C_TOUPPER));?></strong></td></tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("opcao_tit",C_NONE));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="radio" name="var_tit_opcao_gerar" value="TIT_NEW" class="inputclean" checked="checked" />
							<?php echo(getTText("opcao_gerar_tit_quitado",C_NONE));?><br />
							<input type="radio" name="var_tit_opcao_gerar" value="TIT_OLD" class="inputclean" />
							<?php echo(getTText("opcao_n_gerar_tit_quitado",C_NONE));?>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("tipo_documento",C_UCWORDS)); ?>:</strong></td>
						<td align="left"  valign="top"><?php echo(getTText("boleto",C_NONE));?></td>
						<input type="hidden" name="var_tit_tipo_documento" value="BOLETO" />
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("valor",C_UCWORDS)); ?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_valor" value="<?php echo($dblVlrProduto);?>" size="10" maxlength="10" onKeyPress="javascript:return validateFloatKeyNew(this,event);" dir="rtl" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("dt_vcto",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input type="text" name="var_tit_dt_vcto" value="<?php echo($dtVcto);?>" size="14" maxlength="10" onKeyDown="FormataInputData(this,event);" onKeyPress="javascript:return validateNumKey(event);" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("dt_pagamento",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<input type="text" name="var_tit_dt_pgto" id="var_tit_dt_pgto" value="<?php echo(dDate(CFG_LANG,now(),false));?>" size="14" maxlength="10" onKeyDown="FormataInputData(this,event);" onKeyPress="javascript:return validateNumKey(event);" />
							&nbsp;<span class="comment_med"><?php echo(getTText("obs_data_pagamento_titulo_quitado",C_NONE));?></span>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("centro_custo",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_centro_custo" style="width:180px;">
							<?php echo(montaCombo($objConn,"SELECT cod_centro_custo, nome FROM fin_centro_custo WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_centro_custo","nome","","")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("conta",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_conta" style="width:180px;">
							<?php echo(montaCombo($objConn, " SELECT cod_conta, nome FROM fin_conta WHERE dtt_inativo IS NULL ORDER BY ordem, nome","cod_conta","nome",$intCodContaBanco,"")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("plano_conta",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_plano_conta" size="1" style="width:240px;">
							<?php echo(montaCombo($objConn,"SELECT cod_plano_conta, cod_reduzido ||' '|| nome AS rotulo FROM fin_plano_conta WHERE dtt_inativo IS NULL ORDER BY cod_reduzido, ordem, nome ", "cod_plano_conta", "rotulo", getVarEntidade($objConn,"pedido_homo_cod_plano_conta"), "")); ?>
							</select>
						</td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("historico",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_historico" id="var_tit_historico" value="<?php echo((getValue($objRSPF,"cod_pf") != "") ? $strDescProd." (".getValue($objRSPF,"nome").")" : $strDescProd);?>" size="55" maxlength="100" /></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("num_lcto",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_numero_lcto" value="" size="15" maxlength="30" /><span class="comment_med"><?php echo(getTText("obs_numlcto",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("num_documento",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><input name="var_tit_numero_documento" value="<?php echo(str_replace(" ","",(str_replace(":","",(str_replace("-","",now()))))));?>" size="15" maxlength="30" /><span class="comment_med"><?php echo(getTText("obs_numlcto",C_NONE));?></span></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong><?php echo(getTText("obs",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top"><textarea name="var_tit_obs" cols="60" rows="5"></textarea></td>
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td align="right" valign="top"><strong>*<?php echo(getTText("boleto",C_UCWORDS));?>:</strong></td>
						<td align="left"  valign="top">
							<select name="var_tit_boleto" size="1" style="width:160px;">
							<?php echo(montaCombo($objConn, " SELECT cod_cfg_boleto, descricao FROM cfg_boleto WHERE dtt_inativo IS NULL ORDER BY descricao ", "cod_cfg_boleto", "descricao", $intCodCFGBoleto, "")); ?>
							</select>&nbsp;<!--<input type="checkbox" name="var_exibir_boleto" id="var_exibir_boleto" value="T" checked="checked" style="border:none;background:none;">Exibir boleto após gerar o título-->
						</td>
					</tr>
					<!-- DIALOG INSERT -->
					
					<tr><td colspan="2">&nbsp;</td></tr>
					
					<tr><td colspan="2" style="border-bottom:1px solid #CCC;text-align:left"><span class="comment_peq"><?php echo(getTText("campos_obrig",C_NONE));?></span></td></tr>
					<tr>
						<td colspan="2">
							<table cellpadding="0" cellspacing="0" border="0" width="100%">
								<tr>
									<td width="10%" align="right"><img src="../img/mensagem_aviso.gif" /></td>
									<td width="55%" align="left" style="padding-left:10px;"><?php echo(getTText("aviso_gerar_fast",C_NONE));?></td>
									<td width="35%" align="right">
										<button id="but_ok"       onClick="ok();return false;"><?php echo(getTText("ok",C_NONE));?></button>
										<button id="but_cancelar" onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
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