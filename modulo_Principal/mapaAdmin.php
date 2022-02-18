<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
 
 $objConn = abreDBConn(CFG_DB);
	
	
	if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) == "normal")
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
function MyRefresh(prObj, prDia, prMes, prAno){
//				  alert('dia: '+prObj.dt_dia+' mes: '+prObj.dt_mes+' ano: '+prObj.dt_ano);
//				  alert('dia: '+prDia+' mes: '+prMes+' ano: '+prAno);
//				document.getElementById("ifrEventos").src='STagenda.php?var_dia='+prObj.dt_dia+'&var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
//				document.getElementById("ifrCalendar").src='STagendaCalendar.php?var_dia='+prObj.dt_dia+'&var_mes='+prObj.dt_mes+'&var_ano='+prObj.dt_ano;
				self.frames.ifrCalendar.location.href = 'STagendaCalendar.php?var_mes='+prMes+'&var_ano='+prAno;
				self.frames.ifrEventos.location.href = 'STagenda.php?var_dia='+prDia+'&var_mes='+prMes+'&var_ano='+prAno;
	//			parent.frames[ifrCalendar].reload();
			  }		

function ok() {
	document.chamado.submit();	
}

</script>
</head>
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<body marginheight="0" marginwidth="0" topmargin="0" leftmargin="0" bgcolor="#D5D5D5">

	

	    <div style="padding-left:5px;">
	<table width="240" height="98%" cellpadding="0" cellspacing="0" border="0">
	  <tr><td colspan="2" height="10" background="../img/bmap_header.gif"></td></tr>
	  <tr>
		<td width="26" valign="top" align="right" background="../img/bmap_bgTabs.gif">
			<?php 
				if (strtoupper(getSession(CFG_SYSTEM_NAME."_grp_user")) == "NORMAL") {
					echo("<img src='../img/bmap_TabARNetUnico.gif' border='0' />");
				} else  {	
					echo("<img src='../img/bmap_TabGeral.gif' border='0' usemap='#Map'/>");
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
				 <img src="../img/LogoMarca<?php echo(strtoupper($strAux));?>.gif" vspace="5" border="0">
			   </td>
			  </tr>
			  <tr><td height="6"></td></tr><!-- margem -->
              <tr><td height="1%" style="padding-left:2px;"><div style="border:1px solid #C9C9C9; width:192px" align="center"> <?php include("STAniverColega.php")?></div></td></tr>
			  <tr><td height="6"></td></tr><!-- margem -->
			  <tr><td height="1%" style="padding-left:2px;">
			  <div style="border:1px solid #C9C9C9; width:192px" align="center">
				<form id="chamado" name="chamado" action="<?php
					$intAno = date("Y");
					$intMes = date("m");
					$intDia = date("d");
					
					if ((($intAno == 2012) && ($intMes == 8) && ($intDia >= 4)) || (($intAno == 2012) && ($intMes > 8)) || ($intAno > 2012))
						echo "https://virtualboss.proevento.com.br/proevento/default_LoginViasite.asp";
					else
						echo "www.virtualboss.com.br/proevento/default_LoginViasite.asp";
					?>" target='_blank' method="post">
				  <input type="hidden" id='var_user' name='var_user' value='<?php 
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindieventos") echo("tusev");
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindiprom") echo("tuspr");
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "ubrafe") echo("tuubr");
   			      if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "abramge") echo("tuabr");
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sinamge") echo("tuabr");
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sinog") echo("tusng");
				  if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "uca") echo("tuuca");
				  ?>_<?php echo (strtolower(getsession(CFG_SYSTEM_NAME . "_id_usuario")));?>' />
				  <input type="hidden" id='var_password' name='var_password' value='athroute'>
				  <input type="hidden" id='var_db' name='var_db' value='proevento'>
				  <input type='hidden' id='var_extra' name='var_extra' value='<?php echo(getsession(CFG_SYSTEM_NAME."_id_mercado").".".getsession(CFG_SYSTEM_NAME."_id_evento"));?>'>
				  <a href='#' onclick='ok();'> <img alt='HelpDesk' src='../img/button_help_desk.gif' border='0'></a>
				</form>
			  </div></td></tr>
			  <tr><td height="6"></td></tr><!-- margem -->
              <tr height="135" align="center" valign="middle"><td align="center" valign="middle">
		              <iframe id="ifrCalendar"
                      			name="ifrCalendar"
								width="190" 
								height="100%" 
								marginheight="0" 
								marginwidth="0" 
								scrolling="no"
								frameborder="0" 
								style="border:1px solid #C9C9C9" 
								src="STagendaCalendar.php">
				</iframe>
              </td></tr>
              
              <tr><td height="6"></td></tr><!-- margem -->
              
               <tr>
				<td height="20" align="center">
					<table width="192" height="20" cellpadding="0" cellspacing="0" style="border:1px solid #C9C9C9">
					<tr>
					  <td height="20" style="padding-left:5px;text-align:left;background-color:#F9F9F9;">
						<?php echo(getTText("evento",C_NONE));?> 
					  </td>
					  <td height="20" style="padding-right:5px;text-align:right;background-color:#F9F9F9;">
						<img alt='Atualizar' src='../img/icon_refresh.gif' border='0' dt_dia='<?php echo($intDia);?>' dt_mes='<?php echo($intMes);?>' dt_ano='<?php echo($intAno);?>' onClick="MyRefresh(this,'<?php echo($intDia); ?>','<?php echo($intMes); ?>','<?php echo($intAno); ?>');" id="atualizaCalendar" name="atualizaCalendar" style=' cursor:pointer;'>
					  </td>
					</tr>
					</table>
				</td>
			  </tr>
              <tr><td height="6"></td></tr><!-- margem -->   
              
			  <tr>
				<td align="center" valign="top">
					<table width="196" height="99%" cellpadding="0" cellspacing="0" border="0">
					<tr>
						<td align="center">
						<iframe id="ifrEventos"
                        		name="ifrEventos"
								width="190" 
								height="100%" 
								marginheight="0" 
								marginwidth="0" 
								scrolling="auto"
								frameborder="0" 
								style="border:1px solid #C9C9C9" 
								src="STagenda.php">
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
		<area shape="rect" coords="7,14,21,72" href="mapaAdmin.php" />      
		<area shape="rect" coords="7,105,23,178" href="mapaGeral.php" />

	</map>
</body>
</html>