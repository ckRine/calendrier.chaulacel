// Google Calendar Integration

// Variables globales
let googleEvents = [];
let googleCalendars = [];
let googleConnected = false;
window.selectedCalendars = [];

// Initialiser l'intégration Google Calendar
function initGoogleCalendar() {
	// Vérifier si l'utilisateur est connecté à Google Calendar
	// Uniquement si nous sommes sur la page Mon compte ou si l'utilisateur est déjà connecté
	const isAccountPage = document.getElementById('google-calendar-container') !== null;
	
	// Vérifier l'authentification Google dans tous les cas
	// Si l'utilisateur était précédemment connecté à Google, il sera reconnecté automatiquement
	checkGoogleAuth();
	
	if (isAccountPage) {
		// Ajouter le bouton de connexion à Google Calendar
		addGoogleCalendarButton();
		
		// Écouter les messages de la fenêtre d'authentification
		window.addEventListener('message', function(event) {
			if (event.data === 'google-auth-success') {
				checkGoogleAuth();
				showNotification('Connexion à Google Calendar réussie', 'success');
			}
		});
	}
}

// Afficher une notification
function showNotification(message, type = 'info') {
	const notification = document.createElement('div');
	notification.className = `notification ${type}`;
	notification.textContent = message;
	document.body.appendChild(notification);
	
	// Faire disparaître la notification après 3 secondes
	setTimeout(() => {
			notification.style.opacity = '0';
			setTimeout(() => {
					document.body.removeChild(notification);
			}, 500);
	}, 3000);
}

// Vérifier si une couleur est foncée
function isDarkColor(color) {
	// Convertir la couleur en RGB
	let r, g, b;
	
	if (color.startsWith('#')) {
		// Format hexadécimal
		const hex = color.substring(1);
		r = parseInt(hex.substring(0, 2), 16);
		g = parseInt(hex.substring(2, 4), 16);
		b = parseInt(hex.substring(4, 6), 16);
	} else if (color.startsWith('rgb')) {
		// Format rgb() ou rgba()
		const rgbValues = color.match(/\d+/g);
		r = parseInt(rgbValues[0]);
		g = parseInt(rgbValues[1]);
		b = parseInt(rgbValues[2]);
	} else {
		// Couleur non reconnue, considérer comme claire
		return false;
	}
	
	// Calculer la luminosité (formule YIQ)
	const luminance = (r * 299 + g * 587 + b * 114) / 1000;
	
	// Si la luminosité est inférieure à 128, la couleur est considérée comme foncée
	return luminance < 128;
}

// Vérifier l'état de l'authentification Google
function checkGoogleAuth() {
	fetch('./modules/check_google_auth.php')
		.then(response => {
			if (!response.ok) {
					throw new Error('Erreur réseau');
			}
			return response.json();
		})
		.then(data => {
			console.log('État de l\'authentification Google:', data);
			googleConnected = data.connected;
			updateGoogleCalendarButton();
			
			if (googleConnected) {
				// Charger les préférences utilisateur
				loadPreferences().then(() => {
						fetchGoogleEvents(0);
				});
			} else if (data.should_auto_connect) {
				// Si l'utilisateur a choisi de se connecter automatiquement à Google Calendar
				connectGoogleCalendar();
			}
		})
		.catch(error => {
			console.error('Erreur lors de la vérification de l\'authentification Google:', error);
		});
}

// Ajouter le bouton Google Calendar aux contrôles
function addGoogleCalendarButton() {
	// Créer le bouton
	const googleButton = document.createElement('button');
	googleButton.id = 'google-calendar-button';
	googleButton.onclick = handleGoogleCalendarClick;
	
	// Chercher uniquement le conteneur dans la page Mon compte
	const container = document.getElementById('google-calendar-container');
	
	// Ajouter le bouton uniquement si le conteneur existe (page Mon compte)
	if (container) {
		container.appendChild(googleButton);
		updateGoogleCalendarButton();
	}
}

// Mettre à jour l'apparence du bouton Google Calendar
function updateGoogleCalendarButton() {
	const button = document.getElementById('google-calendar-button');
	
	if (button) {
			button.textContent = googleConnected ? 'Déconnecter Google Calendar' : 'Connecter Google Calendar';
			button.className = googleConnected ? 'google-connected' : 'google-disconnected';
	}
	
	// Afficher ou masquer la liste des calendriers
	const calendarsList = document.getElementById('google-calendars-list');
	if (calendarsList) {
		calendarsList.style.display = googleConnected ? 'block' : 'none';
	}
}

