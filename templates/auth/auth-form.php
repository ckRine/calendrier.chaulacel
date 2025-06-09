<div id="auth-modal" class="modal" style="display: none;">
	<div class="modal-overlay" onclick="hideAuthForm()"></div>
	<div class="modal-content">
		<span class="close-button" onclick="hideAuthForm()">&times;</span>
		<div id="auth-form">
			<h2 id="auth-title">Connexion</h2>
			<form id="auth-form-element">
				<div>
					<label for="email">Email</label>
					<input type="email" id="email" name="email" required>
				</div>
				<div>
					<label for="password">Mot de passe</label>
					<input type="password" id="password" name="password" required>
				</div>
				<div class="remember-me">
					<input type="checkbox" id="remember" name="remember">
					<label for="remember">Se souvenir de moi</label>
				</div>
				<div class="forgot-password">
					<a href="#" onclick="showForgotPasswordForm(); return false;">Mot de passe oublié ?</a>
				</div>
				<div class="form-buttons">
					<button type="button" onclick="hideAuthForm()">Annuler</button>
					<button type="submit">Valider</button>
				</div>
			</form>
			<div id="auth-message"></div>
		</div>
	</div>
</div>