<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodMensagem	= request("var_chavereg");		// cod_mensagem
	$strLocation    = request("var_location");
	$strPopulate 	= "yes";
	
	// if($strPopulate  == "yes") { initModuloParams(basename(getcwd())); } //Popula o session
	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	// abre objeto para manipulação com o banco
	$objConn	 = abreDBConn(CFG_DB);
	$strUsuarios = "";
	
	// Busca dados da Mensagem
	try{
		$strSQL = "
			SELECT
				  msg_mensagem.cod_mensagem
				, msg_mensagem.assunto
				, msg_mensagem.mensagem
				, msg_mensagem.dtt_envio
				, msg_mensagem.remetente
				, msg_destino.dtt_lido
				, msg_pasta.nome_pasta
			FROM  msg_mensagem
			INNER JOIN msg_destino ON (msg_destino.cod_mensagem = msg_mensagem.cod_mensagem)
			LEFT  JOIN msg_pasta   ON (msg_pasta.cod_pasta = msg_destino.cod_pasta)
			WHERE
				msg_mensagem.cod_mensagem = ".$intCodMensagem;
		$objResult = $objConn->query($strSQL);
		$objRS     = $objResult->fetch();
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// Localiza Múltiplos DESTINATÁRIOS DA MENSAGEM
	try{
		$strSQL    = "SELECT msg_destino.id_usuario FROM msg_destino WHERE msg_destino.cod_mensagem = ".$intCodMensagem;
		$objResult = $objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	// Concatena os usuários encaminhados
	foreach($objResult as $objRSUsrs){ $strUsuarios .= getValue($objRSUsrs,"id_usuario").";"; }
	
	
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
		<title><?php echo(CFG_SYSTEM_TITLE);?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link rel="stylesheet" href="../_css/<?php echo(CFG_SYSTEM_NAME);?>.css" type="text/css">
		<link href="../_css/tablesort.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../_scripts/tablesort.js"></script>
		<style type="text/css">
			.msg_orig    { color:#000000; font-weight:normal; padding-bottom:8px; }
			.msg_label   { color:#000000; font-weight:bold;   }
			.msg_content { color:#000000; font-weight:normal; padding-left:4px; }
		</style>
		<style media="print" type="text/css">
			.noprint{ display:none; }
		</style>
		<script type="text/javascript">
			/*** Funções JS ***/
			function marcarLida(){
				if(window.confirm("<?php echo(getTText("deseja_marcar_como_lida_qm",C_NONE));?>")){
					document.location.href = "../modulo_Mensagem/STmarcarmensagemlida.php?var_chavereg=<?php echo($intCodMensagem);?>";
				} else{
					return(null);
				}
			}
		</script>
	</head>
<body bgcolor="#EFEFEF" style="margin:10px 10px 10px 10px;">
	<!-- USO -->
	<center>
	<?php athBeginWhiteBox("100%","","<span style='float:right;background-color:#FFF;padding:3px;font-size:9px;font-weight:bold;cursor:pointer;' onclick='marcarLida();'>".getTText("marcar_lida",C_NONE)."</span><span style='float:right;background-color:#FFF;padding:3px;margin-right:5px;font-size:9px;font-weight:bold;cursor:pointer;' onclick='window.print();'>".getTText("imprimir",C_NONE)."</span>",CL_CORBAR_GLASS_2); ?>
		<blockquote dir="ltr" style="padding-right:0px;  padding-left:5px;  border-left:#000000 2px solid;  margin-right:0px; ">
			<div  id="msg_original" class="msg_orig" ><?php echo(getTText("msg_orig",C_NONE));?></div>
			<span id="msg_de"       class="msg_label"><?php echo(getTText("na_pasta",C_NONE));?>:</span><span id="cnt_de" class="msg_content"><?php echo(getValue($objRS,"nome_pasta"));?></span><br />
			<span id="msg_de"       class="msg_label"><?php echo(getTText("de",C_NONE));?>:</span><span id="cnt_de" class="msg_content"><?php echo(getValue($objRS,"remetente"));?></span><br />
			<span id="msg_para"     class="msg_label"><?php echo(getTText("para",C_NONE));?>:</span><span id="cnt_para" class="msg_content"><?php echo($strUsuarios);?></span><br />
			<span id="msg_enviado"  class="msg_label"><?php echo(getTText("enviado",C_NONE));?>:</span><span id="cnt_enviado" class="msg_content"><?php echo(dDate(CFG_LANG,getValue($objRS,"dtt_envio"),true));?></span><br />
			<span id="msg_assunto"  class="msg_label"><?php echo(getTText("assunto",C_NONE));?>:</span><span id="cnt_assunto" class="msg_content"><?php echo(getValue($objRS,"assunto"));?></span><br />
			
			<br />
			<br />
			
			<span id="msg_mensagem" class="msg_content"><?php echo(getValue($objRS,"mensagem"));?></span>
		</blockquote>
	<?php athEndWhiteBox();?>
	<br />
	<div class="noprint">
	<?php //include_once("../modulo_Mensagem/STmensagensintranet.php"); ?>
	</div>
	</center>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>