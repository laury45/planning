<?php
session_start();
echo "vous avez été déconnecté, a bientot ".$_SESSION['login'].".";
session_destroy();

?>

<!DOCTYPE HTML>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title>Fin de session</title>
</head>

<body>
	<form action="auth1.php" method="POST">
		<input type='submit' value="Logout">
	</form>
</body>
</html>