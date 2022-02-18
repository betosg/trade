<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "UPD");

$intUnicID	= 1; 
$strGrpUser	= trim(request("var_strparam"));


//Se não vem o parâmetro de GRUPO de USUARIO a ser editado para o menumx, 
//então só deve poder editar (com o wizard) menuemx do seu próprio grupo
if ($strGrpUser == "") { $strGrpUser = getsession(CFG_SYSTEM_NAME . "_grp_user"); }

$objConn = abreDBConn(CFG_DB);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<?php include_once("../_scripts/scripts.js"); ?>	
		<script language="javascript"  type="text/javascript">
		/* ----------------------------------------------------------------------------------------- 
		  Simula um menu de contexto, fazendo a exibição de um layet na posicão do elemento clicado
		  ------------------------------------------------------------------------------ by Aless -- */
		function athMenuContext(prObjId,prModulo,prChaveReg) { 
			var auxMenuDiv  = document.getElementById("LayerMenu");
			
			//Busca a posição do Elemento clicado, para reposicionar o Layr do Menu de Contexto
			var offsetTrail = document.getElementById(prObjId);
			var offsetLeft  = 0;
			var offsetTop   = 0;
			while (offsetTrail){
				 offsetLeft += offsetTrail.offsetLeft;
				 offsetTop  += offsetTrail.offsetTop;
				 offsetTrail = offsetTrail.offsetParent;
			 }
			 if (navigator.userAgent.indexOf('Mac') != -1 && typeof document.body.leftMargin != 'undefined'){
				 offsetLeft += document.body.leftMargin;
				 offsetTop  += document.body.topMargin;
			 }

			//Modifica os itens do menu de contexto, conforme o contexto passado
			var objMCEdit = document.getElementById("mc_edit");
			var objMCDEL  = document.getElementById("mc_del");
			objMCEdit.href = "javascript:AbreJanelaPAGE('../_fontes/insupddelmastereditor.php?var_oper=UPD&var_populate=yes&var_chavereg=" + prChaveReg + "&var_basename=" + prModulo +"','750','480');";
			objMCDEL.href  = "javascript:AbreJanelaPAGE('../_fontes/insupddelmastereditor.php?var_oper=DEL&var_populate=yes&var_chavereg=" + prChaveReg + "&var_basename=" + prModulo +"','750','480');";
			
			//Reposiciona e exibe o Menu de contexto
		 	auxMenuDiv.style.top  	 = offsetTop+'px';
		 	auxMenuDiv.style.left 	 = offsetLeft+'px';
			auxMenuDiv.style.display = "block";
		}
		
		</script>
		<style type="text/css">
			html { width:100%; vertical-align:top; margin:0px; }
			img  { border:none; }
        </style>
	</head>
	<!-- body scroll="no" -->
	<body bgcolor="#FFFFFF" style='margin:10px;' background='../img/bgFrame_<?php echo(CFG_SYSTEM_THEME . "_main.jpg");?>'>
	<?php athBeginWhiteBox("98%"); ?>
	<!-- INI: Menu de contexto ------------------------------ //-->
	<div id="LayerMenu" name="LayerMenu" style="position:absolute; left:40px; top:8px; width:122px; height:136px; z-index:1; display:none; border:0px solid #466470; background-color:#FFFFFF;">
	  <img src="../img/menumx_contexto.gif" border="0" usemap="#Map" />
		<map name="Map" id="Map">
			<area id="mc_edit" name="mc_edit" shape="rect" coords="5,0,115,21"    href="#" style="cursor:pointer;"/>
			<area id="mc_del"  name="mc_del"  shape="rect" coords="6,91,115,113"  href="#" style="cursor:pointer;"/>
			<area id="mc_quit" name="mc_quit" shape="rect" coords="5,115,115,136" onclick="javascript:document.getElementById('LayerMenu').style.display='none';" style="cursor:pointer;" />
	  </map>
	</div>
	<!-- FIM: Menu de contexto ------------------------------ //-->
	<table id="table_main"  border="0" cellpadding="0" cellspacing="0" class="kernel_grid">
		<tr>
			<td class="name" width="50%" valign="top">
				<?php 
				echo(getTText(getsession($strSesPfx . "_titulo"),C_NONE)  . " - " . $strGrpUser . "&nbsp;"); 
				echo("<a href='STinsabaexec.php?var_grupo=" . $strGrpUser . "'>" );
				echo("<img src='../img/IconMXAddAba.gif' title='Inserir ABA' style='cursor:pointer'>&nbsp;");
				echo("</a>");
				?>
			</td>
			<td align="right" width="50%"></td>
		</tr>
		<tr><td colspan="2" height="3"></td></tr>
		<tr>
			<td colspan="2">
				<table id="table_subgrid" cellpadding="0" cellspacing="3" width="100%" class="grid_box" style="background:url(../img/bgMenuMxWizard.png);">
					<tr><td class="line_divisor"></td></tr>
					<tr>
						<td style="padding:5px 0px 0px 5px;">
						<?php 

							try{
								$strSQL    = " SELECT sys_mx.cod_mx, sys_mx.rotulo, sys_mx.descricao, sys_mx.link, sys_mx.target, sys_mx.ordem, COUNT(sys_mx_item.cod_mx_item) AS count_child
												 FROM sys_mx LEFT OUTER JOIN sys_mx_item ON (sys_mx.cod_mx = sys_mx_item.cod_mx)
												WHERE sys_mx.dtt_inativo IS NULL 
												  AND sys_mx.grp_user = '" . $strGrpUser . "' 
												GROUP BY sys_mx.cod_mx, sys_mx.rotulo, sys_mx.descricao, sys_mx.link, sys_mx.target, sys_mx.ordem
												ORDER BY sys_mx.ordem";
								$objResult = $objConn->query($strSQL);
							} catch(PDOException $e) {
								mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
								die();
							}
							

							foreach($objResult as $objRS) {
								echo("<a href='STinscontainerexec.php?var_grupo=" . $strGrpUser . "&var_chavereg_pai=" . getValue($objRS,"cod_mx") . "'>" );
								echo("<img src='../img/IconMXAddContainer.gif' title='Inserir CONTAINER' style='cursor:pointer'>&nbsp;");
								echo("</a>");
								
								echo("<span id='" . $intUnicID . "' onclick=\"javascript:athMenuContext('" . $intUnicID ."','modulo_MenuMX','" . getValue($objRS,"cod_mx") . "');\" style='cursor:pointer;'>" );
								echo(getTText(getValue($objRS,"rotulo"),C_TOUPPER) . " (" . getValue($objRS,"cod_mx") . "." . getValue($objRS,"rotulo") . ")<br><br>"); 
								echo("</span>");
								$intUnicID++;

								try {
									$strSQL = "SELECT cod_mx_item
													, rotulo 
												FROM sys_mx_item 
											   WHERE cod_mx = " . getValue($objRS,"cod_mx") . " 
											     AND dtt_inativo IS NULL 
											ORDER BY ordem";
									$objResult3 = $objConn->query($strSQL);
								} catch(PDOException $e) {
									mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
									die();
								}
								
								if($objResult3->rowCount() > 0) {
					?>
					<table cellpadding="0" cellspacing="0" border="0" style="display:block"); ?>
						<tr>
					<?php 	
							foreach($objResult3 as $objRS3) { ?> 
							<!--INIC: DRAWBox ---------------------------------------------------- -->
							<td valign="top"><img src="../img/borderBoxLeft.gif"></td>
							<td background="../img/bgBox.jpg" style="background-repeat:repeat-x;">
								<table width="50" height="82" cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>
											<table cellpadding="0" cellspacing="0" border="0">
												<tr>
												<!--INIC: IconITENS ---------------------------------------------------- -->
												<?php
												$intCodMenuPai = NULL;
												try{
													$strSQL = "SELECT cod_mx_item_sub, img, link, target, rotulo,
																	(SELECT COUNT(cod_mx_item_sub) FROM sys_mx_item_sub WHERE cod_mx_item=" . getValue($objRS3,"cod_mx_item") . " AND TIPO<>'ICON') AS outros 
																FROM sys_mx_item_sub 
																WHERE
																	cod_mx_item = " . getValue($objRS3,"cod_mx_item") . " AND
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
												
												if($objResult1->rowCount() > 0) {																		
													foreach($objResult1 as $objRSa){
												?>
													<td align="center" background="box_int_center.jpg" style="white-space:normal">
														<img id="<?php echo($intUnicID); ?>" 
															src="../img/<?php echo(getValue($objRSa,"img"));?>" 
															title="<?php echo(getValue($objRSa,"link"));?>" 
															style="cursor:pointer;" 
															onclick="javascript:athMenuContext('<?php echo($intUnicID); ?>','modulo_MenuMXItemSub','<?php echo(getValue($objRSa,"cod_mx_item_sub")) ?>');" />
														<?php 
														 echo("<br>".getTText(getValue($objRSa,"rotulo"),C_UCWORDS)); 
														 $intUnicID++;
														?>
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
													<td align="right" width="11px">&nbsp;</td>
													<td align="center"><?php
														echo("<span id='" . $intUnicID . "' onclick=\"javascript:athMenuContext('" . $intUnicID ."','modulo_MenuMXItem','" . getValue($objRS3,"cod_mx_item") . "');\" style='cursor:pointer;'>" );
														echo(getTText(getValue($objRS3,"rotulo"),C_TOUPPER)); 
														echo("</span>");
														$intUnicID++;
													?></td>
													<td align="right" width="11px"><?php
														echo("<a href='STinsitemexec.php?var_grupo=" . $strGrpUser . "&var_chavereg_pai=" . getValue($objRS3,"cod_mx_item"). "&var_chavereg_avo=" . getValue($objRS,"cod_mx") . "'>" );
														echo("<img src='../img/IconMXAddItem.gif' title='Inserir ITEM' style='cursor:pointer'>");
														echo("</a>");
													?></td>
												</tr>
											</table>
										</td>
									</tr>
								</table> 
							</td>
							<td valign="top"><img src="../img/borderBoxRight.gif"></td>
							<td width="3"></td>
							<!--FIM: DRAWBox ---------------------------------------------------- -->
						<?php		} ?>	
						<tr><td colspan="3" height="20">&nbsp;</td></tr>
					</table>
						<?php
								}
							}
						?>
						</td>
					</tr>
				</table><!-- table_subgrid //-->		
			</td>
	  </tr>		
	</table><!-- table_main //--> 
	<?php athEndWhiteBox(); ?>
	</body>
</html>