<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// verificação de ACESSO
	// carrega o prefixo das sessions
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");

	// REQUESTS
	// indicativo para qual formato que a grade deve ser exportada. Caso esteja vazio esse campo, a grade é exibida normalmente
	$strAcao 		= request("var_acao");
	$intCodAgenda	= request("var_chavereg");		// cod_agenda para o qual irá ser update da agenda
	$strRedirect	= request("var_redirect");				// pagina que sera feito o redir
	$strDefaultGrp  = request("var_default_grp");   		// se informado, marca o combo de grupo com opção DEFAULT
	
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'','erro','1');
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	// função para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.tr_filtro_field { padding-left:5px; }
			.tr_filtro_label { padding-left:5px; padding-top:5px; }
			.td_search_left  { 
				padding:8px;
				border-top:1px solid #C9C9C9;
				border-left:1px solid #C9C9C9;
				border-bottom:1px solid #C9C9C9; 
			}
			.td_search_right  { 
				padding:5px;
				border-top:1px solid #C9C9C9;
				border-right:1px solid #C9C9C9;
				border-left: 1px dashed #C9C9C9;
				border-bottom:1px solid #C9C9C9;
			}
		</style>
		<script type="text/javascript">
			window.resizeTo(675,550);
			
			function ajaxBuscaUsuario(){
				// Esta funcao coleta os campos preen-
				// chidos no filtro ao lado e com base
				// nisso monta o SQL que busca os resul
				// tados [usuarios] que o ajax irá pre-
				// encher o combo de users disponiveis
				var strSQL;
				var intCodUsuario = document.getElementById('var_search_cod_usuario').value;
				var strIDUsuario  = document.getElementById('var_search_id_usuario').value;
				var strGrpUsuario = document.getElementById('var_search_grp_user').value;
				
				// DEBUGS
				// alert(intCodUsuario);
				// alert(strIDUsuario);
				// alert(strGrpUsuario);
				
				// monta o SQL
				strSQL = "SELECT id_usuario, id_usuario FROM sys_usuario WHERE";
				strSQL = strSQL + " id_usuario <> '<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>'";
				
				// caso alguma das opcoes esteja diferente
				// de nulo, aidiciona clausulas extras SQL
				if((intCodUsuario != "") || (strIDUsuario != "") || (strGrpUsuario != "")){
					if(intCodUsuario != ""){
						strSQL = strSQL + " AND cod_usuario = " + intCodUsuario;
					}
					if(strIDUsuario  != ""){
						strSQL = strSQL + " AND id_usuario <=> '" + strIDUsuario + "%'";
					}
					if(strGrpUsuario != ""){
						strSQL = strSQL + " AND grp_user = '" + strGrpUsuario + "'";
					}
				}
				// debug do sql
				// alert(strSQL);
				
				// altera imagem de busca para um loader
				// document.getElementById('img_search').style.display = 'block';
				// document.getElementById('img_search').innerHTML = "<img src='../img/icon_ajax_loader.gif' style='float:right'>";
				
				// limpa o combo antes 
				// de adicionar valores
				while(document.getElementById('var_usuarios_d').options.length > 0){ 
					document.getElementById('var_usuarios_d').options[0] = null;
				}
				
				ajaxDetailData(strSQL,'ajaxMontaCombo','var_usuarios_d','');
				
				// altera para o icone de pesquisa novamente
				// document.getElementById('img_search').innerHTML = "<img src='../img/icon_search.gif' style='float:right'>";
				
				// exibe imagem
				document.getElementById('img_search').style.display 	  = 'block';
				// exibe table de usuarios disp após consulta
				document.getElementById('table_usuarios_d').style.display = 'block';
				document.getElementById('table_filtro').style.display 	  = 'none';
				return true;
			}
			
			function changeTables(){
				document.getElementById('table_usuarios_d').style.display = 'none';
				document.getElementById('table_filtro').style.display 	  = 'block';
				document.getElementById('img_search').style.display 	  = 'none';
			}
			
			
			
			function addCitado(prAll,prComboFrom,prComboTo){
				var objNewOption;
				var intContador;
				var strOpText, strOpValue;
				strComboTo 	 = document.getElementById(prComboTo);
				strComboFrom = document.getElementById(prComboFrom);
				for(intContador=0; intContador < strComboFrom.options.length; intContador++){
					// verifica se é para inserir
					// todos do campo, ele nao veri
					// fica se o campo esta selecionado
					// mas ainda assim verifica se
					// o campo ja existe no combo da
					// direita
					
					if((prAll || strComboFrom.options[intContador].selected == true) 
					   && (strComboFrom.options[intContador].value != "")){
						if(!verifyID(prComboTo,strComboFrom.options[intContador].value)){
							strOpText  = strComboFrom.options[intContador].value;
							strOpValue = strComboFrom.options[intContador].value;
							objNewOption = document.createElement("option");
							strComboTo.appendChild(objNewOption);
							objNewOption.text  = strOpText;
							objNewOption.value = strOpValue;
						}
					}
				}
			}
						
			function delCitado(prAll,prComboFrom){
				var intContador = 0;
				strComboFrom = document.getElementById(prComboFrom);
				
				for(intContador = 0; intContador < strComboFrom.options.length; intContador++){
					if((!prAll && strComboFrom.options[intContador].selected == true) 
					&& (strComboFrom.options[intContador].value != "")){
						strComboFrom.options[intContador] = null;
					}else if((prAll || strComboFrom.options[intContador].selected == true) 
					&& (strComboFrom.options[intContador].value != "")){
						while(strComboFrom.options.length > 0){
							strComboFrom.options[0] = null;
						}
					}
				}
			}			
			
			function verifyID(prCombo,prValue){
				// Esta funcao verifica a existe
				// ncia de um mesmo ID na sua lista
				// de options. Returns true or false
				var intContador = 0;
				var boolFlag;
				var strCombo 	= document.getElementById(prCombo);
				var strValue 	= prValue;
				boolFlag = false;
				
				while(intContador < strCombo.options.length){
					//alert(strCombo.options[intContador].value);
					if(strCombo.options[intContador].value == strValue){
						boolFlag 	= true;
						break;
					}
					intContador++;
				}
				// retorno TRUE/FALSE
				return(boolFlag);
			}
			
			function limparCombo(prCombo) {
				while (prCombo.options.length > 0) { prCombo.options[0] = null; }
			}
			
			function selectOptions(prCombo){
				// Esta função varre todas as
				// options e coleta seus values
				// e concatena com o campo
				// hidden que receberá todos os
				// usuarios
				var strComboForm = document.getElementById(prCombo);
				var intContador;
				var objHidden;
				
				// limpa o hidden para receber os values
				document.getElementById('var_usuarios_concat').value = "";
				
				// seta o campo
				objHidden = document.getElementById('var_usuarios_concat');
								
				for(intContador = 0; intContador < strComboForm.options.length; intContador++){
					objHidden.value = objHidden.value + strComboForm.options[intContador].value + ";";
				}
				// debug valor de envio
				// alert(objHidden.value);
			}
			
			
			function handleEnter(event){
				var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
				if(keyCode == 13){
					ajaxBuscaUsuario();
				}
			}
		
			function focusField(prIDField){
				// OBS: Esta funcao seta o focus
				// para um campo de id especifico
				// informado como parametro
				strIDField = prIDField;
				document.getElementById(strIDField).focus();
			}
			
			function goBack(){
				// OBS: Esta funcao retorna para
				// o historico anterior.
				window.history.back();
			}
			
			function goNext(prForm){
				// OBS: Esta funcao submita
				// o form enviado como param
				// em forma de id
				var strForm = prForm;
				document.getElementById(strForm).submit();
			}
		</script>
	</head>
