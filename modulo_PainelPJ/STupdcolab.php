<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");
include_once("../_scripts/scripts.js");
include_once("../_scripts/STscripts.js");

$intCodPJ 	= request("var_cod_pj");
$intCodPF 	= request("var_chavereg");
$strCPF  	= request("var_cpf");

$objConn = abreDBConn(CFG_DB);

if ($intCodPF == '') {
	$strRotulo = "Inserção";
	
	$strNome  		  = '';
	$strApelido		  = '';
	$dtDataNasc       = '';
	$strSexo  		  = 'm';
	$strEmail         = '';
	$strEmailExtra    = '';
	$strWebsite       = '';
	$strFoto          = '';
	$strEstadoCivil   = '';
	$strInstrucao     = '';
	$strNacionalidade = '';
	$strNaturalidade  = '';
	$strObs           = '';
	$strStatus        = '';
	$strNomePai       = '';
	$strNomeMae       = '';
	
	$strCPF  = $strCPF;
	$strRG   = '';
	$strCNH  = '';
	$strPIS  = '';
	$strCTPS = '';
	$strTITE = '';
	
	$strEndPrinCEP         = '';
	$strEndPrinLogradouro  = '';
	$strEndPrinNumero      = '';
	$strEndPrinComplemento = '';
	$strEndPrinBairro      = '';
	$strEndPrinCidade      = '';
	$strEndPrinEstado      = 'SP';
	$strEndPrinPais        = 'Brasil';
	$strEndPrinFone1       = '';
	$strEndPrinFone2       = '';
	$strEndPrinFone3       = '';
	$strEndPrinFone4       = '';
	$strEndPrinFone5       = '';
	$strEndPrinFone6       = '';
	
	$strEndComCEP         = '';
	$strEndComLogradouro  = '';
	$strEndComNumero      = '';
	$strEndComComplemento = '';
	$strEndComBairro      = '';
	$strEndComCidade      = '';
	$strEndComEstado      = '';
	$strEndComPais        = '';
	$strEndComFone1       = '';
	$strEndComFone2       = '';
	$strEndComFone3       = '';
	$strEndComFone4       = '';
	$strEndComFone5       = '';
	$strEndComFone6       = '';
	
	$dtInativo 			  = '';
	$strMotivo			  = '';
}
else {
	$strRotulo = "Alteração";
	
	try {
		$strSQL = "
			SELECT 
				  t1.nome
				, t1.data_nasc
				, t1.sexo
				, t1.email
				, t1.email_extra
				, t1.website
				, t1.foto
				, t1.apelido
				, t1.nome_pai
				, t1.nome_mae
				, t1.estado_civil
				, t1.instrucao
				, t1.nacionalidade
				, t1.naturalidade
				, t1.obs
				, t2.arquivo_1
				, t2.arquivo_2
				, t2.arquivo_3
				, t1.rg, t1.cnh, t1.pis, t1.ctps, t1.cpf, t1.titulo_eleitoral, t2.dt_inativo, t2.motivo_inativo
				, t1.endprin_cep, t1.endprin_logradouro, t1.endprin_numero, t1.endprin_complemento, t1.endprin_bairro 
				, t1.endprin_cidade, t1.endprin_estado, t1.endprin_pais, t1.endprin_fone1, t1.endprin_fone2
				, t1.endcom_cep, t1.endcom_logradouro, t1.endcom_numero, t1.endcom_complemento, t1.endcom_bairro
				, t1.endcom_cidade, t1.endcom_estado, t1.endcom_pais, t1.endcom_fone1, t1.endcom_fone2
				, t2.tipo, t2.funcao, t2.categoria, t2.departamento, t2.dt_admissao, t2.obs as obs_vaga
			FROM 
				cad_pf t1, relac_pj_pf t2
			WHERE t1.cod_pf = " . $intCodPF . "
			AND
				t2.cod_pf = " . $intCodPF . "
			AND
				t2.cod_pj = " . $intCodPJ . "
			AND
				t2.cod_pf = t1.cod_pf";
		$objResult = $objConn->query($strSQL);
		
		if ($objResult->rowCount() > 0) {
			$objRS = $objResult->fetch();
			
			$strNome  		  = getValue($objRS, "nome");
			$strApelido		  = getValue($objRS, "apelido");
			$dtDataNasc       = dDate(CFG_LANG, getValue($objRS, "data_nasc"), false);
			$strSexo  		  = getValue($objRS, "sexo");
			$strEmail         = getValue($objRS, "email");
			$strEmailExtra    = getValue($objRS, "email_extra");
			$strWebsite       = getValue($objRS, "website");
			$strFoto          = getValue($objRS, "foto");
			$strEstadoCivil   = getValue($objRS, "estado_civil");
			$strInstrucao     = getValue($objRS, "instrucao");
			$strNacionalidade = getValue($objRS, "nacionalidade");
			$strNaturalidade  = getValue($objRS, "naturalidade");
			$strObs           = getValue($objRS, "obs");
			$strNomePai       = getValue($objRS, "nome_pai");
			$strNomeMae       = getValue($objRS, "nome_mae");
			
			$strCPF  = getValue($objRS, "cpf");
			$strRG   = getValue($objRS, "rg");
			$strCNH  = getValue($objRS, "cnh");
			$strPIS  = getValue($objRS, "pis");
			$strCTPS = getValue($objRS, "ctps");
			$strTITE = getValue($objRS, "titulo_eleitoral");
			
			$strEndPrinCEP         = getValue($objRS, "endprin_cep");
			$strEndPrinLogradouro  = getValue($objRS, "endprin_logradouro");
			$strEndPrinNumero      = getValue($objRS, "endprin_numero");
			$strEndPrinComplemento = getValue($objRS, "endprin_complemento");
			$strEndPrinBairro      = getValue($objRS, "endprin_bairro");
			$strEndPrinCidade      = getValue($objRS, "endprin_cidade");
			$strEndPrinEstado      = getValue($objRS, "endprin_estado");
			$strEndPrinPais        = getValue($objRS, "endprin_pais");
			$strEndPrinFone1       = getValue($objRS, "endprin_fone1");
			$strEndPrinFone2       = getValue($objRS, "endprin_fone2");
			$strEndPrinFone3       = getValue($objRS, "endprin_fone3");
			$strEndPrinFone4       = getValue($objRS, "endprin_fone4");
			$strEndPrinFone5       = getValue($objRS, "endprin_fone5");
			$strEndPrinFone6       = getValue($objRS, "endprin_fone6");
			
			$strEndComCEP         = getValue($objRS, "endcom_cep");
			$strEndComLogradouro  = getValue($objRS, "endcom_logradouro");
			$strEndComNumero      = getValue($objRS, "endcom_numero");
			$strEndComComplemento = getValue($objRS, "endcom_complemento");
			$strEndComBairro      = getValue($objRS, "endcom_bairro");
			$strEndComCidade      = getValue($objRS, "endcom_cidade");
			$strEndComEstado      = getValue($objRS, "endcom_estado");
			$strEndComPais        = getValue($objRS, "endcom_pais");
			$strEndComFone1       = getValue($objRS, "endcom_fone1");
			$strEndComFone2       = getValue($objRS, "endcom_fone2");
			$strEndComFone3       = getValue($objRS, "endcom_fone3");
			$strEndComFone4       = getValue($objRS, "endcom_fone4");
			$strEndComFone5       = getValue($objRS, "endcom_fone5");
			$strEndComFone6       = getValue($objRS, "endcom_fone6");
			
			// Relação PJ x PF
			$strCategoria 		  = getValue($objRS, "categoria");
			$strTipo 			  = getValue($objRS, "tipo");
			$strFuncao 			  = getValue($objRS, "funcao");
			$strDepartamento      = getValue($objRS, "departamento");
			$dtAdmissao			  = getValue($objRS, "dt_admissao");
			$strObsVaga           = getValue($objRS, "obs_vaga");
			$strARQUIVO1		  = getValue($objRS, "arquivo_1");
			$strARQUIVO2		  = getValue($objRS, "arquivo_2");
			$strARQUIVO3		  = getValue($objRS, "arquivo_3");
			
			$dtInativo			  = getValue($objRS, "dt_inativo");
			$strMotivo  		  = getValue($objRS, "motivo_inativo");
			
		}
	}
	catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	$objResult->closeCursor();
}

	// inicializa variavel para pintar linha
	$strColor = CL_CORLINHA_1;
	
	$strModo = "";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindieventos") $strModo = "Modo1";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "sindiprom")    $strModo = "Modo2";
	if (getsession(CFG_SYSTEM_NAME . "_dir_cliente") == "ubrafe")       $strModo = "Modo3";
	
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
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript">
	<!--
		function verifica(){
			//alert(getDDiff(getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'),getVDate(document.formeditor.var_trab_dt_demissao.value,'ptb')));
			var strMSG = "";
			// CABEÇALHO DE DADOS PRINCIPAIS
			if(
			  	(document.formeditor.var_cod_pj.value == "") ||
				(document.formeditor.var_nome.value   == "") ||
				(document.formeditor.var_apelido.value == "") ||
				(document.formeditor.var_sexo.value   == "") ||
				// (document.formeditor.var_foto.value   == "") ||
				(document.formeditor.var_data_nasc.value == "") ||
				(document.formeditor.var_estado_civil.value == "") ||
				(document.formeditor.var_nacionalidade.value   == "")||
				(document.formeditor.var_naturalidade.value   == "")||
				(getDDiff(getNow(),getVDate(document.formeditor.var_data_nasc.value,'ptb'))>0)
			  ){ strMSG += "\n\nDADOS PRINCIPAIS:" }
			if(document.formeditor.var_cod_pj.value == ""){	strMSG += "\nEmpresa"; }
			if(document.formeditor.var_nome.value   == ""){	strMSG += "\nNome";    }
			if(document.formeditor.var_apelido.value == ""){	strMSG += "\nNome Credencial";    }
			if(document.formeditor.var_sexo.value   == ""){	strMSG += "\nSexo";    }
			if(document.formeditor.var_data_nasc.value == ""){ strMSG += "\nData de Nascimento"; }
			if(getDDiff(getNow(),getVDate(document.formeditor.var_data_nasc.value,'ptb'))>0){	strMSG += "\nData de Nascimento Maior que Data Atual";    }
			// if(document.formeditor.var_foto.value   == ""){	strMSG += "\nFoto";    }
			if(document.formeditor.var_estado_civil.value   == ""){	 strMSG += "\nEstado Civil";    }
			if(document.formeditor.var_nacionalidade.value   == ""){ strMSG += "\nNacionalidade";    }
			if(document.formeditor.var_naturalidade.value   == ""){ strMSG += "\nNaturalidade";    }
			
			// CABEÇALHO FILIAÇÃO
			if(
			  	(document.formeditor.var_nome_mae.value == "") ||
				(document.formeditor.var_nome_pai.value == "")
			  ){ strMSG += "\n\nFILIAÇÃO:" }
			if(document.formeditor.var_nome_mae.value == ""){	strMSG += "\nNome da Mãe"; }
			if(document.formeditor.var_nome_pai.value == ""){	strMSG += "\nNome do Pai"; }
			
			// CABEÇALHO DE DOCUMENTOS			
			/*
			if(
				(document.formeditor.var_cpf.value    == "") ||
				(document.formeditor.var_rg.value     == "") ||
				(document.formeditor.var_pis.value    == "") ||
				(document.formeditor.var_ctps.value   == "") ||
				(document.formeditor.var_titulo_eleitoral.value == "")
			  ){ strMSG += "\n\nDOCUMENTOS:" }
			if(document.formeditor.var_cpf.value    == ""){	strMSG += "\nCPF";  }
			if(document.formeditor.var_rg.value     == ""){	strMSG += "\nRG";   }
			if(document.formeditor.var_pis.value    == ""){	strMSG += "\nPIS";	}
			if(document.formeditor.var_ctps.value   == ""){	strMSG += "\nCTPS / Série";      }		
			if(document.formeditor.var_titulo_eleitoral.value   == ""){	strMSG += "\nTítulo Eleitoral";  }		
			*/
			
			// CABEÇALHO DE ENDEREÇO PRINCIPAL			
			if(
				(document.formeditor.var_endprin_cep.value    == "") ||
				(document.formeditor.var_endprin_numero.value == "") ||
				(document.formeditor.var_endprin_bairro.value == "") ||
				(document.formeditor.var_endprin_cidade.value == "") ||
				(document.formeditor.var_endprin_estado.value == "") ||
				(document.formeditor.var_endprin_pais.value   == "") ||
				(document.formeditor.var_endprin_fone1.value  == "") 
			  ){ strMSG += "\n\nENDEREÇO PRINCIPAL:";  }
			if(document.formeditor.var_endprin_cep.value        == ""){ strMSG += "\nCEP";             }
			if(document.formeditor.var_endprin_logradouro.value == ""){ strMSG += "\nLogradouro";      }
			if(document.formeditor.var_endprin_numero.value 	== ""){	strMSG += "\nNúmero";          }
			if(document.formeditor.var_endprin_bairro.value 	== ""){ strMSG += "\nBairro";          }
			if(document.formeditor.var_endprin_cidade.value 	== ""){ strMSG += "\nCidade";          }
			if(document.formeditor.var_endprin_estado.value 	== ""){	strMSG += "\nEstado";          }
			if(document.formeditor.var_endprin_pais.value	 	== ""){ strMSG += "\nPaís";            }
			if(document.formeditor.var_endprin_fone1.value 		== ""){ strMSG += "\nTelefone Um (1)"; }
			
			// CABEÇALHO DE FUNÇÃO DO COLABORADOR
			/*(document.formeditor.dbvar_str_arquivo_1.value  == "")||*/
			if(
				(document.formeditor.var_trab_funcao.value      == "")||
				(document.formeditor.var_trab_dt_admissao.value == "")||				
				(document.formeditor.var_trab_tipo.value  == "")||
				(getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'))>0)||
				((document.formeditor.var_trab_dt_demissao.value  != "") && (getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_demissao.value,'ptb'))>0))||
				(getDDiff(getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'),getVDate(document.formeditor.var_trab_dt_demissao.value,'ptb'))<0)
			  ){ strMSG += "\n\nDADOS DA VAGA:" }
			if(document.formeditor.var_trab_funcao.value      == ""){ strMSG += "\nFunção";           }
			if(document.formeditor.var_trab_dt_admissao.value == ""){ strMSG += "\nData de Admissão"; }
			if(document.formeditor.var_trab_tipo.value == ""){ strMSG += "\nTipo"; }
			if(getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'))>0){ strMSG += "\nData de Admissão maior que Data Atual"; }
			if((document.formeditor.var_trab_dt_demissao.value  != "") && (getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_demissao.value,'ptb'))>0)){ strMSG += "\nData de Demissão maior que Data Atual"; }
			if(getDDiff(getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'),getVDate(document.formeditor.var_trab_dt_demissao.value,'ptb'))<0){ strMSG += "\nData de Demissão menor que Admissão"; }
			/*if(document.formeditor.dbvar_str_arquivo_1.value  == ""){ strMSG += "\nDocumento 1"; 	  }*/
			
			<?php if((getsession(CFG_SYSTEM_NAME."_grp_user") == "ADMIN") || (getsession(CFG_SYSTEM_NAME."_grp_user") == "SU")){?>
			if(
				(document.getElementById("var_situacao_colab").value == "INATIVO")&&
				((document.getElementById("var_dt_inativo").value == "")||(document.getElementById("var_motivo_inativo").innerHTML == ""))
			  ){ strMSG += "\n\nINATIVAÇÃO DO COLABORADOR";  }
			if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_dt_inativo").value == "")){ strMSG += "\nData Inativação"; }
			if((document.getElementById("var_situacao_colab").value == "INATIVO")&&(document.getElementById("var_motivo_inativo").innerHTML == "")){ strMSG += "\nMotivo Inativação"; }
			<?php }?>
			
			if (strMSG == "") {
				//document.formeditor.action.value = "";
				document.formeditor.submit();
			}
			else {
				alert("Informar os campos obrigatórios abaixo:" + strMSG);
			}
		}
		
		/*function verifica(){
			var var_msg = "";
			if (document.formeditor.var_cod_pj.value == '') var_msg += "\nEmpresa";
			if (document.formeditor.var_nome.value == '') var_msg += "\nNome";
			if (document.formeditor.var_sexo.value == '') var_msg += "\nSexo";
			if (document.formeditor.var_cpf.value == '') var_msg += "\nCPF";
			//if (document.formeditor.var_foto.value == '') var_msg += "\nFoto";
			if (document.formeditor.var_rg.value == '') var_msg += "\nRG";
			if (document.formeditor.var_endprin_cep.value == '') var_msg += "\nCEP";
			if (document.formeditor.var_endprin_logradouro.value == '') var_msg += "\nLogradouro";
			if (document.formeditor.var_endprin_numero.value == '') var_msg += "\nNúmero";
			if (document.formeditor.var_endprin_bairro.value == '') var_msg += "\nBairro";
			if (document.formeditor.var_endprin_cidade.value == '') var_msg += "\nCidade";
			if (document.formeditor.var_endprin_estado.value == '') var_msg += "\nEstado";
			if (document.formeditor.var_endprin_pais.value == '') var_msg += "\nPaís";
			if (document.formeditor.var_endprin_fone1.value == '') var_msg += "\nFone 1";
			if (document.formeditor.dbvar_str_arquivo_1.value == '') var_msg += "\nDocumento 1";
			//if (document.formeditor.var_endprin_fone2.value == '') var_msg += "\nFone 2";
			//if (document.formeditor.var_trab_funcao.value == '') var_msg += "\nFunção";
			//if (document.formeditor.var_trab_dt_admissao.value == '') var_msg += "\nData de Admissão";
			
			if (var_msg == "") {
				//document.formeditor.action.value = "";
				document.formeditor.submit();
			}
			else {
				alert("Informar campos abaixo:\n" + var_msg);
			}
		}*/

		function cancelar(){
			window.location= "STColabAtivos.php";
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

		function showHiddenFields(prID){
			var strID = prID;
			if(document.getElementById(strID).style.display == 'block'){
				document.getElementById(strID).style.display = 'none';
				document.getElementById(strID+"_collapse").innerHTML = "<img src='../img/icon_tree_plus.gif' onclick=\"showHiddenFields('"+strID+"');\" style='cursor:pointer;'>";
			}else if(document.getElementById(strID).style.display == 'none'){
				document.getElementById(strID).style.display = 'block';
				document.getElementById(strID+"_collapse").innerHTML = "<img src='../img/icon_tree_minus.gif' onclick=\"showHiddenFields('"+strID+"');\" style='cursor:pointer;'>";
			}
			return true;
		}
		
		function setInativo(){
			if(document.getElementById("var_situacao_colab").value == "ATIVO"){ document.getElementById("table_inativo").style.display = "none"; }
			else { document.getElementById("table_inativo").style.display = "block"; }
		}
	//-->
	</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
 	<tr>
   		<td align="center" valign="top">
		<?php athBeginFloatingBox("720","none","COLABORADOR (" . $strRotulo . ")",CL_CORBAR_GLASS_1); ?>
			<table border="0" width="700" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   		<form name="formeditor" action="STinsColabPasso2exec.php" method="post">
				<input type="hidden" name="var_cod_pj" value="<?php echo($intCodPJ); ?>">
				<input type="hidden" name="var_cod_pf" value="<?php echo($intCodPF); ?>">
				<!--input type="hidden" name="var_cpf" value="<?php echo($strCPF); ?>"/-->
				<input type="hidden" name="var_endprin_pais" value="BRASIL">
				<input type="hidden" name="var_endcom_pais" value="BRASIL">
				<input type="hidden" name="var_str_flag" value="no">
				<!-- 
					CAMPOS DE DADOS SOBRE VAGA - 
					Campos visuais estao com DISABLED, 
					nao sao repassados com o FORM
				 -->
				<input type="hidden" name="var_trab_departamento" value="<?php echo($strDepartamento);?>" />
				<!--input type="hidden" name="var_trab_funcao" value="<?php echo($strFuncao);?>" /-->
				<!--input type="hidden" name="var_trab_tipo" value="<?php echo($strTipo);?>" /-->
				<!--<input type="hidden" name="var_trab_dt_admissao" value="<?php //echo(cDate(CFG_LANG,$dtAdmissao,false));?>" />-->
				<input type="hidden" name="var_trab_obs" value="<?php echo($strObsVaga);?>" />
				<tr> 
					<td align="center" valign="top" style="padding:0px 80px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="4">
							<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
							<tr><td colspan="2"><b>Preencha os campos abaixo:</b></td></tr>
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS PRINCIPAIS</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Nome:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_nome" id="var_nome" value="<?php echo($strNome); ?>" type="text" size="50" maxlength="100" title="nome"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="1%" align="right" valign="top" nowrap><strong>*Nome Credencial:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_apelido" id="var_apelido" value="<?php echo($strApelido); ?>" type="text" size="30" maxlength="20" title="Nome Credencial"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Nascimento:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_data_nasc" id="var_data_nasc" value="<?php echo($dtDataNasc); ?>" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<!--a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_data_nasc);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a--><span class="comment_med">(Formato dd/mm/aaaa)</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Sexo:</strong></td>
								<td nowrap align="left" width="99%">
									<select name="var_sexo" id="var_sexo" style="width:120px" size="1" title="Sexo">
										<option value="m" <?php if(strtoupper($strSexo) == "M") echo("selected='selected'"); ?>><i>MASCULINO</i></option>
										<option value="f" <?php if(strtoupper($strSexo) == "F") echo("selected='selected'"); ?>><i>FEMINININO</i></option>
									</select><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="16%" align="right" valign="top" nowrap><strong>*Estado Civil:</strong></td>
								<td nowrap align="left" width="99%" >
									<select name="var_estado_civil" id="var_estado_civil" style="width:120px" size="1" title="Estado Civil">
										<option value="" <?php if($strEstadoCivil == "")echo("selected");?>></option>
										<option value="CASADO" <?php if($strEstadoCivil == "CASADO")echo("selected");?>><i>Casado(a)</i></option>
										<option value="SEPARADO" <?php if($strEstadoCivil == "SEPARADO")echo("selected");?>><i>Separado(a)</i></option>
										<option value="SOLTEIRO" <?php if($strEstadoCivil == "SOLTEIRO")echo("selected");?>><i>Solteiro(a)</i></option>
										<option value="VIUVO" <?php if($strEstadoCivil == "VIUVO")echo("selected");?>><i>Viúvo(a)</i></option>
										<option value="DIVORCIADO" <?php if($strEstadoCivil=="DIVORCIADO")echo("selected");?>><i>Divorciado(a)</i></option>
										<option value="DESQUITADO" <?php if($strEstadoCivil=="DESQUITADO")echo("selected");?>><i>Desquitado(a)</i></option>
										<option value="AMASIADO" <?php if($strEstadoCivil == "AMASIADO")echo("selected");?>><i>Amasiado(a)</i></option>
									</select><span class="comment_med">&nbsp;</span>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="16%" align="right" valign="top" nowrap><strong>*Nacionalidade:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_nacionalidade" id="var_nacionalidade" value="<?php echo($strNacionalidade); ?>" type="text" size="35" maxlength="250"   title="Nacionalidade"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="16%" align="right" valign="top" nowrap><strong>*Naturalidade:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_naturalidade" id="var_naturalidade" value="<?php echo($strNaturalidade); ?>" type="text" size="35" maxlength="250"   title="Naturalidade"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>E-mail:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_email" id="var_email" value="<?php echo($strEmail); ?>" type="text" size="60" maxlength="255" title="e-mail"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Foto:</strong></td>
								<td nowrap align="left" width="99%">
									<input type="text" name="var_foto" id="var_foto" value="<?php echo($strFoto); ?>" size="50" readonly="true" title="Foto">&nbsp;
									<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor','var_foto','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/','','');">
									<br /><span class="comment_med">Clique <span style="font-weight:bold;cursor:pointer;" onClick="document.formeditor.var_foto.value='';"><u>aqui</u></span> para limpar o campo foto</span>
								</td>
							</tr>
							<?php if($strFoto != ""){?>
							<tr>
								<td>&nbsp;</td>
								<td align="left"><img src="../../<?php echo(getsession(CFG_SYSTEM_NAME."_dir_cliente"))?>/upload/fotospf/<?php echo($strFoto);?>" height="90"></td>
							</tr>
							<?php }?>
							<!--tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Estado Civil:</strong></td>
								<td nowrap align="left" width="99%">
									<select name="var_estado_civil" id="var_estado_civil" style="width:180px" size="1" title="Estado Civil">
										<option value="" <?php if ($strEstadoCivil == "") echo("selected"); ?>></option>
										<option value="CASADO" <?php if ($strEstadoCivil == "CASADO") echo("selected"); ?>><i>Casado(a)</i></option>
										<option value="SEPARADO" <?php if ($strEstadoCivil == "SEPARADO") echo("selected"); ?>><i>Separado(a)</i></option>
										<option value="SOLTEIRO" <?php if ($strEstadoCivil == "SOLTEIRO") echo("selected"); ?>><i>Solteiro(a)</i></option>
										<option value="VIUVO" <?php if ($strEstadoCivil == "VIUVO") echo("selected"); ?>><i>Viúvo(a)</i></option>
										<option value="DIVORCIADO" <?php if ($strEstadoCivil == "DIVORCIADO") echo("selected"); ?>><i>Divorciado(a)</i></option>
										<option value="DESQUITADO" <?php if ($strEstadoCivil == "DESQUITADO") echo("selected"); ?>><i>Desquitado(a)</i></option>
										<option value="AMASIADO" <?php if ($strEstadoCivil == "AMASIADO") echo("selected"); ?>><i>Amasiado(a)</i></option>
									</select><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Nacionalidade:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_nacionalidade" id="var_nacionalidade" value="<?php echo($strNacionalidade); ?>" type="text" size="35" maxlength="250" title="Nacionalidade"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Naturalidade:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_naturalidade" id="var_naturalidade" value="<?php echo($strNaturalidade); ?>" type="text" size="35" maxlength="250" title="Naturalidade"><span class="comment_med">&nbsp;</span></td>
							</tr-->
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Obs:</strong></td>
								<td nowrap align="left" width="99%"><textarea name="var_obs" id="var_obs" cols="60" rows="5"   title="Obs"><?php echo($strObs); ?></textarea><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>FILIAÇÃO</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="1%" align="right" valign="top" nowrap><strong>*Nome Pai:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_nome_pai" id="var_nome_pai" value="<?php echo($strNomePai); ?>" type="text" size="50" maxlength="250" title="Nome Pai"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top"><strong>*Nome Mãe:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_nome_mae" id="var_nome_mae" value="<?php echo($strNomeMae); ?>" type="text" size="50" maxlength="250" title="Nome Mãe"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DOCUMENTOS</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>CPF:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_cpf" id="var_cpf" onBlur="checkCPF(this.value, true);return false" value="<?php echo($strCPF);?>" type="text" size="20" maxlength="11" title="CPF"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>RG:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_rg" id="var_rg" value="<?php echo($strRG); ?>" type="text" size="20" maxlength="10" title="RG"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>PIS:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_pis" id="var_pis" value="<?php echo($strPIS); ?>" type="text" size="20" maxlength="11" title="PIS"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>CTPS / Série:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_ctps" id="var_ctps" value="<?php echo($strCTPS); ?>" type="text" onKeyPress="formatar(this,'#######/#####');return validateNumKey(event);" size="20" maxlength="13" title="CTPS"><span class="comment_med">&nbsp;(Formato XXXXXXX/ZZZZZ)</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Título Eleitoral:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_titulo_eleitoral" id="var_titulo_eleitoral" value="<?php echo($strTITE); ?>" type="text" size="20" maxlength="30" title="TITULO ELEITORAL"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>ENDEREÇO PRINCIPAL</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*CEP:</strong></td>
								<td nowrap align="left" width="99%">
									<table align="left" width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
										<td width="1%"><input name="var_endprin_cep" id="var_endprin_cep" value="<?php echo($strEndPrinCEP); ?>" type="text" size="12" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);" title="CEP"></td>
										<td width="99%">
											<div style="padding-left:5px;">
												<img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="Javascript:ajaxBuscaCEP('var_endprin_cep','var_endprin_logradouro','var_endprin_bairro','var_endprin_cidade','var_endprin_estado','var_endprin_numero','loader_cep');" style="cursor:pointer" />
												&nbsp;<span id="loader_cep"></span>
											</div>
										</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Logradouro:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_endprin_logradouro" id="var_endprin_logradouro" value="<?php echo($strEndPrinLogradouro); ?>" type="text" size="50" maxlength="255" title="Logradouro"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Num. / Compl.:</strong></td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr>	
										<td nowrap align="left"><input name="var_endprin_numero" id="var_endprin_numero" value="<?php echo($strEndPrinNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
  										<td width="1%" align="right" valign="top"></td>
										<td nowrap align="left" width="99%"><input name="var_endprin_complemento" id="var_endprin_complemento" value="<?php echo($strEndPrinComplemento); ?>" type="text" size="12" maxlength="50" title="Complemento"><span class="comment_med">&nbsp;</span></td>
									</tr>
									</table>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="1%" align="right" valign="top" nowrap><strong>*Bairro:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_endprin_bairro" id="var_endprin_bairro" value="<?php echo($strEndPrinBairro); ?>" type="text" size="20" maxlength="30" title="Bairro"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Cidade:</strong></td>
								<td nowrap>
								<table border="0" cellspacing="0" cellpadding="0" width="100%">
									<tr>	
									<td nowrap align="left"><input name="var_endprin_cidade" id="var_endprin_cidade" value="<?php echo($strEndPrinCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
									<td width="1%" align="right" valign="top" nowrap><strong>*Estado:</strong></td>
									<td nowrap align="left" width="99%">
										<select name="var_endprin_estado" id="var_endprin_estado"  style="width:45px" size="1" title="Estado">
											<option value='' <?php if ($strEndPrinEstado == "") echo("selected"); ?>></option>
											<option value='AC' <?php if ($strEndPrinEstado == "AC") echo("selected"); ?>>AC</option>
											<option value='AL' <?php if ($strEndPrinEstado == "AL") echo("selected"); ?>>AL</option>
											<option value='AM' <?php if ($strEndPrinEstado == "AM") echo("selected"); ?>>AM</option>
											<option value='AP' <?php if ($strEndPrinEstado == "AP") echo("selected"); ?>>AP</option>
											<option value='BA' <?php if ($strEndPrinEstado == "BA") echo("selected"); ?>>BA</option>
											<option value='CE' <?php if ($strEndPrinEstado == "CE") echo("selected"); ?>>CE</option>
											<option value='DF' <?php if ($strEndPrinEstado == "DF") echo("selected"); ?>>DF</option>
											<option value='ES' <?php if ($strEndPrinEstado == "ES") echo("selected"); ?>>ES</option>
											<option value='GO' <?php if ($strEndPrinEstado == "GO") echo("selected"); ?>>GO</option>
											<option value='MA' <?php if ($strEndPrinEstado == "MA") echo("selected"); ?>>MA</option>
											<option value='MG' <?php if ($strEndPrinEstado == "MG") echo("selected"); ?>>MG</option>
											<option value='MS' <?php if ($strEndPrinEstado == "MS") echo("selected"); ?>>MS</option>
											<option value='MT' <?php if ($strEndPrinEstado == "MT") echo("selected"); ?>>MT</option>
											<option value='PA' <?php if ($strEndPrinEstado == "PA") echo("selected"); ?>>PA</option>
											<option value='PB' <?php if ($strEndPrinEstado == "PB") echo("selected"); ?>>PB</option>
											<option value='PE' <?php if ($strEndPrinEstado == "PE") echo("selected"); ?>>PE</option>
											<option value='PI' <?php if ($strEndPrinEstado == "PI") echo("selected"); ?>>PI</option>
											<option value='PR' <?php if ($strEndPrinEstado == "PR") echo("selected"); ?>>PR</option>
											<option value='RJ' <?php if ($strEndPrinEstado == "RJ") echo("selected"); ?>>RJ</option>
											<option value='RN' <?php if ($strEndPrinEstado == "RN") echo("selected"); ?>>RN</option>
											<option value='RO' <?php if ($strEndPrinEstado == "RO") echo("selected"); ?>>RO</option>
											<option value='RR' <?php if ($strEndPrinEstado == "RR") echo("selected"); ?>>RR</option>
											<option value='RS' <?php if ($strEndPrinEstado == "RS") echo("selected"); ?>>RS</option>
											<option value='SC' <?php if ($strEndPrinEstado == "SC") echo("selected"); ?>>SC</option>
											<option value='SE' <?php if ($strEndPrinEstado == "SE") echo("selected"); ?>>SE</option>
											<option value='SP' <?php if ($strEndPrinEstado == "SP") echo("selected"); ?>>SP</option>
											<option value='TO' <?php if ($strEndPrinEstado == "TO") echo("selected"); ?>>TO</option>
										</select><span class="comment_med">&nbsp;</span>
									</td>
									</tr>
								</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*Telefone 1:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_endprin_fone1" id="var_endprin_fone1" value="<?php echo($strEndPrinFone1); ?>" type="text" size="27" maxlength="27" title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>Telefone 2:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_endprin_fone2" id="var_endprin_fone2" value="<?php echo($strEndPrinFone2); ?>" type="text" size="27" maxlength="27" title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<!-- DADOS COMPLEMENTARES -->
							<tr>
								<td align="right">
									<div id="dados_complementares_collapse" style="display:inline;">
										<img src="../img/icon_tree_plus.gif" onClick="showHiddenFields('dados_complementares');" style="cursor:pointer;">
									</div>
								</td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS COMPLEMENTARES</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr>
							<td colspan="2">
							<table cellpadding="2" cellspacing="0" id="dados_complementares" style="display:none;" width="100%">
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="16%" align="right" valign="top" nowrap><strong>E-mail Extra:</strong></td>
									<td nowrap align="left" width="99%"><input name="var_email_extra" id="var_email_extra" value="<?php echo($strEmail);?>" type="text" size="60" maxlength="255" title="e-mail"></td>
								</tr>
							</table>
						</td>
						</tr>
						<!-- FIM DADOS COMPLEMENTARES -->
						<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
								<!--
								<tr bgcolor="#FFFFFF">  
									<td width="1%" align="right" valign="top" nowrap><strong>Telefone 3:</strong></td>
									<td nowrap align="left" width="99%"><input name="var_endprin_fone3" id="var_endprin_fone3" value="<?php echo($strEndPrinFone3); ?>" type="text" size="27" maxlength="27"   title="Telefone 3"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr bgcolor="#FAFAFA">  
									<td width="1%" align="right" valign="top" nowrap><strong>Telefone 4:</strong></td>
									<td nowrap align="left" width="99%"><input name="var_endprin_fone4" id="var_endprin_fone4" value="<?php echo($strEndPrinFone4); ?>" type="text" size="27" maxlength="27"   title="Telefone 4"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td width="1%" align="right" valign="top" nowrap><strong>Telefone 5:</strong></td>
									<td nowrap align="left" width="99%"><input name="var_endprin_fone5" id="var_endprin_fone5" value="<?php echo($strEndPrinFone5); ?>" type="text" size="27" maxlength="27"   title="Telefone 5"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr bgcolor="#FAFAFA">
									<td width="1%" align="right" valign="top" nowrap><strong>Telefone 6:</strong></td>
									<td nowrap align="left" width="99%"><input name="var_endprin_fone6" id="var_endprin_fone6" value="<?php echo($strEndPrinFone6); ?>" type="text" size="27" maxlength="27"   title="Telefone 6"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde">
										<div id="dados_complementares_collapse" style="display:inline;">
											<img src="../img/icon_tree_plus.gif" onClick="showHiddenFields('dados_complementares');" style="cursor:pointer;">
										</div>
										<strong>&nbsp;DADOS COMPLEMENTARES</strong>
									</td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr>
									<td colspan="2">
										<table cellpadding="2" cellspacing="0" id="dados_complementares" style="display:none;" width="100%">
										<tr bgcolor="#FFFFFF">
											<td width="16%" align="right" valign="top" nowrap><strong>e-mail extra:</strong></td>
											<td nowrap align="left" width="99%"><input name="var_email_extra" id="var_email_extra" value="<?php echo($strEmailExtra); ?>" type="text" size="60" maxlength="255" title="e-mail extra"><span class="comment_med">&nbsp;</span></td>
										</tr>
										<tr bgcolor="#FAFAFA">
											<td width="1%" align="right" valign="top" nowrap><strong>Website:</strong></td>
											<td nowrap align="left" width="99%"><input name="var_website" id="var_website" value="<?php echo($strWebsite); ?>" type="text" size="60" maxlength="255" title="Website"><span class="comment_med">&nbsp;</span></td>
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="1%" align="right" valign="top" nowrap><strong>Instrução:</strong></td>
											<td nowrap align="left" width="99%">
												<select name="var_instrucao" id="var_instrucao"  style="width:180px" size="1" title="Instrução">
													<option value="" <?php if ($strInstrucao == "") echo("selected"); ?>></option>
													<option value="1G_INCOMPLETO" <?php if ($strInstrucao == "1G_INCOMPLETO") echo("selected"); ?>><i>1° Grau Incompleto</i></option>
													<option value="1G_COMPLETO" <?php if ($strInstrucao == "1G_COMPLETO") echo("selected"); ?>><i>1° Grau Completo</i></option>
													<option value="2G_INCOMPLETO" <?php if ($strInstrucao == "2G_INCOMPLETO") echo("selected"); ?>><i>2° Grau Incompleto</i></option>
													<option value="2G_COMPLETO" <?php if ($strInstrucao == "2G_COMPLETO") echo("selected"); ?>><i>2° Grau Completo</i></option>
													<option value="CURSO_TECNICO" <?php if ($strInstrucao == "CURSO_TECNICO") echo("selected"); ?>><i>Curso Técnico</i></option>
													<option value="SUP_INCOMPLETO" <?php if ($strInstrucao == "SUP_INCOMPLETO") echo("selected"); ?>><i>Curso Superior Incompleto</i></option>
													<option value="SUP_COMPLETO" <?php if ($strInstrucao == "SUP_COMPLETO") echo("selected"); ?>><i>Curso Superior Completo</i></option>
													<option value="MESTRADO" <?php if ($strInstrucao == "MESTRADO") echo("selected"); ?>><i>Mestrado</i></option>
													<option value="DOUTORADO" <?php if ($strInstrucao == "DOUTORADO") echo("selected"); ?>><i>Doutorado</i></option>
												</select><span class="comment_med">&nbsp;</span>
											</td>
										</tr>
										<tr bgcolor="#FAFAFA">  
											<td width="1%" align="right" valign="top" nowrap><strong>CNH:</strong></td>
											<td nowrap align="left" width="99%"><input name="var_cnh" id="var_cnh" value="<?php echo($strCNH); ?>" type="text" size="30" maxlength="120" title="CNH"><span class="comment_med">&nbsp;</span></td>						
										</tr>
										<tr bgcolor="#FFFFFF">
											<td width="1%" align="right" valign="top" nowrap><strong>PIS:</strong></td>
											<td nowrap align="left" width="99%"><input name="var_pis" id="var_pis" value="<?php echo($strPIS); ?>" type="text" size="30" maxlength="120"   title="PIS"><span class="comment_med">&nbsp;</span></td>
										</tr>
										<tr bgcolor="#FAFAFA">
											<td width="1%" align="right" valign="top" nowrap><strong>CTPS:</strong></td>
											<td nowrap align="left" width="99%"><input name="var_ctps" id="var_ctps" value="<?php echo($strCTPS); ?>" type="text" size="30" maxlength="120"   title="CTPS"><span class="comment_med">&nbsp;</span></td>
										</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde">
										<div id="endereco_secundario_collapse" style="display:inline;">
											<img src="../img/icon_tree_plus.gif" onClick="showHiddenFields('endereco_secundario');" style="cursor:pointer;">
										</div>
										<strong>&nbsp;ENDEREÇO SECUNDÁRIO</strong>
									</td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<!-- DISPLAY BLOCK / UNBLOCK >
								<tr>
								<td colspan="2" align="left">					
									<table id="endereco_secundario" cellpadding="2" cellspacing="0" border="0" style="display:none;" width="100%">
									<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
									<tr>
										<td></td>
										<td height="10" bgcolor="#FFFFFF" valign="top">
										<div class="comment_peq" style="padding: 0px 3px 0px 3px;">Obs: Se seu endereço secundário deve ser o mesmo<br>do endereço principal, clique <div onClick="Javascript:copiaCamposEndereco();" style="display: inline; cursor:pointer;"><u>aqui</u></div></div></td>
									</tr>
									<tr bgcolor="#FAFAFA">  
										<td width="1%" align="right" valign="top" nowrap ><strong>CEP:</strong></td>
										<td nowrap align="left" width="99%">
											<table align="left" width="100%" cellpadding="0" cellspacing="0" border="0">
											<tr>
												<td width="1%"><input name="var_endcom_cep" id="var_endcom_cep" value="<?php echo($strEndComCEP); ?>" type="text" size="12" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);" title="CEP"></td>
												<td width="99%">
													<div style="padding-left:5px;">
														<img src="../img/icon_zoom_disabled.gif" alt="Buscar Cep" onClick="Javascript:ajaxBuscaCEP('var_endcom_cep','var_endcom_logradouro','var_endcom_bairro','var_endcom_cidade','var_endcom_estado','var_endcom_numero','loader_cep_cobr');" style="cursor:pointer" />
														&nbsp;<span id="loader_cep_cobr"></span>
													</div>
												</td>
											</tr>
											</table>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right" valign="top" nowrap><strong>Logradouro:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_logradouro" id="var_endcom_logradouro" value="<?php echo($strEndComLogradouro); ?>" type="text" size="50" maxlength="255" title="Logradouro"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right" valign="top" nowrap><strong>Num. / Compl.:</strong></td>
										<td nowrap>
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endcom_numero" id="var_endcom_numero" value="<?php echo($strEndComNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span></td>
 	 											<td width="1%" align="right" valign="top" nowrap><strong></strong></td>
												<td nowrap align="left" width="99%"><input name="var_endcom_complemento" id="var_endcom_complemento" value="<?php echo($strEndComComplemento); ?>" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span></td>
											</tr>
											</table>
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right" valign="top" nowrap><strong>Bairro:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_bairro" id="var_endcom_bairro" value="<?php echo($strEndComBairro); ?>" type="text" size="20" maxlength="30" title="Bairro"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right" valign="top" nowrap><strong>Cidade:</strong></td>
										<td nowrap>
											<table border="0" cellspacing="0" cellpadding="0" width="100%">
											<tr>	
												<td nowrap align="left"><input name="var_endcom_cidade" id="var_endcom_cidade" value="<?php echo($strEndComCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span></td>
												<td width="1%" align="right" valign="top" nowrap><strong>Estado:</strong></td>
												<td nowrap align="left" width="99%" >
													<select name="var_endcom_estado" id="var_endcom_estado" style="width:45px" size="1" title="Estado">
														<option value=''   <?php if ($strEndComEstado == "")   echo("selected"); ?>></option>
														<option value='AC' <?php if ($strEndComEstado == "AC") echo("selected"); ?>>AC</option>
														<option value='AL' <?php if ($strEndComEstado == "AL") echo("selected"); ?>>AL</option>
														<option value='AM' <?php if ($strEndComEstado == "AM") echo("selected"); ?>>AM</option>
														<option value='AP' <?php if ($strEndComEstado == "AP") echo("selected"); ?>>AP</option>
														<option value='BA' <?php if ($strEndComEstado == "BA") echo("selected"); ?>>BA</option>
														<option value='CE' <?php if ($strEndComEstado == "CE") echo("selected"); ?>>CE</option>
														<option value='DF' <?php if ($strEndComEstado == "DF") echo("selected"); ?>>DF</option>
														<option value='ES' <?php if ($strEndComEstado == "ES") echo("selected"); ?>>ES</option>
														<option value='GO' <?php if ($strEndComEstado == "GO") echo("selected"); ?>>GO</option>
														<option value='MA' <?php if ($strEndComEstado == "MA") echo("selected"); ?>>MA</option>
														<option value='MG' <?php if ($strEndComEstado == "MG") echo("selected"); ?>>MG</option>
														<option value='MS' <?php if ($strEndComEstado == "MS") echo("selected"); ?>>MS</option>
														<option value='MT' <?php if ($strEndComEstado == "MT") echo("selected"); ?>>MT</option>
														<option value='PA' <?php if ($strEndComEstado == "PA") echo("selected"); ?>>PA</option>
														<option value='PB' <?php if ($strEndComEstado == "PB") echo("selected"); ?>>PB</option>
														<option value='PE' <?php if ($strEndComEstado == "PE") echo("selected"); ?>>PE</option>
														<option value='PI' <?php if ($strEndComEstado == "PI") echo("selected"); ?>>PI</option>
														<option value='PR' <?php if ($strEndComEstado == "PR") echo("selected"); ?>>PR</option>
														<option value='RJ' <?php if ($strEndComEstado == "RJ") echo("selected"); ?>>RJ</option>
														<option value='RN' <?php if ($strEndComEstado == "RN") echo("selected"); ?>>RN</option>
														<option value='RO' <?php if ($strEndComEstado == "RO") echo("selected"); ?>>RO</option>
														<option value='RR' <?php if ($strEndComEstado == "RR") echo("selected"); ?>>RR</option>
														<option value='RS' <?php if ($strEndComEstado == "RS") echo("selected"); ?>>RS</option>
														<option value='SC' <?php if ($strEndComEstado == "SC") echo("selected"); ?>>SC</option>
														<option value='SE' <?php if ($strEndComEstado == "SE") echo("selected"); ?>>SE</option>
														<option value='SP' <?php if ($strEndComEstado == "SP") echo("selected"); ?>>SP</option>
														<option value='TO' <?php if ($strEndComEstado == "TO") echo("selected"); ?>>TO</option>
													</select><span class="comment_med">&nbsp;</span>
												</td>
											</tr>
											</table>									
										</td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 1:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_fone1" id="var_endcom_fone1" value="<?php echo($strEndComFone1); ?>" type="text" size="27" maxlength="27"   title="Telefone 1"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 2:</strong></td>
										<td nowrap align="left" width="99%">
											<input name="var_endcom_fone2" id="var_endcom_fone2" value="<?php echo($strEndComFone2); ?>" type="text" size="27" maxlength="27"   title="Telefone 2"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 3:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_fone3" id="var_endcom_fone3" value="<?php echo($strEndComFone3); ?>" type="text" size="27" maxlength="27"   title="Telefone 3"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 4:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_fone4" id="var_endcom_fone4" value="<?php echo($strEndComFone4); ?>" type="text" size="27" maxlength="27"   title="Telefone 4"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FFFFFF">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 5:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_fone5" id="var_endcom_fone5" value="<?php echo($strEndComFone5); ?>" type="text" size="27" maxlength="27"   title="Telefone 5"><span class="comment_med">&nbsp;</span></td>
									</tr>
									<tr bgcolor="#FAFAFA">
										<td width="1%" align="right" valign="top" nowrap><strong>Telefone 6:</strong></td>
										<td nowrap align="left" width="99%"><input name="var_endcom_fone6" id="var_endcom_fone6" value="<?php echo($strEndComFone6); ?>" type="text" size="27" maxlength="27"   title="Telefone 6"><span class="comment_med">&nbsp;</span></td>
									</tr>
									</table>
								</td>
								</tr>
								-->
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA VAGA</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<!--
								<tr bgcolor="#FAFAFA">
									<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;">
										<label for="var_trab_cod_cargo"><strong>Cargo:</strong></label>
									</td>
									<td nowrap align="left" width="99%">
									<select name="var_trab_cod_cargo" style="width:280px" size="1" title="Cargo">
										<option value="" selected="selected"></option>
										<?php //echo(montaCombo($objConn, " SELECT cod_cargo, nome FROM cad_cargo ORDER BY nome ", "cod_cargo", "nome", "", "")); ?>
									</select>
									<span class="comment_med">&nbsp;</span>
									</td>
								</tr>
								-->
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>Categoria:</strong></td>
									<td nowrap>
										<select name="var_categoria" style="width:120px" size="1" title="Tipo" <?php if(getsession(CFG_SYSTEM_NAME."_grp_user") == 'NORMAL'){echo("disabled='disabled'");}?>>
											<option value="" <?php if($strCategoria==""){echo("selected=\"selected\"");}?>></option>
											<option value="GERAL" <?php if($strCategoria=="GERAL"){echo("selected=\"selected\"");}?>>GERAL</option>
											<option value="ESPECIAL" <?php if($strCategoria=="ESPECIAL"){echo("selected=\"selected\"");}?>>ESPECIAL</option>
											<option value="PLENO" <?php if($strCategoria=="PLENO"){echo("selected=\"selected\"");}?>>PLENO</option>
										</select>
										<input type="hidden" name="var_categoria_hidden" value="<?php echo($strCategoria)?>" />
										<span class="comment_med">&nbsp;</span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td></td>
									<td height="10" valign="top">
										<span class="comment_peq">
										<?php echo(getTText("obs_cad_arquivo_login",C_NONE));?>
										</span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><b><?php echo(getTText("documento_1",C_NONE));?>:</b></td>
									<td>
										<input type="text" name="dbvar_str_arquivo_1" id="dbvar_str_arquivo_1" size="40" readonly="readonly" value="<?php echo($strARQUIVO1);?>">
										&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor','dbvar_str_arquivo_1','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspf/','','');">
										&nbsp;<span class="comment_med" style="font-style:italic;color:#CCC;cursor:pointer;" onClick="document.getElementById('dbvar_str_arquivo_1').value = '';"><?php echo(getTText("limpar",C_NONE));?></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><b><?php echo(getTText("documento_2",C_NONE));?>:</b></td>
									<td>
										<input type="text" name="dbvar_str_arquivo_2" id="dbvar_str_arquivo_2" size="40" readonly="readonly" value="<?php echo($strARQUIVO2);?>">
										&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor','dbvar_str_arquivo_2','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspf/','','');">
										&nbsp;<span class="comment_med" style="font-style:italic;color:#CCC;cursor:pointer;" onClick="document.getElementById('dbvar_str_arquivo_2').value = '';"><?php echo(getTText("limpar",C_NONE));?></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td align="right"><b><?php echo(getTText("documento_3",C_NONE));?>:</b></td>
									<td>
										<input type="text" name="dbvar_str_arquivo_3" id="dbvar_str_arquivo_3" size="40" readonly="readonly" value="<?php echo($strARQUIVO3);?>">
										&nbsp;<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor','dbvar_str_arquivo_3','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/docspf/','','');">
										&nbsp;<span class="comment_med" style="font-style:italic;color:#CCC;cursor:pointer;" onClick="document.getElementById('dbvar_str_arquivo_3').value = '';"><?php echo(getTText("limpar",C_NONE));?></span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>*Função:</strong></td>
									<td nowrap align="left" width="99%">
									<input type="text" name="var_trab_funcao" id="var_trab_funcao" size="60" maxlength="120" value="<?php echo($strFuncao);?>" />
									<!--<select name="" style="width:280px" size="1" title="Função" disabled="disabled">
										<option value="" selected="selected"></option>
										<?php //echo(montaCombo($objConn, " SELECT DISTINCT(funcao) AS funcao FROM relac_pj_pf WHERE funcao IS NOT NULL AND funcao NOT LIKE '' ORDER BY funcao ", "funcao", "funcao", $strFuncao, "")); ?>
									</select>-->
									<span class="comment_med">&nbsp;</span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>Departamento:</strong></td>
									<td nowrap><input name="var_trab_departamento" value="<?php echo($strDepartamento);?>" type="text" size="50" maxlength="100" title="Departamento"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>*Tipo:</strong></td>
									<td nowrap>
										<select name="var_trab_tipo" style="width:120px" size="1" title="Tipo">
											<option value="" <?php if($strTipo == ""){echo("selected=\"selected\"");}?>></option>
											<?php if((getsession(CFG_SYSTEM_NAME."_grp_user") == "ADMIN") || (getsession(CFG_SYSTEM_NAME."_grp_user") == "SU")){?>
											<option value="AUTONOMO" <?php if($strTipo == "AUTONOMO"){echo("selected=\"selected\"");}?>>AUTÔNOMO</option>
											<option value="AVULSO" <?php if($strTipo == "AVULSO"){echo("selected=\"selected\"");}?>>AVULSO</option>
											<?php }?>
											<option value="TEMPORARIO" <?php if($strTipo == "TEMPORARIO"){echo("selected=\"selected\"");}?>>TEMPORÁRIO</option>
											<option value="EMPREGADO" <?php if($strTipo == "EMPREGADO"){echo("selected=\"selected\"");}?>>EMPREGADO</option>
											<option value="ESTAGIO" <?php if($strTipo == "ESTAGIO"){echo("selected=\"selected\"");}?>>ESTAGIÁRIO</option>
										</select>
										<span class="comment_med">&nbsp;</span>
									</td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>*Data Admissão:</strong></td>
									<td nowrap><input name="var_trab_dt_admissao" id="var_trab_dt_admissao" value="<?php echo(dDate(CFG_LANG,$dtAdmissao,false)); ?>" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" title="data admissão">&nbsp;&nbsp;<!--a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a><span class="comment_med">&nbsp;</span><span class="comment_med">&nbsp;</span--></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>Data Demissão:</strong></td>
									<td nowrap><input name="var_trab_dt_demissao" id="var_trab_dt_demissao" value="" type="text" size="10" maxlength="10" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" title="data de demissão">&nbsp;&nbsp;<!--a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calendário"></a-->
									<br /><span class="comment_med">Preenchendo este campo, o colaborador não aparecerá mais na <br />listagem de colaboradores do painel da Afiliada, somente na <br />listagem completa de colaboradores.</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>Obs:</strong></td>
									<td nowrap align="left" width="99%"><textarea name="var_trab_obs" cols="60" rows="5" title="Obs"><?php echo($strObsVaga);?></textarea><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr><td colspan="2" height="5" bgcolor="#FFFFFF"></td></tr>
								
								<?php if((getsession(CFG_SYSTEM_NAME."_grp_user") == "ADMIN") || (getsession(CFG_SYSTEM_NAME."_grp_user") == "SU")){?>
								<tr>
									<td></td>
									<td align="left" valign="top" class="destaque_gde"><strong>INATIVAR COLABORADOR</strong></td>
								</tr>
								<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
								<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="20%" align="right" valign="top" nowrap><strong>Situação:</strong></td>
									<td nowrap>
										<select name="var_situacao_colab" id="var_situacao_colab" style="width:70px;" onChange="setInativo();">
											<option value="ATIVO">ATIVO</option>
											<option value="INATIVO" <?php echo(($dtInativo == "") ? "" : "selected='selected'");?>>INATIVO</option>
										</select>
									</td>
								</tr>
							</table>	
							<table cellpadding="0" cellspacing="4" width="100%" border="0" id="table_inativo" <?php echo(($dtInativo == "") ? "style='display:none;'" : "style='display:block;'" );?>>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>*Data Inativação:</strong></td>
									<td nowrap style="padding-left:5px;"><input name="var_dt_inativo" id="var_dt_inativo" value="<?php echo(dDate(CFG_LANG,$dtInativo,false));?>" type="text" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);" size="12" maxlength="10" title="Data de Inativação"><span class="comment_med">&nbsp;</span></td>
								</tr>
								<tr bgcolor="<?php getLineColor($strColor);?>">
									<td width="1%" align="right" valign="top" nowrap><strong>*Motivo Inativação:</strong></td>
									<td nowrap style="padding-left:5px;">
										<textarea name="var_motivo_inativo" id="var_motivo_inativo" rows="5" cols="60"><?php echo($strMotivo);?></textarea>
										<br /><span class="comment_med">&nbsp;</span>
									</td>
								</tr>
							<?php }?>
							</table>
							<table width="100%" border="0">
								<tr><td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px"><?php echo(getTText("campos_obrig",C_NONE)); ?></td></tr>
								<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
							</table>
						</td>
					</tr>
					<tr>
						<td align="right" colspan="3" style="padding:10px 30px 10px 10px;">
							<button onClick="verifica(); return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
							<button onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
						</td>
					</tr>					
		  		</form>
			</table>
			<?php athEndFloatingBox();?>
		</td>
	</tr>
</table>
</body>
</html>
<iframe name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="../_class/calendar/source/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;"></iframe>
<?php $objConn = NULL; ?>
