<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx   = strtolower(str_replace("modulo_","",basename(getcwd())));
	$strLocation = request("var_location");
	
	// DEFINES
	define("ICONES_NUM"     ,4);     // NÚMERO DE ÍCONES DA GRADE
	define("ICONES_WIDTH"   ,17);    // LARGURA DOS ÍCONES DA GRADE
	define("GRADE_NUM_ITENS",20);    // NÚMERO DE ITENS DA GRADE (PAGINAÇÃO)
	define("GRADE_ACAO_DEFAULT",""); // AÇÃO PADRÃO DA TECLA ENTER NA GRADE
	define("LIMIT_DEFAULT",25); 	 // LIMIT DEFAULT DA CONSULTA      
	
	// INICIALIZA MODULO
	initModuloParams(basename(getcwd()));
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"VIE");
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<script type="text/javascript" language="javascript">
			var intCurrentPos = 1;
			var intCurrentPosMouse;
			var strDefaultAction = "<?php echo(GRADE_ACAO_DEFAULT); ?>"; 
			var intTotalPaginas = parseInt("<?php echo(GRADE_NUM_ITENS); ?>");
	
			function aplicarFuncao(prValue) {
				if(prValue != "") {
					location.href = prValue;
				}
			}
			
			function setOrderBy(prStrOrder,prStrDirect) {
				location.href = "<?php echo(getsession($strSesPfx . "_grid_default")); ?>?var_order_column=" + prStrOrder + "&var_order_direct=" + prStrDirect;
			}
			
			function paginar(prPagina){
				if(prPagina > 0 && prPagina <= intTotalPaginas){
					document.formpaginacao.var_curpage.value = prPagina;
					document.formpaginacao.submit();
				}	
			}
			
			function switchColor(prObj, prColor){
				prObj.style.backgroundColor = prColor;
			}
				
			var somaCurrentPosDetailUp = 1;
			var somaCurrentPosDetailDown = 1;
			var voltaSetaDown = 1;
			function navigateRow(e) {
				if(!e) { e = window.event; }
	
				objTable = document.getElementById("tableContent");
	
				if(e.keyCode == 40){
					switchColor(objTable.rows[intCurrentPos], "");
					if(intCurrentPos < objTable.rows.length-2) {
						intCurrentPos += somaCurrentPosDetailUp;
						switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = objTable.rows.length-1;
					}
					
				}
				else if(e.keyCode == 38){
					switchColor(objTable.rows[intCurrentPos], "");
					if(intCurrentPos > 2){
						intCurrentPos -= somaCurrentPosDetailDown;
						switchColor(objTable.rows[intCurrentPos], "#CCCCCC");
					}
					else{
						intCurrentPos = voltaSetaDown;
					}
				} 
				else if ((e.keyCode == 0 || e.keyCode == null) && e.type == "mouseover") {
					switchColor(objTable.rows[intCurrentPos], "");
					switchColor(objTable.rows[intCurrentPosMouse], "#CCCCCC");
					intCurrentPos = intCurrentPosMouse;
				}
				else if (e.keyCode == 13) {
					if(strDefaultAction != "" && objTable.rows[intCurrentPos].cells[1] != null){
						location.href = strDefaultAction.replace("{0}",objTable.rows[intCurrentPos].cells[1].innerHTML);
					}
				}else if(e.keyCode == 39) {
					proximaPagina = parseInt(document.formpaginacao.var_curpage.value) + 1;
					paginar(proximaPagina);
				}else if(e.keyCode == 37) {
					paginaAnterior = parseInt(document.formpaginacao.var_curpage.value) - 1;
					paginar(paginaAnterior);
				}
				
				if (e.keyCode != 8 && e.keyCode != 13 && (!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106))){
					return false;
				}
			}
			
			document.onkeydown = navigateRow;
		
			function collapseItem(prCodBookmark){
				if(document.getElementById("bookmark_" + prCodBookmark).style.display == "block"){
					document.getElementById("bookmark_" + prCodBookmark).style.display = "none";
					document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_close.gif";
				}
				else{
					document.getElementById("bookmark_" + prCodBookmark).style.display = "block";
					document.getElementById("bookmark_img_" + prCodBookmark).src = "../img/collapse_generic_open.gif";
				}
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<table cellpadding="0" cellspacing="8" border="0" width="100%">
	<tr>
	<td width="200" align="center" valign="top" style="padding-right:20px;"><?php include_once("_STincludeleft.php");?></td>
	<td width="99%" align="center" valign="top" style="padding-left:20px; "><?php include_once("STdata.php");?></td>
	</tr>
</table>
</body>
</html>