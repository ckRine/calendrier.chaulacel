// Fonctions d'authentification

// Afficher le formulaire d'authentification
function showAuthForm(mode = 'login') {
    // Réinitialiser le formulaire
    document.getElementById('auth-form-element').reset();
    document.getElementById('auth-message').innerHTML = '';
    
    // Configurer le formulaire selon le mode
    if (mode === 'login') {
        document.getElementById('auth-title').textContent = 'Connexion';
        document.getElementById('auth-form-element').onsubmit = handleLogin;
    } else if (mode === 'register') {
        document.getElementById('auth-title').textContent = 'Inscription';
        document.getElementById('auth-form-element').onsubmit = handleRegister;
    }
    
    // Afficher le modal
    document.getElementById('auth-modal').style.display = 'flex';
}

// Cacher le formulaire d'authentification
function hideAuthForm() {
    document.getElementById('auth-modal').style.display = 'none';
    
    // Si on est sur le formulaire de mot de passe oublié, revenir au formulaire de connexion
    if (document.getElementById('forgot-password-form')) {
        document.getElementById('auth-form').innerHTML = originalAuthFormContent;
    }
}

// Gérer la soumission du formulaire de connexion
function handleLogin(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const remember = document.getElementById('remember').checked;
    
    // Afficher un message de chargement
    document.getElementById('auth-message').innerHTML = '<div class="loading">Connexion en cours...</div>';
    
    // Envoyer la requête au serveur
    fetch('./modules/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&remember=${remember ? 1 : 0}`,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('auth-message').innerHTML = `<div class="success">${data.message}</div>`;
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            document.getElementById('auth-message').innerHTML = `<div class="error">${data.message}</div>`;
            
            // Si l'utilisateur n'est pas enregistré, proposer l'inscription
            if (data.register) {
                document.getElementById('auth-message').innerHTML += '<div class="register-prompt">Vous n\'avez pas encore de compte ? <button type="button" onclick="showAuthForm(\'register\')">S\'inscrire</button></div>';
            }
        }
    })
    .catch(error => {
        document.getElementById('auth-message').innerHTML = '<div class="error">Erreur de connexion au serveur</div>';
        console.error('Erreur:', error);
    });
}

// Gérer la soumission du formulaire d'inscription
function handleRegister(event) {
    event.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    
    // Afficher un message de chargement
    document.getElementById('auth-message').innerHTML = '<div class="loading">Inscription en cours...</div>';
    
    // Envoyer la requête au serveur
    fetch('./modules/register.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('auth-message').innerHTML = `<div class="success">${data.message}</div>`;
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            document.getElementById('auth-message').innerHTML = `<div class="error">${data.message}</div>`;
        }
    })
    .catch(error => {
        document.getElementById('auth-message').innerHTML = '<div class="error">Erreur de connexion au serveur</div>';
        console.error('Erreur:', error);
    });
}

// Variable pour stocker le contenu original du formulaire d'authentification
let originalAuthFormContent;

// Afficher le formulaire de mot de passe oublié
function showForgotPassword() {
    // Sauvegarder le contenu original du formulaire
    originalAuthFormContent = document.getElementById('auth-form-element').innerHTML;
    
    // Remplacer par le formulaire de mot de passe oublié
    document.getElementById('auth-form-element').innerHTML = `
        <div class="form-group">
            <label for="forgot-email">Email</label>
            <input type="email" id="forgot-email" name="email" required>
        </div>
        <div class="form-actions">
            <button type="submit">Envoyer</button>
            <button type="button" onclick="backToLogin()">Retour</button>
        </div>
    `;
    
    // Changer le titre
    document.getElementById('auth-title').textContent = 'Mot de passe oublié';
    
    // Ajouter l'événement de soumission
    document.getElementById('auth-form-element').onsubmit = handleForgotPassword;
}

// Revenir au formulaire de connexion
function backToLogin() {
    document.getElementById('auth-form-element').innerHTML = originalAuthFormContent;
    document.getElementById('auth-form-element').onsubmit = handleLogin;
    document.getElementById('auth-title').textContent = 'Connexion';
    document.getElementById('auth-message').innerHTML = '';
}

// Gérer la soumission du formulaire de mot de passe oublié
function handleForgotPassword(event) {
    event.preventDefault();
    
    const email = document.getElementById('forgot-email').value;
    
    // Afficher un message de chargement
    document.getElementById('auth-message').innerHTML = '<div class="loading">Traitement en cours...</div>';
    
    // Envoyer la requête au serveur
    fetch('./modules/forgot_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `email=${encodeURIComponent(email)}`,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('auth-message').innerHTML = `<div class="success">${data.message}</div>`;
            
            // En mode développement, afficher le lien de réinitialisation
            if (data.dev_link) {
                document.getElementById('auth-message').innerHTML += `
                    <div class="dev-link">
                        <p><strong>Lien de réinitialisation (mode développement uniquement):</strong></p>
                        <a href="${data.dev_link}" target="_blank">${data.dev_link}</a>
                    </div>`;
            }
        } else {
            document.getElementById('auth-message').innerHTML = `<div class="error">${data.message}</div>`;
        }
    })
    .catch(error => {
        document.getElementById('auth-message').innerHTML = '<div class="error">Erreur de connexion au serveur</div>';
        console.error('Erreur:', error);
    });
}