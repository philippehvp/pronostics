<?php
	include_once('commun_administrateur.php');
?>
<!DOCTYPE html>
<html lang="en" ng-app="poulpeApp">
	<head>
		<base href="/pronostics/">
		<meta charset="UTF-8">
		<title>Le Poulpe d'Or - Historique</title>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.6/paper/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" href="dist/main.css">
	</head>


	<body ng-controller="MainController as mainCtl">
		<?php
			$nomPage = 'consulter_historique.php';
			
			echo '<input id="nomPage" type="hidden" value="' . $nomPage . '" />';
			
			// Page de consultation des données d'historique
			// La page sait également afficher les données de la saison en cours

		?>

		<navbar></navbar>

		<div ui-view></div>

		<footer>
			<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>
			<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular.min.js"></script>
			<!--script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-route.min.js"></script-->

			<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-router/0.3.1/angular-ui-router.min.js"></script>

			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-animate.min.js"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.5.6/angular-touch.min.js"></script>
			<script type="text/javascript" src="https://code.angularjs.org/1.5.6/i18n/angular-locale_fr-fr.js"></script>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/1.3.2/ui-bootstrap-tpls.min.js"></script>
			
			<script src="https://rawgithub.com/angular-ui/ui-layout/master/src/ui-layout.js"></script>

			<script type="text/javascript" src="dist/app.js" type="text/javascript"></script>
			<script type="text/javascript" src="https://use.fontawesome.com/b704cb508c.js"></script>
		</footer>


		<script>
			$(function() {

			});
		</script>
		
	</body>
</html>