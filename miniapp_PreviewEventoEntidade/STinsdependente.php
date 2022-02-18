<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">-->
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	/***           DEFINIÇÃO DE PARÂMETROS            ***/
	/****************************************************/
	$intCodDado   = request("var_codigo"); 
	$strRedirect = request("var_redirect"); // redirect para qual página deve ir

	// ABERTURA DE CONEXÃO COM BANCO DE DADOS
	$objConn = abreDBConn(CFG_DB);
	
	
	//***    AÇÃO DE PREPARAÇÃO DA GRADE - OPCIONAL    ***/
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

	if(!verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession(basename(getcwd()) . "_chave_app"),"INS","not die")){
		mensagem("err_acesso_titulo","err_acesso_desc","Ação a ser realizada:&nbsp;INS","","erro",1,"not html");
		$strScript  = "";
		$strScript .= "<script type=\"text/javascript\">";
		$strScript .= "/* usado para redimensionar o IFRAME */";
		$strScript .= "resizeIframeParent('" . CFG_SYSTEM_NAME . "_detailiframe_" . $var_cod_cad ."',05)";
		$strScript .=" </script>";
		echo($strScript);die();
	}
		

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
				strLocation = "../miniapp_cadfuncionario_dependente/index.php?var_cod_cadastro=<?php echo $intCodDado; ?>";
				submeterForm();
			}

			function cancelar() {
				document.location.href = "../miniapp_cadfuncionario_dependente/index.php?var_chavereg=<?php echo $intCodDado; ?>";
			}

			function aplicar() {
				strLocation = "../miniapp_cadfuncionario_dependente/STinscontato.php?var_codigo=<?php echo $intCodDado; ?>";
				submeterForm();
			}

			function submeterForm() {
				document.formstatic.DEFAULT_LOCATION.value = strLocation;
				document.formstatic.submit();
			}			
			/****** Funções de ação dos botões - Fim ******/
function validaCPF(){
	var retorno;

	valor = document.getElementById("DBVAR_STR_CPF").value;
	
	valor = valor.replace(".","");
	valor = valor.replace(".","");
	valor = valor.replace("-","");
	valor = valor.replace("/","");
	
	tamanhoPalavra = valor.length;
	
	if (tamanhoPalavra == 11){
		retorno = checkCPF(document.getElementById("DBVAR_STR_CPF").value, false);
	}else{
		if (tamanhoPalavra == 14){
			retorno = checkCNPJ(document.getElementById("DBVAR_STR_CPF").value, false);
		}else{
			alert ("CPF/CNPJ inválido!");
			document.getElementById("DBVAR_STR_CPF").focus();
		}
	}  		
}

		</script>
	</head>
	
	<!-- UTILIZAMOS O BODY ABAIXO QUANDO ESTA PÁGINA NÃO É CHAMADA EM UMA IFRAME DETAIL -->
	<!--<body style="margin:10px 0px 0px 0px;" bgcolor="#FFFFFF" 
	     background="../img/bgFrame_|?php echo(CFG_SYSTEM_THEME);?|_main.jpg">-->
	
