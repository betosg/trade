<%@ LANGUAGE=VBScript%> 
<%
Set objConn = Server.CreateObject("ADODB.Connection") '//creating ado connection.
DbDriver ="Driver={PostgreSQL ANSI}; server=localhost; PORT=5432; database=prostudio_cm; uid=proeventostudio; pwd=prostudio" ' specifying db path, user name, port etc...
strConn = DbConnString & DbDriver
objConn.Open strConn '//Opening Ado connection

strSQL = "select * from sys_usuario" '//selecting table with SQL.
Set objRS = Server.CreateObject("ADODB.Recordset") '//Creating ADO RecorSet.
objRS.Open strSQL,objConn,1,3

Response.Write("<b>Teste inicial de migração - Connexão com o BD PostgreSQL</b><br><br>")

While NOT objRS.EOF
	Response.Write(objRS("cod_usuario") & " - " & objRS("id_usuario") & "<br>")
	objRS.MoveNext
Wend

Response.Write("<br><a href=""console.asp"">Console Rápido</a>")

Set objRS = Nothing '//Closing Recordset.
objConn.Close
Set objConn = Nothing '//Closing Ado connection.
%>