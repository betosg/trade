<?php

$strLang = CFG_LANG;
$strBaseName = request("var_basename");                    // basename indica a pasta da aplicaзгo - nome completo: Ex. "modulo_CadPJ"
if ($strBaseName == "") $strBaseName = basename(getcwd()); // Para as STs que nгo recebem "var_basename"

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

//09/2012 - LANG passou a ser ЪNICO na pasta '_lang/' para todo o projetio
//$strDirTrans = substr($strBaseName,strrpos($strBaseName,DIRECTORY_SEPARATOR)+1);
//$objLang = new phpMultiLang("../" . $strDirTrans . "/lang","../" . $strDirTrans . "/lang");

//Arquivo de LANG deve conter a sigla do idioma ("ptb") e a extensгo ".lang"
//O sistema busca primeiro por um arquivo que tenha o [idioma]_[nomeCliente]_[nomeMуdulo].lang
//se encontrar ele abre este para trabalhar no mуdulo de forma individual, se nгo achar ele 
//procura por [idioma]_[nomeMуdulo].lang, se nгo achar ele abre o geral chamado [idioma].lang

//if ($strBaseName == "") echo ("Nгo foi possнvel identificar o mуdulo");

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