// Gérer le clic sur le bouton Google Calendar
function handleGoogleCalendarClick() {
	if (googleConnected) {
			disconnectGoogleCalendar();
	} else {
			connectGoogleCalendar();
	}
}

// Connecter à Google Calendar
function connectGoogleCalendar() {
	fetch('./modules/google_auth.php')
			.then(response => {
					if (!response.ok) {
							throw new Error('Erreur réseau');
					}
					return response.json();
			})
			.then(data => {
					console.log('Réponse de google_auth.php:', data);
					if (data.success && data.authUrl) {
							// Ouvrir une nouvelle fenêtre pour l'authentification Google
							const authWindow = window.open(data.authUrl, 'GoogleAuth', 'width=600,height=600');
							
							// Vérifier périodiquement si l'authentification est terminée
							const checkAuth = setInterval(() => {
									if (authWindow.closed) {
											clearInterval(checkAuth);
											checkGoogleAuth();
											
											// Sauvegarder l'état de connexion dans les préférences utilisateur
											saveGoogleConnectionState(true);
									}
							}, 1000);
					} else {
							console.error('Erreur lors de la génération de l\'URL d\'authentification:', data.message);
							alert('Erreur: ' + data.message);
					}
			})
			.catch(error => {
					console.error('Erreur lors de la connexion à Google Calendar:', error);
					alert('Erreur de connexion à Google Calendar. Vérifiez la console pour plus de détails.');
			});
}

// Déconnecter de Google Calendar
function disconnectGoogleCalendar() {
	fetch('./modules/google_disconnect.php')
			.then(response => {
					if (!response.ok) {
							throw new Error('Erreur réseau');
					}
					return response.json();
			})
			.then(data => {
					console.log('Réponse de google_disconnect.php:', data);
					if (data.success) {
							googleConnected = false;
							googleEvents = [];
							googleCalendars = [];
							updateGoogleCalendarButton();
							showNotification('Déconnexion de Google Calendar réussie', 'success');
							
							// Sauvegarder l'état de déconnexion dans les préférences utilisateur
							saveGoogleConnectionState(false);
							
							// Rafraîchir le calendrier si on est sur la page d'accueil
							if (document.getElementById('calendar')) {
									renderVisibleMonths();
							}
					} else {
							console.error('Erreur lors de la déconnexion de Google Calendar:', data.message);
							alert('Erreur: ' + data.message);
					}
			})
			.catch(error => {
					console.error('Erreur lors de la déconnexion de Google Calendar:', error);
					alert('Erreur de déconnexion de Google Calendar. Vérifiez la console pour plus de détails.');
			});
}

// Sauvegarder l'état de connexion à Google Calendar
function saveGoogleConnectionState(connected) {
    fetch('./modules/save_google_state.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ connected: connected })
    })
    .then(response => response.json())
    .then(data => {
        console.log('État de connexion Google sauvegardé:', data);
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde de l\'état de connexion Google:', error);
    });
}

