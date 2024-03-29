<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	/***           DEFINI��O DE PAR�METROS            ***/
	/****************************************************/
	$var_cod_cad     = request("var_cod_cad");  // CHAVE PAI P/ RESIZE
	$intcodigo       = request("var_cod_dado"); 
	$strRedirect 	 = request("var_redirect"); // redirect para qual p�gina deve ir
	
	
	// ABERTURA DE CONEX�O COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	// SQL PADR�O DA LISTAGEM - BREVE DESCRI��O
	try{
		// seleciona todos os contatos do Industrial
		// com cod_cadastro enviado para este script
		$strSQL = "	select t2.cod_area_cobertura as codigo, t2.cod_municipio, t4.cod_estado, t2.cod_pj, t2.coord_gps
					FROM relac_area_cobertura t2 
					INNER JOIN lc_municipio t3 on t2.cod_municipio = t3.cod_municipio
					INNER JOIN lc_estado t4 ON t3.cod_estado = t4.cod_estado
				WHERE t2.cod_area_cobertura = " . $intcodigo ;			
				
				
					
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	
	/***    A��O DE PREPARA��O DA GRADE - OPCIONAL    ***/
	/****************************************************/
	// Controle de acesso diferenciado por estar em n�vel IFRAME.
	// caso sua p�gina n�o esteja em um IFRAME DETAIL, utilize a-
	// penas a linha abaixo:
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"UPD");
	// no caso abaixo, fazemos a verficarAcesso() retornar um valor
	// false caso o usu�rio nao tenha direito sobre a app, e com base
	// no true ou false manipulamos a mensagem para que funcione no
	// IFRAME DETAIL. Ainda assim, posicionamos esse trecho de codigo
	// aqui pq anteriormente n�o t�nhamos o cod_fornec para que seja
	// feito resize do iframe.

//	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"UPD","not die")){
//		mensagem("err_acesso_titulo","err_acesso_desc","A��o a ser realizada:&nbsp;UPD","","erro",1,"not html");
//		$strScript  = "";
//		$strScript .= "<script type=\"text/javascript\">";
//		$strScript .= "/* usado para redimensionar o IFRAME ";
//		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
//		$strScript .=" <script>";
//		echo($strScript);die();
//	}
	
		

	/***         FUN��ES AUXILIARES - OPCIONAL        ***/
	/****************************************************/
	$strColor = CL_CORLINHA_2; 				// inicializa variavel para pintar linha
	function getLineColor(&$prColor){ 	// fun��o para cores de linhas
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
			/* suas adapta��es css aqui */
		</style>
		<script language="javascript" type="text/javascript">
			/* seu c�digo javascript aqui */
			/****** Fun��es de a��o dos bot�es - In�cio ******/
			var strLocation = null;
			
			function ok() {
				//strLocation = "../miniapp_cadpfFornec/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
				submeterForm();
			}

			function cancelar() {
				window.location = "../miniapp_relacAreaCobertura/index.php?var_chavereg=<?php echo $var_cod_cad;?>";
			}
			
			
			function submeterForm() {
				document.formstatic.submit();
			}
			
				
			
			
			
			/****** Fun��es de a��o dos bot�es - Fim ******/
		</script>
	</head>
	
	<!-- UTILIZAMOS O BODY ABAIXO QUANDO ESTA P�GINA N�O � CHAMADA EM UMA IFRAME DETAIL -->
	<!--<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" 
	     background="../img/bgFrame_|?php echo(CFG_SYSTEM_THEME);?|_main.jpg">-->
	
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none","Atualizar",CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="STupdAreaCoberturaExec.php" method="POST" />
	
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
							<strong>�rea de Cobertura</strong></td>
					</tr>
					<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
					<!-- FIM LINE GRUPO -->
					<!-- CAMPOS: OS CAMPOS S�O DIVIDOS BASICAMENTE EM UM ROTULO + CAMPO / VALOR POR LINHA -->
				
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>C�d:</strong></td>
						<td width="73%" align="left">
							<?php echo(getValue($objRS,"codigo"));?>
							<input type="hidden" name="dbvar_codigo_area_cobertura" id="dbvar_codigo_area_cobertura" value="<?php echo(getValue($objRS,"codigo"))?>"style="width:200px;" maxlength="99" />							
							<input type="hidden" name="codigo_pai" id="codigo_pai" style="width:200px;" maxlength="99" value="<?php echo(getValue($objRS,"cod_pj"))?>"/>							
					</tr>
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>UF:</strong></td>
						<td width="73%" align="left">
							<select onchange="listaMunicipio(this.value);" id="uf" name="cod_uf">
							<option value="">Selecione</option>
							<?php  echo (montaCombo($objConn, "SELECT nome as uf, cod_estado from lc_estado order by nome", "cod_estado", "uf", getValue($objRS,"cod_estado"), ""));?>
						</td>
					</tr>					
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>Munic�pio:</strong></td>
						<td width="73%" align="left"> 
						<select id="municipio" name="cod_municipio">
							<option value="">Selecione</option>							
						</td>
					</tr>	
					<tr bgcolor="<?php echo(getLineColor($strColor));?>">
						<td width="27%" align="right"><strong>Coord. GPS:</strong></td>
						<td width="73%" align="left">
                            <input type="text" name="DBVAR_STR_COORD_GPS" id="DBVAR_STR_COORD_GPS" style="width:200px;" maxlength="255" value="<?php echo(getValue($objRS,"coord_gps"))?>"/>							
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
							<!-- MENSAGEM DE AVISO VAI AQUI, PARA DIALOG DE DELE��O -->
							<!-- CASO VOC� QUEIRA INFORMAR UMA MENSAGEM, ALTERE O ICONE
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
							<button onClick="ok();" id="botaoOK" name="botaoOK">
								ok
							</button>
						</td>
						<td width="10%" align="left">
							<button onClick="cancelar();return false;">cancelar</button>
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
		<!-- LINHA ACIMA DOS BOT�ES -->
	</table>
	</form>
	<?php athEndFloatingBox();?>
	</td>
	</tr>
</table>
</body>
	<script type="text/javascript">


function listaMunicipio(prCodEstado, prCodMunicipio=""){
				//alert(prCodEstado + " muni: "+ prCodMunicipio)
				prSQL = "SELECT * from lc_municipio where cod_estado = "+prCodEstado;
				
				var xhttp = new XMLHttpRequest();
				xhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						
						objJson = JSON.parse(this.responseText)     
						
						var select = document.getElementById("municipio");
						var length = select.options.length;
						for (i = 0; i < length; i++) {
							select.remove(select.i);
						}

						for (var key in objJson) {
						
						if (objJson.hasOwnProperty(key)) {
							//console.log(objJson[key].nome + ' ' + objJson[key].cod_municipio);
							var option = document.createElement("option");
							option.text = objJson[key].nome;
							option.value = objJson[key].cod_municipio;
							if(option.value == prCodMunicipio){
								option.selected = true;
							}							
							select.appendChild(option);
							}        
						}
					}
				};
				xhttp.open("GET", "../_ajax/STreturndadosMunicipio.php?var_sql=" + prSQL, true);
				xhttp.send();
			
			}

		listaMunicipio(<?php echo(getValue($objRS,"cod_estado"));?>,<?php echo(getValue($objRS,"cod_municipio"));?>)

	  // Quando esta p�gina for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo $var_cod_cad; ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>