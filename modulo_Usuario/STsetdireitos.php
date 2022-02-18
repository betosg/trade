<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	
	// REQUESTS
	$intCodUsuario = request("var_chavereg"); // cod_usuario
	
	// VEROFICAÇÃO DE ACESSO
	$strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "UPD_DIR");
	
	//$strBGColor = CL_CORLINHA_1;
	$strBGColor = "#FFFFFF";
	
	$objConn    = abreDBConn(CFG_DB);
	if($intCodUsuario != "") {
	   try {
		   $strSQL = " SELECT nome, grp_user FROM sys_usuario WHERE cod_usuario = " . $intCodUsuario;
		   $objResultUsuario = $objConn->query($strSQL); // Seleciona o nome do usuário
		  
		  if($objRSUsuario = $objResultUsuario->fetch()) {
			$strNomeUsuario = getValue($objRSUsuario,"nome"); 	  //nome do usuário 
			$strGrpUser     = getValue($objRSUsuario,"grp_user"); //nome do grupo usuário 
		  }
		  
		  $objResultUsuario->closeCursor();
		  
		  /*
		  $strSQL = " SELECT cod_app, dir_app 
						FROM sys_app 
					  " . (($strGrpUser != "") ? " WHERE dir_app IN (SELECT dir_app FROM sys_mx AS mx INNER JOIN sys_mx_item_sub AS sub ON (mx.cod_mx = sub.cod_mx) WHERE grp_user = '" . $strGrpUser . "')" : "") . " 
					ORDER BY dir_app ";
		  */			
		  $strSQL = "SELECT DISTINCT(sys_app.cod_app) ";
		  $strSQL .= "      , sys_app.dir_app  ";
		  $strSQL .= "      , sys_mx_item_sub.rotulo ";
		  $strSQL .= "      , sys_mx_item_sub.descricao  ";
		  $strSQL .= "      , sys_mx_item_sub.img ";
		  $strSQL .= "      , sys_mx_item_sub.dtt_inativo ";
		  $strSQL .= "  FROM sys_app, sys_mx, sys_mx_item_sub ";
		  $strSQL .= " WHERE sys_app.dir_app ilike sys_mx_item_sub.dir_app ";
		  $strSQL .= "   AND sys_mx.cod_mx = sys_mx_item_sub.cod_mx ";
		  $strSQL .= "   AND sys_app.dir_app IS NOT NULL ";
		  $strSQL .= ($strGrpUser != "") ? "AND sys_mx.grp_user = '". $strGrpUser ."'" : "";  
		  $strSQL .= "UNION ";
		  $strSQL .= "SELECT DISTINCT(sys_app.cod_app) ";
		  $strSQL .= "      , sys_app.dir_app  ";
		  $strSQL .= "      , '000_ROTULO' ";
		  $strSQL .= "      , '000_DESCRICAO' ";
		  $strSQL .= "      , '000_IMAGEM' ";
		  $strSQL .= "      , CURRENT_TIMESTAMP ";
		  $strSQL .= "  FROM sys_app  ";
		  $strSQL .= " WHERE NOT EXISTS  ";
		  $strSQL .= "       (SELECT sys_mx_item_sub.dir_app  ";
		  $strSQL .= "          FROM sys_mx_item_sub ";
		  $strSQL .= "         WHERE sys_app.dir_app = sys_mx_item_sub.dir_app ) ";
		  $strSQL .= "ORDER BY 4 DESC, 3 ASC ";
	
		  $objResult = $objConn->query($strSQL); //Seleciona todas as aplicações cadastradas no sistema
		  
	   } catch(PDOException $e){
		  mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		  die();
	   }
	} else {
	   mensagem("err_dados_titulo","err_dados_obj_desc",$e->getMessage(),"","erro",1);
	   die();
	}
?>
<html>
	<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript">
		var intI = 0;
		function submeterForm(){
		   if(intI < document.forms.length){
			  document.forms[intI].var_direitos.value = processarDireitos(intI);
			  document.forms[intI].submit();
			  intI++;
		   }
		   else{
			  intI = 0;
			  location.href="STsetdireitos.php?var_chavereg=<?php echo($intCodUsuario); ?>";
		   }
		}
		
		function submeterFormAtual($formAtual){
		   document.forms[$formAtual].var_direitos.value = processarDireitos($formAtual);
		   document.forms[$formAtual].submit();
		}
		
		function submeterTForm(){
		   document.forms[intI].var_todos.value = "T";
		   submeterForm();
		}
		
		function processarDireitos(prCodForm){
		   var codigos = "";
		   var intAux = 0;
		   while (eval("document.forms[" + prCodForm + "].msguid_" + intAux) != null){
			  if (eval("document.forms[" + prCodForm + "].msguid_" + intAux) != null){
				 if (eval("document.forms[" + prCodForm + "].msguid_" + intAux).checked){
					if (codigos != ""){
					   codigos = codigos + ", " + eval("document.forms[" + prCodForm + "].msguid_" + intAux).value;
					}
					else{
					   codigos = eval("document.forms[" + prCodForm + "].msguid_" + intAux).value;
					}
				 }
			  }
			  intAux++;
		   }
		   return(codigos);
		}
	</script>
