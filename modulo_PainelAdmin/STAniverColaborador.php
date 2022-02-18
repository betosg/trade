<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athutils.php");

// REQUEST DE CARGO
$strTipocargo = (request("var_tipo_cargo") == "" ? "vip" : request("var_tipo_cargo"));

//Leitura Session
$intCodPJ	=	getsession(CFG_SYSTEM_NAME."_entidade_codigo");
function verificaMes($mes){

	switch($mes){
		case "1":
			return "Jan";
			break;
		case "2":
			return "Fev";
			break;
		case "3":
			return "Mar";
			break;		
		case "4":
			return "Abr";
			break;
		case "5":
			return "Mai";
			break;
		case "6":
			return "Jun";
			break;
		case "7":
			return "Jul";
			break;
		case "8":
			return "Ago";
			break;
		case "9":
			return "Set";
			break;
		case "10":
			return "Out";
			break;
		case "11":
			return "Nov";
			break;
		case "12":
			return "Dez";
			break;							
	}
}

$objConn = abreDBConn(CFG_DB); // Abertura de banco
try{
 	 $strSQL = " SELECT  DISTINCT t1.nome 
						  , to_char(t1.data_nasc,'DD/MM') as dt_nasc
						  , CASE WHEN (t4.nome IS NULL) THEN  '' ELSE t4.nome END AS cargo
						  , t2.razao_social AS empresa
						  , t2.cnpj
						  , t1.email
						  , t1.endprin_fone1
					 FROM cad_pf t1, cad_pj t2, relac_pj_pf t3 LEFT JOIN cad_cargo t4 ON t3.cod_cargo = t4.cod_cargo
			WHERE  ";
			if ($intCodPJ != ""){$strSQL .="	t2.cod_pj <> " . $intCodPJ ."
						AND t3.cod_pf = t1.cod_pf 
					    AND t3.cod_pj = t2.cod_pj 
					    AND t1.dtt_inativo IS NULL 
					    AND t1.data_nasc IS NOT NULL AND EXTRACT(WEEK FROM t1.data_nasc) = EXTRACT(WEEK FROM CURRENT_TIMESTAMP)";}
			else {$strSQL .= "
					       t3.cod_pf = t1.cod_pf 
					   AND t3.cod_pj = t2.cod_pj 
					   AND t1.dtt_inativo IS NULL 
					   AND t1.data_nasc IS NOT NULL AND EXTRACT(WEEK FROM t1.data_nasc) = EXTRACT(WEEK FROM CURRENT_TIMESTAMP)";}
			$strSQL .="  AND t4.tipo ILIKE '".$strTipocargo."'";
			
			$strSQL .="   AND t3.dt_demissao IS NULL AND t2.dtt_inativo IS NULL
			ORDER BY to_char(t1.data_nasc,'DD/MM'), t1.nome";
			
	$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

$strMes = verificaMes(date("m"));
$strAno = date("y");
$dataColega = "";
?>
<?php  
	
				// MONTA HTML DO SELECT A SER COLOCADO NO CABEÇALHO
				
			$strSQLCombo = "SELECT DISTINCT   t1.tipo, upper(t1.tipo) as tp_name											
							FROM cad_cargo t1
							ORDER BY t1.tipo DESC";
				
				
			$strHTML  = '<div style="float:right;vertical-align:top;margin:0px 5px 0px 2px;"><form name="formcargo" action="'.$_SERVER['PHP_SELF'].'">
				<span style="float:left;margin-top:5px;">'.getTText("tipo",C_NONE).'</span>';

			$strHTML .= '<select name="var_tipo_cargo" id="var_tipo_cargo"  style="width:100px;" onchange="document.formcargo.submit();">';			
			$strHTML .=  	montaCombo($objConn,$strSQLCombo,"tipo","tp_name",$strTipocargo); 						
			$strHTML .= '</select></form></div>';
			// Inicializa o box ao redor da tabela
			athBeginFloatingBox("100%","",$strHTML."<a href=\"../modulo_SdCredencial/\"><strong>Aniversariantes Colaboradores - ".$strMes."/".$strAno."</strong></a><div style=\"width:100%;height:5px;background-color:#".CL_CORBAR_GLASS_2.";\"></div>",CL_CORBAR_GLASS_2);
	
		if($objResult->rowCount()> 0)
			{

//			athBeginFloatingBox("100%","","<strong>Aniversariantes Colaboradores - ".$strMes."/".$strAno."</strong>",CL_CORBAR_GLASS_2);
?>
				<div id="grupo_4" style="display:block;">
					<table width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"  border="0" >
					  <tr>
						<td valign="top" align="left">
							
									<table width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"  border="0">
									<?php 	$data ="";	
											foreach($objResult as $objRS){
													$dateAniverColega = cDate(CFG_LANG,getValue($objRS,"dt_nasc")."/".date("y"),true);
													$diaSemanaColega = substr(getWeekDay($dateAniverColega),0,3);
													$diaColega = dDate("PTB", $dateAniverColega, "");
													
													//echo $dia; 
													$diaColega = substr(getValue($objRS,"dt_nasc"),0,5);
													//echo "<br>".$dia;
													if($dataColega == getValue($objRS,"dt_nasc")){
															if ($strNome == getValue($objRS,"nome")){?>
																<tr>
                                                                    <td height="20" style="padding-left:25px; background-color:#F0F0F0; border:solid #E9E9E9 1px;">                                                               
                                                                     <table>
                                                                		<tr>
                                                                    	<td><?php if(getValue($objRS,"email")!=""){?>
																				<img src='../img/icon_send_mail.gif' onclick="javascript: void(0);AbreJanelaPAGE('STsendAniver.php?var_email=<?php echo(getValue($objRS,"email"));?>','500','400');" style='cursor:pointer' />
																		<?php }?>
                                                                        </td>                                                                                                                                   	
                                                                    	<td>                                                               
																			<span class="corpo_mdo"><?php echo(getValue($objRS,"empresa") . (getValue($objRS,"cargo")=="" ? "" : " / ".(getValue($objRS,"cargo"))) . (getValue($objRS,"email")=="" ? "" : " / ".(getValue($objRS,"email"))) .(getValue($objRS,"endprin_fone1")=="" ? "" : " / ".(getValue($objRS,"endprin_fone1")))  );?></span>
                                                                        </td>
                                                                    </tr>
                                                                 </table>
                                                                    </td>
                                                                </tr>
                                                              <?php }?>
											  <?php }else{ ?>								
															<tr>
																<td height="22" style="padding-left:5px; border:solid #CCCCCC 1px">
																<span class="titulo_mdo"><?php echo($diaColega)." - ".$diaSemanaColega;?> </span>
																</td>
															</tr>
                                                         
															<tr>
																<td height="20" style="padding-left:15px; background-color:#F0F0F0; border:solid #E9E9E9 1px">                                                               
																<span class="corpo_mdo"><?php echo("<strong>" . getValue($objRS,"nome") . "</strong>");?></span>
																</td>
															</tr>
                                                            <tr>
																<td height="20" style="padding-left:25px; background-color:#F0F0F0; border:solid #E9E9E9 1px">
                                                                <table>
                                                                	<tr>
                                                                    	<td><?php if(getValue($objRS,"email")!=""){?> 
																				<img src='../img/icon_send_mail.gif' onclick="javascript: void(0);AbreJanelaPAGE('STsendAniver.php?var_email=<?php echo(getValue($objRS,"email"));?>','500','400');" style='cursor:pointer' />
																		<?php }?>
                                                                        </td>
                                                                    	<td>                                                               
																			<span class="corpo_mdo"><?php echo(getValue($objRS,"empresa") . (getValue($objRS,"cargo")=="" ? "" : " / ".(getValue($objRS,"cargo"))) . (getValue($objRS,"email")=="" ? "" : " / ".(getValue($objRS,"email"))) .(getValue($objRS,"endprin_fone1")=="" ? "" : " / ".(getValue($objRS,"endprin_fone1")))  );?></span>
                                                                        </td>
                                                                    </tr>
                                                                 </table>
																</td>
															</tr>
											 <?php }
											$dataColega = getValue($objRS,"dt_nasc");
											//$strCNPJ	= getValue($objRS,"cnpj");
											$strNome	= getValue($objRS,"nome");
										     }//foreach?>
								</table>
						</td>		
					  </tr>
					</table>
				</div>
			
	<?php	}//if rowcount
			else
			{?>
			<table style="width:100%;margin-bottom:0px;background-color:#FFFFFF;" class="tablesort">
            <tbody style="border:none;">
				<tr><td colspan="11" style="border:1px dashed #CCC;color:#999;font-style:italic;text-align:center;">Nenhum Aniversariante para o tipo escolhido!</td></tr>
            </tbody>			
            </table>
	<?php		}
		athEndFloatingBox(); ?>