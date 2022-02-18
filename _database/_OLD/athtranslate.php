<?php

$strLang = CFG_LANG;

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

$strDirTrans = substr(getcwd(),strrpos(getcwd(),DIRECTORY_SEPARATOR)+1);
 
$objLang = new phpMultiLang("../" . $strDirTrans . "/lang","../" . $strDirTrans . "/lang");
 
$objLang->AssignLanguage($strLang,NULL,array("LC_ALL",$strLocale));
$objLang->AssignLanguageSource($strLang,$strLang . ".lang",3600);
 
$objLang->SetLanguage($strLang,false);
 
?>