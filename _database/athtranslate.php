<?php

$strLang = CFG_LANG;
$strBaseName = request("var_basename");                    // basename indica a pasta da aplica��o - nome completo: Ex. "modulo_CadPJ"
if ($strBaseName == "") $strBaseName = basename(getcwd()); // Para as STs que n�o recebem "var_basename"

if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
    $strLocale = $strLang;
}
else {
    switch($strLang){
        case "ptb": $strLocale = "pt_BR"; break;
        case "en":  $strLocale = "en_US"; break;
        case "es":  $strLocale = "es_ES"; break;
    }
}

//09/2012 - LANG passou a ser �NICO na pasta '_lang/' para todo o projetio
//$strDirTrans = substr($strBaseName,strrpos($strBaseName,DIRECTORY_SEPARATOR)+1);
//$objLang = new phpMultiLang("../" . $strDirTrans . "/lang","../" . $strDirTrans . "/lang");

//Arquivo de LANG deve conter a sigla do idioma ("ptb") e a extens�o ".lang"
//O sistema busca primeiro por um arquivo que tenha o [idioma]_[nomeCliente]_[nomeM�dulo].lang
//se encontrar ele abre este para trabalhar no m�dulo de forma individual, se n�o achar ele 
//procura por [idioma]_[nomeM�dulo].lang, se n�o achar ele abre o geral chamado [idioma].lang

//if ($strBaseName == "") echo ("N�o foi poss�vel identificar o m�dulo");

$strArqIdioma = $strLang."_".getsession(CFG_SYSTEM_NAME . "_dir_cliente")."_".$strBaseName.".lang";

//echo($strArqIdioma);
if (!file_exists("../_lang/".$strArqIdioma)) {
	$strArqIdioma = $strLang."_".$strBaseName.".lang";
	if (!file_exists("../_lang/".$strArqIdioma)) {
		$strArqIdioma = $strLang.".lang";
	}
}
//echo("<br>".$strArqIdioma);
//die();

$objLang = new phpMultiLang("../_lang/","../_lang/");

$objLang->AssignLanguage($strLang,NULL,array("LC_ALL",$strLocale));
$objLang->AssignLanguageSource($strLang,$strArqIdioma,3600);

$objLang->SetLanguage($strLang,false);

?>