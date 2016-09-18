<?php
	session_start();
	
	// Check for user connexion
	$user = isset($_SESSION["pronostiqueur"]) ? $_SESSION["pronostiqueur"] : 0;
	$administrator = isset($_SESSION["administrateur"]) ? $_SESSION["administrateur"] : 0;
	
	if($user == 0 || $administrator == 0)
		header('Location: ../accueil.php');

?>


<!DOCTYPE html>
<html lang="en" ng-app="poulpeApp">
	<head>

		<?php
			if($_SERVER['HTTP_HOST'] == 'localhost')
				echo '<base href="/pronostics/poulpe/">';
			else
				echo '<base href="/poulpe/">';
		?>

		<meta charset="UTF-8">
		<title>Le Poulpe d'Or - Historique</title>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/paper/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="https://rawgithub.com/angular-ui/ui-layout/master/src/ui-layout.css"/>
		<link rel="stylesheet" type="text/css" href="dist/main.css">


	</head>
	<body ng-controller="HomeController as homeCtl">

		<navbar></navbar>

		<div ui-view></div>

		<footer>
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
			<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular.min.js"></script>

			<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.3.1/angular-ui-router.min.js"></script>

			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-animate.min.js"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-touch.min.js"></script>
			<script type="text/javascript" src="https://code.angularjs.org/1.5.6/i18n/angular-locale_fr-fr.js"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/1.3.2/ui-bootstrap-tpls.min.js"></script>
			
			<script src="https://rawgithub.com/angular-ui/ui-layout/master/src/ui-layout.js"></script>

      		<script type="text/javascript" src="dist/app.js" type="text/javascript"></script>
			<script type="text/javascript" src="https://use.fontawesome.com/b704cb508c.js"></script>
		</footer>
		
	</body>
</html>