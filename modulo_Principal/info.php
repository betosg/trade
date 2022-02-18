<?php
 include_once("../_database/athdbconn.php");
 include_once("../_database/athtranslate.php");
 include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");
 
 define("MAXCTRL",6);

 $objConn = abreDBConn(CFG_DB);

 //Busca dados sobre o site ---------------------------------
 montaArraySiteInfo($objConn, $arrScodi, $arrSdesc);
?>
<html>
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <script>
 		function switchColor(prObj, prColor){
			prObj.style.backgroundColor = prColor;
		}
			
 </script>
</head>
<body style="margin:15px 0px 10px 0px;" bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_filtro.jpg">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border:none; background:none">
  <tr> 
    <td width="1%" align="center" valign="top" style="border:none; background:none;">
      <table border="0" cellspacing="0" cellpadding="0" style="border:none; background:none">
        <tr> 
          <td align="left" valign="top" style="border:none; background:none">
		      <?php athBeginFloatingBox("220","","<center class='padrao_gde'><b>(" . getsession(CFG_SYSTEM_NAME . "_cod_evento") . ") " . getsession(CFG_SYSTEM_NAME . "_nome_evento")."</b></center>",CL_CORBAR_GLASS_2); ?> 
				  <table width="100%" border="0" cellpadding="2" cellspacing="3" style="border:none; background:none">
					<tr> 
                      <td align="left" style="border:none; background:none"><?php echo(getTText("dominio",C_UCWORDS)); ?>:&nbsp;
					  <br><strong><?php echo($arrSdesc[arrayIndexOf($arrScodi,"dominio")]); ?></strong></td>
                    </tr>
                    <tr> 
                      <td align="left" style="border:none; background:none"><?php echo(getTText("servidor",C_UCWORDS)); ?>:&nbsp;
                      <br><strong><?php echo($_SERVER["SERVER_NAME"]); ?> 
                        (<?php echo($_SERVER["REMOTE_ADDR"]); ?>)</strong></td>
                    </tr>
                    <tr> 
                      <td align="left" style="border:none; background:none" nowrap><?php echo(getTText("bancodedados",C_UCWORDS)); ?>:&nbsp;
                      <br><strong><?php echo(getsession(CFG_SYSTEM_NAME . "_db_name"))?></strong></td>
                    </tr>
                    <tr> 
                      <td align="left" style="border:none; background:none"><?php echo(getTText("cliente",C_UCWORDS)); ?>:&nbsp;
                      <br><strong><?php echo($arrSdesc[arrayIndexOf($arrScodi,"cliente")]); ?></strong></td>
                    </tr>
				</table>
				<?php athEndFloatingBox(); ?>
				<br>
				<?php athBeginFloatingBox("220","","<center class='padrao_gde'><b>Novos Cadastros</b></center>",CL_CORBAR_GLASS_2); ?> 
				 <table width="100%" border="0" cellspacing="0" style="border:none; background:none">
				<?php
				 try{
				   $strSQL = "SELECT codigo, id_usuario FROM sys_usuario WHERE grp_user = 'PRE_CADASTRO' LIMIT 10";
				   $objResultCad = $objConn->query($strSQL);
				}
				catch(PDOException $e){
					mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
					die();
				}
				$strBgColor = "#FFFFFF";
				foreach($objResultCad as $objRSCad){	
				?>
                    <tr bgcolor="<?php echo($strBgColor)?>" onMouseOver="switchColor(this,'#CCCCCC');" onMouseOut="switchColor(this,'<?php echo($strBgColor); ?>');"> 
                      <td width="10%" style="cursor:pointer"onClick="window.open('../modulo_CadPJPreCadastro/STliberacad.php?var_chavereg=<?php echo(getValue($objRSCad,'codigo'));?>','<?php echo(CFG_SYSTEM_NAME)?>_liberacao','popup=yes,width=500,height=300,scrollbars=yes');"><img src="../img/icon_liberar_cad.gif"></td>
					  <td align="left"><?php echo(getValue($objRSCad,'id_usuario'));?></td>
                    </tr>
				<?php
					if($strBgColor == "#FFFFFF"){
						$strBgColor = "#F7F7F7";
					}else{
						$strBgColor = "#FFFFFF";
					}
				}
				?>
                  </table>
				<?php athEndFloatingBox(); ?>
				 </td>
              </tr>
            </table>
		</td>
	</tr>
</table>
</body>
</html>