<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

//Recebe apenas o código da PJ pai...
$intCodPJ = request("var_chavereg");
$intCodPF = request("var_cod_pf");

$objConn = abreDBConn(CFG_DB);


	// LOCALIZA PF CORRENTE
	try{
		$strSQL  = " SELECT cad_pf.cod_pf ";
		$strSQL .= "  	   ,cad_pf.nome ";
		$strSQL .= "	   ,cad_pf.matricula ";
		$strSQL .= "	   ,relac_pj_pf.cod_pj_pf ";
		$strSQL .= "  FROM cad_pf ";
		$strSQL .= "  LEFT JOIN relac_pj_pf on (relac_pj_pf.cod_pf = ".$intCodPF." AND relac_pj_pf.cod_pj = ".$intCodPJ.") ";
		$strSQL .= " WHERE cad_pf.cod_pf = ".$intCodPF;
		$objResultC = $objConn->query($strSQL);
			}				
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// FETCH EM DADOS
	$objRS = $objResultC->fetch();
	$strCodPf	    = getValue($objRS,"cod_pf");
	$strCodRelac    = getValue($objRS,"cod_pj_pf");
	$strNome 		= getValue($objRS,"nome");
	$strMatricula   = getValue($objRS,"matricula");

    $strExibeForm =  "display:none; visibility:hidden; ";
	
	if ($strCodPf!="") {
		//ACHOU a PF
		if ($strCodRelac!="") {
			//STupdcolab.php?var_cod_pj=1&var_chavereg=17457codpf'
			//PF PERTENCE A EMPRESA ATUAL <=> chama EDIÇÂO
			$strMSG = "Esta PF pertence a empresa de código[$intCodPJ]. <br>Clique <b> [<a href='STverifyFree.php?var_chavereg=".$intCodPJ."&var_flag_inserir=INS_LIVRE' style='cursor:pointer;'>VOLTAR</a>] </b> ou <b>  [<a href='STupdcolab.php?var_cod_pj=".$intCodPJ."&var_chavereg=".$strCodPf."'>EDITAR</a>]</b> para alterar.";
		} else {
			//NOVA RELAÇAO <=> FREE_RELAC
			$strMSG = "";
			$strExibeForm = "display:inline-table;; visibility:visible;";
		}
	} else {
		//NOVO 
		$strMSG = "O codigo de PF não foi encontrado.<br> Clique <b> [<a href='STverifyFree.php?var_chavereg=".$intCodPJ."&var_flag_inserir=INS_LIVRE' style='cursor:pointer;'>VOLTAR</a>] </b> ou <b> [<a href='STinsFreePF.php?var_chavereg=".$intCodPJ."'>NOVO</a>]</b> para cadastrar.";
	}
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript">
<!--
function verifica(prLocation){
	var var_msg = "";
	var strLocation = prLocation;
	
	if (document.formeditor.var_cod_pj.value == '') var_msg += "\nEmpresa";
	if (document.formeditor.var_nome.value == '') var_msg += "\nNome";
	if (document.formeditor.var_apelido.value == '') var_msg += "\nNome Credencial";
	if (document.formeditor.var_sexo.value == '') var_msg += "\nSexo";
	if (document.formeditor.var_cpf.value == '') var_msg += "\nCPF";
	if (document.formeditor.var_endprin_cep.value == '') var_msg += "\nCEP";
	if (document.formeditor.var_endprin_logradouro.value == '') var_msg += "\nLogradouro";
	if (document.formeditor.var_endprin_numero.value == '') var_msg += "\nNúmero";
	if (document.formeditor.var_endprin_bairro.value == '') var_msg += "\nBairro";
	if (document.formeditor.var_endprin_cidade.value == '') var_msg += "\nCidade";
	if (document.formeditor.var_endprin_estado.value == '') var_msg += "\nEstado";
	if (document.formeditor.var_endprin_pais.value == '') var_msg += "\nPaís";
	if (document.formeditor.var_endprin_fone1.value == '') var_msg += "\nFone 1";
	
	if(
		(document.getElementById("var_situacao_colab").value == "INATIVO")&&
		((document.getElementById("var_dt_inativo").value == "")||(document.getElementById("var_motivo_inativo").innerHTML == ""))
	  ){ var_msg += "\n\nINATIVAÇÃO DO COLABORADOR";  }
	if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_dt_inativo").value == "")){ var_msg += "\nData Inativação"; }
	if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_motivo_inativo").innerHTML == "")){ var_msg += "\nMotivo Inativação"; }
	
	if (true){
		if(strLocation != ""){ document.getElementById('var_redirect').value = strLocation; }
		document.formeditor.submit();
	}
	else {
		alert("Informar campos abaixo:\n" + var_msg);
	}
}

