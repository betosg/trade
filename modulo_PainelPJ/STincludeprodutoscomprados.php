<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	header("Cache-Control:no-cache, must-revalidate");
	header("Pragma:no-cache");
	
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	//SESSION
	//Pega código da PJ selecionada
	$intCodDado = getsession(CFG_SYSTEM_NAME."_pj_selec_codigo");
	
	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	
	//Caminho logico para leitura do certificado
	$strCaminhoLogico = findLogicalPath(getsession(CFG_SYSTEM_NAME."_dir_cliente"));
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? "#F5FAFA" : CL_CORLINHA_1;
		echo($prColor);
	}
?>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="_css/default.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../_css/tablesort.css">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../_scripts/tablesort.js"></script>
<style>
	.menu_css { border:0px solid #dddddd; background:#FFFFFF; padding:0px 0px 0px 0px; margin-bottom:5px }
	body{ margin: 0px; background-color:#FFFFFF; } 
	ul{ margin-top: 0px; margin-bottom: 0px; }
	li{ margin-left: 0px; }
	.fontgrid { font: normal 11px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;}
	.overGray { background-color:#CCCCCC; }
	.outRose  { background-color:#FFF0F0; }
	.outYellow { background-color:#FFFFF0;}
	.outWhite  { background-color:#FFFFFF;}
	/* padding: 6px 12px 6px 12px; */
	padding: 2px 3px 2px 3px;
	color: #444444;
	vertical-align:top;
</style>
<script language="javascript"> 
function ShowArea(prCodigo1, prCodigo2)
{
	if (document.getElementById(prCodigo1).style.display == 'none') {
		document.getElementById(prCodigo1).style.display = 'block';
		document.getElementById(prCodigo2).src = '../img/BulletMenos.gif';
	}
	else { 
		document.getElementById(prCodigo1).style.display = 'none';
		document.getElementById(prCodigo2).src = '../img/BulletMais.gif';
	}
}
</script>
</head>
<body bgcolor="#FFFFFF">
<table cellpadding='0' cellspacing='0' border='0' width='100%' bgcolor='#FFFFFF'>
<tr>
	<td>
	<?php
	//INICIO SD_ANUNCIO
	$iCodigo = 1;
	$strRotulo = "<strong>".getTText("anuncio",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.sys_dtt_ins, t1.arquivo, t1.dtt_pedido, t1.dt_validade, t2.obs, t2.it_descricao
					FROM sd_anuncio t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.dt_validade >= CURRENT_DATE
					AND t1.cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);	
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}			
	
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='10%'><strong>".getTText("pedido_peq",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("validade",C_NONE)."</strong></th>";
		echo "  <th width='28%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='30%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "  <th width='1%'></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>
					<td></td>
					<td>".getValue($objRS,"cod_pedido")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dt_validade"), false)."</td>
					<td>".getValue($objRS,"obs")."</td>
					<td>".getValue($objRS,"it_descricao")."</td>
					<td>";
			if (getValue($objRS,"arquivo") != "")
				echo "<a href='../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/".getValue($objRS,"arquivo")."' target='_blank'><img src='../img/icon_anexo.gif' border='0'></a>";
			echo "</td></tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div><br>";
	}
	$objResult->closeCursor();
	//FIM SD_ANUNCIO
	
	//INICIO SD_PERFIL
	$iCodigo = 2;
	$strRotulo = "<strong>".getTText("perfil",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.sys_dtt_ins, t1.dtt_pedido, t1.dt_validade, t2.obs, t2.it_descricao
					FROM sd_perfil t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.dt_validade >= CURRENT_DATE
					AND t1.cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='10%'><strong>".getTText("pedido_peq",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("validade",C_NONE)."</strong></th>";
		echo "  <th width='29%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='30%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>
					<td></td>
					<td>".getValue($objRS,"cod_pedido")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dt_validade"), false)."</td>
					<td>".getValue($objRS,"obs")."</td>
					<td>".getValue($objRS,"it_descricao")."</td>
				</tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div><br>";
	}
	$objResult->closeCursor();
	//FIM SD_PERFIL
	
	//INICIO SD_CERTIFICADO
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.cod_certificado, t1.sys_dtt_ins, t1.arquivo, t1.dtt_pedido, t1.dt_validade, t2.obs, t2.it_descricao
					FROM sd_certificado t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.dt_validade >= CURRENT_DATE
					AND t1.cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}	
	
	$iCodigo = 3;
	$strRotulo = "<strong>".getTText("certificado",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='6%'><strong>".getTText("pedido_peq",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("validade",C_NONE)."</strong></th>";
		echo "  <th width='33%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='30%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			//Certificado é gerado quando pedido é faturado e criado o título
			//Poderá ter o caso do certificado não existir em arquivo, apenas o nome do arquivo na tabela
			//Então podemos gerar usando a STcecamreader.php
			$strArquivo = "";
			if (getValue($objRS,"arquivo") != "") $strArquivo = $strCaminhoLogico."/upload/certificado/".getValue($objRS,"arquivo");
			echo "<tr bgcolor='".$strColor."'>";
			if ($strArquivo == "")
				echo "	<td><a href='javascript:void(0);' onClick=\"AbreJanelaPAGE('STcecamreader.php?var_chavereg=".getValue($objRS,"cod_certificado")."','750','900');\"><img src='../img/icon_certificado.gif' title='".getTText("impr_certificado",C_NONE)."' alt='".getTText("impr_certificado",C_NONE)."' border='0'></a></td>";
			else
				echo "	<td><a href='javascript:void(0);' onClick=\"AbreJanelaPAGE('".$strArquivo."','750','900');\"><img src='../img/icon_certificado.gif' title='".getTText("impr_certificado",C_NONE)."' alt='".getTText("impr_certificado",C_NONE)."' border='0'></a></td>";
			echo "  <td>".getValue($objRS,"cod_pedido")."</td>";
			echo "  <td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>";
			echo "  <td>".dDate(CFG_LANG, getValue($objRS,"dt_validade"), false)."</td>";
			echo "  <td>".getValue($objRS,"obs")."</td>";
			echo "  <td>".getValue($objRS,"it_descricao")."</td>";
			echo "</tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div><br>";
	}
	$objResult->closeCursor();
	//FIM SD_CERTIFICADO
	
	//INICIO SD_CATALOGO
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.sys_dtt_ins, t1.dtt_pedido, t1.dt_validade, t2.obs, t2.it_descricao
					FROM sd_catalogo t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.dt_validade >= CURRENT_DATE 
					AND t1.cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}	
	
	$iCodigo = 4;
	$strRotulo = "<strong>".getTText("catalogo",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='10%'><strong>".getTText("pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("validade",C_NONE)."</strong></th>";
		echo "  <th width='30%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='29%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>
					<td></td>
					<td>".getValue($objRS,"cod_pedido")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dt_validade"), false)."</td>
					<td>".getValue($objRS,"obs")."</td>
					<td>".getValue($objRS,"it_descricao")."</td>
				</tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div>";
	}
	$objResult->closeCursor();
	//FIM SD_CATALOGO
	
	//INICIO SD_CREDENCIAL
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.sys_dtt_ins, t1.dtt_pedido, t1.dt_validade, t1.pf_nome, t1.pf_matricula, t2.obs, t2.it_descricao
					FROM sd_credencial t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.dt_validade >= CURRENT_DATE 
					AND t1.cod_pj = ".$intCodDado."
					ORDER BY t1.cod_credencial DESC ";
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}	
	
	$iCodigo = 5;
	$strRotulo = "<strong>".getTText("credencial",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='6%'><strong>".getTText("pedido",C_NONE)."</strong></th>";
		echo "  <th width='10%'><strong>".getTText("validade",C_NONE)."</strong></th>";
		echo "  <th width='7%'><strong>".getTText("matr",C_NONE)."</strong></th>";
		echo "  <th width='30%'><strong>".getTText("nome",C_NONE)."</strong></th>";
		echo "  <th width='13%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='18%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='15%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>
					<td></td>
					<td>".getValue($objRS,"cod_pedido")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dt_validade"), false)."</td>
					<td>".getValue($objRS,"pf_matricula")."</td>
					<td>".getValue($objRS,"pf_nome")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>
					<td>".getValue($objRS,"obs")."</td>
					<td>".getValue($objRS,"it_descricao")."</td>
				</tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div>";
	}
	$objResult->closeCursor();
	//FIM SD_CREDENCIAL
	
	//INICIO SD_HOMOLOGACAO
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.sys_dtt_ins, t1.dtt_pedido, t1.pf_nome, t1.pf_matricula, t1.dtt_homologacao, t1.usr_homologacao, t1.situacao, t2.obs, t2.it_descricao
					FROM sd_homologacao t1, prd_pedido t2
					WHERE t1.cod_pedido = t2.cod_pedido
					AND t1.dtt_inativo IS NULL 
					AND t1.cod_pj = ".$intCodDado;
		$objResult = $objConn->query($strSQL);	
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}	
	
	$iCodigo = 6;
	$strRotulo = "<strong>".getTText("homologacao",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	if($objResult->rowCount()> 0){
		echo "<table width='100%' height='20' cellpadding='2' cellspacing='0' style='background-color:".CL_CORBAR_SHAPE.";>";
		echo "<tr style='background-color:".CL_CORBAR_SHAPE.";><td width='16' align='left'><a href=\"Javascript: ShowArea('prj_".$iCodigo."', 'icon_prj_".$iCodigo."');\">";
		echo "<img src='../img/BulletMenos.gif' border='0' align='absmiddle' name='icon_prj_".$iCodigo."' id='icon_prj_".$iCodigo."'></a></td>";
		echo "<td><strong>".$strRotulo."</strong></td></tr>";
		echo "<td></td></tr>";
		echo "</table>";
		echo "<div id='prj_".$iCodigo."' style='padding:0px;'>";
		echo "<table width='99%' border='0' height='20' cellpadding='0' cellspacing='0' class='tablesort'>";
		echo "<thead><tr>";
		echo "  <th width='1%'></th>";
		echo "  <th width='6%'><strong>".getTText("pedido",C_NONE)."</strong></th>";
		echo "  <th width='7%'><strong>".getTText("matr",C_NONE)."</strong></th>";
		echo "  <th width='20%'><strong>".getTText("nome",C_NONE)."</strong></th>";
		echo "  <th width='10%'><strong>".getTText("situacao",C_NONE)."</strong></th>";
		echo "  <th width='10%'><strong>".getTText("homologacao",C_NONE)."</strong></th>";
		echo "  <th width='10%'><strong>".getTText("por",C_NONE)."</strong></th>";
		echo "  <th width='13%'><strong>".getTText("data_pedido",C_NONE)."</strong></th>";
		echo "  <th width='13%'><strong>".getTText("obs_pedido",C_NONE)."</strong></th>";
		echo "  <th width='10%'><strong>".getTText("produto",C_NONE)."</strong></th>";
		echo "</tr></thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>
					<td></td>
					<td>".getValue($objRS,"cod_pedido")."</td>
					<td>".getValue($objRS,"pf_matricula")."</td>
					<td>".getValue($objRS,"pf_nome")."</td>
					<td>".getValue($objRS,"situacao")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_homologacao"), false)."</td>
					<td>".getValue($objRS,"usr_homologacao")."</td>
					<td>".dDate(CFG_LANG, getValue($objRS,"dtt_pedido"), false)."</td>
					<td>".getValue($objRS,"obs")."</td>
					<td>".getValue($objRS,"it_descricao")."</td>
				</tr>";
			if ($strColor == "#EEEEEE") 
				$strColor = "#FFFFFF";
			else
				$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div>";
	}
	$objResult->closeCursor();
	//FIM SD_HOMOLOGACAO
	?>
	</td>
</tr>
</table>
</body>
</html>
<?php
	$objConn = NULL;
?>