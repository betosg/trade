<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

$intCodDado = request("var_chavereg");


$strAcao       = request("var_acao");       // Ação para exportação (excel, word...)
$strAcaoGrid   = request("var_acaogrid");   // Ação de retorno da grade (single, multiple)
$strSQLRelOrig = request("var_strparam");   // A consulta deve chegar com as TAGs do tipo (<ASLW_APOSTROFE>, etc...) 
$strDescricao  = request("var_descricao");  // A descrição do relatório (inativo)
$strNome       = request("var_nome");       // O nome do campo para retorno para o formulário
//$strCampoRet   = requests("var_camporet");   // O nome do campo no formulário para qual o relatório deve retornar o valor
$strDBCampoRet = request("var_dbcamporet"); // O nome do campo na cosulta que deve ser retornado
$strDBCampoLbl = request("var_dbcampolbl"); // O label do campo na cosulta que deve ser retornado
$strDialogGrp  = request("var_dialog_grp"); // O índice do formulário que deve ser retornado
$strRelatTitle = request("var_relat_title");// O nome do relatório, caso ele for um ASLW
$strHTMLBody   = ""; // Variável que receberá o HTML da página para ser exibido posteriormente. (Para não usar muitos echos)

$strDBCampoRet = preg_replace("/[[:alnum:]_]+\./i","",$strDBCampoRet); //Para tirar o nome da tabela do campo que será retornado

function filtraAlias($prValue){
	return(strtolower(preg_replace("/([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+ AS )|([[:alnum:]_\"]+\.)|/i","",$prValue)));
}

/********* Verificação de acesso e localização do módulo *********/
$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "VIE");

/********* Preparação SQL - Início *********/
$strSQLRel = removeTagSQL($strSQLRelOrig); //Remove as tags
$strSQLRel = replaceParametersSession($strSQLRel); //Coloca os valores de sistema (session)
//preg_match_all("/\[(?<operador>[[:punct:]]?) +(?<campo>[[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+( AS [[:alnum:]_\"]+)*)\]/i",$strSQLRel,$arrParams); //Verifica se há funções ASLW e as coloca num array
preg_match_all("/\[([[:punct:]]?[0-9]*) +([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+( AS [[:alnum:]_\"]+)*)\]/i",$strSQLRel,$arrParams); //Verifica se há funções ASLW e as coloca num array
$strSQLRel = preg_replace("/\[[[:punct:]]([0-9])*|\]|\"/","",$strSQLRel); //retira as funções do SQL deixando somente o nome do campo com suas dependencias
/********* Preparação SQL - Fim *********/

$boolIsExportation = ($strAcao == ".xls") || ($strAcao == ".doc") || ($strAcao == ".pdf");
if($strAcao == '.pdf'){
	//seto a session do sql para executar na exportacao do pdf
	setsession($strSesPfx . "_sqlorig", $strSQLRel); 
	redirect("exportpdf_relatorio.php");
	die;
} else {
	VerificaModoExportacao($strAcao, getTText(getsession($strSesPfx . "_titulo"),C_NONE));
}
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$objConn = abreDBConn(CFG_DB);