function cancelar(){
	window.location= "STviewpfs.php?var_chavereg=<?php echo($intCodPJ);?>";
}

function callUploader(prFormName, prFieldName, prDir, prPrefix, prFlagSufix){
	strLink = "../modulo_Principal/athuploader.php?var_formname=" + prFormName + "&var_fieldname=" + prFieldName + "&var_dir=" + prDir + "&var_prefix=" + prPrefix + "&var_flag_sufix=" + prFlagSufix;
	AbreJanelaPAGE(strLink, "570", "270");
}

function setFormField(formname, fieldname, valor){
	if ((formname != "") && (fieldname != "") && (valor != "")){
    	eval("document." + formname + "." + fieldname + ".value = '" + valor + "';");
  	}
}

function copiaCamposEndereco(){
	document.getElementById('var_endcom_cep').value = document.getElementById('var_endprin_cep').value;
	document.getElementById('var_endcom_logradouro').value = document.getElementById('var_endprin_logradouro').value;
	document.getElementById('var_endcom_numero').value = document.getElementById('var_endprin_numero').value;
	document.getElementById('var_endcom_complemento').value = document.getElementById('var_endprin_complemento').value;
	document.getElementById('var_endcom_bairro').value = document.getElementById('var_endprin_bairro').value;
	document.getElementById('var_endcom_cidade').value = document.getElementById('var_endprin_cidade').value;
	document.getElementById('var_endcom_estado').value = document.getElementById('var_endprin_estado').value;
	document.getElementById('var_endcom_fone1').value = document.getElementById('var_endprin_fone1').value;
	document.getElementById('var_endcom_fone2').value = document.getElementById('var_endprin_fone2').value;
}

function setInativo(){
		if(document.getElementById("var_situacao_colab").value == "ATIVO"){ document.getElementById("table_inativo").style.display = "none"; }
		else { document.getElementById("table_inativo").style.display = "block"; }
		resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_cod_pj")); ?>',20);
}

function swapInputCombo(prCombo,prInput) {
  //alert('aqui' + document.getElementById(prCombo).style.display);
  if (document.getElementById(prCombo).style.display == "none") {
	  document.getElementById(prCombo).style.display = "block";  document.getElementById(prCombo).disabled = 0;
	  document.getElementById(prInput).style.display = "none";   document.getElementById(prInput).disabled = 1;
  } else {
	  document.getElementById(prCombo).style.display = "none"; 	  document.getElementById(prCombo).disabled = 1;
	  document.getElementById(prInput).style.display = "block";	  document.getElementById(prInput).disabled = 0;
  }
}


