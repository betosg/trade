<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

// Requests
$arrPfs   = request("var_pf_selec"); // cod_pf
$intCodPJ =	request("var_chavereg"); // cod_pj

if($arrPfs == "" || $intCodPJ == ""){
	mensagem("alert_consulta_vazia_titulo","alert_consulta_vazia_desc", "", "","aviso",1,"","");
	die();
}

// quebra array em string separada por virgula
$strPfs = implode(",",$arrPfs);


// abertura de conexão
$objConn = abreDBConn(CFG_DB);

// sql dados de relação para etiqueta
try{
	$strSQL = "
		SELECT 
			  cad_pf.nome
			, cad_pj.razao_social
			, 'CPF: '|| cad_pf.cpf ||' / RG: '|| cad_pf.rg AS documento
			, 'MAT.: '|| cad_pf.matricula ||' - '|| relac_pj_pf.funcao as funcao
		FROM
			relac_pj_pf
		INNER JOIN cad_pj ON (cad_pj.cod_pj = relac_pj_pf.cod_pj AND relac_pj_pf.cod_pj = ".$intCodPJ.")
		INNER JOIN cad_pf ON (cad_pf.cod_pf = relac_pj_pf.cod_pf AND cad_pf.cod_pf IN(".$strPfs."))";
	$objResult = $objConn->query($strSQL);
	//die($strSQL);
}catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

?>
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE." - ".getTText("pimaco_a4356",C_NONE)); ?></title>
	<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME );?>.css" type="text/css">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<style>
		table.pagina { border:0px #FFFFFF solid; width:810px; background-color:#FFF; }
		div.box 	 { border:1px #FFFFFF solid; margin-bottom:8px; float:left; margin-left:8px; width:240px; height:87px; }
		div.linha    { width:240px; height:18px; overflow:hidden; }
		div.conteudo { width:240px; height:87px; padding:0px 0px 0px 0px; text-align:left; vertical-align:middle; background:#FFF;}
	</style>
	<script type="text/javascript">
		window.resizeTo(730,570);
	</script>
</head>
<body>
<!-- INI: Página ---------------------------------------------------------------------------------------- -->
<table class="pagina" cellpadding="0" cellspacing="0" align="left">
	<tr>
		<td nowrap="nowrap" valign="top">
		<?php
		    /*
			// INI: SIMULAÇÃO ----------------------------------------------------------------------------
			for($i=1;$i<=33;$i++){
				$strAux  = "<div class='linha'><strong>FULANO DE TAL</div>";
				$strAux .= "<div class='linha'>abc tres ARQUITETOS ASSOCIADOS LTDA</strong></div>";
				$strAux .= "<div class='linha'>Rua Alvaro Seixas,60 - Engenho Novo</div>";
				$strAux .= "<div class='linha'>RIO DE JANEIRO - RJ</div>";
				$strAux .= "<div class='linha'><strong>20665-445</strong></div>";
				// boxEtiqueta 
				echo("<div class='box'>\n");
				echo("<div class='conteudo'>" . $strAux . "</div>\n");
				echo("</div>\n");
			}
			// FIM: SIMULAÇÂO ---------------------------------------------------------------------------- 
			*/
			$intI = 0;
			//$objRS = $objResult->fetch();
			$intActuallyCount = 1;
			while($objRS = $objResult->fetch()) {
				$strHTMLBody  = "";
					$strHTMLBody  .= "<div class='linha'>" . getValue($objRS,"nome") . "</div>";
					$strHTMLBody  .= "<div class='linha'>" . getValue($objRS,"razao_social") . "</div>";
					$strHTMLBody  .= "<div class='linha'>" . getValue($objRS,"documento") . "</div>";
					$strHTMLBody  .= "<div class='linha'>" . getValue($objRS,"funcao") . "</div>";
				// INI: boxEtiqueta ----------------------------------------
				echo("<div class='box'>\n");
				echo("<div class='conteudo'>" . $strHTMLBody . "</div>\n");
				echo("</div>\n");
				if (($intActuallyCount % 33)==0) { echo("<br style='page-break-after:always;'>\n"); }
				// FIM: boxEtiqueta  ---------------------------------------
				$intActuallyCount++;
			}
		?>	
		</td>
	</tr>
</table>
<!-- FIM: Página ---------------------------------------------------------------------------------------- -->
</body>
</html>
<?php
$objResult->closeCursor();
$objConn = NULL;
?>