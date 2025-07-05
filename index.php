<?php
		include('./common/conf.php');
		include('./common/twig.php');
		
		// Déterminer quelle page afficher
		$page = 'calendrier';
		if (isset($_GET['page'])) {
				$page = $_GET['page'];
		}
		
		// Vérifier si le template existe
		$template_file = $page . '.twig';
		if (!file_exists(TEMPLATES_PATH . '/' . $template_file)) {
				$template_file = 'calendrier.twig';
		}
		
		// Rendre le template
		echo render_template($template_file);
?>