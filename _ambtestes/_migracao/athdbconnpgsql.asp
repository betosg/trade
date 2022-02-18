<%
Sub AbreDBConnPgSQL(byRef objConn)
	Set objConn = Server.CreateObject("ADODB.Connection") '//creating ado connection.
	DbDriver    = "Driver={PostgreSQL ANSI}; server=localhost; PORT=5432; database=prostudio_cm; uid=proeventostudio; pwd=prostudio" ' specifying db path, user name, port etc...
	strConn     = DbConnString & DbDriver
	objConn.Open strConn
End Sub
%>