</head>
<body bgcolor="#CFCFCF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" text="#000000" 
 style="margin:10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
  		<td valign="top" align="center">
  	<?php 
     	athBeginFloatingBox("720","none",getTText(getsession($strSesPfx."_titulo"),C_TOUPPER).
										" (".getTText("tabela_de_direitos",C_UCWORDS).") - <b>".
										 $strNomeUsuario."</b>",CL_CORBAR_GLASS_1);?>
		<table border="0" width="100%" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;">
	 		<tr>
	  			<td align="center">
	   				<table width="90%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td colspan='3' style="padding:10px;">
								<span style="font-weight:bold;"><?php echo(getTText("apps_para_este_usuario",C_NONE));?>:</span>
							</td>
							<td></td>
						</tr>
				<?php
					//o contador abaixo foi necessario para contagem da posicao atual
					//pois quando queremos executar o update da linha corrente, temos
					//que informar a posicao dela.
					$cont = 0;
					$strFlagLinha  = "";		
					//Foreach das aplicações
					foreach($objResult as $objRS){
						//Coleta o nome da aplicacao e calcula a posicao do caracter '_'
						//Logo em seguida coloca tudo em maiusc e corta a string apos
						//a posicao do caracter '_' - ($posMod+1)
						$posMod 	   = strpos(getValue($objRS,"dir_app"),"_");
						$strLabelCOLOR = "";
						$strMod = (getValue($objRS,"rotulo") == "000_ROTULO") ? strtoupper(substr(getValue($objRS,"dir_app"),$posMod+1)) : getValue($objRS,"rotulo");
						if((getValue($objRS,"rotulo") != "000_ROTULO") && (getValue($objRS,"dtt_inativo") != "")){
							$strLabelCOLOR = "#CCC";
						} else{
							$strLabelCOLOR = "#000";
						}
						
						if((getValue($objRS,"rotulo") == "000_ROTULO") && ($strFlagLinha == "")){
							echo("<tr><td style='border-bottom:1px dashed #CCC;' colspan='3'>&nbsp;</td><td></td></tr>");
							echo("<tr><td colspan='3' style='padding:10px;'>
								     <span style='font-weight:bold;'>
									 ".getTText("apps_extra_menu",C_NONE).":</span></td><td></td></tr>");
							$strFlagLinha = "no_line";
						}
						
						echo("
							<tr>
								<td align='right' nowrap title='".getValue($objRS,"dir_app")."'>
									<span style='color:".$strLabelCOLOR.";font-weight:bold;'>".getTText($strMod,C_NONE).":</span>
								</td>
								<td align='right' width='80' height='35' background='".getValue($objRS,"img")."'
								 style='background-position:center;background-repeat:no-repeat;' 
								 title='".getValue($objRS,"descricao")."'>
								</td>
								<td>
									<table>
										<tr>
											<td style=\"border: 1px solid; border-color: #D3D3D3;\">
												<table bgcolor=\"". $strBGColor ."\" border=\"0\" cellpadding=\"0\" 
												 cellspacing=\"0\" style=\"padding-right: 8px;\">
													<tr bgcolor=\"". $strBGColor ."\">
														<form name=\"formdir_" . getValue($objRS,"cod_app") . "\" 
														 id=\"formdir_" . getValue($objRS,"cod_app") . "\" 
														 action='STsetdireitosexec.php' method='post' 
														 target=\"VBossIframeSave_" . getValue($objRS,"cod_app") . "\">
															<input type=\"hidden\" name=\"var_coduser\" 
															 value=\"" . $intCodUsuario . "\">
															<input type=\"hidden\" name=\"var_codapp\"  
															 value=\"" . getValue($objRS,"cod_app") . "\">
															<input type=\"hidden\" name=\"var_direitos\"  value=\"\">
															<input type=\"hidden\" name=\"var_todos\" value=\"F\">");
			
						//String de consulta
						$strSQL = "
							SELECT 
								sys_app_direito.id_direito 
							FROM sys_app_direito_usuario, sys_app_direito  
							WHERE sys_app_direito_usuario.cod_usuario = " . $intCodUsuario . "
							AND sys_app_direito.cod_app = " . getValue($objRS,"cod_app") . " 
							AND sys_app_direito_usuario.cod_app_direito = sys_app_direito.cod_app_direito";
						
						// Seleciona as operações existentes na aplicação
						$objResultLocal = $objConn->query($strSQL); 
			
						//Cria um array que conterá as permissões			
						$arrPerm = array();
			
						//Preenche um array com as operações
						foreach($objResultLocal as $objRSLocal){ 
							array_push($arrPerm,$objRSLocal["id_direito"]);
						}
			
						try{	
							//Seleciona todas os direitos que a aplicação atual (getValue($objRS,"cod_app")) contém
							$strSQL = " SELECT * FROM sys_app_direito 
							WHERE cod_app = " . getValue($objRS,"cod_app") . "
							ORDER BY cod_app_direito";
				
							//Executa query que contém os direitos ($strSQL)
							//Seleciona as operações do usuário para esta aplicação
							$objResultLocal = $objConn->query($strSQL);
						}
						//Exceção
						catch(PDOException $e){
							mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
							die();
						}
			
						// Contador auxiliar para os checkboxes
						$intI = 0; 
						
						//Contador para a linha de input atual - Explicação abaixo
						$contLinha = 0;
			
						//Foreach das operações nessa aplicação
						foreach($objResultLocal as $objRSLocal){ 
							//Adicionamos um contador que verifica o número do campo input atual
							//quando o input for o de numero 5 e a linha de contagem for igual a
							//a 5, este input é inserido em uma nova linha (</tr><tr nowrap>). Além
							//disso, é adicionado um valor TRUE para aplicar o estilo correto.
							//(Quando a linha é quebrada, o alinhamento do Botão OK não fica corre-
							//to se colocarmos o mesmo estilo dos forms de unica linha)
							$contLinha++;
				
							if(($contLinha % 5 == 0) and ($contLinha == 5)){
								//Caso esteja no momento correto de quebra de linha,
								//a Tag da linha corrente é fechada e uma nova é aberta
								echo "
								</tr>
								<tr bgcolor=\"". $strBGColor ."\">";
					
								//zera contador para o próximo Form e aplica estilo correto para a linha corrente
								$contLinha = 0;
							}
						
							// Mantendo o alinhamento da tabela
							// Celula Checkbox
							echo("<td>
								  <input type=\"checkbox\" name=\"msguid_".$intI."\" id=\"msguid_".$intI."\" 
								   value=\"".getValue($objRSLocal,"cod_app_direito")."\"");
						   
							// Verifica se o usuário tem a premissão para essa operação
							// caso tenha, marca a checkbox como "checked"
							(in_array($objRSLocal["id_direito"],$arrPerm)) ? print(" checked ") : NULL; 
							echo(" 
								   class=\"inputclean\" style=\"margin-left: 8px; margin-right: -6px;\">&nbsp;&nbsp;
								 </td>
								 <div style=\"margin-right: 2px;\">
								 	<td style=\"position: relative; margin-right: 7x;\">"
										.getTText(getValue($objRSLocal,"id_direito"),C_NONE).
								   "</td>
								 </div>");
				
							//Incrementa o contador de checkbox
							$intI++;
			
							//Finaliza Foreach
					}
				
					$objResultLocal->closeCursor();
					echo("						</form>	
											   </tr>
											 </table>
											</td>
			 								<td valign=\"middle\">
												<a href=javascript:submeterFormAtual(" . $cont . ");><img src=\"../img/BtOk.gif\" border=\"0\" hspace=\"6\"></a>
											</td>
			  	   						
									</tr>
		     					</table>
								</center>");
		
			echo ("			</td>		   						  
		   <td nowrap>
		    <iframe id=\"VBossIframeSave_" . getValue($objRS,"cod_app") . "\" allowtransparency=\"true\" frameborder=\"0\" width=\"16\" height=\"16\" name=\"VBossIframeSave_" . getValue($objRS,"cod_app") . "\" scrolling=\"no\"></iframe>&nbsp;&nbsp;
		   </td>
		  </tr>");
					
		//verifica a cor da linha corrente	
		//$strBGColor = ($strBGColor == CL_CORLINHA_2) ? CL_CORLINHA_1 : CL_CORLINHA_2;
		$strBGColor = ($strBGColor == "#F5F5F5") ? "#FFFFFF" : "#F5F5F5";
		
		//Incrementa o contador da linha atual
		$cont++;
		}
		
		$objResult->closeCursor();
	?>
		
		<tr><td height="5" colspan="4"></td></tr>
		<tr><td class="linedialog" colspan="4"></td></tr>
		<tr>
		 <td align="right" colspan="4" style="padding:10px 0px 10px 10px;">
		  <button onClick="submeterTForm(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
		  <button onClick="location.href='<?php if (strpos(getsession($strSesPfx . "_grid_default"),"?") === false) echo("../_fontes/".getsession($strSesPfx . "_grid_default")."?var_basename=".getsession($strSesPfx . "_dir_modulo")); else echo("../_fontes/".getsession($strSesPfx . "_grid_default")."&var_basename=".getsession($strSesPfx . "_dir_modulo")); ?>'; return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
		 </td>
		</tr>
	   </table>
	  </td>
	 </tr>
	</table>
	<?php athEndFloatingBox(); ?>
  </td>
 </tr>
</table>
</body>
</html>