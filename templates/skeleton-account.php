<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Security-Policy" content="
			default-src 'self';
			script-src 'self' 'unsafe-inline' https://apis.google.com;
			connect-src 'self' https://data.education.gouv.fr https://calendrier.api.gouv.fr https://www.googleapis.com;
			style-src 'self' 'unsafe-inline';
			img-src 'self' data:;
			object-src 'none';
		">
		<title>Mon Compte - Calendrier</title>
		<link rel="stylesheet" href="<?= STATICS_PATH ?>/css/styles.css">
		<link rel="stylesheet" href="<?= STATICS_PATH ?>/css/account.css">
		<link rel="stylesheet" href="<?= STATICS_PATH ?>/css/google-calendar.css">
		<link rel="stylesheet" href="statics/css/logo.css">
		<script>
			const STATICS_PATH = '<?= STATICS_PATH ?>';
			const MODULES_PATH = '<?= MODULES_PATH ?>';
		</script>
	</head>
	<body>
		<div class="container">
			<?php include(TEMPLATES_PATH.'/header/header.php'); ?>
			<?php include(TEMPLATES_PATH.'/auth/auth-controls.php'); ?>
			<?php include('./pages/mon-compte.php'); ?>
		</div>
		<script src="<?= STATICS_PATH ?>/js/script.js"></script>
		<script src="<?= STATICS_PATH ?>/js/google-calendar.js"></script>
	</body>
</html>