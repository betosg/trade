<?php	
	include_once("../updater_define.php");
	// DEFINIÇÕES DE DIRETORIOS
	$auxStr = $_SERVER["PHP_SELF"];
	$auxStr = explode(DIRECTORY_SEPARATOR,$auxStr);
	
	define("ERROR_TYPE"  ,E_ALL ^ E_NOTICE);	
	define("DIR_SEP"     ,DIRECTORY_SEPARATOR);
	define("DIR_SYSTEM" ,(($strDirSystem == "") ? ((count($auxStr) > 0) ? $auxStr[count($auxStr)-2] : "") : $strDirSystem));
	define("DIR_PROJECT",(($strDirProject == "") ? ((count($auxStr) > 0) ? $auxStr[count($auxStr)-3] : "") : $strDirProject));
	define("PATH_DEST",$_SERVER["DOCUMENT_ROOT"] . DIR_SEP . DIR_PROJECT . DIR_SEP . DIR_SYSTEM . DIR_SEP);
	define("PATH_SRC" ,$_SERVER["DOCUMENT_ROOT"] . DIR_SEP . "@kernelps" . DIR_SEP . "_kernelps" . DIR_SEP);
	
	error_reporting(ERROR_TYPE);
	
    // -------------------------------------------------------------------------------------------------
    // - INIC: Funcões GERAIS --------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------

    // - Layout ----------------------------------------------------------------------------------
	function athBeginWhiteBox($prWidth, $prHeight="", $prTitulo="", $prHeadBGColor="") {
		(strpos($prWidth,"%") !== false) ? $strWidth = intval($prWidth - 1) . "%" : $strWidth = intval($prWidth - 18);
		echo("<div id='DialogWhite' class='bordaBox' style='width:" . $prWidth . "; height:" . $prHeight . ";'>
				<div class='b1'></div><div class='b2'></div><div class='b3'></div><div class='b4'></div>
				<div class='center'><div id='Conteudo' class='conteudo' style='width:" . $strWidth . ";  height:" . $prHeight . ";'>");
		if($prTitulo != "" && $prHeadBGColor != ""){
				echo("<div id='WhiteHeader' class='header' style='background-color:" . $prHeadBGColor . ";width:" . $strWidth . "px;'>" . $prTitulo . "</div>");
		}  
	}

	function athEndWhiteBox(){ echo("</div></div><div class='b4'></div><div class='b3'></div><div class='b2'></div><div class='b1'></div></div>");	}

	function mensagem($prTitulo, $prAviso, $prAdText="", $prButton="", $prForm="") {
	  $strTitulo = $prTitulo;
	  $strAviso  = $prAviso;
	  $arrArquivoVersao = buscaArquivos(PATH_SRC . "_fontes","[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\.html");
	  if($prTitulo == 'AVISO') {
		$strImg = '..' . DIR_SEP . DIR_SYSTEM . DIR_SEP . 'img' . DIR_SEP . 'mensagem_aviso.gif';
	  } else if($prTitulo == 'SUCESSO') {
		$strImg = '..' . DIR_SEP . DIR_SYSTEM . DIR_SEP . 'img' . DIR_SEP . 'mensagem_info.gif';
	  } else if($prTitulo == 'ERRO') {
		$strImg = '..' . DIR_SEP . DIR_SYSTEM . DIR_SEP . 'img' . DIR_SEP . 'mensagem_erro.gif';
	  }
	  echo("<center>");
	  athBeginWhiteBox("450");
	  echo("<form method='POST' name='frmUpdater'><input type='hidden' name='var_acao' value='copiar' /><table align='center' border='0' cellpadding='5' cellspacing='0' width='100%'>
			  <tr><td valign='top' width='1%'><img src='".$strImg."' hspace='5'></td>
				<td width='99%'><table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr><td colspan='2' style='font-size:14px;padding-left:5px;padding-bottom:5px;'><b>" . $strTitulo . "</b></td></tr>
					<tr><td colspan='2' class='padrao_gde' style='padding:10px 0px 10px 5px;'><b>" . $strAviso . "</b></td></tr>");
				 if($prForm != ""){	
					echo("
					<tr><td  style='font-size:14px;padding-left:5px;padding-bottom:5px;' align='right'><b>Versão:</b></td>
						<td style='font-size:14px;padding-left:5px;padding-bottom:5px;'>".str_replace(".html"," ",$arrArquivoVersao[0])."</td>
					</tr>
					<tr>
						<td  style='font-size:14px;padding-left:5px;padding-bottom:5px;' align='right'><b>Senha:</b></td>
						<td style='font-size:14px;padding-left:5px;padding-bottom:5px;'><input type='password' name='var_password'></td>
					</tr>
					<tr>
						<td style='font-size:14px;padding-left:5px;padding-bottom:5px;' align='right'><b>Projeto:</b></td>
						<td style='font-size:14px;padding-left:5px;padding-bottom:5px;'><input type='text' name='var_projeto' value='_kernelps'></td>
					</tr>");
				}
				echo("<tr><td colspan='2' height='1' bgcolor='#BDBDBF'></td></tr>
					<tr><td colspan='2' style='padding:10px 0px 10px 5px;'>" . $prAdText . "</td></tr>
					<tr><td colspan='2' align='right' class='comment_peq'></td></tr>
				</table></td></tr>");
	  if($prButton != ""){ echo("<tr><td align='right' colspan='2'><button onClick='document.frmUpdater.submit()'>COPIAR</button></td></tr>"); }  
	  echo("</table></form>");
	  athEndWhiteBox();
	  echo("</center><br>");
	}
	
	function buscaArquivos($dir,$filtro){
		
		$openDir = opendir($dir);
		while($arq = readdir($openDir)){
			//pega somente os arquivos com extensao .php
			$filtro = $filtro.'$';
			if(ereg($filtro,$arq) && $arq != '.' && $arq != '..'){
				$arrayArquivos[] = $arq;
			}
		}
		
		if(isset($arrayArquivos)) {
			sort($arrayArquivos,SORT_STRING); // Coloca os arquivos em ordem alfabetica
		}
		
		return($arrayArquivos);
	}
	
	function buscaModulos($dir, $filtro="")	{
		$arrPastas1 = array();
		$arrPastas2 = array();
		$diraberto = opendir($dir); // Abre o diretorio especificado
		chdir($dir); // Muda o diretorio atual p/ o especificado
		while($arq = readdir($diraberto)) { // Le o conteudo do arquivo
			if($arq == ".." || $arq == ".")continue; // Desconsidera os diretorios
			if (is_dir($arq)) {
				$arr_ext = explode(";",$filtro);
				foreach($arr_ext as $ext) {
					$extpos = stripos($arq, $ext);
					if ($extpos !== false) $arrPastas1[] = $arq;
				}
				//$arrPastas2 = buscaModulos($arq, $filtro); // Executa a funcao novamente se subdiretorio
			}
		}
		//chdir(".." . DIR_SEP); // Volta um diretorio
		closedir($diraberto); // Fecha o diretorio atual
		
		if(isset($arrPastas1)) {
			sort($arrPastas1,SORT_STRING); // Coloca os diretorios em ordem alfabetica
		}
		
		return $arrPastas1;
	}
	
    // -------------------------------------------------------------------------------------------------
    // - FIM: Funcões GERAIS ---------------------------------------------------------------------------
    // -------------------------------------------------------------------------------------------------
?>
<head>
	<title>KernelPS - UPDATE</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<style type="text/css">
		body       { font-family:tahoma; font-size:10px; color:#111111; background-repeat:no-repeat; background-attachment:fixed;}
		button     { font-family:arial; font-size:11px; color:#000000; background-image:url(../../tradeunion/_tradeunion/img/But_XPSilver_clean.gif); width:85px; height:21px; border:0px; margin:0px 6px 0px 6px; }
		div	       { font-family:tahoma; font-size:10px; color:#111111; }
		td	       { font-family:tahoma; font-size:10px; color:#111111; }
		td a	   { text-decoration:none;  color:#111111; }
		td a:hover { text-decoration:none;  color:#999999; }
		div#DialogWhite.bordaBox { background: transparent; }
		div#DialogWhite.bordaBox .b1, div#DialogWhite.bordaBox .b2, div#DialogWhite.bordaBox .b3, div#DialogWhite.bordaBox .b4 {display:block; overflow:hidden; font-size:1px;}
		div#DialogWhite.bordaBox .b1, div#DialogWhite.bordaBox .b2, div#DialogWhite.bordaBox .b3 {height:1px;}
		div#DialogWhite.bordaBox .b2, div#DialogWhite.bordaBox .b3, div#DialogWhite.bordaBox .b4 {background:#E0DFE3; border-left:1px solid #C9C9C9; border-right:1px solid #C9C9C9;}
		div#DialogWhite.bordaBox .b1 {margin:0 5px; background:#C9C9C9;}
		div#DialogWhite.bordaBox .b2 {margin:0 3px; border-width:0 2px;}
		div#DialogWhite.bordaBox .b3 {margin:0 2px;}
		div#DialogWhite.bordaBox .b4 {height:2px; margin:0 1px;}
		div#DialogWhite.bordaBox .center {padding:4px 8px 4px 8px; background:#E0DFE3; border-left:1px solid #C9C9C9; border-right:1px solid #C9C9C9; }
		div#DialogWhite.bordaBox .conteudo { position:static; color:#111111; text-align:left; }
		div#DialogWhite.bordabox .conteudo .header { padding:4px; font-size:11px; margin-bottom:10px; text-align:left;}
	</style>
</head>
<html>
<body>
  <?php
	if ($_POST['var_acao'] != "copiar") {
		mensagem("AVISO","Você esta prestes a atualizar os FONTES dos módulos deste sistema.<br><br>" .
		         "Todos os módulos (modulo_ e mform_) do sistema serão atualizadso a partir da <br> " .
				 "pasta <i>_fontes</i> do KernelPS, exceto os módulos abaixo que foram marcados no " . 
				 "UPDATER_DEFINE.PHP para não serem atualizados.<br><br>" .
				 "[ <br>" . implode("<br>", $arrExcecao) . "<br>]<br><br>" .
		         "* Demais pastas TAMBÉM serão atualizadas:<br>" .
				 "<li>_ajax</li><br>" .
				 "<li>_scripts - exceto o arquivo STscripts.js</li><br>" .
				 "<li>_database - exceto os arquivos STathutils.php e STconfiginc.php</li><br>"
		        ,"Atenção! Se você não seja atualizar o sistema, apenas fecha esta mensagem!!!"
				,"ok",1);
	}
	else {
		if($_POST['var_password'] == "athbbsi".date("dm") && $_POST['var_projeto'] == DIR_SYSTEM){ 

			//Array com as pastas que devem ser verificadas para atualização
			$arrModulos = buscaModulos("." . DIR_SEP,"modulo_");
			$arrFormMod = buscaModulos("." . DIR_SEP,"mform_");
			$arrModulos = array_merge ($arrModulos, $arrFormMod);
			chdir(".." . DIR_SEP); // Volta um diretorio
			
			//Array com os arquivos que devem ser copiados
			$arrArquivos = buscaArquivos(PATH_SRC . "_fontes",".php");

			//DEBUG: print_r ($arrModulos);	echo("<br><br>"); print_r ($arrFormMod); die();
			
			//atualiza os modulos com os arquivos da pasta _fontes do kernelps
			foreach($arrModulos as $modulo){
				if(!in_array($modulo,$arrExcecao)){
					foreach($arrArquivos as $arquivo){
						copy(PATH_SRC . "_fontes" . DIR_SEP . $arquivo, PATH_DEST . $modulo . DIR_SEP . $arquivo);
					}
					$arrModulosAtualizados[] = $modulo;
				}
			}
			//atualiza os _scripts/
			$arrArquivosScript = buscaArquivos(PATH_SRC . "_scripts",".js");
			foreach($arrArquivosScript as $arquivoScript){
				if($arquivoScript != "STscripts.js"){
					copy(PATH_SRC . "_scripts" . DIR_SEP . $arquivoScript, PATH_DEST . "_scripts" . DIR_SEP . $arquivoScript);
					$arrArquivosScriptAtualizados[] =  $arquivoScript;
				}
			}

			//atualiza os arquivos do _ajax/
			$arrArquivosAjax = buscaArquivos(PATH_SRC . "_ajax",".php");
			foreach($arrArquivosAjax as $arquivoAjax){
				copy(PATH_SRC . "_ajax" . DIR_SEP . $arquivoAjax, PATH_DEST . "_ajax" . DIR_SEP . $arquivoAjax);
				$arrArquivosAjaxAtualizados[] = $arquivoAjax;
			}
			
			//atualiza os _database/
			$arrArquivosDB = buscaArquivos(PATH_SRC . "_database",".php");
			foreach($arrArquivosDB as $arquivoDB){
				if ( ($arquivoDB != "STconfiginc.php") and ($arquivoDB != "STathutils.php") ){
					copy(PATH_SRC . "_database" . DIR_SEP . $arquivoDB, PATH_DEST . "_database" . DIR_SEP . $arquivoDB);
					$arrArquivosDBAtualizados[] = $arquivoDB;
				}
			}
			

			//colocar a versão no sistema
			$arrArquivoVersao = buscaArquivos(PATH_SRC . "_fontes",".html");
			copy(PATH_SRC . "_fontes" . DIR_SEP . $arrArquivoVersao[0],$arrArquivoVersao[0]);
			

			//6
			$strModulosAtualizados = implode("<br/>",$arrModulosAtualizados);
			$strModulosExcecao     = implode("<br/>",$arrExcecao);
			$strArquivosScript     = implode("<br/>",$arrArquivosScriptAtualizados);
			$strArquivosAjax       = implode("<br/>",$arrArquivosAjaxAtualizados);
			$strArquivosDB         = implode("<br/>",$arrArquivosDBAtualizados);
			mensagem("SUCESSO","OS FONTES FORAM ATUALIZADOS COM SUCESSO para a versão <span style='font-size:12px'>'".str_replace(".html","",$arrArquivoVersao[0])."'</span> nos módulos deste sistema: [ ]"
					,"<b>_scripts/ :</b><br>".$strArquivosScript."<br/><br/><b>_ajax/ :</b><br/>".$strArquivosAjax."<br/><br/><b>_database/ :</b><br/>".$strArquivosDB." <br/><br/><b>Módulos atualizados(".count($arrModulosAtualizados)."):</b><br />".$strModulosAtualizados."<hr width='100%' /><br/><b>Módulos em Exceção(".count($arrExcecao)."):</b><br />".$strModulosExcecao
					,"");
		}else{
			mensagem("ERRO","Você informou senha/projeto invalido. Corrija-os e tente novamente."
		        ,"Atenção! Se você não deseja atualizar o sistema, apenas fecha esta aba/janela!!!"
				,"ok",1);
		}
	}
  ?>

</body>
</htmL>