// Récupérer les événements Google Calendar
function fetchGoogleEvents(offset = 0) {
    // Ne pas essayer de récupérer les événements si l'utilisateur n'est pas connecté à Google
    if (!googleConnected) {
        return;
    }
    
    // Vérifier si on est sur la page principale du calendrier
    const isCalendarPage = document.getElementById('calendar') !== null;
    
    // Utiliser l'année et le mois actuels si on n'est pas sur la page principale
    let year = new Date().getFullYear();
    let month = new Date().getMonth() + 1;
    
    // Si on est sur la page principale, utiliser les valeurs du calendrier
    if (isCalendarPage && typeof yearSelect !== 'undefined' && yearSelect !== null) {
        year = parseInt(yearSelect.value);
        month = centralDate.getMonth() + 1;
    }
    
    fetch(`./modules/fetch_google_events.php?year=${year}&month=${month}&offset=${offset}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                googleEvents = data.events;
                googleCalendars = data.calendars;
                
                // Mettre à jour la liste des calendriers dans la page Mon compte
                updateCalendarsList();
                
                // Rafraîchir le calendrier si on est sur la page d'accueil
                if (document.getElementById('calendar')) {
                    renderVisibleMonths();
                }
            } else if (data.needAuth) {
                // Token expiré, déconnecter l'utilisateur
                googleConnected = false;
                updateGoogleCalendarButton();
            } else {
                console.error('Erreur lors de la récupération des événements Google Calendar:', data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des événements Google Calendar:', error);
        });
}

// Mettre à jour la liste des calendriers dans la page Mon compte
function updateCalendarsList() {
    const container = document.getElementById('google-calendars-checkboxes');
    if (!container || !googleCalendars || googleCalendars.length === 0) {
        return;
    }
    
    // Vider le conteneur
    container.innerHTML = '';
    
    // Ajouter un checkbox pour chaque calendrier
    googleCalendars.forEach(calendar => {
        const label = document.createElement('label');
        label.className = 'calendar-checkbox';
        label.style.borderLeft = `4px solid ${calendar.backgroundColor}`;
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = calendar.id;
        checkbox.checked = window.selectedCalendars.length === 0 || window.selectedCalendars.includes(calendar.id);
        checkbox.onchange = function() {
            toggleCalendar(calendar.id, this.checked);
        };
        
        label.appendChild(checkbox);
        label.appendChild(document.createTextNode(` ${calendar.name}`));
        container.appendChild(label);
    });
}

// Activer/désactiver un calendrier
function toggleCalendar(calendarId, checked) {
    if (checked) {
        if (!window.selectedCalendars.includes(calendarId)) {
            window.selectedCalendars.push(calendarId);
        }
    } else {
        window.selectedCalendars = window.selectedCalendars.filter(id => id !== calendarId);
    }
    
    // Sauvegarder automatiquement les préférences
    saveCalendarPreferences();
    
    // Rafraîchir le calendrier si on est sur la page d'accueil
    if (document.getElementById('calendar')) {
        renderVisibleMonths();
    }
}

// Sauvegarder les préférences de calendriers
function saveCalendarPreferences() {
    fetch('./modules/save_calendar_preferences.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ calendars: window.selectedCalendars })
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
    })
    .catch(error => {
        console.error('Erreur lors de la sauvegarde des préférences de calendriers:', error);
        showNotification('Erreur lors de la sauvegarde des préférences', 'error');
    });
}

// Afficher les événements Google dans le calendrier
function renderGoogleEvents(date, dayDiv) {
	// Si la fonction est appelée mais que l'utilisateur n'est pas connecté à Google
	// ou qu'il n'y a pas d'événements, on sort simplement sans erreur
	if (!googleConnected || !googleEvents || googleEvents.length === 0) {
			return;
	}
	
	const dateStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
	
	// Filtrer les événements pour cette date
	const eventsForDay = googleEvents.filter(event => {
			const eventDate = event.start.substring(0, 10);
			// Vérifier si le calendrier est sélectionné ou si aucun calendrier n'est sélectionné
			const isCalendarSelected = window.selectedCalendars.length === 0 || window.selectedCalendars.includes(event.calendarId);
			return eventDate === dateStr && isCalendarSelected;
	});
	
	// Ajouter les événements au jour
	if (eventsForDay.length > 0) {
			const eventsContainer = document.createElement('div');
			eventsContainer.className = 'google-events';
			
			eventsForDay.forEach(event => {
					// Ignorer les événements dont l'ID commence par "Weeknum"
					if (event.id.startsWith('Weeknum')) {
							return;
					}
					
					const eventElement = document.createElement('div');
					eventElement.className = 'google-event';
					eventElement.title = `${event.title} (${event.calendarName})`;
					eventElement.textContent = event.title;
					eventElement.dataset.eventId = event.id;
					eventElement.dataset.calendarId = event.calendarId;
					
					// Appliquer les couleurs exactes de Google Calendar
					if (event.backgroundColor) {
							eventElement.style.backgroundColor = event.backgroundColor;
							
							// Adapter la couleur du texte en fonction de la couleur de fond
							if (isDarkColor(event.backgroundColor)) {
									eventElement.style.color = '#FFFFFF'; // Texte blanc pour fond foncé
							} else {
									eventElement.style.color = '#000000'; // Texte noir pour fond clair
							}
					} else if (event.foregroundColor) {
							eventElement.style.color = event.foregroundColor;
					}
					
					eventsContainer.appendChild(eventElement);
			});
			
			// N'ajouter le conteneur que s'il contient des événements
			if (eventsContainer.children.length > 0) {
					dayDiv.appendChild(eventsContainer);
					dayDiv.classList.add('has-google-events');
			}
	}
}

// Initialiser l'intégration Google Calendar au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
	// Attendre que le calendrier soit initialisé
	setTimeout(initGoogleCalendar, 500);
});