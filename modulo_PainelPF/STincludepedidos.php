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
	$intCodDado = getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
	
	// Abre conexão com o banco de dados
	$objConn = abreDBConn(CFG_DB);
	
	// Inicializa variavel para pintar linha
	$strColor = "#F5FAFA";
	
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
	//INICIO PRD_PEDIDOS
	$iCodigo = 1;
	$strRotulo = "<strong>".getTText("pedidos_a_faturar",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.it_tipo, t1.valor, t1.it_descricao, t1.it_arquivo, t1.obs, t1.sys_dtt_ins
					FROM prd_pedido t1
					WHERE t1.cod_pj = ".$intCodDado."
					AND t1.situacao ILIKE 'aberto' 
					ORDER BY t1.sys_dtt_ins ";
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
		echo "<thead>";
		echo "<tr>";
		echo "<th width='1%'></th>";
		echo "<th width='6%' class='sortable'><strong>".getTText("cod",C_NONE)."</strong></th>";
		echo "<th width='12%' class='sortable'><strong>".getTText("tipo",C_NONE)."</strong></th>";
		echo "<th width='46%' class='sortable'><strong>".getTText("descricao",C_NONE)."</strong></th>";
		echo "<th width='15%' class='sortable-currency'><strong>".getTText("valor",C_NONE)."</strong></th>";
		echo "<th width='17%' class='sortable-date-dmy'><strong>".getTText("solicitacao",C_NONE)."</strong></th>";
		echo "<th width='1%'></th>";
		echo "<th width='1%'></th>";
		echo "</tr>";
		echo "</thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>";
			echo "<td></td>";
			echo "<td>".getValue($objRS,"cod_pedido")."</td>";
			echo "<td>".getValue($objRS,"it_tipo")."</td>";
			echo "<td>".getValue($objRS,"it_descricao")."</td>";
			echo "<td align='right'>".number_format((double) getValue($objRS,"valor"),2,',','.')."</td>";
			echo "<td>".dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true)."</td>";
			echo "<td>";
			if (getValue($objRS,"obs") != "")
				echo "<img src='../img/icon_obs.gif' border='0' alt='".getValue($objRS,"obs")."' title='".getValue($objRS,"obs")."'></a>";
			echo "</td>";
			echo "<td>";
			if (getValue($objRS,"it_arquivo") != "")
				echo "<a href='../../".getsession(CFG_SYSTEM_NAME."_dir_cliente")."/upload/".getValue($objRS,"it_arquivo")."' target='_blank'><img src='../img/icon_anexo.gif' border='0'></a>";
			echo "</td>";
			echo "</tr>";
			
			//if ($strColor == "#EEEEEE") 
			//	$strColor = "#FFFFFF";
			//else
			//	$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div><br>";
	}
	$objResult->closeCursor();
	//FIM PRD_PEDIDOS
	
	
	//INICIO PRD_PEDIDOS_DELETADOS
	$iCodigo = 2;
	$strRotulo = "<strong>".getTText("pedidos_deletados",C_TOUPPER)."</strong>";
	$strColor = "#FFFFFF";
	
	try{
		$strSQL = " SELECT t1.cod_pedido, t1.it_tipo, t1.valor, t1.it_descricao, t1.it_arquivo
		                 , t1.obs, t1.dtt_ins, t1.sys_dtt_ins, t1.obs_delecao
					FROM prd_pedido_deletado t1
					WHERE t1.cod_pj = ".$intCodDado."
					AND t1.sys_dtt_ins BETWEEN (CURRENT_DATE - INTERVAL '2 Month') AND CURRENT_TIMESTAMP
					ORDER BY t1.sys_dtt_ins ";
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
		echo "<thead>";
		echo "<tr>";
		echo "<th width='1%'></th>";
		echo "<th width='6%' class='sortable'><strong>".getTText("cod",C_NONE)."</strong></th>";
		echo "<th width='12%' class='sortable'><strong>".getTText("tipo",C_NONE)."</strong></th>";
		echo "<th width='20%' class='sortable'><strong>".getTText("descricao",C_NONE)."</strong></th>";
		echo "<th width='10%' class='sortable-currency'><strong>".getTText("valor",C_NONE)."</strong></th>";
		echo "<th width='15%' class='sortable-date-dmy'><strong>".getTText("solicitacao",C_NONE)."</strong></th>";
		echo "<th width='15%' class='sortable-date-dmy'><strong>".getTText("deleção",C_NONE)."</strong></th>";
		echo "<th width='21%' class='sortable-date-dmy'><strong>".getTText("obs_delecao",C_NONE)."</strong></th>";
		echo "</tr>";
		echo "</thead>";
		foreach($objResult as $objRS){
			echo "<tr bgcolor='".$strColor."'>";
			echo "<td></td>";
			echo "<td>".getValue($objRS,"cod_pedido")."</td>";
			echo "<td>".getValue($objRS,"it_tipo")."</td>";
			echo "<td>".getValue($objRS,"it_descricao")."</td>";
			echo "<td align='right'>".number_format((double) getValue($objRS,"valor"),2,',','.')."</td>";
			echo "<td>".dDate(CFG_LANG,getValue($objRS,"dtt_ins"),true)."</td>";
			echo "<td>".dDate(CFG_LANG,getValue($objRS,"sys_dtt_ins"),true)."</td>";
			echo "<td>".getValue($objRS,"obs_delecao")."</td>";
			echo "</tr>";
			
			//if ($strColor == "#EEEEEE") 
			//	$strColor = "#FFFFFF";
			//else
			//	$strColor = "#EEEEEE";
		}
		echo "</table>";
		echo "</div><br>";
	}
	$objResult->closeCursor();
	//FIM PRD_PEDIDOS_DELETADOS
	?>
	</td>
</tr>
</table>
</body>
</html>
<?php
	$objConn = NULL;
?>