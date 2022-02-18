<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
 
 $objConn = abreDBConn(CFG_DB);
	
 if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) == "NORMAL") 
 {
	$strFramePage = "STmenulateralAdmin.php";
 }
  else 
 {
	$strFramePage = "STmenulateral.php";
 }
?>
<html>
<head>
<title>GERAL</title>
<script language="javascript">
function MyRefresh(prObj){
	//  alert('dia: '+prObj.dt_dia+' mes: '+prObj.dt_mes+' ano: '+prObj.dt_ano);
	//	document.getElementById("ifrEventos").src='STagenda.php?var_dia='+prObj.dt_dia+'&var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
	//	document.getElementById("ifrCalendar").src='STagendaCalendar.php?var_dia='+prObj.dt_dia+'&var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
	self.frames.ifrCalendar.location.href = 'STagendaCalendar.php?var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
	self.frames.ifrEventos.location.href = 'STagenda.php?var_dia='+prObj.dt_dia+'&var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
	// parent.frames[ifrCalendar].reload();
}		
</script>
</head>
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" bgcolor="#D5D5D5">

		
<div style="padding-left:5px;">
	<table width="240" height="98%" cellpadding="0" cellspacing="0" border="0">
	  <tr><td colspan="2" height="10" background="../img/bmap_header.gif"></td></tr>
	  <tr>
		<td width="26" valign="top" align="right" background="../img/bmap_bgTabs.gif"><br>
			<?php 
				if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) == "NORMAL") {
					echo("<!--img src='../img/bmap_TabARNetUnico.gif' border='0' /-->");
				} else  {	
					echo("<img src='../img/bmap_arnet.gif' border='0' usemap='#Map'/>");
				}
			?></td>
		<td valign="top" align="center" background="../img/bmap_bgContent.gif">
			<!-- ------------------------------------------------------------------------------------------ -->
			<!-- INI: NUCLEO da FICHA --------------------------------------------------------------------- -->
			<!-- ------------------------------------------------------------------------------------------ -->
			<table width="196" height="100%" cellpadding="0" cellspacing="0" border="0">
			  <tr>
				<td height="1%" align="center">
				 <?php
				   //Como o nome do DB tem de ter como sufixo o nome da pasta do cliente ("datawide_abrh", por exemplo)
				   //então pegamos esse sufixo para descobrir o nome da imagem de LogoMarca do cliente que conforme 
				   //nossa padronização segue a regra de nome: LogoMarca_[nome_cliente].jpg
				   $strAux = getsession(CFG_SYSTEM_NAME."_db_name"); 
				   $strAux = str_replace(CFG_SYSTEM_NAME,"",$strAux);
				 ?>
				 <?php 
						if (strtoupper(trim(getSession(CFG_SYSTEM_NAME."_grp_user"))) != "NORMAL") {
				?>
					 	<img src="../img/LogoMarca<?php echo(strtoupper($strAux));?>.gif" vspace="5" border="0">
				<?php   } else{
							if (trim(strtoupper(getSession(CFG_SYSTEM_NAME."_entidade_tipo"))) == "CAD_PF"){
						?>					
							<a href="../modulo_PainelPF/STindex.php" target="tradeunion_frmain"><img src="../img/LogoMarca<?php echo(strtoupper($strAux));?>.gif" vspace="5" border="0"></a>
					<?php   } else{ ?>
							<img src="../img/LogoMarca<?php echo(strtoupper($strAux));?>.gif" vspace="5" border="0">
					<?php   	}
						}?>
			   </td>
			  </tr>
			  <tr><td height="6"></td></tr><!-- margem -->
			  <tr>
				<td height="20" align="center">
					<table width="192" height="20" cellpadding="0" cellspacing="0" style="border:1px solid #C9C9C9">
					<tr>
					  <td height="20" style="padding-left:5px;text-align:left;background-color:#F9F9F9;">
						<?php echo(getTText("menu_lateral",C_NONE));?> 
					  </td>
					  <td height="20" style="padding-right:5px;text-align:right;background-color:#F9F9F9;">
					  <?php 
							if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) != "NORMAL") {
					  ?>
								<a href='mapaGeral.php'><img alt="Atualizar" src="../img/icon_refresh.gif" border="0"></a>
					  <?php } ?>
					  </td>
					</tr>
					</table>
				</td>
			  </tr>
			  <tr><td height="6"></td></tr><!-- margem -->
			  <tr>
				<td align="center" valign="top">
					<table width="196" height="100%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td>
						<iframe id="ifrGeral"
		                        name="ifrGeral"
								width="190" 
								height="100%" 
								marginheight="0" 
								marginwidth="0" 
								scrolling="auto"
								frameborder="0" 
								style="border:1px solid #C9C9C9" 
								src="STmenulateral.php">
						</iframe>
				  	  </td>
			  		</tr>
					</table>
				</td>
			  </tr>
			  <tr><td height="5"></td></tr><!-- margem -->
			</table>
			<!-- ------------------------------------------------------------------------------------------ -->
			<!-- FIM: NUCLEO da FICHA --------------------------------------------------------------------- -->
			<!-- ------------------------------------------------------------------------------------------ -->
		</td>
	  </tr>
	  <tr><td colspan="2" height="10" background="../img/bmap_footer.gif"></td></tr>
	  <tr><td colspan="2"><br></td></tr><!-- para garantir a margem inferior -->
	</table>
	</div>
	<map name="Map" id="Map">
		<area shape="rect" coords="9,15,23,74" href="mapaAdmin.php" />      
		<area shape="rect" coords="8,101,23,173" href="mapaGeral.php" />
	</map>
</body>
</html>