// SQL PADRÃO DA LISTAGEM - BREVE DESCRIÇÃO
try{
	$strSQL = "	SELECT 
				  t1.cod_pj
				, t1.cod_tipo_normal
				, t1.cod_tipo_prest
				, t1.cod_categoria
				, t1.cod_segmento
				, t1.cod_atividade
				, t1.cod_atuacao
				, t1.razao_social
				, t1.nome_fantasia
				, t1.nome_comercial
				, t1.cnpj
				, t1.insc_est
				, t1.insc_munic
				, t1.email
				, t1.email_extra
				, t1.website
				, t1.contato
				, t1.num_funcionarios
				, t1.dtt_fundacao
				, t1.capital
				, t1.obs
				, t1.matricula
				, t1.cod_pj_contabil
				, t1.porte
				, t1.categoria
				, t1.socio
				, t1.img_logo
				, t1.dtt_inativo
				
				, t1.arquivo_1
				, t1.arquivo_2
				, t1.arquivo_3
				
				, t1.cod_cnae_n1
				, t1.cod_cnae_n2
				, t1.cod_cnae_n3
				, t1.cod_cnae_n4
				, t1.cod_cnae_n5
				, t2.cod_digi_secao_cnae || ' - ' || t2.nome AS cnae_secao
				, t3.cod_digi_divisao    || ' - ' || t3.nome AS cnae_divisao
				, t4.cod_digi_grupo      || ' - ' || t4.nome AS cnae_grupo
				, t5.cod_digi_classe     || ' - ' || t5.nome AS cnae_classe
				, t6.cod_digi_subclasse  || ' - ' || t6.nome AS cnae_subclasse
				
				, t1.endprin_cep
				, t1.endprin_logradouro
				, t1.endprin_numero
				, t1.endprin_complemento
				, t1.endprin_bairro
				, t1.endprin_cidade
				, t1.endprin_estado
				, t1.endprin_pais
				, t1.endprin_fone1
				, t1.endprin_fone2
				, t1.endprin_fone3
				, t1.endprin_fone4
				, t1.endprin_fone5
				, t1.endprin_fone6
				
				, t1.endcobr_cep
				, t1.endcobr_rotulo
				, t1.endcobr_logradouro
				, t1.endcobr_numero
				, t1.endcobr_complemento
				, t1.endcobr_bairro
				, t1.endcobr_cidade
				, t1.endcobr_estado
				, t1.endcobr_pais
				, t1.endcobr_fone1
				, t1.endcobr_fone2
				, t1.endcobr_fone3
				, t1.endcobr_fone4
				, t1.endcobr_fone5
				, t1.endcobr_fone6
				, t1.endcobr_email
				, t1.endcobr_contato
				
				, t1.sys_usr_ins
				, t1.sys_dtt_ins
				, t1.sys_dtt_upd
				, t1.sys_usr_upd
				FROM cad_pj t1
				LEFT JOIN cad_cnae_secao t2     ON (t1.cod_cnae_n1 = t2.cod_cnae_secao)
				LEFT JOIN cad_cnae_divisao t3   ON (t1.cod_cnae_n2 = t3.cod_cnae_divisao)
				LEFT JOIN cad_cnae_grupo t4     ON (t1.cod_cnae_n3 = t4.cod_cnae_grupo)
				LEFT JOIN cad_cnae_classe t5    ON (t1.cod_cnae_n4 = t5.cod_cnae_classe)
				LEFT JOIN cad_cnae_subclasse t6 ON (t1.cod_cnae_n5 = t6.cod_cnae_subclasse)
				WHERE t1.cod_pj = ".$intCodDado;
	$objResult1 = $objConn->query($strSQL);
	
	$strSQL = " SELECT 
					  t1.cod_conta_pagar_receber
					, t1.situacao
					, t1.vlr_conta
					, t1.vlr_saldo
					, t1.vlr_pago 
					, t1.vlr_mora_multa
					, t1.vlr_outros_acresc
					, t1.dt_emissao
					, t1.dt_vcto
					, t1.num_documento
					, t1.nosso_numero
					, t1.historico
					, t1.tipo_documento
				FROM fin_conta_pagar_receber t1 
				WHERE t1.tipo = 'cad_pj' 
				AND t1.codigo = ".$intCodDado."
				AND (t1.situacao = 'aberto' OR t1.situacao = 'lcto_parcial') 
				ORDER BY t1.sys_dtt_ins DESC
				LIMIT 500 ";  //Pedido do chamado 23909, colocamos o LIMIT em 500, e era 20
	$objResult2 = $objConn->query($strSQL);
	

	$strSQL = " SELECT 
				  t1.cod_conta_pagar_receber
				, t1.situacao
				, t1.vlr_conta
				, t1.dt_emissao
				, t1.dt_vcto
				, t1.num_documento
				, t1.nosso_numero
				, t1.historico
				, t1.tipo_documento
				, t1.vlr_outros_acresc
				, t1.vlr_mora_multa
				, t1.vlr_saldo
				, t1.vlr_pago 
				, SUM (COALESCE(t2.vlr_lcto,0) - COALESCE(t2.vlr_desc,0) + COALESCE(t2.vlr_juros,0) + COALESCE(t2.vlr_multa,0) ) vlr_creditado
			 FROM fin_conta_pagar_receber t1 
			 LEFT JOIN fin_lcto_ordinario t2 ON (t1.cod_conta_pagar_receber = t2.cod_conta_pagar_receber)
	 	 -- WHERE t1.tipo = 'cad_pj' 
		 --   AND t1.codigo = 10125
			WHERE t1.tipo = 'cad_pj' 
 			  AND t1.codigo = ".$intCodDado."
			GROUP By 1,2,3,4,5,6,7,8,9,10,11,12,13
			ORDER BY t1.sys_dtt_ins DESC
			LIMIT 500 ";  //Pedido do chamado 23909, colocamos o LIMIT em 500, e era 20
				
	$objResult3 = $objConn->query($strSQL);
}catch(PDOException $e) {
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
	die();
}

