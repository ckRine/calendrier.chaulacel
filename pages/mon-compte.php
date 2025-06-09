<?php
// Rediriger si l'utilisateur n'est pas connecté
if (!isset($_SESSION['user_id'])) {
	header('Location: ./index.php');
	exit;
}
?>

<div class="account-container">
	<h1>Mon compte</h1>
	
	<div class="account-section">
		<h2>Informations personnelles</h2>
		<div class="account-info">
				<p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></p>
		</div>
	</div>
	
	<div class="account-section">
		<h2>Intégrations</h2>
		<div class="account-integrations">
				<div class="integration-item">
					<h3>Google Calendar</h3>
					<p>Connectez votre compte Google Calendar pour afficher vos événements dans le calendrier.</p>
					<div id="google-calendar-container"></div>
					<div id="google-calendars-list" style="display: none; margin-top: 15px;">
						<h4>Calendriers disponibles</h4>
						<div id="google-calendars-checkboxes" class="calendars-selection">
							<!-- Les calendriers seront ajoutés ici dynamiquement -->
						</div>
						<button onclick="saveCalendarPreferences()" class="save-calendars-btn">Enregistrer les calendriers</button>
					</div>
				</div>
		</div>
	</div>
	
	<div class="account-section">
		<h2>Préférences</h2>
		<div class="account-preferences">
				<h3>Zones de vacances scolaires</h3>
				<div class="zones-selection">
					<label><input type="checkbox" value="A" onchange="updateZones('A', this.checked)" <?php echo in_array('A', $selectedZones ?? ['A']) ? 'checked' : ''; ?>> Zone A</label>
					<label><input type="checkbox" value="B" onchange="updateZones('B', this.checked)" <?php echo in_array('B', $selectedZones ?? ['A']) ? 'checked' : ''; ?>> Zone B</label>
					<label><input type="checkbox" value="C" onchange="updateZones('C', this.checked)" <?php echo in_array('C', $selectedZones ?? ['A']) ? 'checked' : ''; ?>> Zone C</label>
					<button onclick="savePreferences()">Enregistrer</button>
				</div>
		</div>
	</div>
	
	<div class="account-actions">
		<a href="./index.php" class="button">Retour au calendrier</a>
	</div>
</div>

<script>
	// Déplacer le bouton Google Calendar dans la section appropriée
	document.addEventListener('DOMContentLoaded', function() {
		setTimeout(function() {
			const googleButton = document.getElementById('google-calendar-button');
			if (googleButton) {
				const container = document.getElementById('google-calendar-container');
				if (container) {
					container.appendChild(googleButton);
				}
			}
		}, 1000);
	});
</script>