<%@LANGUAGE="VBSCRIPT" CODEPAGE="1252"%>
<% 
  Option Explicit 
  Session.LCID     = 1046
  Session.Timeout  = 240
  Response.Expires = 0 
    
  'Banco de Dados que esta sendo administrado
  Dim CFG_DB 

  CFG_DB = Request.Cookies("ATHCSM")("DBNAME")'DataBase File

  'Variáveis de definição de ambiente (no futuro poderão ir para BD ou mesmo CSS)
  Dim CFG_NUM_PER_PAGE, CFG_VERSION, CFG_WINDOW 
  Dim CFG_CORBAR_TOP, CFG_CORBAR_MIDDLE_A, CFG_CORBAR_MIDDLE_B, CFG_CORBG_FILTRO
  
  CFG_WINDOW          = "NORMAL"  'Dialogs: "POPUP" OU "NORMAL"
  CFG_NUM_PER_PAGE    = 18        'Núm. Default de ítens por página	  

  'Tenta definir o número de itens a exibir na consulta  
  'mais adequado em função da área cliente na resolução atual
  if Request.Cookies("ATHCSM")("CLIENT_AREA_HEIGHT")>=768  then CFG_NUM_PER_PAGE = 29
  if Request.Cookies("ATHCSM")("CLIENT_AREA_HEIGHT")>=864  then CFG_NUM_PER_PAGE = 32
  if Request.Cookies("ATHCSM")("CLIENT_AREA_HEIGHT")>=960  then CFG_NUM_PER_PAGE = 36
  if Request.Cookies("ATHCSM")("CLIENT_AREA_HEIGHT")>=1024 then CFG_NUM_PER_PAGE = 40

  CFG_VERSION         = "3.0.8"   'Versao atual do SISTEMA
  CFG_CORBAR_TOP      = "#808080"
  CFG_CORBAR_MIDDLE_A = "#FFFFFF"
  CFG_CORBAR_MIDDLE_B = "#F2F2F2"
  CFG_CORBG_FILTRO    = "#F2F2F2"
%>

<!-- ------------------------------------------------------------------------- 
 Essas coisas colocadas ACIMA, não precisariam mais ser colocadas em página 
 alguma do sistema, pois todas incluem esta aqui
<!-- ---------------------------------------------------------------------- -->
<!--#include file="ADOVBS.INC"-->
<%

' ------------------------------------------------------------------------------------------------------------------
' Função para abrir a conexão com o BD, procura ser genérica e funcionar tanto quando o site esta funcionando na 
' Athenas quando quando eta já no hosting, usa DSN ou faz por arquivo...
Sub AbreDBConnAccess(byref pr_objConn, byval prPath)
Dim auxmappath, strConnection, strDBUsername, strDBPassword
Dim objFSO, strPath, aviso

  Set pr_objConn = Server.CreateObject("ADODB.Connection")

  If instr(prPath,"DSN=") > 0 Then 
   'CONEXÃO VIA DSN -----------------------------------------------------------------------------
    strConnection   = "DSN="
    strDBUsername   = ""
    strDBPassword   = ""
    pr_objConn.Open strConnection, strDBUsername, strDBPassword
   '---------------------------------------------------------------------------------------------
  Else
   'CONEXÃO VIA ARQUIVO: CAMINHO LOCAL e REMOTO; ------------------------------------------------
    'auxmappath = Trim(FindBDPath())
	Set objFSO = Server.CreateObject("Scripting.FileSystemObject")
	If objFSO.FileExists(prPath) Then
	  Set objFSO = NOThing
	  
	  'LOCAWEB: recomenda usarmos SEMPRE OLEDB - strConnection = "DRIVER={Microsoft Access Driver (*.mdb)};DBQ=" & auxmappath & pr_StrDataBase	
	  strConnection = "PROVIDER=Microsoft.Jet.OLEDB.4.0;Data Source=" & prPath & ";Persist Security Info=False"

	  'Response.Write(strConnection)
	  'Response.End()

	  pr_objConn.Open strConnection
	Else
	  Set objFSO = NOThing
	  Response.Write("<br><br>")
	  if (prPath="") then
  	    aviso = "Você deve selecionar um Banco de Dados para conexão.<br>Atenção! Para acessar o sistema seu navegador deve permitir gravação de Cookies."
	  else
  	    aviso = "O sistema encontra-se em manutenção.<br>Aguarde alguns instantes e tente novamente, ou entre em contato com o administrador.<br><br>" &_ 
	  	        " DB: (" & prPath & ")"
	  end if
      Mensagem aviso, "",  True
	  Response.End()
	End If
  End If
End Sub

