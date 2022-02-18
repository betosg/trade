<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

define("MAXCTRL",6);

$objConn = abreDBConn(CFG_DB);

//Busca dados sobre o site ---------------------------------
montaArraySiteInfo($objConn, $arrScodi, $arrSdesc);


// ROTINA para montagem dos ícones do Painel de Controle (desktop icons)
function montaDesktopIcons(){

$objConnLocal   = abreDBConn(CFG_DB);
   
try{
   $strSQLLocal    = " SELECT path_img_enabled, path_img_disabled, path_img_over, descricao, link, link_param, ordem FROM sys_painel 
					   WHERE ativo = true AND grp_user = '" . getsession(CFG_SYSTEM_NAME . "_grp_user") . "' ORDER BY ordem";
   $objResultLocal = $objConnLocal->query($strSQLLocal);
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}
      
$intCont = 1;
   
// Início da primeira linha da tabela
$retValue = "<tr>";
   
foreach($objResultLocal as $objRSLocal){
	$retValue .= "
	         <td align=\"center\" valign=\"top\" width=\"" . intval(100/MAXCTRL) . "%\">
			 	<a href=\""  . str_replace("*",getsession(CFG_SYSTEM_NAME . "_cod_usuario"),getValue($objRSLocal, "link")) . getValue($objRSLocal, "link_param") . "\" target=\"_parent\" style=\"text-decoration:none;\">
				 <img src=\"" . getValue($objRSLocal, "path_img_enabled") . "\" ";
		
	if(getValue($objRSLocal, "path_img_over") != ""){
		$retValue .= "onMouseOver=\"this.src='" . getValue($objRSLocal, "path_img_over") . "';\" onMouseOut=\"this.src='" . getValue($objRSLocal, "path_img_enabled") . "';\"";
	}
		
	$retValue .= "border=\"0\"><br>" . getTText(getValue($objRSLocal, "descricao"),C_UCWORDS) . "
				</a>
			</td>
		   ";
       if($intCont % MAXCTRL == 0){
         $retValue .= "
		       </tr>
		 	   <tr>
			 ";
       }
       $intCont++;
}
   
// Verifica se preencheu toda a linha com imagens senão coloca coluna em branco
while(($intCont-1) % MAXCTRL != 0){
	$retValue .= "<td width=\"85\">&nbsp;</td>";
	$intCont++;
}
   
$retValue .= "</tr>"; // Fecha a linha da tabela
   
$objResultLocal->closeCursor();
$objConnLocal = NULL;
   
return($retValue);
} 
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css">
		<script>
			function switchColor(prObj, prColor){
				prObj.style.backgroundColor = prColor;
			}
			
			function selectAction(prThis){
				if(prThis.value == "chamadoins.php"){
					window.open(prThis.value,"<?php echo(CFG_SYSTEM_NAME . "_chamado"); ?>",'popup=yes,width=650,height=400');
				}
				else{
					parent.location.href = prThis.value;
				}
				
				prThis.options.selectedIndex = 0;
			}
		</script>
	</head>
