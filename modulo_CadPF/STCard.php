<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	
	//Inicia conexão com banco
	$objConn = abreDBConn(CFG_DB);
	
	//Recebe cod_pf
	$intCodPF = request("var_chavereg");
	
	//Consulta dados da PF conforme o cod_pf enviado
	try {
		$strSQL = "SELECT 
							cad_pf.nome, 
							cad_pf.rg, 
							cad_pf.cpf, 
							cad_pf.foto, 
							relac_pj_pf.funcao,
							sd_credencial.pf_matricula
						FROM 
							relac_pj_pf,
							cad_pf
						LEFT JOIN sd_credencial ON cad_pf.cod_pf = sd_credencial.cod_pf
						WHERE 
							cad_pf.cod_pf = ". $intCodPF ."
						AND 
						relac_pj_pf.cod_pf = cad_pf.cod_pf";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//Cria array associativo para o sql gerado acima
	$objRS = $objResult->fetch();
	
	//Associa valores da consulta com variáveis a serem usadas no modelo html
	$TAG_FOTO = getValue($objRS,"foto");
	//Sem tempo agora, inserir tratamento para não mostrar o último sobrenome caso passe de 26 caracteres e
	//caracter anterior for igual a espaço. também nao mostrar caso antes desse ultimo sobrenome for um de ou dos
	$TAG_NOME = substr(getValue($objRS,"nome"),0,26);
	$TAG_RG = getValue($objRS,"rg");
	$TAG_CPF = getValue($objRS,"cpf");
	$TAG_FUNCAO	= getValue($objRS,"funcao");
	$TAG_NUM_MATRICULA = getValue($objRS,"pf_matricula");
	
	//Consulta código da empresa através do cod_pf e relação
	try {
		$strSQL = "SELECT 
							cad_pj.razao_social
						FROM
							cad_pj,
							relac_pj_pf
						WHERE
							relac_pj_pf.cod_pf = " . $intCodPF . "
						AND
							cad_pj.cod_pj = relac_pj_pf.cod_pj";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objRS = $objResult->fetch();
	$TAG_RAZAO_SOCIAL = substr(getValue($objRS,"razao_social"),0,25).".";
	
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title><?php echo (CFG_SYSTEM_TITLE) ?></title>
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<style media="print">.no_imp {display: none;}</style>
	</head>
<body style="padding: 0px 0px 0px 0px; background-color:#FFFFFF;" 
      marginheight="0" marginwidth="0" topmargin="0"; leftmargin="0"; 
      onLoad="resizePag(430,355);" return false;>
	<table align="left" valign="top" width="378" height="245" cellpadding="0" cellspacing="0" border="0" background="../img/layout_credencial_sfoto.jpg">
		<tr>
			<td width="251" align="center" valign="bottom">
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td width="234" height="27" align="left" valign="bottom">&nbsp;</td>
					</tr>
					<tr>
						<td align="left" valign="bottom">
							<table cellpadding="2" cellspacing="0" border="0" style="display: inline;">
								<tr><td>&nbsp;</td></tr>
							</table>	
							<table cellpadding="4" cellspacing="" border="0" bgcolor="" style="display: inline;">
								<tr>
									<td width="213" height="40" align="left" valign="bottom">
										<table cellpadding="0" cellspacing="3">
											<tr>
												<td>NOME: <?php echo ($TAG_NOME) ?></td>
											</tr>
											<tr>
												<td>EMPRESA: <?php echo ($TAG_RAZAO_SOCIAL) ?></td>
											</tr>
											<tr>
												<td>MATRÍCULA nº <?php echo ($TAG_NUM_MATRICULA) ?></td>
											</tr>
											<tr>
												<td>RG: <?php echo ($TAG_RG) ?></td>
											</tr>
											<tr>
												<td>CPF: <?php echo ($TAG_CPF) ?></td>
											</tr>
											<tr>
												<td>FUNÇÃO: <?php echo ($TAG_FUNCAO) ?></td>
											</tr>
										</table>
								  </td>			
								</tr>
							</table>
						</td>
					</tr>
				</table>
		  </td>
		    <td width="121" align="center" valign="top">
				<table border="0" cellpadding="3" cellspacing="0">
					<tr>
						<td width="100" height="166" align="center" valign="top"><img src="../../<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/<?php echo ($TAG_FOTO)?>" width="115" height="150"/></td>
					</tr>
				</table>
		 	 </td>
		</tr>
	</table>
</body>
</html>
<?php
	$objConn = NULL;
?>