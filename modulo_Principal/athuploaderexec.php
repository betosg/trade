<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

global $php_errormsg;
ini_set("track_errors", true);

if((!isset($_FILES["file1"]["tmp_name"]))||(!isset($_FILES["file1"]["name"]))||(!isset($_FILES["file1"]["size"]))){
	mensagem("err_stream_titulo","err_stream_desc",getTText("erro_no_arquivo",C_NONE),"","erro",1);
	die();
}

$strFormName  = request("var_formname");
$strFieldName = request("var_fieldname");
$strPrefix    = request("var_prefix");
if ($strPrefix != ""){$strPrefix = $strPrefix ."_";}
$strPath   	  = str_replace("\\\\","\\",request("var_path"));
$strDir    	  = str_replace("\\\\","\\",request("var_dir"));
$strFile	  = $_FILES["file1"]["tmp_name"];
$strFileName  = strtolower($strPrefix.getNormalString(date("YmdHis")."_".$_FILES["file1"]["name"]));
$intFileSize  = $_FILES["file1"]["size"];
$intFileError = $_FILES["file1"]["error"];

$strArqPath   = str_replace("\\",DIRECTORY_SEPARATOR,substr(findPhysicalPath(),0,strlen(findPhysicalPath())-1) . $strDir . $strFileName);

// rename($strFileName,strtolower(date("YmdHis")."_".$strFileName));
// rename($strFile,strtolower(date("YmdHis")."_".$strFile));
// echo($intFileError);

if($intFileError == 1){
	mensagem("err_stream_titulo","err_stream_desc",getTText("file_size_exceeds",C_NONE)."<br />".getTText("size_max_php_ini",C_NONE).ini_get("upload_max_filesize"),"","erro",1);
	die();
}
try{
	copy($strFile,$strArqPath);
	//die();
}catch(Exception $e){
	mensagem("err_stream_titulo","err_stream_desc",$e->getMessage(),"","erro",1);
	die();
}
// die();
// if(!@copy($strFile,$strArqPath)){
//	mensagem("err_stream_titulo","err_stream_desc",$php_errormsg . "<br><br>path orig: " . $strArqPath . "<br> tmp_file:" . $strFile,"","erro",1);
//	die();
// }

?>
<script language="javascript">location.href = "athuploader.php?var_dir=<?php echo(request("var_dir")); ?>" + "&var_func=2" + "&var_formname=<?php echo($strFormName); ?>" + "&var_fieldname=<?php echo($strFieldName); ?>" + "&var_filename=<?php echo($strFileName); ?>";</script>