$objRS1 = $objResult1->fetch();
//tem foreach mais abaixo
//$objRS2 = $objResult2->fetch();
//$objRS3 = $objResult3->fetch();

?>
<html>
<head>
<title>
<?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php 
	if(!$boolIsExportation || $strAcao == "print"){
		echo("<link rel=\"stylesheet\" href=\"../_css/" . CFG_SYSTEM_NAME . ".css\" type=\"text/css\">
			  <link href='../_css/tablesort.css' rel='stylesheet' type='text/css'>
			  <script type='text/javascript' src='../_scripts/tablesort.js'></script>");
	}
?>
<script language="JavaScript" type="text/javascript">
	function switchColor(prObj, prColor){
		prObj.style.backgroundColor = prColor;
	}
</script>
<style type="text/css">
.tdicon{
	text-align:center;
	font-size:11px;
	font:bold;
	width:25%;		
}
img{
	border:none;
}

.folha {
    page-break-after: always;
}

</style>
</head>
<body style="margin:15px 15px 15px 15px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center"><h5><?php echo(getTText("historico_cliente",C_TOUPPER));?></h5></td>
	</tr>	
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
			<tr>
				<td align="center" valign="top" style="padding:10px 10px 10px 10px;">
					<table cellspacing="2" cellpadding="3" border="0" width="100%">
					<tr>
						<td width="18%" align="right"><strong><?php echo(getTText("cod_pj",C_NONE));?>:</strong></td>
						<td width="35%" align="left"><?php echo(getValue($objRS1,"cod_pj"));?></td>
						<td width="15%" align="right"><strong><?php echo(getTText("email",C_NONE));?>:</strong></td>
						<td width="32%" align="left" colspan="5"><?php echo(getValue($objRS1,"email"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cnpj",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"cnpj"));?></td>
						<td align="right"><strong><?php echo(getTText("email_extra",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"email_extra"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("insc_est",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"insc_est"));?></td>
						<td align="right"><strong><?php echo(getTText("website",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"website"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("insc_munic",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"insc_munic"));?></td>
						<td align="right"><strong><?php echo(getTText("dtt_fundacao",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(dDate(CFG_LANG, getValue($objRS1, "dtt_fundacao"), false));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("razao_social",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"razao_social"));?></td>
						<td align="right"><strong><?php echo(getTText("num_funcionarios",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"num_funcionarios"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("nome_fantasia",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"nome_fantasia"));?></td>
						<td align="right"><strong><?php echo(getTText("capital",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"capital"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("nome_comercial",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"nome_comercial"));?></td>
						<td align="right"><strong><?php echo(getTText("segmento",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"segmento"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("porte",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"porte"));?></td>
						<td align="right"><strong><?php echo(getTText("atuacao",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"atuacao"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("categoria",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"categoria"));?></td>
						<td align="right"><strong><?php echo(getTText("atividade",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"atividade"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("socio",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"socio"));?></td>
						<td align="right"><strong><?php echo(getTText("status",C_NONE));?>:</strong></td>
						<td align="left"><?php echo((getValue($objRS1,"dtt_inativo") == "") ? "Ativo" : "Inativo"); ?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("arquivos",C_NONE));?>:</strong></td>
						<td align="left" colspan="3">
						<?php 
						if (getValue($objRS1,"arquivo_1") != "") echo "<a href='../../".getsession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/docspj/".getValue($objRS1,"arquivo_1")."' target='_blank'>".getValue($objRS1,"arquivo_1")."</a><br>";
						if (getValue($objRS1,"arquivo_2") != "") echo "<a href='../../".getsession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/docspj/".getValue($objRS1,"arquivo_2")."' target='_blank'>".getValue($objRS1,"arquivo_2")."</a><br>";
						if (getValue($objRS1,"arquivo_3") != "") echo "<a href='../../".getsession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/docspj/".getValue($objRS1,"arquivo_3")."' target='_blank'>".getValue($objRS1,"arquivo_3")."</a><br>";
						?>
						</td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("obs",C_NONE));?>:</strong></td>
						<td align="left" colspan="3"><?php echo getValue($objRS1,"obs"); ?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("img_logo",C_NONE));?>:</strong></td>
						<td align="left" colspan="3"><?php if (getValue($objRS1,"img_logo") != "") echo "<img src='../../".getsession(CFG_SYSTEM_NAME . "_dir_cliente")."/upload/imgdin/".getValue($objRS1,"img_logo")."' border='0'>"; ?></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
			<tr>
				<td align="center" valign="top" >
					<table cellspacing="2" cellpadding="3" border="0" width="100%" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
					<tr>
						<td width="15%" align="right"><strong><?php echo(getTText("cnae_secao",C_NONE));?>:</strong></td>
						<td width="85%" align="left"><?php echo(getValue($objRS1,"cnae_secao"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cnae_grupo",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"cnae_grupo"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cnae_divisao",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"cnae_divisao"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cnae_classe",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"cnae_classe"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cnae_subclasse",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"cnae_subclasse"));?></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td height="10"></td></tr>
	<tr>
		<td align="center">
			<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
			<tr>
				<td align="center" valign="top" >
					<table cellspacing="2" cellpadding="3" border="0" width="100%" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
					<tr>
						<td align="center" colspan="2"><strong><?php echo(getTText("endereco_principal",C_TOUPPER));?></strong></td>
						<td align="center" colspan="2"><strong><?php echo(getTText("endereco_para_cobranca",C_TOUPPER));?></strong></td>
					</tr>
					<tr>
						<td width="10%"  align="right"><strong><?php echo(getTText("logradouro",C_NONE));?>:</strong></td>
						<td width="40%" align="left"><?php echo(getValue($objRS1,"endprin_logradouro"));?></td>
						<td width="10%"  align="right"><strong><?php echo(getTText("logradouro",C_NONE));?>:</strong></td>
						<td width="40%" align="left"><?php echo(getValue($objRS1,"endcobr_logradouro"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("numero",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_numero"));?></td>
						<td align="right"><strong><?php echo(getTText("numero",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_numero"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("complemento",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_complemento"));?></td>
						<td align="right"><strong><?php echo(getTText("complemento",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_complemento"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("bairro",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_bairro"));?></td>
						<td align="right"><strong><?php echo(getTText("bairro",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_bairro"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cidade",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_cidade"));?></td>
						<td align="right"><strong><?php echo(getTText("cidade",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_cidade"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("estado",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_estado"));?></td>
						<td align="right"><strong><?php echo(getTText("estado",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_estado"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("pais",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_pais"));?></td>
						<td align="right"><strong><?php echo(getTText("pais",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_pais"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("cep",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endprin_cep"));?></td>
						<td align="right"><strong><?php echo(getTText("cep",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_cep"));?></td>
					</tr>
					<tr>
						<td align="right"><strong><?php echo(getTText("fones",C_NONE));?>:</strong></td>
						<td align="left">
						<?php 
						if (getValue($objRS1,"endprin_fone1") != "") echo getValue($objRS1,"endprin_fone1")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endprin_fone2") != "") echo getValue($objRS1,"endprin_fone2")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endprin_fone3") != "") echo getValue($objRS1,"endprin_fone3")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endprin_fone4") != "") echo getValue($objRS1,"endprin_fone4")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endprin_fone5") != "") echo getValue($objRS1,"endprin_fone5")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endprin_fone6") != "") echo getValue($objRS1,"endprin_fone6")."&nbsp;&nbsp;";
						?>
						</td>
						<td align="right"><strong><?php echo(getTText("fones",C_NONE));?>:</strong></td>
						<td align="left">
						<?php
						if (getValue($objRS1,"endcobr_fone1") != "") echo getValue($objRS1,"endcobr_fone1")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endcobr_fone2") != "") echo getValue($objRS1,"endcobr_fone2")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endcobr_fone3") != "") echo getValue($objRS1,"endcobr_fone3")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endcobr_fone4") != "") echo getValue($objRS1,"endcobr_fone4")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endcobr_fone5") != "") echo getValue($objRS1,"endcobr_fone5")."&nbsp;&nbsp;";
						if (getValue($objRS1,"endcobr_fone6") != "") echo getValue($objRS1,"endcobr_fone6")."&nbsp;&nbsp;";
						?>
						</td>
					</tr>
					<tr>
						<td align="left"></td>
						<td align="left"></td>
						<td align="right"><strong><?php echo(getTText("email",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_email"));?></td>
					</tr>
					<tr>
						<td align="left"></td>
						<td align="left"></td>
						<td align="right"><strong><?php echo(getTText("contato",C_NONE));?>:</strong></td>
						<td align="left"><?php echo(getValue($objRS1,"endcobr_contato"));?></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	<?php 
	//Método "rouCount" só funciona porque no SQL tem apenas UMA tabela no FROM
	if($objResult2->rowCount()>0) { 
		?>
		<tr><td height="10"></td></tr>
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
				<tr>
					<td align="center" valign="top" >
						<table cellspacing="2" cellpadding="3" border="0" width="100%" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
						<tr>
							<td align="center" colspan="8"><strong><?php echo(getTText("ult_titulos_abertos",C_TOUPPER));?></strong>&nbsp;<small>(500)</small></td>
						</tr>
						<tr>
							<td width="10%" align="right"><strong><?php echo(getTText("codigo",C_NONE));?></strong></td>
							<td width="13%" align="left"><strong><?php echo(getTText("nosso_numero",C_NONE));?></strong></td>
							<td width="10%" align="right"><strong><?php echo(getTText("vlr_conta",C_NONE));?></strong></td>
							<td width="10%" align="right"><strong><?php echo(getTText("vlr_saldo",C_NONE));?></strong></td>
							<td width="12%" align="center"><strong><?php echo(getTText("emissao",C_NONE));?></strong></td>
							<td width="12%" align="center"><strong><?php echo(getTText("vcto",C_NONE));?></strong></td>
							<td width="33%" align="left"><strong><?php echo(getTText("historico",C_NONE));?></strong></td>
						</tr>
						<?php
						foreach($objResult2 as $objRS2) {
							?>
							<tr>
								<td align="right"><?php echo(getValue($objRS2,"cod_conta_pagar_receber")); ?></td>
								<td align="left"><?php echo(getValue($objRS2,"nosso_numero")); ?></td>
								<td align="right"><?php echo(number_format((double) getValue($objRS2,"vlr_conta"),2,",","")); ?></td>
								<td align="right"><?php echo(number_format((double) getValue($objRS2,"vlr_saldo"),2,",","")); ?></td>
								<td align="center"><?php echo(dDate(CFG_LANG,getValue($objRS2,"dt_emissao"),false)); ?></td>
								<td align="center"><?php echo(dDate(CFG_LANG,getValue($objRS2,"dt_vcto"),false)); ?></td>
								<td align="left"><?php echo(getValue($objRS2,"historico")); ?></td>
							</tr>
							<?php
						}
						?>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	<?php
	}
	
	//Método "rouCount" só funciona porque no SQL tem apenas UMA tabela no FROM
	if($objResult3->rowCount()>0) { 
		?>
		<tr><td height="10"></td></tr>
		<tr>
			<td align="center">
				<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" >
				<tr>
					<td align="center" valign="top" >
						<table cellspacing="2" cellpadding="3" border="0" width="100%" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
						<tr>
							<td align="center" colspan="8"><strong><?php echo(getTText("ult_titulos_pagos",C_TOUPPER));?></strong>&nbsp;<small>(500)</small></td>
						</tr>
						<tr>
							<td width="10%" align="right"><strong><?php echo(getTText("codigo",C_NONE));?></strong></td>
							<td width="10%" align="left"><strong><?php echo(getTText("nosso_numero",C_NONE));?></strong></td>
							<td width="10%" align="right"><strong><?php echo(getTText("vlr_conta",C_NONE));?></strong></td>
							<td width="10%" align="right"><strong><?php echo(getTText("vlr_pago",C_NONE));?></strong></td>
							<td width="10%" align="right"><strong><?php echo(getTText("creditado",C_NONE));?></strong></td>
							<td width="10%" align="center"><strong><?php echo(getTText("emissao",C_NONE));?></strong></td>
							<td width="10%" align="center"><strong><?php echo(getTText("vcto",C_NONE));?></strong></td>
							<td width="30%" align="left"><strong><?php echo(getTText("historico",C_NONE));?></strong></td>
						</tr>
						<?php
						foreach($objResult3 as $objRS3) {
							?>
							<tr>
								<td align="right"><?php echo(getValue($objRS3,"cod_conta_pagar_receber")); ?></td>
								<td align="left"><?php echo(getValue($objRS3,"nosso_numero")); ?></td>
								<td align="right"><?php echo(number_format((double) getValue($objRS3,"vlr_conta"),2,",","")); ?></td>
								<td align="right"><?php echo(number_format((double) getValue($objRS3,"vlr_pago"),2,",","")); ?></td>
								<td align="right"><?php echo(number_format((double) getValue($objRS3,"vlr_creditado"),2,",","")); ?></td>
								<td align="center"><?php echo(dDate(CFG_LANG,getValue($objRS3,"dt_emissao"),false)); ?></td>
								<td align="center"><?php echo(dDate(CFG_LANG,getValue($objRS3,"dt_vcto"),false)); ?></td>
								<td align="left"><?php echo(getValue($objRS3,"historico")); ?></td>
							</tr>
							<?php
						}
						?>
						</table>
					</td>
				</tr>
				</table>
			</td>
		</tr>
	<?php
	}
	?>
</table>
<script type="text/javascript">
	window.print();
</script>
</body>
</html>
<?php 
$objResult1->closeCursor();
$objResult2->closeCursor();
$objResult3->closeCursor();

$objConn = NULL; 
?>
