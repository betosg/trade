
<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athutils.php");


//Leitura Session
$intCodPJ	=	getsession(CFG_SYSTEM_NAME."_entidade_codigo");
function verificaMesColega($mes){

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
if ($intCodPJ != ""){
$objConn = abreDBConn(CFG_DB); // Abertura de banco
try{
	 $strSQL = " SELECT 
				  t1.nome
				, to_char(t1.data_nasc,'DD/MM') as dt_nasc
			FROM 
				cad_pf t1, relac_pj_pf t2
			WHERE  t2.cod_pj = " . $intCodPJ . "
			AND    t2.cod_pf = t1.cod_pf
			AND t1.dtt_inativo IS NULL 
			AND t1.data_nasc IS NOT NULL
			AND DATE_PART('MONTH', CURRENT_TIMESTAMP) = DATE_PART('MONTH', t1.data_nasc)
			ORDER BY to_char(t1.data_nasc,'DD/MM')";

			
			
			
	$objResult = $objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}

$strMes = verificaMesColega(date("m"));
$strAno = date("y");

?>
<?php  
	if($objResult->rowCount()> 0)
		{
//			echo("<tr><td height='10'>&nbsp;</td></tr><tr><td width='1%' align='center' valign='top' style='border:none; background:none; padding:0px 0px 0px 5px;'>");?>
					
                    <table width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"  border="0">					  
                      <tr>
                            <td height="20" align="left">
                                <table width="100%" height="20" cellpadding="0" cellspacing="0">
                                <tr>
                                  <td height="20" style="padding-left:5px;text-align:left;background-color:#F9F9F9;">
                                    <?php echo(getTText("aniversariantes",C_NONE));?> 
                                  </td>
                                  <td height="20" style="padding-right:5px;text-align:right;background-color:#F9F9F9;">                                    
                                  </td>
                                </tr>
                                </table>
                            </td>
                          </tr>                      
                      <tr>
						<td valign="middle" align="center">							
									<div style='overflow:auto; width:187px; height:100px; border:0px solid #CCC; padding-botton=5px;vertical-align:top;' id='str_div_frame'>
                                    <table width="100%" height="50%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF"  border="0">
									<?php 	$dataColega ="";	
											foreach($objResult as $objRS){
													$dateAniver = cDate(CFG_LANG,getValue($objRS,"dt_nasc")."/".date("y"),true);
													$diaSemana = substr(getWeekDay($dateAniver),0,3);
													$dia = dDate("PTB", $dateAniver, "");
													
													//echo $dia; 
													$dia = substr(getValue($objRS,"dt_nasc"),0,5);
													//echo "<br>".$dia;
													if($dataColega == getValue($objRS,"dt_nasc")){?>							
															<tr>
																<td height="20" style="padding-left:15px; background-color:#F0F0F0;">
																<span class="corpo_mdo"><?php echo(getValue($objRS,"nome"));?></span>
																</td>
															</tr>
											  <?php }else{ ?>								
															<tr>
																<td height="20" style="padding-left:5px;">
																<span class="titulo_mdo"><?php echo($dia)." - ".$diaSemana;?> </span>
																</td>
															</tr>
															<tr>
																<td height="20" style="padding-left:15px; background-color:#F0F0F0;">
																<span class="corpo_mdo"><?php echo(getValue($objRS,"nome"));?></span>
																</td>
															</tr>
											 <?php }
											$dataColega = getValue($objRS,"dt_nasc"); 
										  }//foreach?>
									</table>
                                    </div>
						</td>		
					  </tr>
					</table>
				
		<?php }//if rowcount
}//if codpj?>
