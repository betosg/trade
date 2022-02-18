<?php 
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
//include_once("../_scripts/STscripts.js");

$boolNotIcon	= false;
$intMXSelected	= request("var_chavereg");

$objConn		= abreDBConn(CFG_DB); 

$boolNotIcon	= false;
$intHeightMenu	= "52";

//Busca os menus MX
try{
	$strSQL    = " SELECT cod_mx, rotulo, descricao, link, target, ordem 
					 FROM sys_mx 
					WHERE dtt_inativo IS NULL 
					  AND grp_user = '" . getsession(CFG_SYSTEM_NAME . "_grp_user") . "' 
					ORDER BY ordem";
	$objResult = $objConn->query($strSQL);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

	
if($objResult->rowCount() > 0) { 
	$objRS = $objResult->fetch();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<script language="javascript">
			var intSelected = <?php echo(getValue($objRS,"cod_mx"))?>;
			var tid = null;
			//var ttr  = 300; //5 min (Time to refresh  1800s = 30min) 
			var ttr  = 14400; //2h) 
			var cmin = 0;

			function visualizarMenuTree(prStats){
				var objFrsMain = parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>");
				var objFrmMenu = parent.window.frames["<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>"];
				
				if(objFrsMain.cols == "0,*" && objFrmMenu.winpopup_prostudio != null){
					objFrmMenu.winpopup_prostudio.close();
				}
				
				parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>").src="mapa.php?var_stats=FRAME";
				objFrsMain.cols = "250,*";
			}

			function swapWidth() {
				var objFrsPrincipal = self.parent.document.getElementById("frprincipal");
				objFrsPrincipal.rows = (objFrsPrincipal.rows=="145,*,22") ? "50,*,22" : "145,*,22";
	
				//location.href = 'mxmenu.php'; //Desmarca a ficha ao 'minimizar' o menu
			}

			function pageRedirect(prUrl, prTarget, prCodSelected) {
				var objIfrMain = document.getElementById('<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>');
				var objDocument = document.body;
				
				if(prTarget == "_blank") {
					window.open(prUrl);
				} else {
					 if (prUrl!=="")	parent.document.getElementById(prTarget).src = prUrl; 
				}
				
				if(prCodSelected != intSelected) {
					setEnableTab(true, prCodSelected); // habilita a nova tab
					setEnableTab(false, intSelected); // desabilita a tab antiga
				}
				
				intSelected = prCodSelected;
				
				//location = 'mxmenu.php?var_chavereg=' + prCodSelected;
			}

			function setFirstTab() {
				setEnableTab(true, intSelected);
			}
			
			function setEnableTab(prFlag, prSelected) {
				if(prFlag) {
					document.getElementById("container_" + prSelected).style.display = "block";
					document.getElementById("ficha_left_" + prSelected).className = "ficha_left";
					document.getElementById("ficha_right_" + prSelected).className = "ficha_right";
					document.getElementById("ficha_center_" + prSelected).className = "ficha_center";
				} else if(!prFlag) {
					document.getElementById("container_" + prSelected).style.display = "none";
					document.getElementById("ficha_left_" + prSelected).className = null;
					document.getElementById("ficha_right_" + prSelected).className = null;
					document.getElementById("ficha_center_" + prSelected).className = null;
				}
			}


			function visualizarMenuMX(prCodPai){
				window.open("VisualizarMenu.php?var_chavereg=" + prCodPai,"","width=300, height=600, left=30, top=30, scrollbars=1, status=0");
			}


			

	</script>
		<style type="text/css">
			html, body		{ width:100%; }
			img				{ border:none; }
			.ficha_left		{ background-image: url('../img/borderFichaTopLeft.jpg'); }
			.ficha_center	{ background-image: url('../img/bgFichaTop.jpg'); }
			.ficha_right	{ background-image: url('../img/borderFichaTopRight.jpg'); }
		</style>
	</head>
	<body style="margin:0px;" scroll="no" onLoad="setFirstTab(); countdown(ttr);">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" background="../img/bgHeader.jpg">
			<tr>
			  <td width="1%" align="left" valign="bottom" style="padding-left:8px;"><img src="../img/system_logo_partea.gif" border="0"></td>
			  <td width="99%" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="1%" valign="left" class="comment_med" style="vertical-align:middle;">
                          <iframe src="mxcheckdb.php" style="border:0px; width:10px; height:16px; vertical-align:middle; margin-left:3px;"></iframe>
                        </td>
 						<td width="1%"  align="left"  class="comment_med" style="vertical-align:bottom; padding:0px 0px 5px 2px;" nowrap="nowrap"><?php psVersion(CFG_SYSTEM_NAME . "_frmain"); ?>&nbsp;<span id='tel' alt='Session/Cookie' title='Session/Cookie' style='color:#CCCCCC;'>(..)</span></td>
						<td width="98%" align="right" class="comment_med" style="vertical-align:top;">&nbsp;<a href="logout.php" target="<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>"><img src="../img/but_Logout.gif" hspace="10" border="0" alt="Fechar sessão!"></a></td>
					</tr>
				</table>		
			  </td>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#D5D5D5">
			<tr>
				<td width="100%" height="24">
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="8"></td>
							<td background="../img/bgMenuMxTop.jpg" height="24">
								<table height="24" cellpadding="0" cellspacing="0" border="0"> 
									<tr>
										<td width="140" valign="top"><img src="../img/system_logo_parteb.gif"></td>
										<?php do{ ?>
										<td id="ficha_left_<?php echo(getValue($objRS,"cod_mx")); ?>" width="5"></td>
										<td id="ficha_center_<?php echo(getValue($objRS,"cod_mx")); ?>" nowrap><a href="javascript:pageRedirect('<?php echo(getValue($objRS,"link")."','".getValue($objRS,"target")."',".getValue($objRS,"cod_mx"));?>);">&nbsp;<?php echo(getTText(getValue($objRS,"rotulo"),C_UCWORDS)); ?>&nbsp;</a></td>
										<td id="ficha_right_<?php echo(getValue($objRS,"cod_mx")); ?>" width="5"></td>
										<?php } while($objRS = $objResult->fetch()); ?>
										<!-- 
										<td id="ficha_left_NULL" width="5"></td> 
										<td id="ficha_center_NULL" nowrap></td> 
										<td id="ficha_right_NULL" width="5"></td> 
										//--> 
									</tr>
								</table>
							</td>
							<td width="8" align="right"><a href="javascript:swapWidth();"><img src="../img/borderMenuMxFRight.jpg" border="0"></a></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table width="100%" height="94" cellpadding="0" cellspacing="0"> 
			<tr>
				<td width="1"><img src="../img/borderFichaLeft.jpg" border="0"></td>
				<td background="../img/bgFicha.jpg" valign="top" nowrap style="padding-top:4px;">
					<?php
							try {
								$strSQL    = " SELECT cod_mx
												 FROM sys_mx 
												WHERE dtt_inativo IS NULL 
												  AND grp_user = '" . getsession(CFG_SYSTEM_NAME . "_grp_user") . "' 
												ORDER BY ordem";
								$objResult2 = $objConn->query($strSQL);
							} catch(PDOException $e) {
								mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
								die();
							}
							
							$intI = 0;
							foreach($objResult2 as $objRS2) {
						?>
					<table id="container_<?php echo(getValue($objRS2,"cod_mx")); ?>" cellpadding="0" cellspacing="0" border="0" style="display:<?php echo(($intI == 0) ? "block" : "none"); ?>">
						<tr>
						<?php	
								try {
									$strSQL = "SELECT cod_mx_item, rotulo FROM sys_mx_item WHERE cod_mx = " . getValue($objRS2,"cod_mx") . " AND dtt_inativo IS NULL ORDER BY ordem";
									$objResult3 = $objConn->query($strSQL);
								} catch(PDOException $e) {
									mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
									die();
								}
							
								foreach($objResult3 as $objRS) { 
						?> 
							<!--INIC: DRAWBox ---------------------------------------------------- -->
							<td><img src="../img/borderBoxLeft.gif"></td>
							<td background="../img/bgBox.jpg">
								<table width="50" height="82" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
												<!--INIC: IconITENS ---------------------------------------------------- -->
												<?php
												$intCodMenuPai = NULL;
												try{
													$strSQL = "SELECT img, link, target, rotulo,
																	(SELECT COUNT(cod_mx_item_sub) FROM sys_mx_item_sub WHERE cod_mx_item=" . getValue($objRS,"cod_mx_item") . " AND TIPO<>'ICON') AS outros 
																FROM sys_mx_item_sub 
																WHERE
																	cod_mx_item = " . getValue($objRS,"cod_mx_item") . " AND
																	tipo = 'ICON' AND
																	dtt_inativo IS NULL 
																ORDER BY
																	ordem ";
													$objResult1 = $objConn->query($strSQL);
												}
												catch(PDOException $e){
													mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
													die();
												}
												
												if($objResult1->rowCount() > 0){																		
													/*if(intval("0" . getValue($objResult1->fetch(),"outros")) > 0){
														$boolNotIcon = true;
														$intCodMenuPai = getValue($objRS,"cod_mx_item");
													}*/
													foreach($objResult1 as $objRSa){
												?>
													<td align="center">
														<a href="<?php echo(getValue($objRSa,"link"));?>" target="<?php echo(getValue($objRSa,"target"));?>">
															<img src="../img/<?php echo(getValue($objRSa,"img"));?>"><br>&nbsp;<?php echo(getTText(getValue($objRSa,"rotulo"),C_UCWORDS)); ?>
														</a>
													</td>
												<?php
													}
												}
												?>
												<!--FIM: IconITENS ---------------------------------------------------- -->
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td height="17" align="center">
											<table cellpadding="0px" cellspacing="0px" width="100%" height="100%">
												<tr>
													<?php if($boolNotIcon){ ?>
													<td align="right" width="11px">&nbsp;</td>
													<td align="center"><?php echo(getValue($objRS,"rotulo")); ?></td>
													<td align="right" width="11px">
														<a href="JavaScript:visualizarMenuMX(<?php echo($intCodMenuPai); ?>);"><img src="../img/IconMXExtra.gif"></a>
													</td><?php 	
															$boolNotIcon = ! $boolNotIcon;	
														} else { ?>
													<td align="center"><?php echo(getTText(getValue($objRS,"rotulo"),C_UCWORDS)); ?></td>
													<?php } ?>
												</tr>
											</table>
										</td>
									</tr>
								</table> 
							</td>
							<td><img src="../img/borderBoxRight.gif"></td>
							<td width="3"></td>
							<!--FIM: DRAWBox ---------------------------------------------------- -->
						<?php
								}	
						?>	
						</tr>
					</table>
						<?php
								$intI++;
							}
						?>
				</td>
				<td width="1"><img src="../img/borderFichaRight.jpg"></td>
			</tr>
		</table>
	</body>
</html>
		<?php
			} else {
		?>
		<html>
		<head>
			<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
			<script type="text/javascript">
			var tid = null;
			//var ttr  = 300; //5 min (Time to refresh  1800s = 30min) 
			var ttr  = 14400; //2h) 
			var cmin = 0;
				function visualizarMenuTree(prStats){
					var objFrsMain = parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_frsmain"); ?>");
					var objFrmMenu = parent.window.frames["<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>"];
					
					if(objFrsMain.cols == "0,*" && objFrmMenu.winpopup_prostudio != null){
						objFrmMenu.winpopup_prostudio.close();
					}
					parent.document.getElementById("<?php echo(CFG_SYSTEM_NAME . "_menu"); ?>").src="mapa.php?var_stats=FRAME";
					objFrsMain.cols = "250,*";
				}
			</script>
			<style type="text/css">
				html, body  { width:100%; }
				img{ border:none; }
				.ficha_left { background-image: url('../img/borderFichaTopLeft.jpg'); }
				.ficha_center { background-image: url('../img/bgFichaTop.jpg'); }
				.ficha_right { background-image: url('../img/borderFichaTopRight.jpg'); }
			</style>
			</head>
			<body style="margin:0px;" scroll="no" onLoad="countdown(ttr);">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" background="../img/bgHeader.jpg">
				<tr>
				  <td align="left" valign="bottom" width="15%"><div style="padding-left:8px"><a href="javascript:visualizarMenuTree();"><img src="../img/system_logo_partea.gif" border="0"></a></div></td>
				  <td valign="midlle" style="vertical-align:middle;"><span id='tel' alt='Session/Cookie' title='Session/Cookie' style='color:#CCCCCC;'>(..)</span>&nbsp;&nbsp;&nbsp;</td>
						<!--td width="1%" valign="left" class="comment_med" style="vertical-align:middle;">
                          <iframe src="mxcheckdb.php" style="border:0px; width:10px; height:16px; vertical-align:middle; margin-left:3px;"></iframe>
                        </td-->
						 <!--td width="1%"  align="left"  class="comment_med" style="vertical-align:bottom; padding:0px 0px 5px 2px;" nowrap="nowrap"><?php psVersion(CFG_SYSTEM_NAME . "_frmain"); ?>&nbsp;
						 <span id='tel' alt='Session/Cookie' title='Session/Cookie' style='color:#CCCCCC;'>(..)</span-->						


				  <td align="right"><a href="logout.php" target="<?php echo(CFG_SYSTEM_NAME . "_frmain"); ?>"><img src="../img/but_Logout.gif" hspace="10" border="0" alt="Fechar sessão!"></a></td>
				</tr>
				<tr>
				  <td align="left" valign="top" colspan="3"><div style="padding-left:8px"><img src="../img/system_logo_parteb.gif" border="0"></div></td>
				</td>
			</table>
			</body>
<script language="javascript">
// INI: Session Pooling - Faz o Pooling para verificar se a session esta ativa ------------------------------------------------------

			function countdown(sec) {
			 
			 //INI: COMENT - ESSA LINHA ESTA DANDO ERRO NO DW1 pois nao da direito de acesso para fazer o pooling por culpa do LGB / BY GS
			 document.getElementById( 'tel' ).firstChild.nodeValue = sec + ( sec===1 ? '' : 's' ); 
			 //FIM: COMENT ---------------------------------------------------------------------------------------------------------------
			
			 if( sec ) {
			  tid = window.setTimeout( 'countdown(' + ( --sec ) + ');', 1000 );
			 } else {
			   stop();
			   cmin++;
			   tryGetSessionData('<?php echo(CFG_SYSTEM_NAME."_id_usuario");?>'); //Chama página PHP via AJAX que retorna os valores lidos na session
			   countdown(ttr); //re-inicia o countdown para a próxima checagem
			 }
			}
			
			function stop() { 
				window.clearTimeout( tid );
			}
			
			function tryGetSessionData(prSesField){
				var objAjax;
			
				//Função cria objeto ajax
				objAjax = createAjax();
				//Durante sua execução, faz-se a verificação do estado atual do ajax.
				objAjax.onreadystatechange = function() {
					if(objAjax.readyState == 4) {
						if(objAjax.status == 200) {
							//window.alert( objAjax.responseText );
							if (objAjax.responseText=="") {
							 stop();
							 alert("Sua sessão expirou! " + cmin + " minutos de inatividade.");
							 document.getElementById('form_logout').submit();
							}
						} else {
							stop();
							alert("Erro no processamento da página: " + objAjax.status + "\n\n" + objAjax.responseText);
						}
					}
				}
				objAjax.open("GET", "../_ajax/GetSessionData.php?var_sesfield=" + prSesField, true);
				objAjax.send(null);	
			}
			// FIM: Session Pooling --------------------------------------------------------------------------------------------------
</script>
		</html>
<?php
	}
	$objResult->closeCursor();
?>