//-->
</script>
</head>
<body bgcolor="#FFFFFF" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="700" height="100%" align="center">
 <tr>
  <td align="center" valign="top">
	<?php athBeginFloatingBox("630","none","COLABORADOR (inserção livre/contato)",CL_CORBAR_GLASS_1); ?>
		<table width="100%" bgcolor="#FFFFFF" style="border:0px #A6A6A6 solid; display:inline-table;">
		<tr> 
			<td align="center" valign="top">
				<table style="border:0px; width:550px;" cellspacing="0" cellpadding="4"> 
				<tr bgcolor="#FFFFFF">
					<td width="23%" align="right">&nbsp;</td>
					<td width="77%" align="left" class="destaque_gde"><strong>DADOS DA PESSOAS FÍSICA</strong></td>
				</tr>
				<tr><td colspan="2" height="2" background="../img/line_dialog.jpg"></td></tr>                   
                    
                    <tr bgcolor="#FAFAFA">
                        <td width="23%" align="right" valign="top"><strong>Cod.PF</strong></td>
                        <td width="77%" align="left"><?php echo($strCodPf);?> <small>(<?php echo($intCodPF);?>)</small></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="23%" align="right" valign="top">Matricula</strong></td>
                        <td width="77%" align="left"><?php echo($strMatricula);?></td>
                    </tr>
                    <tr bgcolor="#FAFAFA">
                        <td width="23%" align="right" valign="top">Nome</strong></td>
                        <td width="77%" align="left"><?php echo($strNome);?></td>
                    </tr>                    

                    <tr bgcolor="#FAFAFA">
                        <td width="23%" align="right" valign="top"></td>
                        <td width="77%" align="left"><?php echo($strMSG);?></td>
                    </tr>                    
				</table>
		</tr>
		</table><br><br>


		<table width="100%" bgcolor="#FFFFFF" style="border:0px #A6A6A6 solid; <?php echo($strExibeForm);?>">
	   		<form name="formeditor" action="STinsFreePFexec.php" method="post">
                <input type="hidden" name="var_endprin_pais" value="BRASIL">
                <input type="hidden" name="var_cod_pf"		 value="<?php echo($strCodPf);?>">
                <input type="hidden" name="var_cod_pj"		 value="<?php echo($intCodPJ);?>">
                <input type="hidden" name="var_nome"		 value="<?php echo($strNome);?>">

				<input type="hidden" name="var_redirect" id="var_redirect" value="../modulo_CadPJ/STviewpfs.php?var_chavereg=<?php echo($intCodPJ);?>"/>
		<tr> 
			<td align="center" valign="top">
				<table style="border:0px; width:550px;" cellspacing="0" cellpadding="4"> 
                    
                    <tr>
                        <td></td>
                        <td align="left" valign="top" class="destaque_gde"><strong>DADOS DA VAGA</strong>&nbsp;(relação PJ x PF)</td>
                        <?php
							/* Nestes campos da Relação PJ x PF existe a possibilidade de ocultação dos mesms atraves da configuração
							Os campos tens suas funcionalidades mantidas, mas nesse processo de cadastramento FAST(ou livre), damos a 
							possibilidade do cliente ocultar alguns destes campos da relação PJxPJ  tanto no INS quando na UPD.
							   Até a data atual este teste esta [STInsFreePF.asp] e na [STupdcolab.php] do modulo_CadPJ
							Os campso a exibir ou não devem estar no registro de configuração da empresa (sys_VarEntidade) e devem 
							estar na ordem:
 							   CATEGORIA:S,FUNCAO:S,DEPARTAMENTO:S,COD_CARGO:S,COD_NIVEL_HIERARQUICO:S,TIPO:S,CLASSIFICACAO_VIP:S
							*/
							
							
							 $str = strtoupper(getVarEntidade($objConn,"campos_livrerelpjxpf"));
							/*
							Código feito com ARR gerava problema quando a variável fosse mal preenchida, 
							então troquei para um tratamento mais simples de strings -> if ( (stripos($str,"CATEGORIA")<=-1) || (stripos($str,"CATEGORIA:S")>-1) ...
							
							define("CONST_CATEGORIA",0);
							define("CONST_FUNCAO",1);
							define("CONST_DEPARTAMENTO",2);
							define("CONST_COD_CARGO",3);
							define("CONST_COD_NIVEL_HIERARQUICO",4);
							define("CONST_TIPO",5);
							define("CONST_CLASSIFICACAO_VIP",6);
						
							if ($str == "") { $str = "CATEGORIA:S,FUNCAO:S,DEPARTAMENTO:S,COD_CARGO:S,COD_NIVEL_HIERARQUICO:S,TIPO:S,CLASSIFICACAO_VIP:S"; } 
							$sep = Array(",",":");
							$mat = multiexplode($sep,$str);
							if ($mat[CONST_CATEGORIA][1]=='S') ...
							if ($mat[CONST_FUNCAO][1]=='S') ...
							*/
						?>
                    </tr>
                    <tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
                    <tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
                    <?php if ( (stripos($str,"CATEGORIA")<=-1) || (stripos($str,"CATEGORIA:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_categoria"><strong>Categoria:</strong></label></td>
                        <td nowrap>
           	                <select name="var_categoria" id="var_categoria_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT categoria, categoria FROM relac_pj_pf WHERE categoria NOT IN ('GERAL','ESPECIAL','PLENO') ";
								    $strSQL .= " UNION   SELECT 'GERAL'    , 'GERAL'  ";
								    $strSQL .= " UNION   SELECT 'ESPECIAL' , 'ESPECIAL'  ";
								    $strSQL .= " UNION   SELECT 'PLENO'    , 'PLENO'  ";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"categoria","categoria","")); 
								?>
							</select>
							<input type="text" name="var_categoria" id="var_categoria_input" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
                                                             alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_categoria_combo','var_categoria_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"FUNCAO")<=-1) || (stripos($str,"FUNCAO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_funcao"><strong>Função:</strong></label></td>
                        <td nowrap align="left" width="99%">
           	                <select name="var_trab_funcao" id="var_trab_funcao_combo" class="edtext" style="display:block; float:left; width:200px;">
								<!-- option value="" selected></option //-->
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(funcao), funcao FROM relac_pj_pf ORDER BY 1","funcao","funcao","")); ?>
							</select>
							<input type="text" name="var_trab_funcao" id="var_trab_funcao_input" size="60" style="display:none; float:left; width:200px;"  disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
							                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_funcao_combo','var_trab_funcao_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"DEPARTAMENTO")<=-1) || (stripos($str,"DEPARTAMENTO:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_departamento"><strong>Departamento:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_trab_departamento" id="var_trab_departamento_combo" class="edtext" style="display:block; float:left; width:200px;">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT DISTINCT trim(departamento), departamento FROM relac_pj_pf ORDER BY 1","departamento","departamento","")); ?>
							</select>
                           
                            <input name="var_trab_departamento" id="var_trab_departamento_input" type="text" size="50" style="display:none; float:left; width:200px;" maxlength="100" title="Departamento" disabled="disabled">
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0"
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_departamento_combo','var_trab_departamento_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"COD_CARGO")<=-1) || (stripos($str,"COD_CARGO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_cargo"><strong>Cargo:</strong></label></td>
                        <td nowrap align="left" width="99%">
							<select name="var_cod_cargo" id="var_cod_cargo" class="edtext" style="width:200px;" tabindex="6">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_cargo, nome FROM cad_cargo ORDER BY 2","cod_cargo","nome",$strCargo)); ?> 
							</select>									
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"COD_NIVEL_HIERARQUICO")<=-1) || (stripos($str,"COD_NIVEL_HIERARQUICO:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Nível:</strong></label></td>									
                        <td nowrap>
							<select name="var_cod_nivel" id="var_cod_nivel" class="edtext" style="width:200px;" tabindex="7">
								<option value="" selected></option>
								<?php echo(montaCombo($objConn,"SELECT cod_nivel_hierarquico, nome FROM cad_nivel_hierarquico ORDER BY 2","cod_nivel_hierarquico","nome",$strNivel)); ?>
							</select>(nivel hierarquico)									
                        </td>
                    </tr>
                    <?php } ?>

                    <?php if ( (stripos($str,"TIPO")<=-1) || (stripos($str,"TIPO:S")>-1) ) {?>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_tipo"><strong>Tipo:</strong></label></td>
                        <td nowrap>
           	                <select name="var_trab_tipo" id="var_trab_tipo_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT tipo, tipo FROM relac_pj_pf WHERE tipo NOT IN ('AUTONOMO','AVULSO','TEMPORARIO','EMPREGADO', 'ESTAGIO') ";
								    $strSQL .= " UNION   SELECT 'AUTONOMO'    , 'AUTONOMO'   ";
								    $strSQL .= " UNION   SELECT 'AVULSO'      , 'AVULSO'     ";
								    $strSQL .= " UNION   SELECT 'TEMPORARIO'  , 'TEMPORARIO' ";
								    $strSQL .= " UNION   SELECT 'EMPREGADO'   , 'EMPREGADO'  ";
								    $strSQL .= " UNION   SELECT 'ESTAGIO'     , 'ESTAGIO'    ";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"tipo","tipo","")); 
								?>
							</select>
							<input type="text" name="var_trab_tipo" id="var_trab_tipo_input" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
                            <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_trab_tipo_combo','var_trab_tipo_input'); return false;"></span>
                        </td>
                    </tr>
                    <?php } ?>
                    
                    <?php if ( (stripos($str,"CLASSIFICACAO_VIP")<=-1) || (stripos($str,"CLASSIFICACAO_VIP:S")>-1) ) {?>
                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_cod_nivel"><strong>Classificação:</strong></label></td>									
                        <td nowrap>
           	                <select name="var_classificacao_vip" id="var_classificacao_vip_combo" class="edtext" style="display:block; float:left; width:120px;">
								<!-- option value="" selected></option //-->
								<?php 
								    $strSQL  = "SELECT DISTINCT classificacao_vip, classificacao_vip FROM relac_pj_pf WHERE classificacao_vip is NOT NULL";
								    $strSQL .= " ORDER BY 1";
									echo(montaCombo($objConn,$strSQL ,"classificacao_vip","classificacao_vip","")); 
								?>
							</select>
							<input type="text" name="var_classificacao_vip" id="var_classificacao_vip_inpuit" size="60" style="display:none; float:left; width:200px;" disabled="disabled" />
	                        <span class="comment_med">&nbsp;<img align="absmiddle" src="../img/icon_combo2input.gif" border="0" 
						                            alt="" style="cursor:hand" title="" onclick="swapInputCombo('var_classificacao_vip_combo','var_classificacao_vip_inpuit'); return false;"></span>

                        </td>
                    </tr>
                    <?php } ?>
                    <tr bgcolor="#FFFFFF">  
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_obs"><strong>Obs / <br />Representado por:</strong></label></td>
                        <td nowrap align="left" width="99%"><textarea name="var_trab_obs" cols="60" rows="2" title="Obs"></textarea><span class="comment_med"><br />
                        Campo livre para preenchimento de livre observações como<br />
                        "autorizado pelo Rodrigo","representa a Proevento",<br />
                        "através da Ubrafe","Presidente do Sindiprom",...</span></td>
                    </tr>

                    <tr bgcolor="#FAFAFA">
                        <td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><label for="var_trab_dt_admissao"><strong>Admissão:</strong></label></td>
                        <td nowrap><input name="var_trab_dt_admissao" id="var_trab_dt_admissao" value="" type="text" size="10" maxlength="10" title="Data Admissão" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span></td>
                    </tr>
                    <tr bgcolor="#FFFFFF">
                        <td width="1%" align="right" valign="top" nowrap><strong>Data Demissão:</strong></td>
                        <td nowrap><input name="var_trab_dt_demissao" id="var_trab_dt_demissao" value="" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" title="data de demissão">&nbsp;&nbsp;<a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_demissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a>
                        <br /><span class="comment_med">Preenchendo este campo, o colaborador que está sendo inserido não <br />aparecerá na listagem de colaboradores, do painel da Afiliada, somente <br />na listagem completa de colaboradores.</span></td>
                    </tr>
                    <tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
                        
                    <tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
                    <tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
				</table>

			</td>
		</tr>
		<tr>
			<td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
				<button onClick="verifica('');"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
				<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
				<button onClick="verifica('../modulo_CadPJ/STupdcolab.php?var_chavereg=<?php echo($intCodPF);?>&var_cod_pj=<?php echo($intCodPJ);?>&var_cpf=<?php echo($strCPF);?>');" ><?php echo(getTText("aplicar",C_UCWORDS)); ?></button>
            </td>
		</tr>		
			</form>
		</table>
	<?php athEndFloatingBox(); ?>
  </td>
 </tr>
</table>
</body>
<script type="text/javascript">
  // Quando esta página for chamda de dentro de um iframe denominado pelo nome [system_name]_detailiframe_[num]
  resizeIframeParent('<?php echo(CFG_SYSTEM_NAME); ?>_detailiframe_<?php echo(request("var_chavereg")); ?>',20);
  // ----------------------------------------------------------------------------------------------------------
</script>
</html>
<iframe name="gToday:normal:agenda.js" id="gToday:normal:agenda.js"
        src="../_class/calendar/source/ipopeng.htm" scrolling="no" frameborder="0"
        style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>
<?php
$objConn = NULL;
?>
