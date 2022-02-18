<?php

function callUploader(prFormName, prFieldName, prDir){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}







									("<input type=\"text\" name=\"" . $strComponente . "\" id=\"" . $strComponente . "_" . getValue($objRS,"dlg_grp") . "\" value=\"" . $strValor . "\" size=\"50\" readonly=\"true\" title=\"" . getTText($strNomeField,C_NONE) . "\">");
									
									
									echo("<input type=\"button\" name=\"btn_uploader\" value=\"Upload\" class=\"inputclean\" onClick=\"callUploader('formeditor_" . getValue($objRS,"dlg_grp") . "','" . $strComponente ."','\\\\" . str_replace("\\","\\\\",replaceParametersSession(getValue($objRS,"file_dir_arquivos"))) . "\\\\');\" tabindex=\"" . $intI . "\">");

?>