<body style="margin:15px 0px 10px 0px;" bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr> 
	  <td>&nbsp;&nbsp;</td>
	  <td align="center" valign="top" width="100%">
		<table width="97%" align="center" border="0" cellspacing="0" cellpadding="0">
		 <tr>
			<td width="99%" valign="top" align="center">
				<?php athBeginWhiteBox("100%"); ?>
				<table width="100%" align="center" border="0" cellspacing="5" cellpadding="0">
					<tr><td colspan="<?php echo(MAXCTRL); ?>"><b>Agenda <?php //echo(getsession(CFG_SYSTEM_NAME . "_grp_user")); ?>: </b></td></tr>
					<tr>
						<td height="1" colspan="<?php echo(MAXCTRL); ?>">
							<table width="100%" border="0" cellpadding="6">
								<tr>
									<td>
										<iframe src="calendar.php" frameborder="0" width="175" height="105" allowtransparency="true"></iframe>									</td>
									<td width="80%" valign="top">
										<?php athBeginWhiteBox("100%"); ?>
											<table width="100%" align="center" border="0" cellspacing="5" cellpadding="0">
												<tr><td colspan="<?php echo(MAXCTRL); ?>"><b> Agendamento(s) para hoje<?php //echo(getsession(CFG_SYSTEM_NAME . "_grp_user")); ?>: </b></td></tr>
												<tr>
													<td>
														<table width="100%" cellspacing="0">
														<?php 
															try{
															   $strSQL = "	SELECT 
															   					cod_agenda,
																				titulo,
																				ag_categoria.nome as nomecat
																			FROM 
																				ag_agenda,
																				ag_categoria 
																			WHERE 
																				--prev_dt_ini = CURRENT_DATE
																			--AND
																				situacao <> 'status_img_fechado' 
																			AND
																				ag_agenda.cod_categoria = ag_categoria.cod_categoria
																			AND
																				id_responsavel = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' ";
															   $objResultAgenda = $objConn->query($strSQL);
															}
															catch(PDOException $e){
																mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
																die();
															}
															$strBgColor = "#FFFFFF";
															foreach($objResultAgenda as $objRSAgenda){
														?>
															<tr bgcolor="<?php echo($strBgColor)?>" onMouseOver="switchColor(this,'#CCCCCC');" onMouseOut="switchColor(this,'<?php echo($strBgColor); ?>');">
																<?php if(getsession(CFG_SYSTEM_NAME. "_grp_user") == 'ADMIN' || getsession(CFG_SYSTEM_NAME. "_grp_user") == 'SU' ){?><td width="4%"><img style="cursor:pointer" src="../img/icon_trash.gif"></td><?php } ?>
																<?php if(getsession(CFG_SYSTEM_NAME. "_grp_user") == 'ADMIN' || getsession(CFG_SYSTEM_NAME. "_grp_user") == 'SU' ){?><td width="4%"><img style="cursor:pointer" onClick="window.open('../modulo_Agenda/insupddelmastereditor.php?var_oper=UPD&var_chavereg=<?php echo(getValue($objRSAgenda,'cod_agenda'));?>&var_populate=yes','<?php echo(CFG_SYSTEM_NAME)?>_agenda','popup=yes,width=800,height=600,scrollbars=yes');" src="../img/icon_write.gif"></td><?php } ?>
																<td width="4%"><img style="cursor:pointer" onClick="window.open('../modulo_Agenda/wizardresposta.php?var_oper=INS_RESP&var_chavereg=<?php echo(getValue($objRSAgenda,'cod_agenda'));?>&var_populate=yes','<?php echo(CFG_SYSTEM_NAME)?>_agenda','popup=yes,width=800,height=600,scrollbars=yes');" src="../img/icon_nova_resposta.gif"></td>
																<?php if((getsession(CFG_SYSTEM_NAME. "_grp_user") == 'ADMIN' || getsession(CFG_SYSTEM_NAME. "_grp_user") == 'SU') && getValue($objRSAgenda,'nomecat')  == 'Homologação'  ){?><td width="4%"><img  style="cursor:pointer" onClick="window.open('../modulo_CadHomologacao/SThomologapasso1.php?var_oper=INS&var_chavereg=<?php echo(getValue($objRSAgenda,'cod_agenda'));?>&var_populate=yes','<?php echo(CFG_SYSTEM_NAME)?>_homologacao','popup=yes,width=800,height=600,scrollbars=yes');" src="../img/icon_homologa.gif"></td><?php } ?>
																<td><?php echo(getValue($objRSAgenda,'titulo'))?></td>
															</tr>
														<?php
																if($strBgColor == "#FFFFFF"){
																	$strBgColor = "#F7F7F7";
																}else{
																	$strBgColor = "#FFFFFF";
																}
															}
														?>
														</table>													</td>
												</tr>
											</table>
										<?php athEndWhiteBox(); ?>									</td>
								</tr>
							</table>						</td>
					</tr>
				</table>
				<?php athEndWhiteBox(); ?>			</td>
			<td style="padding-left:10px;" valign="top">
				<?php athBeginWhiteBox("120"); 
					  $strImage = (getsession(CFG_SYSTEM_NAME . "_foto_usuario") != "") ? getsession(CFG_SYSTEM_NAME . "_cli_dir_logical_path") . "/fotosusuario/" . getsession(CFG_SYSTEM_NAME . "_foto_usuario") : "../img/unknownuser.jpg";
					
					  $arrImageInfo = getimagesize($strImage); // Coloca num array algumas informações sobre o arquivo selecionado.
					  $intWidth = $arrImageInfo[0];               // Largura em pixels da imagem
					  $intWidth = ($intWidth < 100) ? $intWidth : 100; // Se largura é menor que 100 ele mantém a largura, caso contrário ele fixa em 100
					
					  echo("
					<img src=\"" . $strImage . "\" width=\"" . $intWidth . "\"><br><br>
					<b>" . getsession(CFG_SYSTEM_NAME . "_nome_usuario") . "</b> (<small>" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "</small>)<br>
					" . getsession(CFG_SYSTEM_NAME . "_grp_user"));
					
				      athEndWhiteBox(); 
				?>			</td>
		 </tr>
		 <tr>
		   <td align="center" valign="middle" colspan="2"><br>
				<?php athBeginWhiteBox("100%"); ?>
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td width="1%" align="center" nowrap><?php include_once("_includemensagem.php"); ?></td>
						<td width="98%" align="center" valign="top" style="padding:0px 10px;"><?php	include_once("_includechamado.php"); ?></td>
						<td width="1%" align="right">
							<img src="<?php echo(getsession(CFG_SYSTEM_NAME . "_cli_dir_logical_path")); ?>/img/logomarca.gif" border="0">						</td>
					</tr>
				</table>
			    <?php athEndWhiteBox(); ?></td>
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