<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");

//LE SESSAO
$strGrpUser	=	getsession(CFG_SYSTEM_NAME."_grp_user");
//DEFINE MAPA A SER CARREGADO

if (strtoupper($strGrpUser) == "GUEST") { 
  $strMapaPage = "mapaGuest.php"; 
} else { 
    if (strtoupper($strGrpUser) == "NORMAL") { 	$strMapaPage = "mapaGeral.php"; } 
    else { $strMapaPage = "mapaAdmin.php"; }
}

$strMode = request("var_stats");

if(empty($strMode)) { $strMode = "WINDOW"; }

$objConn = abreDBConn(CFG_DB);

$strHeader = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"97%\">
				<tr>
					<td>" . getTText("mapa_sistema",C_UCWORDS) . "</td>
					<td width=\"1\"><img src=\"../img/icon_swapmenu_" . $strMode . ".gif\" onClick=\"swapMenu('" . $strMode . "');\" title=\"" . getTText(strtolower($strMode),C_UCWORDS) . "\" hspace=\"2\" style=\"cursor:pointer\"></td>
					<td width=\"1\"><img src=\"../img/icon_swapmenu_close.gif\" onClick=\"closeMenu('" . $strMode . "');\" title=\"" . getTText("fechar",C_UCWORDS) . "\" style=\"cursor:pointer\"></td>
				</tr>
			 </table>";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
<style>
	dd   	   { margin-left: 15px; }
	dd a       { text-decoration:none;  color:#111111; }
	dd a:hover { text-decoration:none;  color:#999999; }
</style>
<script>
		    var boolCollapsed = false;
			var strMode = "<?php echo($strMode); ?>";
			
			function swapMenu(prMode){
				if(prMode == "WINDOW"){
					window.opener.parent.frames["<?php echo(CFG_SYSTEM_NAME . "_frmenumx"); ?>"].visualizarMenuTree(prMode); //Está dessa maneira para poder chamar sempre do mesmo lugar a janela
					window.close();
				}
				else if(prMode == "FRAME"){
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>").cols = "0,*";
					AbreJanelaPAGE("mapa.php","300","400"); 
				}
				else if(prMode){
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>").cols = "250,*";
					document.getElementById("img_collapse").src = "../img/collapse_mapa_open.gif";
					boolCollapsed = false;
				}
				else if(!prMode){
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>").cols = "10,*";
					document.getElementById("img_collapse").src = "../img/collapse_mapa_closed.gif";
					boolCollapsed = true;
				}
			}
			
			function closeMenu(){
				if(strMode == "WINDOW"){
					window.close();
				}
				else if(strMode == "FRAME"){
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>").cols = "0,*";
				}
			}
			
			function pageRedirect(prLocation){
				if(strMode == "WINDOW"){
					window.opener.parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>").src = prLocation; 
				}
				else if(strMode == "FRAME"){
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>").src = prLocation;
				}
			}
		
			function scrolling(e) { 
				if(!e) { 
					e = window.event; 
				} 
				
				if(e.keyCode == 38){
					window.scrollBy(0,-10);
				}
				else if(e.keyCode == 40){
					window.scrollBy(0,10);
				}
				
			}
			
			document.onkeydown = scrolling;
			
			function collapseItem(prIndex,prPattern) {
				var intCont = 1;
				
				if(img = eval(document.getElementById("i_" + prPattern + "_" + prIndex))){
					(img.src.indexOf("icon_tree_plus.gif") != -1) ? img.src = "../img/icon_tree_minus.gif" : img.src = "../img/icon_tree_plus.gif";
				}
				
				while(bloco = eval(document.getElementById("p_" + prIndex + "_" + intCont))){				
					if(bloco.style.display == "" || bloco.style.display == "block") {
						bloco.style.display = "none";
					} else {
				        bloco.style.display = "block";
					}
					intCont++;
				}
			}
			
</script>
</head>
<body bgcolor="#D5D5D5" style="margin:0px;">
<?php
	if($strMode != "WINDOW"){ 
		echo("<a style=\"cursor:pointer\" onClick=\"swapMenu(boolCollapsed);\"><img id=\"img_collapse\" src=\"../img/collapse_mapa_open.gif\"></a>");
	} else {
		echo("<br>");
	}
?>	
<iframe width="100%" height="100%" src="<?php echo($strMapaPage);?>" frameborder="0" scrolling="no"></iframe>
</body>
</html>