<body background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px;">
<!-- USO -->
<center>
<?php athBeginFloatingBox("580","",getTText("citados_title",C_UCWORDS),CL_CORBAR_GLASS_2); ?>
<table cellpadding="0" cellspacing="0" border="0" height="315" width="550" bgcolor="#FFFFFF">
	<tr>
		<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
			<!-- WHITE BOX DA ESQUERDA -->
			<?php athBeginFloatingBox("200","220","<span id='img_search' style='display:none;cursor:pointer;'
				  onclick='changeTables();'><img src='../img/icon_search.gif' style='float:right'></span>"
				  ."<span style='padding-left:5px;'>".getTText("usuarios_d",C_UCWORDS)."</span>"
				  ,CL_CORBAR_GLASS_2); ?>
			<form name="formstatic_filtro" action="#" method="post">
			<table cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" id="table_filtro">
				<tr class="tr_filtro_label">
					<td><?php echo("<b>".getTText("filtrar_por",C_UCWORDS)."</b>");?></td>
				</tr>
				<tr><td>&nbsp;</td></tr>
				<tr class="tr_filtro_label">
					<td><?php echo(getTText("cod_usuario",C_UCWORDS));?>:</td>
				</tr>
				<tr class="tr_filtro_field">
					<td><input type="text" name="var_search_cod_usuario"
						 size="5"maxlength="10" onKeyPress="return validateNumKey(event);"
						 onKeyDown="handleEnter(event);"/>
					</td>
				</tr>
				<tr class="tr_filtro_label">
					<td><?php echo(getTText("id_usuario",C_UCWORDS));?>:</td>
				</tr>
				<tr class="tr_filtro_field">
					<td><input type="text"name="var_search_id_usuario"size="38"maxlength="120"
						 onKeyPress="handleEnter(event);"/>
					</td>
				</tr>
				<tr class="tr_filtro_label">
					<td><?php echo(getTText("grp_user",C_UCWORDS));?>:</td>
				</tr>
				<tr class="tr_filtro_field">
					<td>
						<select name="var_search_grp_user" style="width:120px;"
					     onKeyPress="handleEnter(event);"/>
							<option value="" selected="selected"></option>
							<?php echo(montaCombo($objConn,"SELECT DISTINCT grp_user FROM sys_usuario
												  WHERE grp_user <> 'SU'",'grp_user','grp_user',$strDefaultGrp,''));
							 ?>
						</select>
					</td>
				</tr>
				<tr height="9"><td></td></tr>
				<tr>
					<td align="right" style="padding-top:15px;border-top:1px solid #CCC;">
						<button onClick="ajaxBuscaUsuario();return false;">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
				</tr>
			</table>
			</form>
			<table cellpadding="0" cellspacing="0" border="0" width="100%" 
			 id="table_usuarios_d" style="display:none;">
				<tr>
					<td width="100%">
						<form name="formstatic_0">
							<select multiple="multiple" name="var_usuarios_d" id="var_usuarios_d" 
							 style="width:100%;border:1px solid #C9C9C9;" size="11">
							</select>
						</form>
						<div class="comment_peq" style="text-align:justify;padding:5px;"><?php echo(getTText("comentario_m_escolha",C_NONE));?></div>
					</td>
				</tr>
			</table> 									
			<?php athEndFloatingBox();?>
			<!-- WHITE BOX DA ESQUERDA -->
		</td>
	
		<td align="center" valign="middle" style="padding:30px 15px 15px 15px;">
		<!-- BUTTONS DO MEIO -->
			<button onClick="addCitado(false,'var_usuarios_d','var_citados_s');">
				<?php echo("<b>".getTText("um_so",C_NONE)."</b>");?>
			</button>
			<br />
			<br />
			<button onClick="addCitado(true,'var_usuarios_d','var_citados_s');">
				<?php echo("<b>".getTText("todos_esq",C_NONE)."</b>");?>
			</button>
			<br />
			<br />
			<br />
			<br />
			<button onClick="delCitado(false,'var_citados_s');">
				<?php echo("<b>".getTText("um_so_del",C_NONE)."</b>");?>
			</button>
			<br />
			<br />
			<button onClick="delCitado(true,'var_citados_s');">
				<?php echo("<b>".getTText("todos_dir_del",C_NONE)."</b>");?>
			</button>
		<!-- BUTTONS DO MEIO -->
		</td>
			
		<td align="left" valign="top" style="padding:15px 15px 0px 0px;">
			<!-- WHITE BOX DA DIREITA -->
			<?php athBeginFloatingBox("200","220",getTText("citados_s",C_UCWORDS),CL_CORBAR_GLASS_2); ?>
			<form name="formstatic" action="STcitadosexec.php" method="post">
			<input type="hidden" name="var_usuarios_concat" id="var_usuarios_concat" value="" />
			<input type="hidden" name="var_chavereg" id="var_chavereg" value="<?php echo($intCodAgenda);?>" />
			<input type="hidden" name="var_criador_usr" id="var_criador_usr" 
			 value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
			<input type="hidden" name="var_redirect" value="<?php echo($strRedirect);?>" />
			<table cellpadding="0" cellspacing="0" border="0" width="180">
				<tr>
					<td>
						<select multiple="multiple" name="var_citados_s" id="var_citados_s" 
						 style="width:100%;border:1px solid #C9C9C9;" size="15">
						 <?php echo(montaCombo($objConn,"SELECT id_usuario FROM ag_agenda_citado
											   WHERE cod_agenda = ".$intCodAgenda." AND id_usuario <> 
											   '".getsession(CFG_SYSTEM_NAME."_id_usuario")."'",
											   "id_usuario","id_usuario",''));?>
						</select>
					</td>
				</tr>
			</table> 									
			</form>
			<?php athEndFloatingBox();?>
			<!-- WHITE BOX DA DIREITA -->
		</td>
	</tr>

	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" border="0" width="98%">
				<tr><td style="border-top:0px;">&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table cellpadding="0" cellspacing="0" border="0" width="98%">
				<tr><td style="border-top:0px;">&nbsp;</td></tr>
			</table>
		</td>
	</tr>
	
	<!-- LINHA ACIMA DOS BOTÕES -->
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td colspan="3">
						<table cellpadding="0" cellspacing="" border="0" width="95%" align="center">
							<tr>
								<td width="100%" align="right" 
								 style="padding-right:15px;border-top:1px solid #CCC;">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="78%">
						<table cellspacing="0" cellpadding="0" border="0" width="100%">
							<tr>
								<td align="right" width="20%" style="padding-right:8px;"><img src="../img/mensagem_info.gif" /></td>
								<td align="left"  width="80%"><?php echo(getTText("info_inferior_msg",C_NONE));?></td>
							</tr>
						</table>
					</td>
					<!-- goNext() -->
					<td width="10%" align="left">
						<button onClick="selectOptions('var_citados_s');goNext('formstatic');return false;">
							<?php echo(getTText("ok",C_NONE));?>
						</button>
					</td>
					<td width="12%" align="left" style="padding-right:10px;">
						<button onClick="window.close();"><?php echo(getTText("cancelar",C_NONE));?></button>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="3">&nbsp;</td></tr>	
	<!-- LINHA ACIMA DOS BOTÕES -->
</table>
<?php athEndFloatingBox();?>
</center>
</body>
</html>