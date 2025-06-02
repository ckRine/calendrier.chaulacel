<?php
	include('../common/conf.php');
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Security-Policy" content="
			default-src 'self';
			script-src 'self' 'unsafe-inline' 'unsafe-eval' https://apis.google.com https://accounts.google.com;
			connect-src https://www.googleapis.com https://accounts.google.com;
			frame-src https://accounts.google.com https://content.googleapis.com;
			style-src 'self' 'unsafe-inline';
			img-src 'self' data:;
			child-src https://accounts.google.com;
			object-src 'none';
		">

		<title>Calendrier</title>
		<script src="https://accounts.google.com/gsi/client" async defer></script>
		<script src="https://accounts.google.com/gsi/client" async defer></script>
		<script src="https://apis.google.com/js/api.js"></script>
		<link rel="stylesheet" href="<?= STATICS_PATH ?>/css/styles.css">
	</head>

	 <body>
		<div class="container">
			<button onclick="handleAuthClick()">Connexion Google</button>
			<button id="sync-button" onclick="syncToGoogleCalendar()" disabled>Synchroniser agenda</button>
			<div class="header">
					<h1>Calendrier</h1>
					<div class="nav">
						<button onclick="prevMonths()">◄ 2 mois</button>
						<span id="year"></span>
						<button onclick="nextMonths()">2 mois ►</button>
						<button onclick="goToToday()">Aujourd'hui</button>
					</div>
			</div>
			<div class="calendar" id="calendar"></div>
		</div>
		<script src="<?= STATICS_PATH ?>/js/script.js"></script>
	</body>
</html>