<body bgcolor="#FFFFFF" style="margin:10px 0px 0px 0px;">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
	<td align="center">
	<?php athBeginFloatingBox("520","none",getTText("dependente_inserir_title",C_NONE)." (". getTText("inserir",C_NONE).")",CL_CORBAR_GLASS_1); ?>
	<form name="formstatic" action="../_database/athinserttodb.php" method="POST" onSubmit="checkCPF(document.formstatic.DBVAR_STR_CPF.value,true);"  />
        <input type="hidden" name="DEFAULT_TABLE" 			value="cad_funcionario_dependente" />
        <input type="hidden" name="DEFAULT_DB" 				value="<?php echo(CFG_DB);?>" />
        <input type="hidden" name="FIELD_PREFIX" 			value="DBVAR_" />
        <input type="hidden" name="RECORD_KEY_NAME" 		value="COD_DEPENDENTE" />
        <input type="hidden" name="DEFAULT_LOCATION" 		value=""/>
    
        <input type="hidden" name="DBVAR_STR_COD_FUNCIONARIO"   		value="<?php echo $intCodDado;?>" />
    
        <input type="hidden" name="DBVAR_STR_SYS_USR_INS" 	value="<?php echo(getsession(CFG_SYSTEM_NAME."_id_usuario"));?>" />
        <input type="hidden" name="DBVAR_AUTODATE_SYS_DTT_INS" value="false" />
        
        
        <table cellpadding="0" cellspacing="0" border="0" height="100%" width="500" bgcolor="#FFFFFF" style="background-color:#FFFFFF; border:1px solid #CCCCCC;">
            <tr>
                <td align="left" valign="top" style="padding:15px 0px 0px 15px;"><strong><?php echo(getTText("preencha_campos",C_NONE));?></strong></td>
            </tr>
            <tr>
                <td align="left" valign="top" style="padding:10px 50px 0px 50px;">
                    <table cellspacing="2" cellpadding="3" border="0" width="100%">
                        
                        
                        <!-- MIOLO DA TABELA - CORE DA DIALOG ONDE FICAM  OS CAMPOS -->
                        <!-- LINHA DO GRUPO E NOME GRUPO -->
                        <tr bgcolor="#FFFFFF">
                            <td width="27%" align="right">&nbsp;</td>
                            <td width="73%" align="left" class="destaque_gde">
                                <strong><?php echo(getTText("dados_contato_ins",C_TOUPPER));?></strong>						</td>
                        </tr>
                        <tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>
                        <!-- FIM LINE GRUPO -->
                        <!-- CAMPOS: OS CAMPOS SÃO DIVIDOS BASICAMENTE EM UM ROTULO + CAMPO / VALOR POR LINHA -->
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td width="27%" align="right"><strong><?php echo(getTText("cod_ind_pai",C_UCWORDS));?>:</strong></td>
                            <td width="73%" align="left"><?php echo($intCodDado);?><span class="comment_peq"><?php echo(getTText("obs_dependente",C_NONE));?></span></td>
                        </tr>
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td width="27%" align="right"><strong><?php echo(getTText("cpf",C_UCWORDS));?></strong></td>
                            <td width="73%" align="left"><input type="text" name="DBVAR_STR_CPF" id="DBVAR_STR_CPF" maxlength="11" size="15"  onblur='validaCPF();' onkeypress='validateNumKey(this);'></span></td>
                        </tr>
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td width="27%" align="right"><strong><?php echo(getTText("nome_dependente",C_UCWORDS));?>:</strong></td>
                            <td width="73%" align="left"><input name="DBVAR_STR_NOME" size="50" maxlength="100" ></td>                            
                        </tr>						
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td width="27%" align="right"><strong><?php echo(getTText("datanascmt",C_UCWORDS));?></strong></td>
                            <td width="73%" align="left"><input type="text" name="DBVAR_STR_DATA_NASC" maxlength="10" size="15" onkeyup='FormataInputData(this);'onkeypress='validateNumKey(this);'></span></td>
                        </tr>                        
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>">
                            <td width="27%" align="right"><strong><?php echo(getTText("tipo_parentesco",C_UCWORDS));?>:</strong></td>
                            <td width="73%" align="left">
								<select name="DBVAR_STR_COD_PARENTESCO" id="DBVAR_STR_COD_PARENTESCO" style="width:160px;" onChange="ajaxShowCombo(this.value);">
                                    <option value='' selected></option>
                                    <?php echo(montaCombo($objConn, "SELECT cod_parentesco, parentesco FROM cad_parentesco WHERE dt_inativo IS NULL;" ,"cod_parentesco","parentesco"));?>
                                </select>
                            </td>
                        </tr>					
                        <tr bgcolor="<?php echo(getLineColor($strColor));?>"></tr>
                        
                                                                                        
                        <!-- FIM CAMPOS -->
                        <!-- FIM DO CORE DE CAMPOS DA TABELA -->
                    
                        
                        <tr><td colspan="2" class="destaque_med"></td></tr>
                        <tr><td colspan="2" class="linedialog"></td></tr>
                    </table>			
                </td>
            </tr>
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
                                <button onClick="ok();">
                                    <?php echo(getTText("ok",C_UCWORDS));?>
                                </button>
                            </td>
                            <td width="10%" align="left">
                                <button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS));?></button>
                            </td>
                            <td width="10%" align="left">
                                <button onClick="aplicar();">
                                    <?php echo(getTText("aplicar",C_UCWORDS));?>
                                </button>
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
	  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo $intCodDado; ?>',20);
	  // ----------------------------------------------------------------------------------------------------------
	</script>
</html>
<?php $objConn = NULL; ?>