Function FindBDPath
  Dim auxmappath
  auxmappath = lcase(server.mappath("/"))
  If instr(auxmappath,"wwwroot")>0 then 'LOCAL - conforme o nosso servidor: ZEUS
    if instr(auxmappath,"domains")>0 then
      auxmappath = replace(auxmappath,"wwwroot", "db\") 'SOUTHTECH
	else
	  auxmappath = auxmappath & "\proeventovista\_database\"  'ATHENAS
	end if
  else
    if instr(auxmappath,"home")>0 then
	  auxmappath = replace(auxmappath,"web", "dados\") 'LOCAWEB v1
	else 
	  if instr(auxmappath,"httpdocs")>0 then 'LOCAWEB v2
	    auxmappath = replace(auxmappath,"httpdocs", "private\db\") 
	  else
        auxmappath = replace(auxmappath,"html","") 'PLUGIN 
	    auxmappath = auxmappath & "data\"
	  end if
	end if
  End If
  FindBDPath = auxmappath
End Function
' ------------------------------------------------------------------------------------------------------- by Aless -

Function FindPhysicalPath(pr_pasta)
  Dim auxmappath
  auxmappath = lcase(server.mappath("/"))
  If instr(auxmappath,"wwwroot")>0 then 
    if instr(auxmappath,"domains")>0 then
      auxmappath = auxmappath & "\" & pr_pasta 'SOUTHTECH
	else
	  auxmappath = auxmappath & "\proeventovista\" & pr_pasta 'ATHENAS
	end if
  else
  	'LOCAWEB v1 Ou LOCAWEB v2 Ou PLUGIN
  	auxmappath = auxmappath & "\" & pr_pasta 
  End If
  FindPhysicalPath = auxmappath
End Function

Function FindLogicalPath(pr_pasta)
  Dim auxmappath
  auxmappath = lcase(server.mappath("/"))
  If instr(auxmappath,"wwwroot")>0 then 
    if instr(auxmappath,"domains")>0 then
      auxmappath = "http://www.athcsm.com.br/" & pr_pasta 'SOUTHTECH
	else
	  auxmappath = "http://" & Request.ServerVariables("HTTP_HOST") & "/athcsm/" & pr_pasta 'ATHENAS
	end if
  else
  	'LOCAWEB v1 Ou LOCAWEB v2 Ou PLUGIN
  	auxmappath = "http://www.athcsm.com.br/" & pr_pasta 
  End If
  FindLogicalPath = auxmappath
End Function


' ------------------------------------------------------------------------------------------------------------------
' Função para abrir a RecordSet de maneira padrão. Assim teremos duas maneiras "oficiais" de abrir um RecordSet:
' set objRS = objConn.Execute(strSQL)
' AbreRecordSet objRS, strSQL, objConn, adLockOptimistic, adOpenDynamic, adUseClient, -1 
Sub AbreRecordSet (byref prObjRS, prSQL, prObjConn, prLockType, prCursorType, prCursorLocation, prCacheEPageSize)
  set prObjRS = Server.CreateObject("ADODB.Recordset")
  prObjRS.LockType       = prLockType
  prObjRS.CursorType     = prCursorType 
  prObjRS.CursorLocation = adUseClient 'prCursorLocation  - LOCAWEB: recomenda que seja SEMPRE adUseClient 
  if prCacheEPageSize>0 then prObjRS.CacheSize = prCacheEPageSize
  prObjRS.Open prSQL,prObjConn
  if prCacheEPageSize>0 then prObjRS.PageSize = prCacheEPageSize
End Sub
' ------------------------------------------------------------------------------------------ by Aless e Cleverson --


Sub FechaRecordSet(byref pr_objRS)
  pr_objRS.close
  set pr_objRS = NOThing
End Sub


Sub FechaDBConn(byref pr_objConn)
 pr_objConn.Close()
 Set pr_objConn = NOThing
End Sub


' -------------------------------------------------------------------------------
'  Essa Sub escreve o combo de clientes, buscando pelos bancos 
'  que contém a máscara (NOMEDOSISTEMA_NOMEDOCLIENTE.mdb) dentro
'  da pasta db do sistema
'  Criada em 01/08/2007 - Implementada 21/12/2007
' ---------------------------------------------------- by Alan (01/08/2007) -----

Sub ComboDBClientes(prPREFIX,prSEARCHWORD,prSUFFIX)
	Dim objFSO, objFolder, objFile, regEx
	Dim strNome
	
	Set objFSO 	  = Server.CreateObject("Scripting.FileSystemObject")
	Set objFolder = objFSO.GetFolder(FindBDPath)
	Set regEx = New RegExp
    regEx.Pattern = "(.*)-(.*).(.*)" 
    regEx.IgnoreCase = True
    regEx.Global = True
	
	For Each objFile in objFolder.files
		'***** AQUI!!!! A ultima cláusula do if é para retirar o banco de multilínguas do combo do login. 
		'*****  Informando o parâmetro prSEARCHWORD, a função trará todos os bancos, mesmo os de linguas.
		if(InStr(1,objFile.name,prPREFIX) = 1 And InStr(1,objFile.name,prSEARCHWORD) > 0  And InStr(1,objFile.name,prSUFFIX) > 0) And (Not regEx.Test(objFile.name) Or prSEARCHWORD <> "") then 
			strNome = Replace(Replace(Replace(objFile.name,prSUFFIX,""),prPREFIX,""),"_","")
			Response.Write("<option value='" & objFile.name & "'")
			if CFG_DB = objFile.name then response.write(" selected")
			Response.Write(">" & strNome & "</option>" & VbCrLf)
		end if
	Next
    
	Set regEx     = Nothing
	Set objFSO    = Nothing
	Set objFolder = Nothing
End Sub

' ------------------------------------------------------
' Rotina para exibir tela de mensagem de aviso ou erro
' ------------------------------------------- by Aless -
Sub Mensagem(pr_aviso, pr_hyperlink, pr_flaghtml)
  If pr_flaghtml<>0 then 
    Response.Write ("<html>")
    Response.Write ("<head>")
    Response.Write ("<title></title>")
    Response.Write ("<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>")
    Response.Write ("</head>")
    Response.Write ("<body bgcolor='#FFFFFF' text='#000000'>")
  End If
	
  Response.Write ("<p align='center'><font face='Arial, Helvetica, sans-serIf' size='2'><b>.:: AVISO ::.</b></font></p>")
  Response.Write ("<p align='center'><font face='Arial, Helvetica, sans-serIf' size='2'>" & pr_aviso & "</font></p>")
  Response.Write ("<p align='center'><font face='Arial, Helvetica, sans-serIf' size='2'>" )
  If pr_hyperlink<>"" then  
    Response.Write ("<a href='" & pr_hyperlink & "'>Voltar</a>")
  End If
  Response.Write ("</font></p>")

  If pr_flaghtml<>0 then 
    Response.Write ("</body>")
    Response.Write ("</html>")
  End If
End Sub

' ------------------------------------------------------------------------------------------
' Apenas verifica se existe alguém logado, ou melhor, se tem um USER_ID no cookie do athCSM
' --------------------------------------------------------------------- by Aless/Cleverson -
sub VerificaLogon()
  If (Request.Cookies("ATHCSM")("ID_USER")="") or (Request.Cookies("ATHCSM")("ID_USER")=Empty) then
     Mensagem "Não foi identificado um usuário válido. <br>Para ter acesso você deve efetuar login no sistema.", "", 1
			  'Javascript:window.close()
	 Response.End()
  End If
End sub

' ----------------------------------------------------------------------------------------------------------------
' Para ser utilizado na veriicaçào de direoto de aceso a um determinado módulo para um ou mais grupos de usuários
' ------------------------------------------------------------------------------------------- by Aless/Cleverson -
Function VerificaAcesso(pr_grp, link)
Dim FlagOk, auxSTRGRP 
  
 FlagOk    = False
 pr_grp    = ucase(pr_grp)
 auxSTRGRP = ucase(Request.Cookies("ATHCSM")("GRP_USER"))

 If inStr(auxSTRGRP,pr_grp)>0 then 
   FlagOk=True  
 End If

 If NOT FlagOk then 
   Mensagem "Você não esta autorizado a efetuar esta operação.<BR><BR>GRUPO = " & Request.Cookies("ATHCSM")("GRP_USER"),link,1
 End If
 
 VerificaAcesso = FlagOk
End Function


' ------------------------------------------------------------------------
' Faz o DECODE de uma string que estiver Encoded:
' exemplo: aux = "http%3A%2F%2Fwww%2Eissi%2Enet "
'          URLDecode(Aux)
'          => aux será igual a "http://www.issi.net"
'-------------------------------------------------------------- by Aless -
Function URLDecode(S3Decode)
Dim S3Temp(1,1)
Dim S3In, S3Out, S3Pos, S3Len, S3i

 S3In  = S3Decode
 S3Out = ""
 S3In  = Replace(S3In, "+", " ")
 S3Pos = Instr(S3In, "%")
 Do While S3Pos
	S3Len = Len(S3In)
	If S3Pos > 1 Then S3Out = S3Out & Left(S3In, S3Pos - 1)
	S3Temp(0,0) = Mid(S3In, S3Pos + 1, 1)
	S3Temp(1,0) = Mid(S3In, S3Pos + 2, 1)
	For S3i = 0 to 1
		If Asc(S3Temp(S3i,0)) > 47 And Asc(S3Temp(S3i, 0)) < 58 Then
			S3Temp(S3i, 1) = Asc(S3Temp(S3i, 0)) - 48
		Else
			S3Temp(S3i, 1) = Asc(S3Temp(S3i, 0)) - 55
		End If
	Next
	S3Out = S3Out & Chr((S3Temp(0,1) * 16) + S3Temp(1,1))
	S3In  = Right(S3In, (S3Len - (S3Pos + 2)))
	S3Pos = Instr(S3In, "%")
 Loop
 URLDecode = S3Out & S3In
End Function

' ------------------------------------------------------------------------
' Busca dados relativos as informações do site no banco (athcsm.mdb) 
' para montagem na tela principal
'-------------------------------------------------------------- by Aless -
Public Function MontaArraysContainer(pr_srtSQL,byref pr_arrScodi,byref pr_arrSdesc )
Dim strSQL_CSM
Dim objConn_CSM, objRS_CSM
Dim auxStrScodi, auxStrSdesc

  AbreDBConn objConn_CSM, CFG_DB

  strSQL_CSM = pr_srtSQL
  Set objRS_CSM = objConn_CSM.execute(strSQL_CSM)
  
  auxStrScodi = ""
  auxStrSdesc  = ""
  Do While NOT objRS_CSM.EOF
    auxStrScodi = auxStrScodi & "|" & objRS_CSM(0)
    auxStrSdesc = auxStrSdesc & "|" & objRS_CSM(1)
	'Response.write auxStrScodi & " => " & auxStrSdesc & "<BR>"
    objRS_CSM.MoveNext
  Loop
  'Response.End

  pr_arrScodi = Split (auxStrScodi, "|")
  pr_arrSdesc = Split (auxStrSdesc, "|")

  FechaRecordSet objRS_CSM
  FechaDBConn ObjConn_CSM
End Function

'----------------------------
' Obtain database field value
'---------------- by Aless --
function GetValue(rs, strFieldName)
CONST bDebug = True
dim res
  on error resume next
  if rs is nothing then
  	GetValue = ""
  elseif (not rs.EOF) and (strFieldName <> "") then
    res = rs(strFieldName)
    if isnull(res) then 
      res = ""
    end if
    if VarType(res) = vbBoolean then
      if res then res = "1" else res = "0"
    end if
    GetValue = res
  else
    GetValue = ""
  end if
  if bDebug then response.write err.Description
  on error goto 0
end function

'----------------------------------------------
' Obtain specific URL Parameter from URL string
'---------------------------------- by Aless --
function GetParam(ParamName)
Dim auxStr
  if ParamName = "" then 
    auxStr = Request.QueryString
	if auxStr = Empty or Cstr(auxStr) = "" or isNull(auxStr) then auxStr = Request.Form
  else
   if Request.QueryString(ParamName).Count > 0 then 
     auxStr = Request.QueryString(ParamName)
   elseif Request.Form(ParamName).Count > 0 then
     auxStr = Request.Form(ParamName)
   else 
     auxStr = ""
   end if
  end if
  
  if auxStr = "" then
    GetParam = Empty
  else
    auxStr = Trim(Replace(auxStr,"'","''"))
    GetParam = auxStr
  end if
end function



' -------------------------------------------------------------------------
' Facilita a montagem dos SQLs de cada include correspondente a um site_area
'--------------------------------------------------------------- by Aless -
Function MontaSiteAreaSQL(strCodSiteArea, strTipoCons)
Dim MSAS_strSQL,MSAS_FrGeral   

    MSAS_strSQL = " SELECT" &_
	              " RV_REVISTA.TITULO  AS REVISTA_TITULO, "&_ 
                  " RV_EXEMPLAR.TITULO AS EXEMPLAR_TITULO , "&_ 
                  " RV_SECAO.TITULO    AS SECAO_TITULO , "&_
                  " RV_MATERIA.TITULO  AS MATERIA_TITULO, "&_
 
	              " RV_REVISTA.ROTULO_MENU  AS REVISTA_ROTULO, "&_ 
                  " RV_EXEMPLAR.ROTULO_MENU AS EXEMPLAR_ROTULO , "&_ 
                  " RV_SECAO.ROTULO_MENU    AS SECAO_ROTULO , "&_
                  " RV_MATERIA.ROTULO_MENU  AS MATERIA_ROTULO, "&_
 
 		          " RV_REVISTA.COD_REVISTA   AS COD_REVISTA, " &_
    		      " RV_EXEMPLAR.COD_EXEMPLAR AS COD_EXEMPLAR, " &_
    		      " RV_SECAO.COD_SECAO       AS COD_SECAO, " &_
    		      " RV_MATERIA.COD_MATERIA   AS COD_MATERIA, " &_

                  " RV_REVISTA.TEXTO  AS REVISTA_TEXTO, " &_
                  " RV_EXEMPLAR.TEXTO AS EXEMPLAR_TEXTO , " &_
                  " RV_SECAO.TEXTO    AS SECAO_TEXTO , " &_
                  " RV_MATERIA.TEXTO  AS MATERIA_TEXTO, " &_

                  " RV_REVISTA.DESCRICAO  AS REVISTA_DESCRICAO, " &_
                  " RV_EXEMPLAR.DESCRICAO AS EXEMPLAR_DESCRICAO, " &_
                  " RV_SECAO.DESCRICAO    AS SECAO_DESCRICAO, " &_
                  " RV_MATERIA.DESCRICAO  AS MATERIA_DESCRICAO, " &_

                  " RV_REVISTA.COD_REVISTA  AS REVISTA_COD_PAI, " &_
                  " RV_EXEMPLAR.COD_REVISTA AS EXEMPLAR_COD_PAI, " &_ 
                  " RV_SECAO.COD_EXEMPLAR   AS SECAO_COD_PAI, " &_
                  " RV_MATERIA.COD_SECAO    AS MATERIA_COD_PAI, " &_

                  " RV_REVISTA.IMG          AS REVISTA_IMG, " &_
                  " RV_EXEMPLAR.IMG         AS EXEMPLAR_IMG, " &_ 
                  " RV_SECAO.IMG            AS SECAO_IMG, " &_
                  " RV_MATERIA.IMG          AS MATERIA_IMG, " &_

                  " RV_REVISTA.IMG_THUMB    AS REVISTA_IMG_THUMB, " &_
                  " RV_EXEMPLAR.IMG_THUMB   AS EXEMPLAR_IMG_THUMB, " &_ 
                  " RV_SECAO.IMG_THUMB      AS SECAO_IMG_THUMB, " &_
                  " RV_MATERIA.IMG_THUMB    AS MATERIA_IMG_THUMB, " &_

                  " RV_REVISTA.IMG_THUMB_OVER    AS REVISTA_IMG_THUMB_OVER, " &_
                  " RV_EXEMPLAR.IMG_THUMB_OVER   AS EXEMPLAR_IMG_THUMB_OVER, " &_ 
                  " RV_SECAO.IMG_THUMB_OVER      AS SECAO_IMG_THUMB_OVER, " &_
                  " RV_MATERIA.IMG_THUMB_OVER    AS MATERIA_IMG_THUMB_OVER, " &_

                  " RV_REVISTA.IMG_DESCRICAO    AS REVISTA_IMG_DESCRICAO, " &_
                  " RV_EXEMPLAR.IMG_DESCRICAO   AS EXEMPLAR_IMG_DESCRICAO, " &_ 
                  " RV_SECAO.IMG_DESCRICAO      AS SECAO_IMG_DESCRICAO, " &_
                  " RV_MATERIA.IMG_DESCRICAO    AS MATERIA_IMG_DESCRICAO, " &_

                  " RV_SITE_AREA.TIPO, RV_SITE_AREA.COD_SITE_AREA, RV_SITE_AREA.COD, " &_ 
                  " RV_SITE_AREA.BLOQUEADO, RV_SITE_AREA.ORDEM, RV_SITE_AREA.COD_REVISTA "

	MSAS_FrGeral = " FROM ((( (RV_SITE_AREA  LEFT  JOIN RV_REVISTA ON RV_SITE_AREA.COD_REVISTA=RV_REVISTA.COD_REVISTA)  "&_
                   "   LEFT JOIN RV_EXEMPLAR ON RV_SITE_AREA.COD_REVISTA=RV_EXEMPLAR.COD_EXEMPLAR) "&_
                   "   LEFT JOIN RV_SECAO ON RV_SITE_AREA.COD_REVISTA=RV_SECAO.COD_SECAO) "&_
                   "   LEFT JOIN RV_MATERIA ON RV_SITE_AREA.COD_REVISTA=RV_MATERIA.COD_MATERIA) "

    Select Case strTipoCons
    Case "JOIN-ALL"      : MSAS_strSQL = MSAS_strSQL & MSAS_FrGeral &_ 
                          " WHERE RV_SITE_AREA.COD_SITE_AREA = '" & strCodSiteArea & "' " &_ 
                          " AND RV_SITE_AREA.BLOQUEADO = False ORDER BY RV_SITE_AREA.ORDEM "
	Case "JOIN-ALLIMAGES": MSAS_strSQL = MSAS_strSQL & " ,RV_IMAGES.IMG, RV_IMAGES.IMG_THUMB " & MSAS_FrGeral &_
	                      " LEFT JOIN RV_IMAGES ON (RV_SITE_AREA.TIPO = RV_IMAGES.TIPO " &_
						  " AND RV_SITE_AREA.COD_REVISTA = RV_IMAGES.CODIGO)"&_
                          " WHERE RV_SITE_AREA.COD_SITE_AREA = '" & strCodSiteArea & "' "  &_ 
                          " AND RV_SITE_AREA.BLOQUEADO = False ORDER BY RV_SITE_AREA.ORDEM, RV_IMAGES.ORDEM "
    End Select

    MontaSiteAreaSQL = MSAS_strSQL
End Function


'-------------------------------------------------------------------------------------
' Facilita a montagem do SQl de cada Show: RV, EX, SE e MA
'------------------------------------------------------------------------- by Aless --
Function MontaLogicaRevistaSQL(pr_tipo, pr_codigo)
Dim MLR_strSQL	

	MLR_strSQL = " SELECT RV_" & pr_tipo & ".COD_" & pr_tipo  &_
				 "       ,RV_" & pr_tipo & ".TEXTO " &_
				 "       ,RV_" & pr_tipo & ".IMG " &_
				 "       ,RV_" & pr_tipo & ".IMG_THUMB " &_
				 "       ,RV_" & pr_tipo & ".IMG_THUMB_OVER " &_

	MontaLogicaRevistaSQL = MLR_strSQL & " FROM RV_" & pr_tipo & " WHERE COD_" & pr_tipo & " = " & pr_codigo
End Function

'------------------------------------------------------------------------
' Retorna o tipo do pai de EX, SE, MA
'----------------------------------------------------- by Aless & Davi --
Function RetTipoPai(pr_tipo)
	Select Case UCase(pr_tipo)
		Case "REVISTA" : RetTipoPai = "REVISTA"
		Case "EXEMPLAR": RetTipoPai = "REVISTA"
		Case "SECAO"   : RetTipoPai = "EXEMPLAR"
		Case "MATERIA" : RetTipoPai = "SECAO"
	End Select
End Function

'------------------------------------------------------------------------
' Retorna o tipo do filho de RV, EX, SE
'----------------------------------------------------- by Aless & Davi --
Function RetTipoFilho(pr_tipo_pai)
	Select Case UCase(pr_tipo_pai)
		Case "REVISTA" : RetTipoFilho = "EXEMPLAR"
		Case "EXEMPLAR": RetTipoFilho = "SECAO"
		Case "SECAO"   : RetTipoFilho = "MATERIA"
		Case "MATERIA" : RetTipoFilho = "MATERIA"
	End Select
End Function

'-- NOVA ----------------------------------------------------------------------
' Facilita a montagem dos filhos de RV, EX, SE e MA (com LEFT OUTER JOIN)
'----------------------------------------------------------- by Aless & Davi --
Function MontaChildsSQL(pr_tipo, pr_codigo, pr_Area, pr_Ordenacao1, pr_Ordenacao2)
	Dim MCS_strSQL, MCS_TipoFilho

	MCS_TipoFilho = RetTipoFilho(pr_tipo)

	MCS_strSQL = " SELECT RV_" & MCS_TipoFilho & ".COD_" & MCS_TipoFilho &_
				 "       ,RV_" & MCS_TipoFilho & ".TITULO " &_
				 "       ,RV_" & MCS_TipoFilho & ".TEXTO " &_
				 "       ,RV_" & MCS_TipoFilho & ".DESCRICAO " &_
				 "       ,RV_" & MCS_TipoFilho & ".ROTULO_MENU AS ROTULO " &_
				 "       ,RV_" & MCS_TipoFilho & ".IMG " &_
				 "       ,RV_" & MCS_TipoFilho & ".IMG_THUMB " &_
				 "       ,RV_" & MCS_TipoFilho & ".IMG_THUMB_OVER " &_
				 "       ,RV_" & MCS_TipoFilho & ".IMG_DESCRICAO " &_
				 "       ,RV_" & MCS_TipoFilho & ".DT_PUBLICACAO AS DT_PUB " &_
				 "       ,RV_" & pr_tipo & ".COD_"                 & pr_tipo &_
				 "       ,RV_" & pr_tipo & ".TITULO AS "           & pr_tipo & "_TITULO " &_
				 "       ,RV_" & pr_tipo & ".TEXTO AS "            & pr_tipo & "_TEXTO " &_
				 "       ,RV_" & pr_tipo & ".DESCRICAO AS "        & pr_tipo & "_DESCRICAO " &_
				 "       ,RV_" & pr_tipo & ".ROTULO_MENU AS "      & pr_tipo & "_ROTULO " &_
				 "       ,RV_" & pr_tipo & ".IMG AS "              & pr_tipo & "_IMG " &_
				 "       ,RV_" & pr_tipo & ".IMG_THUMB AS "        & pr_tipo & "_IMG_THUMB " &_
				 "       ,RV_" & pr_tipo & ".IMG_THUMB_OVER AS "   & pr_tipo & "_IMG_THUMB_OVER " &_
				 "       ,RV_" & pr_tipo & ".IMG_DESCRICAO AS "    & pr_tipo & "_IMG_DESCRICAO " &_
				 "       ,RV_" & pr_tipo & ".DT_PUBLICACAO AS "    & pr_tipo & "_DT_PUB " 

	MCS_strSQL = MCS_strSQL & "   FROM RV_" & pr_tipo 
	If pr_tipo <> MCS_TipoFilho Then
		MCS_strSQL = MCS_strSQL & " LEFT OUTER JOIN " &_
								  "        RV_" & MCS_TipoFilho & " on RV_" & pr_tipo & ".COD_" & pr_tipo & " = RV_" & MCS_TipoFilho & ".COD_" & pr_tipo
	End If
	'Caso deseje pesquisar incluíndo o parâmetro área
	If Trim(pr_Area) <> "" Then
 		MCS_strSQL = MCS_strSQL & " , RV_SITE_AREA "
	End If

	MCS_strSQL = MCS_strSQL & "  WHERE RV_" & pr_tipo & ".COD_" & pr_tipo & " = " & pr_codigo &_
							  "    AND RV_" & MCS_TipoFilho & ".DT_INATIVO IS NULL "

	'Caso deseje pesquisar incluíndo o parâmetro área
	If Trim(pr_Area) <> "" Then
		MCS_strSQL = MCS_strSQL & "   AND RV_SITE_AREA.TIPO = '" & pr_tipo & "' " &_
				    			  "   AND RV_SITE_AREA.COD_SITE_AREA = '" & pr_area & "' " &_
								  "   AND RV_" & MCS_TipoFilho & ".COD_REVISTA = RV_SITE_AREA.COD_REVISTA "
	End If

	MCS_strSQL = MCS_strSQL & " ORDER BY RV_" & MCS_TipoFilho & ".DT_PUBLICACAO" & " " & pr_Ordenacao1 & ", RV_" & MCS_TipoFilho & ".ORDEM  " & pr_Ordenacao2

	MontaChildsSQL = MCS_strSQL
End Function


'--------------------------------------------------------------------------------------
' Facilita a montagem da consulta dos irmão de um determinado conteúdo RV, EX, SE e MA 
'-------------------------------------------------------------------- by Aless e Clv --
Function MontaSiblingsSQL(pr_tipo, pr_codigo, pr_Ordenacao)
Dim MS_strSQL, MS_TipoPai

 MS_TipoPai = RetTipoPai (pr_tipo)
 
 MS_strSQL = " SELECT COD_" & pr_tipo & ", TITULO, TEXTO, IMG, IMG_THUMB ORDEM " &_
             "   FROM RV_"  & pr_tipo &_
             "  WHERE COD_" & MS_TipoPai & " in ( SELECT COD_" & MS_TipoPai & " FROM RV_" & pr_tipo & " WHERE COD_" & pr_tipo & " = " & pr_codigo & " )" &_
			 "    AND DT_INATIVO IS NULL " & _
             "  ORDER BY ORDEM " & pr_Ordenacao
 'Response.Write(ms_strsql)
 'response.End()
 MontaSiblingsSQL = MS_strSQL
End Function

'---------------------------------------------------------------------------------------
' Facilita a montagem da consulta dos filhos de um determinado conteúdo RV, EX, SE e MA 
'----------------------------------------------------------------------------- by Clv --
Function MontaChildrenSQL(pr_tipo, pr_codigo, pr_Ordenacao)
Dim MS_strSQL, MS_TipoFilho

 MS_TipoFilho = RetTipoFilho (pr_tipo)
 
 MS_strSQL = " SELECT COD_" & MS_TipoFilho & ", TITULO, TEXTO, IMG, IMG_THUMB, ORDEM " &_
             "   FROM RV_"  & MS_TipoFilho &_
             "  WHERE COD_" & pr_tipo & " = " & pr_codigo &_
			 "    AND DT_INATIVO IS NULL " & _
             "  ORDER BY ORDEM " & pr_Ordenacao
 'Response.Write(ms_strsql)
 'response.End()
 MontaChildrenSQL = MS_strSQL
End Function

'-----------------------------------------------------------------------------------------
' Troca o order pra randômico, assim basta pegar o primeiro registro retornado da consulta
'----------------------------------------------------------------------------- by Aless --
Function MontaRNDOrderSQL(pr_strSQL, pr_cod)
Dim auxPOS
  Randomize()

  auxPOS = instr(pr_strSQL,"ORDER BY")
  If auxpos > 0 Then pr_strSQL = Mid(pr_strSQL, 1, auxPOS-1)
  MontaRNDOrderSQL = pr_strSQL & "	ORDER BY RND(" & -1 * (Int(1000 * Rnd()) + 1) & " * " & pr_cod & ")"
End Function

'-----------------------------------------------------------------------------------------
' Troca o order por outro específico
'----------------------------------------------------------------------------- by Aless --
Function MontaOrderSQL(pr_strSQL, pr_order)
Dim auxPOS
  auxPOS = instr(pr_strSQL,"ORDER BY")
  If auxpos > 0 Then pr_strSQL = Mid(pr_strSQL, 1, auxPOS-1)
  MontaOrderSQL = pr_strSQL & " " & pr_order
End Function

'------------------------------------------------------------------------------
' 
'------------------------------------------------------------------ by Aless --
Function MontaImagesSQL(pr_tipo, pr_codigo, pr_Ordenacao)
  MontaImagesSQL = "SELECT  RV_IMAGES.COD_IMAGES, RV_IMAGES.DESCRICAO, RV_IMAGES.IMG, RV_IMAGES.IMG_THUMB" & _
                   "  FROM RV_IMAGES" & _
                   " WHERE RV_IMAGES.CODIGO = " & pr_codigo & " AND RV_IMAGES.TIPO = '"  & pr_tipo & "'" & _
                   " ORDER BY ORDEM " & pr_Ordenacao 
End Function


'------------------------------------------------------------------------------
' 
'------------------------------------------------------------------ by Aless --
sub BuscaFields(prModulo, prTabela, byRef prRetFields, byRef prRetTam, byRef prRetOrdem)
Dim objRS_local, objConn_local, strSQL_Local, CLocal
Dim auxSTR_Field, auxSTR_Tam, auxSTR_Ordem

 AbreDBConn objConn_local, CFG_DB

 strSQL_Local = "SELECT CAMPO,TAMANHO,ORDENACAO FROM CSM_FIELDS_QUERY WHERE TABELA = '" & prTabela & "' AND DT_INATIVO IS NULL "
 if (CStr(prModulo)<>"") then 
   strSQL_Local = strSQL_Local & " AND MODULO = '" & prModulo & "' "
 else 
   strSQL_Local = strSQL_Local & " AND MODULO = 'DEFAULT' " 
 end if
 strSQL_Local = strSQL_Local & " ORDER BY ORDEM"
 set objRS_local = objConn_local.Execute(strSQL_Local)

 auxSTR_Field = ""
 auxSTR_Tam   = ""
 auxSTR_Ordem = ","
 cLocal = 1
 while not objRS_local.EOF 
   auxSTR_Field = auxSTR_Field & "," & prTabela & "." & objRS_local("CAMPO")
   auxSTR_Tam   = auxSTR_Tam   & "," & objRS_local("TAMANHO")
   auxSTR_Ordem = auxSTR_Ordem & "," & prTabela & "." & objRS_local("CAMPO") & " " & objRS_local("ORDENACAO")
   cLocal = cLocal + 1
   objRS_local.movenext
 wend 

 'Retorna com uma vírgula na frente para facilitar a concatenação nos select 
 'que sempre terão o campo COD_ com inicia;
 prRetFields = auxSTR_Field
 prRetTam    = auxSTR_Tam
 prRetOrdem  = replace(auxSTR_Ordem,",,","")
 
 if prRetOrdem="," then prRetOrdem = " 1 "

 FechaRecordSet objRS_local 
 FechaDBConn objConn_local 
End Sub

Function BuscaServidorEmail(prObjConn, byref prSERVIDOR_EMAIL_SENDER , byref prSERVIDOR_SMTP, byref prSERVIDOR_PORTA, byref prSERVIDOR_AUTENTICAR, byref prSERVIDOR_EMAIL, byref prSERVIDOR_SENHA, prCOD)
	Dim objRS_local, strSQL_Local
	
	strSQL_Local =                " SELECT TOP 1 EMAIL_SENDER, SMTP_SERVIDOR, SMTP_PORTA, AUTENTICAR, EMAIL, SENHA " 
	strSQL_Local = strSQL_Local & " FROM NW_SERVIDOR "
	If IsNumeric(prCOD) Then
		If prCOD >= 0 Then strSQL_Local = strSQL_Local & "WHERE COD_SERVIDOR = " & prCOD 
	End If
	Set objRS_local = prObjConn.Execute(strSQL_Local)
		
	If Not objRS_local.Eof Then	    
		prSERVIDOR_EMAIL_SENDER = GetValue(objRS_local,"EMAIL_SENDER")
		prSERVIDOR_SMTP         = GetValue(objRS_local,"SMTP_SERVIDOR")
		prSERVIDOR_PORTA        = GetValue(objRS_local,"SMTP_PORTA")
		prSERVIDOR_EMAIL        = GetValue(objRS_local,"EMAIL")
		prSERVIDOR_SENHA        = GetValue(objRS_local,"SENHA")
		prSERVIDOR_AUTENTICAR   = UCase(CStr(GetValue(objRS_local,"AUTENTICAR")))
		BuscaServidorEmail = true
	Else
	    BuscaServidorEmail = false
	End If
	FechaRecordSet objRS_local
End Function
%>