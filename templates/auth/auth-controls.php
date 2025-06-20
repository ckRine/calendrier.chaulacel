<div class="auth-controls">
	<?php if (isset($_SESSION['user_id'])): ?>
		<div class="user-menu">
			<div class="avatar-container test" onclick="toggleUserMenu()">
				<div class="avatar">
					<span><?php echo strtoupper(substr($_SESSION['user_email'] ?? 'U', 0, 1)); ?></span>
				</div>
			</div>
			<div class="user-dropdown" id="user-dropdown">
				<div class="user-info">
					<span><?php echo $_SESSION['user_email'] ?? 'Utilisateur'; ?></span>
				</div>
				<div class="dropdown-divider"></div>
				<a href="./mon-compte.php" class="dropdown-button">Mon compte</a>
				<button onclick="forceLogout()" class="dropdown-button">Déconnexion</button>		</div>
	<?php else: ?>
		<button onclick="showAuthForm('login')">Connexion</button>
		<button onclick="showAuthForm('register')">Inscription</button>
	<?php endif; ?>
</div>