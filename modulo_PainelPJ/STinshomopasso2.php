<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	
	// REQUESTS
	$intCodPJ = request("var_cod_pj");
	$intCodPF = request("var_cod_pf");
	$strCPF   = request("var_cpf");

	// ABERTURA DE CONEX�O COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// Tipo inser��o ou n�o
	if($intCodPF == ""){
		// Define at� R�TULO
		$strRotulo 		  = "Cadastrar e Solicitar Homologa��o";
		$strNome  		  = "";
		$strApelido		  = "";
		$dtDataNasc       = "";
		$strSexo  		  = "m";
		$strEmail         = "";
		$strEmailExtra    = "";
		$strWebsite       = "";
		$strFoto          = "";
		$strEstadoCivil   = "";
		$strInstrucao     = "";
		$strNacionalidade = "";
		$strNaturalidade  = "";
		$strObs           = "";
		$strStatus        = "";
		$strNomePai       = "";
		$strNomeMae       = "";
		
		$strCPF  = $strCPF;
		$strRG   = "";
		$strCNH  = "";
		$strPIS  = "";
		$strCTPS = "";
		$strTITE = "";
		
		$strEndPrinCEP         = "";
		$strEndPrinLogradouro  = "";
		$strEndPrinNumero      = "";
		$strEndPrinComplemento = "";
		$strEndPrinBairro      = "";
		$strEndPrinCidade      = "";
		$strEndPrinEstado      = "SP";
		$strEndPrinPais        = "Brasil";
		$strEndPrinFone1       = "";
		$strEndPrinFone2       = "";
		$strEndPrinFone3       = "";
		$strEndPrinFone4       = "";
		$strEndPrinFone5       = "";
		$strEndPrinFone6       = "";
		
		$strEndComCEP         = "";
		$strEndComLogradouro  = "";
		$strEndComNumero      = "";
		$strEndComComplemento = "";
		$strEndComBairro      = "";
		$strEndComCidade      = "";
		$strEndComEstado      = "";
		$strEndComPais        = "";
		$strEndComFone1       = "";
		$strEndComFone2       = "";
		$strEndComFone3       = "";
		$strEndComFone4       = "";
		$strEndComFone5       = "";
		$strEndComFone6       = "";
	} 
	else{
		// Tipo de Altera��o
		$strRotulo = "Alterar e Solicitar Homologa��o";
		
		// Localiza os DADOS da PF atual
		try {
			$strSQL  = " SELECT t1.nome, t1.data_nasc, t1.sexo, t1.email, t1.email_extra, t1.website, t1.foto, t1.apelido ";
			$strSQL .= "      , t1.nome_pai, t1.nome_mae, t1.estado_civil, t1.instrucao, t1.nacionalidade, t1.naturalidade, t1.obs ";
			$strSQL .= "      , t1.rg, t1.cnh, t1.pis, t1.ctps, t1.titulo_eleitoral ";
			$strSQL .= "      , t1.endprin_cep, t1.endprin_logradouro, t1.endprin_numero, t1.endprin_complemento, t1.endprin_bairro ";
			$strSQL .= "      , t1.endprin_cidade, t1.endprin_estado, t1.endprin_pais, t1.endprin_fone1, t1.endprin_fone2 ";
			$strSQL .= "      , t1.endprin_fone3, t1.endprin_fone4, t1.endprin_fone5, t1.endprin_fone6 ";
			$strSQL .= "      , t1.endcom_cep, t1.endcom_logradouro, t1.endcom_numero, t1.endcom_complemento, t1.endcom_bairro ";
			$strSQL .= "      , t1.endcom_cidade, t1.endcom_estado, t1.endcom_pais, t1.endcom_fone1, t1.endcom_fone2 ";
			$strSQL .= "      , t1.endcom_fone3, t1.endcom_fone4, t1.endcom_fone5, t1.endcom_fone6 ";
			$strSQL .= " FROM cad_pf t1 ";
			$strSQL .= " WHERE t1.cod_pf = " . $intCodPF;
			
			$objResult = $objConn->query($strSQL);
			
			if ($objResult->rowCount() > 0) {
				// Fetch dos dados localizados
				$objRS = $objResult->fetch();
				
				// Passagem para vari�veis
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
				
				$strCPF  = $strCPF;
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
	
	// fun��o para cores de linhas
	function getLineColor(&$prColor){
		$prColor = ($prColor == CL_CORLINHA_1) ? CL_CORLINHA_2 : CL_CORLINHA_1;
		echo($prColor);
	}
?> 
<html>
<head>
	<title><?php echo(CFG_SYSTEM_TITLE); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../_css/<?php echo(CFG_SYSTEM_NAME); ?>.css" rel="stylesheet" type="text/css">
	<script language="JavaScript" type="text/javascript">
		<!--
		function verifica(){
			var strMSG = "";
			// CABE�ALHO DE DADOS PRINCIPAIS
			if(
			  	(document.formeditor.var_cod_pj.value == "") ||
				(document.formeditor.var_nome.value   == "") ||
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
			/*if(document.formeditor.var_apelido.value   == ""){	strMSG += "\nNome Credencial";    }*/
			if(document.formeditor.var_sexo.value   == ""){	strMSG += "\nSexo";    }
			if(document.formeditor.var_data_nasc.value == ""){ strMSG += "\nData de Nascimento"; }
			if(getDDiff(getNow(),getVDate(document.formeditor.var_data_nasc.value,'ptb'))>0){	strMSG += "\nData de Nascimento Maior que Data Atual";    }
			// if(document.formeditor.var_foto.value   == ""){	strMSG += "\nFoto";    }
			if(document.formeditor.var_estado_civil.value   == ""){	 strMSG += "\nEstado Civil";    }
			if(document.formeditor.var_nacionalidade.value   == ""){ strMSG += "\nNacionalidade";    }
			if(document.formeditor.var_naturalidade.value   == ""){ strMSG += "\nNaturalidade";    }
			
			// CABE�ALHO FILIA��O
			if(
			  	(document.formeditor.var_nome_mae.value == "") ||
				(document.formeditor.var_nome_pai.value == "")
			  ){ strMSG += "\n\nFILIA��O:" }
			if(document.formeditor.var_nome_mae.value == ""){	strMSG += "\nNome da M�e"; }
			if(document.formeditor.var_nome_pai.value == ""){	strMSG += "\nNome do Pai"; }
			
			// CABE�ALHO DE DOCUMENTOS			
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
			if(document.formeditor.var_ctps.value   == ""){	strMSG += "\nCTPS / S�rie";      }		
			if(document.formeditor.var_titulo_eleitoral.value   == ""){	strMSG += "\nT�tulo Eleitoral";  }		
			
			// CABE�ALHO DE ENDERE�O PRINCIPAL			
			if(
				(document.formeditor.var_endprin_cep.value    == "") ||
				(document.formeditor.var_endprin_numero.value == "") ||
				(document.formeditor.var_endprin_bairro.value == "") ||
				(document.formeditor.var_endprin_cidade.value == "") ||
				(document.formeditor.var_endprin_estado.value == "") ||
				(document.formeditor.var_endprin_pais.value   == "") ||
				(document.formeditor.var_endprin_fone1.value  == "") 
			  ){ strMSG += "\n\nENDERE�O PRINCIPAL:";  }
			if(document.formeditor.var_endprin_cep.value        == ""){ strMSG += "\nCEP";             }
			if(document.formeditor.var_endprin_logradouro.value == ""){ strMSG += "\nLogradouro";      }
			if(document.formeditor.var_endprin_numero.value 	== ""){	strMSG += "\nN�mero";          }
			if(document.formeditor.var_endprin_bairro.value 	== ""){ strMSG += "\nBairro";          }
			if(document.formeditor.var_endprin_cidade.value 	== ""){ strMSG += "\nCidade";          }
			if(document.formeditor.var_endprin_estado.value 	== ""){	strMSG += "\nEstado";          }
			if(document.formeditor.var_endprin_pais.value	 	== ""){ strMSG += "\nPa�s";            }
			if(document.formeditor.var_endprin_fone1.value 		== ""){ strMSG += "\nTelefone Um (1)"; }
			
			// CABE�ALHO DE FUN��O DO COLABORADOR
			if(
				(document.formeditor.var_trab_funcao.value      == "")||
				(document.formeditor.var_trab_tipo.value 		== "")||
				(document.formeditor.var_trab_dt_admissao.value == "")||
				(getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'))>0)
			  ){ strMSG += "\n\nDADOS DA VAGA:" }
			if(document.formeditor.var_trab_funcao.value      == ""){ strMSG += "\nFun��o";           }
			if(document.formeditor.var_trab_tipo.value 		  == ""){ strMSG += "\nTipo"; }
			if(document.formeditor.var_trab_dt_admissao.value == ""){ strMSG += "\nData de Admiss�o"; }
			if(getDDiff(getNow(),getVDate(document.formeditor.var_trab_dt_admissao.value,'ptb'))>0){ strMSG += "\nData de Admiss�o maior que Data Atual"; }
			
			// CABE�ALHO DE DADOS DO AGENDAMENTO
			if(
				(document.formeditor.var_dtt_ped_agendamento_homo.value == "") ||
				(document.formeditor.var_ped_hr_agendamento_homo.value  == "")
			  ){ strMSG += "\n\nDADOS DO AGENDAMENTO:" }
			if(document.formeditor.var_dtt_ped_agendamento_homo.value == ""){ strMSG += "\nData de Agendamento"; }
			if(document.formeditor.var_ped_hr_agendamento_homo.value  == ""){ strMSG += "\nHor�rio do Agendamento"; }
			
			if (strMSG == "") {
				//document.formeditor.action.value = "";
				document.formeditor.submit();
			}
			else {
				alert("Informar os campos obrigat�rios abaixo:" + strMSG);
			}
		}
		
		function cancelar(){
			window.location= "../modulo_PainelPJ/STindex.php";
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
		
		function ajaxGetHorarios(prDia){
			// alert(prDia);
			if(prDia == null || prDia == ""){ return(null); } 
			var objAjax;
			var strReturnValue;
			
			document.getElementById("loader_ajax").innerHTML = "<img src='../img/icon_ajax_loader.gif' border='0' />";
						
			objAjax = createAjax();
			objAjax.onreadystatechange = function() {
				if(objAjax.readyState == 4) {
					if(objAjax.status == 200) {
						strReturnValue = objAjax.responseText.replace(/^\s*|\s*$/,"");
						// alert(strReturnValue);
						// alert(strReturnValue.search(/@@/ig));
						
						if(strReturnValue.search(/@@/ig) != -1){						
							document.getElementById("loader_ajax").innerHTML = "<span style='color:red;font-size:09px;'>(Nenhum Hor�rio Dispon�vel Neste Dia!)</span>";
							setTimeout("document.getElementById('loader_ajax').innerHTML = '';",4000);
							return(null);
						}
											
						// alert(strReturnValue);
						// Cria uma op��o em branco
						var optionBlank   = document.createElement('option');
						optionBlank.text  = "...";
						var obj 		  = document.getElementById("var_ped_hr_agendamento_homo");
						obj.add(optionBlank);
						
						// Dados
						var Item1, Item2, prDados;
						var arrAux1 = null;
						var arrAux2 = null;
						prDados = strReturnValue;
						arrAux1 = prDados.split("|");
																	
						if(prDados.length > 1) {
							for(Item1 in arrAux1) {
								Item2 = arrAux1[Item1];
								//arrAux2 = Item2.split("|");
							
								var optionNew = document.createElement('option');
								optionNew.setAttribute('value',Item2);
								var textOption =  document.createTextNode(Item2);
								optionNew.appendChild(textOption);
								obj.appendChild(optionNew);
							
								//obj.add( new Option(caption,value) );
								//obj.add( new Option(arrAux2[1],arrAux2[0]) );
							}
						}
						document.getElementById("loader_ajax").innerHTML = "";					
					} else {
						alert("Erro no processamento da p�gina: " + objAjax.status + "\n\n" + objAjax.responseText);
					}
				}
			}
			objAjax.open("GET", "../_ajax/STreturnhorarios.php?var_dia=" + prDia,  true); 
			objAjax.send(null); 
		}
		
		//-->
	</script>
</head>
<body bgcolor="#FFFFFF" background="../img/bgFrame_<?php echo(CFG_SYSTEM_THEME); ?>_main.jpg" style="margin:10px 0px 10px 0px;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%" align="center">
	<tr>
   		<td align="center" valign="top">
		<?php athBeginFloatingBox("720","none","NOVA HOMOLOGA��O (" . $strRotulo . ")",CL_CORBAR_GLASS_1); ?>
			<table border="0" width="700" bgcolor="#FFFFFF" style="border:1px #A6A6A6 solid;" cellspacing="0" cellpadding="4">
	   			<form name="formeditor" action="STinshomopasso2exec.php" method="post">
				<input type="hidden" name="var_cod_pj" 	     value="<?php echo($intCodPJ); ?>">
				<input type="hidden" name="var_cod_pf" 		 value="<?php echo($intCodPF); ?>">
				<input type="hidden" name="var_cpf" 		 value="<?php echo($strCPF); ?>">
				<input type="hidden" name="var_endprin_pais" value="BRASIL">
				<input type="hidden" name="var_endcom_pais"  value="BRASIL">
				<tr> 
		  			<td align="center" valign="top" style="padding:20px 80px 0px 80px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="4">
							<tr><td colspan="2"><b>Preencha os campos abaixo:</b></td></tr>
							<tr><td colspan="2" height="10">&nbsp;</td></tr>
							
							<!-- BLOCO: DADOS PRINCIPAIS -->
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS PRINCIPAIS</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="25%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Nome:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_nome" id="var_nome" value="<?php echo($strNome); ?>" type="text" size="50" maxlength="100" title="nome"><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Nome Credencial:</strong></td>
								<td nowrap align="left" width="99%" ><input name="var_apelido" id="var_apelido" value="<?php echo($strApelido); ?>" type="text" size="30" maxlength="20"   title="Nome Credencial"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Nascimento:</strong></td>
								<td nowrap align="left" valign="top" width="99%" >
									<input name="var_data_nasc" id="var_data_nasc" value="<?php echo($dtDataNasc); ?>" type="text" size="10" maxlength="10" title="data nascimento" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<!--a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_data_nasc);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calend�rio"></a-->
									&nbsp;<span class="comment_med">(Formato dd/mm/aaaa)</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Sexo:</strong></td>
								<td nowrap align="left" width="99%" >
									<select name="var_sexo" id="var_sexo"  style="width:120px" size="1" title="Sexo">
										<option value="m" <?php if($strSexo=="m") echo("selected");?>><i>MASCULINO</i></option>
										<option value="f" <?php if($strSexo=="f") echo("selected");?>><i>FEMINININO</i></option>
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
									<option value="VIUVO" <?php if($strEstadoCivil == "VIUVO")echo("selected");?>><i>Vi�vo(a)</i></option>
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
								<td width="1%" align="right" valign="top" nowrap><strong>Foto:</strong></td>
								<td>
									<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td>
											<input type="text" name="var_foto" id="var_foto" value="<?php echo($strFoto); ?>" size="50" readonly="true" title="Foto">
											<span class="comment_med">Clique <span style="font-weight:bold;cursor:pointer;" onClick="document.formeditor.var_foto.value='';"><u>aqui</u></span> para limpar o campo foto</span>
										</td>
										<td nowrap align="left" width="99%" valign="top">
											<input type="button" name="btn_uploader" value="Procurar..." class="inputclean" onClick="callUploader('formeditor','var_foto','/<?php echo getSession(CFG_SYSTEM_NAME . "_dir_cliente"); ?>/upload/fotospf/','','');">
										</td>
									</tr>
									</table>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Obs:</strong></td>
								<td nowrap align="left" width="99%" >
									<textarea name="var_obs" id="var_obs" cols="60" rows="5"   title="Obs"><?php echo($strObs); ?></textarea><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<!-- BLOCO: FILIA��O -->
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>FILIA��O</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">  
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Nome Pai:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_nome_pai" id="var_nome_pai" value="<?php echo($strNomePai); ?>" type="text" size="50" maxlength="250" title="Nome Pai"><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Nome M�e:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_nome_mae" id="var_nome_mae" value="<?php echo($strNomeMae); ?>" type="text" size="50" maxlength="250" title="Nome M�e"><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DOCUMENTOS</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*CPF:</strong></td>
								<td nowrap align="left" width="99%" ><?php echo($strCPF); ?><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*RG:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_rg" id="var_rg" value="<?php echo($strRG); ?>" type="text" size="20" maxlength="10" title="RG"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*PIS:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_pis" id="var_pis" value="<?php echo($strPIS); ?>" type="text" size="20" maxlength="11" title="PIS"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*CTPS / S�rie:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_ctps" id="var_ctps" value="<?php echo($strCTPS); ?>" type="text" onKeyPress="formatar(this,'#######/#####');return validateNumKey(event);" size="20" maxlength="13" title="CTPS"><span class="comment_med">&nbsp;(Formato XXXXXXX/ZZZZZ)</span></td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap><strong>*T�tulo Eleitoral:</strong></td>
								<td nowrap align="left" width="99%"><input name="var_titulo_eleitoral" id="var_titulo_eleitoral" value="<?php echo($strTITE); ?>" type="text" size="20" maxlength="30" title="TITULO ELEITORAL"><span class="comment_med">&nbsp;</span></td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<!-- BLOCO: ENDERE�O PRINCIPAL -->							
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>ENDERE�O PRINCIPAL</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*CEP:</strong></td>
								<td nowrap align="left" width="99%">
									<table align="left" width="100%" cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td width="1%">
												<input name="var_endprin_cep" id="var_endprin_cep" value="<?php echo($strEndPrinCEP); ?>" type="text" size="12" maxlength="8"  onkeypress="Javascript:return validateNumKey(event);" title="CEP">
											</td>
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
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Logradouro:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_endprin_logradouro" id="var_endprin_logradouro" value="<?php echo($strEndPrinLogradouro); ?>" type="text" size="50" maxlength="255"   title="Logradouro"><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Num. / Compl.:</strong></td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left">
												<input name="var_endprin_numero" id="var_endprin_numero" value="<?php echo($strEndPrinNumero); ?>" type="text" size="5" maxlength="20" title="Num. / Compl."><span class="comment_med">&nbsp;</span>
											</td>
			  								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"></td>
											<td nowrap align="left" width="99%" >
												<input name="var_endprin_complemento" id="var_endprin_complemento" value="<?php echo($strEndPrinComplemento); ?>" type="text" size="12" maxlength="50"   title="Complemento"><span class="comment_med">&nbsp;</span>
											</td>
										</tr>
									</table>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Bairro:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_endprin_bairro" id="var_endprin_bairro" value="<?php echo($strEndPrinBairro); ?>" type="text" size="20" maxlength="30"   title="Bairro"><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Cidade:</strong></td>
								<td nowrap>
									<table border="0" cellspacing="0" cellpadding="0" width="100%">
										<tr>	
											<td nowrap align="left">
												<input name="var_endprin_cidade" id="var_endprin_cidade" value="<?php echo($strEndPrinCidade); ?>" type="text" size="20" maxlength="30" title="Cidade"><span class="comment_med">&nbsp;</span>
											</td>
			  								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Estado:</strong></td>
											<td nowrap align="left" width="99%" >
												<select name="var_endprin_estado" id="var_endprin_estado"  style="width:45px" size="1" title="Estado">
													<option value="" <?php if($strEndPrinEstado == ""){ echo("selected='selected'"); }?>></option>
													<?php echo(montaCombo($objConn,"SELECT sigla_estado FROM lc_estado ORDER BY sigla_estado","sigla_estado","sigla_estado",$strEndPrinEstado));?>
												</select><span class="comment_med">&nbsp;</span>									
											</td>
										</tr>
									</table>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Telefone 1:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_endprin_fone1" id="var_endprin_fone1" value="<?php echo($strEndPrinFone1); ?>" type="text" size="15" maxlength="12" onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);"  title="Telefone 1">
									&nbsp;<span class="comment_med">(Formato XX 2340-8976)</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Telefone 2:</strong></td>
								<td nowrap align="left" width="99%" >
									<input name="var_endprin_fone2" id="var_endprin_fone2" value="<?php echo($strEndPrinFone2); ?>" type="text" size="15" maxlength="12"  onKeyPress="formatar(this,'## ####-####');return validateNumKey(event);" title="Telefone 2">
									&nbsp;<span class="comment_med">(Formato XX 2340-8976)</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
										
							<!-- BLOCO: DADOS COMPLEMENTARES -->
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
									<td width="25%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>E-mail:</strong></td>
									<td nowrap align="left" width="99%">
										<input name="var_email" id="var_email" value="<?php echo($strEmail); ?>" type="text" size="60" maxlength="255" title="e-mail">
									</td>
								</tr>
							</table>
							</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<!-- BLOCO: DADOS DA VAGA -->	
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>DADOS DA VAGA</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Categoria:</strong></td>
								<td nowrap>
									<select name="var_categoria" style="width:120px" size="1" title="Tipo" <?php if(getsession(CFG_SYSTEM_NAME."_grp_user") == 'NORMAL'){echo("disabled='disabled'");}?>>
										<option value=""></option>
										<option value="GERAL" selected="selected">GERAL</option>
										<option value="ESPECIAL">ESPECIAL</option>
										<option value="PLENO">PLENO</option>
									</select>
									<input type="hidden" name="var_categoria_hidden" value='GERAL' />
									<span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Fun��o:</strong></td>
								<td nowrap align="left" width="99%" >
									<input type="text" name="var_trab_funcao" id="var_trab_funcao" size="60" maxlength="120" />
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Departamento:</strong></td>
								<td nowrap>
									<input name="var_trab_departamento" value="" type="text" size="60" maxlength="100" title="Departamento"><span class="comment_med">&nbsp;</span>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Tipo:</strong></td>					
								<td nowrap>
									<select name="var_trab_tipo" style="width:120px" size="1" title="Tipo">
										<option value="" selected="selected"></option>
										<?php if((getsession(CFG_SYSTEM_NAME."_grp_user") == "ADMIN") || (getsession(CFG_SYSTEM_NAME."_grp_user") == "SU")){?>
											<option value="AUTONOMO">AUT�NOMO</option>
											<option value="AVULSO">AVULSO</option>
										<?php }?>
										<option value="TEMPORARIO">TEMPOR�RIO</option>
										<option value="EMPREGADO">EMPREGADO</option>
										<option value="ESTAGIO">ESTAGI�RIO</option>
									</select><span class="comment_med">&nbsp;</span>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>*Admiss�o:</strong></td>
								<td nowrap>
									<input name="var_trab_dt_admissao" value="" type="text" size="10" maxlength="10" title="Data Admiss�o" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<!--a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formeditor.var_trab_dt_admissao);return false;"><img class="PopcalTrigger" align="absmiddle" src="../img/bullet_dataatual.gif" border="0" alt="" style="cursor:hand" title="ver calend�rio"></a-->
									&nbsp;<span class="comment_med">(Formato dd/mm/aaaa)</span>									
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" nowrap style="padding-right:5px;"><strong>Obs:</strong></td>
								<td nowrap align="left" width="99%" >
									<textarea name="var_trab_obs" cols="60" rows="5" title="Obs"></textarea><span class="comment_med">&nbsp;</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF">&nbsp;</td></tr>
							
							
							<!-- BLOCO: AGENDAMENTO DA HOMOLOGA��O -->
							<tr>
								<td></td>
								<td align="left" valign="top" class="destaque_gde"><strong>AGENDAMENTO DA HOMOLOGA��O</strong></td>
							</tr>
							<tr><td colspan="2" height="2" align="center" background="../img/line_dialog.jpg"></td></tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" style="padding-right:5px;"><strong>*Pr�ximos <?php echo(getVarEntidade($objConn,"ag_intervalo_busca_horarios"));?> dias de Atendimento:</strong></td>
								<td>
									<select name="var_dtt_ped_agendamento_homo" style="width:170px;" onChange="limpaSelect('var_ped_hr_agendamento_homo');ajaxGetHorarios(this.value);">
										<option value="" selected="selected">Selecione uma Data...</option>
										<?php if(getVarEntidade($objConn,"ag_atendimento_fds") != 1){?>
											<?php if((getWeekDay($strDATE) != "sabado")&&(getWeekDay($strDATE) != "domingo")){?>
											<option value="<?php echo(date("d-m-Y"));?>"><?php echo("Dia ".dDate(CFG_LANG,now(),false)." - ".ucwords(getWeekDay(date("Y-m-d")))."-feira");?></option>
											<?php }?>
										<?php }?>
										<?php for($auxCounter = 1; $auxCounter <= getVarEntidade($objConn,"ag_intervalo_busca_horarios"); $auxCounter++){?>
										<?php $strDATE = cDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false); ?>
										<?php $arrDATE = explode("-",$strDATE); ?>
										<?php $strDATE = $arrDATE[2]."-".$arrDATE[1]."-".$arrDATE[0];?>
										<?php if(getVarEntidade($objConn,"ag_atendimento_fds") != 1){?>
											<?php if((getWeekDay($strDATE) == "sabado")||(getWeekDay($strDATE) == "domingo")){ continue; }?>
										<?php }?>
										<option value="<?php echo(cDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false));?>">
											<?php 
												echo("Dia ".dDate(CFG_LANG,dateAdd("d",$auxCounter,now()),false));
												echo(((getWeekDay($strDATE) == "sabado")||(getWeekDay($strDATE) == "domingo")) ? " - ".ucwords(getWeekDay($strDATE)) : " - ".ucwords(getWeekDay($strDATE))."-feira");
											?>
										</option>
										<!-- $strSQL = "SELECT ag_agenda.cod_agenda FROM ag_agenda WHERE DATE(ag_agenda.prev_dtt_ini) = CURRENT_DATE + ".$auxCounter." AND ag_agenda.dtt_realizado IS NULL";echo($strSQL."<br />"); -->
										<?php }?>
									</select>
									<!--input name="var_dtt_ped_agendamento_homo" value="" type="text" size="12" maxlength="10" title="Data de Agendamento" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<input type="text" name="var_ped_hr_agendamento_homo" size="5" maxlength="5" onKeyPress="FormataInputHoraMinuto(this,event);" /-->
								</td>
							</tr>
							<tr bgcolor="<?php getLineColor($strColor);?>">
								<td width="1%" align="right" valign="top" style="padding-right:5px;"><strong>*Hor�rios Dispon�veis:</strong></td>
								<td>
									<select name="var_ped_hr_agendamento_homo" id="var_ped_hr_agendamento_homo" style="width:70px"></select>
									&nbsp;
									<span id="loader_ajax"></span>
									<!--input name="var_dtt_ped_agendamento_homo" value="" type="text" size="12" maxlength="10" title="Data de Agendamento" onKeyPress="return validateNumKey(event);" onKeyDown="FormataInputData(this,event);">&nbsp;&nbsp;<input type="text" name="var_ped_hr_agendamento_homo" size="5" maxlength="5" onKeyPress="FormataInputHoraMinuto(this,event);" /-->
									<br /><span class="comment_med">Observa��es:</span>
									<br /><span class="comment_med">&bull;&nbsp;<?php echo(getTText("horario_atendimento",C_NONE));?></span>
									<br /><span class="comment_med">&bull;&nbsp;Este agendamento ser� confirmado posteriormente pelo devido respons�vel, portanto podem haver altera��es. Caso sua disponibilidade n�o se enquadre com os hor�rios e dias dispon�veis, por favor entre em contato com nossa sede.</span>
								</td>
							</tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr><td colspan="2" height="10" bgcolor="#FFFFFF"></td></tr>
							<tr>
								<td height="10" colspan="2" class="destaque_med" style="padding-top:5px; padding-right:25px">
									<?php echo(getTText("campos_obrig",C_NONE));?>
								</td>
							</tr>
							<tr><td height="1" colspan="2" bgcolor="#DBDBDB"></td></tr>																					
						</table>
					</td>
				</tr>
				<tr>
					<td align="right" colspan="3" style="padding:10px 70px 10px 30px;">
						<button onClick="verifica();return false;"><?php echo(getTText("ok",C_UCWORDS)); ?></button>
						<button	onClick="cancelar();return false;"><?php echo(getTText("cancelar",C_UCWORDS)); ?></button>
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