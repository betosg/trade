<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$var_cod_cad     = request("var_cod_cad");  // CHAVE PAI P/ RESIZE
	$intcodigo       = request("var_cod_dado"); 
	$strRedirect 	 = request("var_redirect"); // redirect para qual página deve ir
	$strCodespecialidade   = request("var_cod_especialidade");
	
	// ABERTURA DE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	// SQL PADRÃO DA LISTAGEM - BREVE DESCRIÇÃO
/*	try{
		// seleciona todos os contatos do Industrial
		// com cod_cadastro enviado para este script
		$strSQL = "	select t2.cod_marca as codigo, t2.marca, t2.cod_pj
					FROM cad_pj_marcas t2 					
				WHERE t2.cod_marca = " . $intcodigo ;	
				
				
		$strSQL = "	select t1.cod_pf_especialidade as codigo, t2.nome
				from cad_pf_especialidade t1
				inner join cad_especialidade t2 on t2.cod_especialidade = t1.cod_especialidade
				where t1.cod_pf = " . $intCodCadastro . "
				order by t2.nome";
				
				
					
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	 */
	
	/***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
	/****************************************************/
	// Controle de acesso diferenciado por estar em nível IFRAME.
	// caso sua página não esteja em um IFRAME DETAIL, utilize a-
	// penas a linha abaixo:
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");
	// no caso abaixo, fazemos a verficarAcesso() retornar um valor
	// false caso o usuário nao tenha direito sobre a app, e com base
	// no true ou false manipulamos a mensagem para que funcione no
	// IFRAME DETAIL. Ainda assim, posicionamos esse trecho de codigo
	// aqui pq anteriormente não tínhamos o cod_fornec para que seja
	// feito resize do iframe.

//	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"UPD","not die")){
//		mensagem("err_acesso_titulo","err_acesso_desc","Ação a ser realizada:&nbsp;UPD","","erro",1,"not html");
//		$strScript  = "";
//		$strScript .= "<script type=\"text/javascript\">";
//		$strScript .= "/* usado para redimensionar o IFRAME ";
//		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
//		$strScript .=" <script>";
//		echo($strScript);die();
//	}
	
		

	/***         FUNÇÕES AUXILIARES - OPCIONAL        ***/
	/****************************************************/
	$strColor = CL_CORLINHA_2; 				// inicializa variavel para pintar linha
	function getLineColor(&$prColor){ 	// função para cores de linhas
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
	
?>
<html>
	<head>
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			/* suas adaptações css aqui */
		</style>
		<script language="javascript" type="text/javascript">
			/* seu código javascript aqui */
			/****** Funções de ação dos botões - Início ******/
			var strLocation = null;
			
			function ok() {
				//strLocation = "../miniapp_cadpfFornec/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
				submeterForm();
			}

			function cancelar() {
				window.location = "../miniapp_cadPfEspecialidade/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
			}
			
			
			function submeterForm() {
				document.formstatic.submit();
			}
			
				
			
			
			
			/****** Funções de ação dos botões - Fim ******/
		</script>
	</head>
	
	<!-- UTILIZAMOS O BODY ABAIXO QUANDO ESTA PÁGINA NÃO É CHAMADA EM UMA IFRAME DETAIL -->
	<!--<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" 
	     background="../img/bgFrame_|?php echo(CFG_SYSTEM_THEME);?|_main.jpg">-->
	
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none","Atualizar",CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="STupdespecialidadeExec.php" method="POST" />
	
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="500" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
		<tr>
			<td align="left" valign="top" style="padding:15px 0px 0px 15px;">
				<strong>Preencha os campos</strong>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" style="padding:10px 50px 0px 50px;">
				<table cellspacing="2" cellpadding="3" border="0" width="100%">
						
					
					<!-- MIOLO DA TABELA - CORE DA DIALOG ONDE FICAM  OS CAMPOS -->
					<!-- LINHA DO GRUPO E NOME GRUPO -->
					<tr bgcolor="#FFFFFF">
						<td width="27%" align="right">&nbsp;</td>
						<td width="73%" align="left" class="destaque_gde">
							<strong>Especialidade</strong></td>
					</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<!-- FIM LINE GRUPO -->
					<!-- CAMPOS: OS CAMPOS SÃO DIVIDOS BASICAMENTE EM UM ROTULO + CAMPO / VALOR POR LINHA -->
				
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>Cód:</strong></td>
						<td width="73%" align="left">
							<?php echo $intcodigo;?>
							<input type="hidden" name="dbvar_cod_especialidade" id="dbvar_cod_especialidade" value="<?php echo($intcodigo)?>"style="width:200px;" maxlength="99" />							
							<input type="hidden" name="codigo_pai" id="codigo_pai" style="width:200px;" maxlength="99" value="<?php echo($var_cod_cad);?>"/>							
					</tr>
					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>Especialidade</strong></td>
						<td width="73%" align="left">
							<select id="DBVAR_STR_especialidade" name="DBVAR_STR_especialidade">
								<option value="">Selecione...</option>
							<?php  
								$strSQL = "select cod_especialidade,nome
								from cad_especialidade";
								echo (montaCombo($objConn  , $strSQL, "cod_especialidade", "nome"  , $strCodespecialidade, ""));
								
							?>
							</select>
					</tr>			
				</table>
			</td>					
		</tr>																		
        <!-- FIM CAMPOS -->
        <!-- FIM DO CORE DE CAMPOS DA TABELA -->
        <tr><td colspan="2" class="destaque_med"></td></tr>
        <tr><td colspan="2" class="linedialog"></td></tr>										
		<!-- LINHA DOS BUTTONS E AVISO -->
		<tr>
			<td colspan="3" style="padding:10px 50px 0px 50px;">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td width="70%">
							<!-- MENSAGEM DE AVISO VAI AQUI, PARA DIALOG DE DELEÇÃO -->
							<!-- CASO VOCÊ QUEIRA INFORMAR UMA MENSAGEM, ALTERE O ICONE
							 	 E O LANG UTILIZADO PARA A MENSAGEM
							<table cellspacing="0" cellpadding="0" border="0" width="100%">
								<tr>
									<td align="right" width="23%">
										<img src="../img/mensagem_aviso.gif" />
									</td>
									<td align="left"  width="77%">|?php echo(getTText("aviso_del_resp_txt",C_NONE));?|</td>
								</tr>
							</table>
							<!-- BLOCO PARA MENSAGEM . FIM -->
						</td>
						<!-- goNext() -->
						<td width="10%" align="left">
							<button class="inputcleanActionOk" onClick="ok();" id="botaoOK" name="botaoOK">
								ok
							</button>
						</td>
						<td width="10%" align="left">
							<button class="inputcleanActionCancelar" onClick="cancelar();return false;">cancelar</button>
						</td>
						<td width="10%" align="left">
							<?php /*?><button onClick="aplicar();">
								<?php echo(getTText("aplicar",C_UCWORDS));?>
							</button><?php */?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>	
		<!-- LINHA ACIMA DOS BOTÕES -->
	</table>
	</form>
	<?php athEndFloatingBox();?>
	</td>
	</tr>
</table>
</body>
	<script type="text/javascript">




	  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo $var_cod_cad; ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>