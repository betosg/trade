<?php
function filtraAlias($prValue) { return(strtolower(preg_replace("/([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+ AS )|([[:alnum:]_\"]+\.)|/i","",$prValue))); }

function ShowCR($prTipo,$prValue) { 
  if ($prValue!="") {
    return("<div style='width:100%; height:auto; margin:5px 0px 10px 0px; border:0px solid #CCCCCC;'>" . $prValue . "</div>"); 
  }
}

function ShowDebugConsuta($prA,$prB) {
  $strAUX  = "<table width='100%' cellpadding='2' cellspacing='2' border='1'>";
  $strAUX .= "<tr><td colspan='2' style='text-align:left; vertical-align:top;'><b>DEBUG</b>&nbsp;(CFG_SYSTEM_DEBUG='true')</small></td></tr>";
  $strAUX .= "<tr><td style='width:50%; text-align:left; vertical-align:top;'>ASL</td><td style='width:50%; text-align:left; vertical-align:top;'>SQL</td></tr><tr>";
  $strAUX .= " <td style='width:50%; text-align:left; vertical-align:top;'>" . str_replace(chr(13),"<br>",htmlspecialchars($prA)) . "</td>";
  $strAUX .= " <td style='width:50%; text-align:left; vertical-align:top;'>" . str_replace(chr(13),"<br>",$prB) . "</td>";
  $strAUX .= "</tr></table>";
  return ($strAUX);
}

function BeginHtmlBuffer () { ob_start(); } //Inicia a captura em buffer (ob_star())
function FlushHtmlBuffer () { return(ob_get_flush()); ob_start(); } //Descarrega o Buffer e reinicia buffering
function EndHtmlBuffer()	{ $auxStr = ob_get_contents(); /*ob_end_clean();*/ return($auxStr); }  //Descarrega e finaliza o buffering

/* INI: SEGURANÇA: Faz verificação se existe usuário logado no sistema --- */
if (getsession(CFG_SYSTEM_NAME . "_id_usuario")=="") {
  mensagem("Acesso Negado", getTText("aviso_semlogonm",C_NONE), getTText("aviso_semlogon_comp",C_NONE),  "javascript:window.close();","standarderro",1);
  die();
}
/* FIM: SEGURANÇA: Faz verificação se existe usuário logado no sistema --- */


// GETTING THE CURRENT DIR - usado na exportação
$strDIR = strtoupper(str_replace("modulo_","",basename(getcwd())));


/* INI: Preparação SQL - ------------------------------------------------ */
//Remove as TAGs - Exemplo: troca <ASLW_EXCLAMACAO> por !  
$strSQL = removeTagSQL($strRelASL); 
//Verifica se há MODIFICADORES ASLW e as coloca num array
preg_match_all("/\[([[:punct:]]?[0-9]*) +([[:alnum:]_\"\(\)\.\+\-\*\/\^' ]+( AS [[:alnum:]_\"]+)*)\]/i",$strSQL,$arrModificadores); 
//pego o SQL PURO, ou seja, SEM os MODIFICADORES, TAGS, etc...
$strSQL = $strRelSQL; 
/* FIM: Preparação SQL - ------------------------------------------------ */
?>