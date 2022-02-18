<!-- #include file="athdbconnaccess.asp"-->
<!-- #include file="athdbconnpgsql.asp"-->
<%
'On Error Resume Next
Server.ScriptTimeout = 99999

Dim strSQLSelect, strSQLInsert, strSQLInsertNew
Dim objConnAccess, objConnPgSQL, strDBPath
Dim objRSAccess, objRSPgSQL, objField

Dim strNomeCampo, strValor, strDbDriver, strHora
Dim i, intGeneralCount
Dim boolDebug

boolDebug = false

strDBPath    = Request("var_db_path")
strSQLSelect = Request("var_select_dados")
strSQLInsert = Request("var_insert_dados")

'Response.Write(strSQLSelect & " <br> <br>" & strSQLInsert)

Function ReturnType(prValue,prType)
	Select Case prType
		Case adVarWChar: ReturnType = Replace(Replace(CStr(prValue),"'","''"),"\","")  ' Indicates a string 
		Case adInteger : ReturnType = CInt(prValue)  ' Indicates an integer 
		Case adBigInt  : ReturnType = CLng(prValue)  ' Indicates a long integer 
		Case adSingle  : ReturnType = CInt(prValue)  ' Indicates a single-precision floating-point number 
		Case adDouble  : ReturnType = CDbl(prValue)  ' Indicates a double-precision floating-point number 
		Case adCurrency: ReturnType = CDbl(prValue)  ' Indicates a currency 
		Case adDate    : ReturnType = CDateToPgSQL(prValue) ' Indicates a date 
		Case adBoolean : if res then ReturnType = "true" else ReturnType = "false" ' Indicates a boolean 
		'Case adByte    : if res then ReturnType = "1" else ReturnType = "0" ' Indicates a byte 
		Case Else: ReturnType = ""
	End Select
End Function

Function CDateToPgSQL(prDate)
    Dim arrHour, arrDate
	
	If Not IsNull(prDate) And prDate <> "" Then 
		arrHour = Split(prDate," ")
		arrDate = Split(arrHour(0),"/")
		
		If Ubound(arrHour) = 1 Then strHora = arrHour(1) Else strHora = ""
		
		CDateToPgSQL = "'" & Trim(arrDate(2) & "-" & arrDate(1) & "-" & arrDate(0) & " " & strHora) & "'"
	Else
		CDateToPgSQL = "NULL"
	End If
End Function

AbreDBConnAccess objConnAccess, strDBPath 
'AbreDBConnPgSQL(objConnPgSQL)
Set objConnPgSQL = Server.CreateObject("ADODB.Connection") '//creating ado connection.
	strDbDriver    = "Driver={PostgreSQL ANSI}; server=localhost; PORT=5432; database=prostudio_cm; uid=proeventostudio; pwd=prostudio" ' specifying db path, user name, port etc...
	objConnPgSQL.Open strDbDriver

Set objRSAccess = objConnAccess.execute(strSQLSelect)

'strNomeCampo = "nomecli"
intGeneralCount = 0
While Not objRSAccess.EOF
	i = 0
	
	For Each objField In objRSAccess.Fields
		If i = 0 Then strSQLInsertNew = strSQLInsert End If
		
		strNomeCampo = objField.Name
		
		strValor = ReturnType(objRSAccess(strNomeCampo)&"", objField.Type)
		
		strSQLInsertNew = Replace(strSQLInsertNew,":" & strNomeCampo, strValor)
		
		i = i + 1
	Next
	
	If boolDebug Then
		Response.Write(strSQLInsertNew & "<br>")
	Else
		objConnPgSQL.BeginTrans
		objConnPgSQL.execute(strSQLInsertNew)
		
		If Err.Number = 0 Then 
			objConnPgSQL.CommitTrans
			intGeneralCount = intGeneralCount + 1
		Else 
			objConnPgSQL.RollbackTrans
			Response.Write("Erro: " & Err.Description & "<br>")
		End If
	End If
	
	objRSAccess.MoveNext
Wend

Response.Write("Foram inserido(s) " & intGeneralCount & " registros.")

FechaDBConn(objConnPgSQL)
FechaDBConn(objConnAccess)
%>