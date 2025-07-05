const calendar = document.getElementById('calendar');
const yearSelect = document.getElementById('year');
let currentYear = new Date().getFullYear();
let centralDate = new Date();
let selectedZones = ['A'];

const months = [
	'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
	'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];
const dayLetters = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

function showAuthForm(type) {
	const modal = document.getElementById('auth-modal');
	const title = document.getElementById('auth-title');
	const formElement = document.getElementById('auth-form-element');
	const message = document.getElementById('auth-message');

	title.textContent = type === 'login' ? 'Connexion' : 'Inscription';
	formElement.onsubmit = type === 'login' ? handleLogin : handleRegister;
	message.textContent = '';
	message.className = '';
	modal.style.display = 'flex';
	
	// Focus sur le champ email
	setTimeout(() => {
		document.getElementById('email').focus();
	}, 100);
}

function hideAuthForm() {
	document.getElementById('auth-modal').style.display = 'none';
	document.getElementById('auth-form-element').reset();
	const message = document.getElementById('auth-message');
	message.textContent = '';
	message.className = '';
}

// Fermer la modal avec la touche Escape
document.addEventListener('keydown', function(event) {
	if (event.key === 'Escape') {
		const modal = document.getElementById('auth-modal');
		if (modal && modal.style.display === 'flex') {
			hideAuthForm();
		}
	}
});

async function handleRegister(e) {
	e.preventDefault();
	const email = document.getElementById('email').value;
	const password = document.getElementById('password').value;
	const messageElement = document.getElementById('auth-message');

	try {
		const response = await fetch('./modules/register.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
		});
		
		const text = await response.text();
		let data;
		try {
			data = JSON.parse(text);
		} catch (error) {
			console.error("Réponse non-JSON:", text);
			messageElement.textContent = "Erreur serveur: réponse invalide";
			messageElement.className = "error";
			return;
		}
		
		messageElement.textContent = data.message;
		if (data.success) {
			messageElement.className = "success";
			setTimeout(() => location.reload(), 1000);
		} else {
			messageElement.className = "error";
		}
	} catch (error) {
		console.error("Erreur:", error);
		messageElement.textContent = "Erreur de connexion au serveur";
		messageElement.className = "error";
	}
}

async function handleLogin(e) {
	e.preventDefault();
	const email = document.getElementById('email').value;
	const password = document.getElementById('password').value;
	const remember = document.getElementById('remember').checked;
	const messageElement = document.getElementById('auth-message');

	try {
		const response = await fetch('./modules/login.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&remember=${remember ? 1 : 0}`
		});
		
		const text = await response.text();
		let data;
		try {
			data = JSON.parse(text);
		} catch (error) {
			console.error("Réponse non-JSON:", text);
			messageElement.textContent = "Erreur serveur: réponse invalide";
			messageElement.className = "error";
			return;
		}
		
		messageElement.textContent = data.message;
		if (data.success) {
			messageElement.className = "success";
			setTimeout(() => location.reload(), 1000);
		} else {
			messageElement.className = "error";
		}
	} catch (error) {
		console.error("Erreur:", error);
		messageElement.textContent = "Erreur de connexion au serveur";
		messageElement.className = "error";
	}
}

function toggleUserMenu() {
	const dropdown = document.getElementById('user-dropdown');
	dropdown.classList.toggle('show');
}

function showUserPreferences() {
	// À implémenter: afficher les préférences utilisateur
	alert('Fonctionnalité "Mon compte" à venir');
}

// Fermer le menu déroulant si l'utilisateur clique en dehors
window.addEventListener('click', function(event) {
	if (!event.target.closest('.user-menu')) {
		const dropdown = document.getElementById('user-dropdown');
		if (dropdown && dropdown.classList.contains('show')) {
			dropdown.classList.remove('show');
		}
	}
});

async function logout() {
	const response = await fetch('./modules/logout.php');
	const data = await response.json();
	if (data.success) {
		location.reload();
	}
}

async function savePreferences() {
	try {
		console.log("Sauvegarde des préférences:", selectedZones);
		const response = await fetch('./modules/save_preferences.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ zones: selectedZones })
		});
		const text = await response.text();
		let data;
		try {
			data = JSON.parse(text);
		} catch (e) {
			console.error("Réponse non-JSON:", text);
			alert("Erreur serveur: réponse invalide");
			return;
		}
		
		console.log("Réponse du serveur:", data);
		
		// Créer un élément de message temporaire
		const messageDiv = document.createElement('div');
		messageDiv.textContent = data.message;
		messageDiv.className = data.success ? 'notification success' : 'notification error';
		document.body.appendChild(messageDiv);
		
		// Faire disparaître le message après 3 secondes
		setTimeout(() => {
			messageDiv.style.opacity = '0';
			setTimeout(() => {
				document.body.removeChild(messageDiv);
			}, 500);
		}, 3000);
	} catch (error) {
		console.error("Erreur:", error);
		alert("Erreur lors de la sauvegarde des préférences");
	}
}

async function loadPreferences() {
	const response = await fetch('./modules/get_preferences.php');
	if (response.ok) {
		const data = await response.json();
		if (data.success) {
			// Charger les zones sélectionnées
			if (data.zones && data.zones.length > 0) {
				selectedZones = data.zones;
				['A', 'B', 'C'].forEach(zone => {
					const checkbox = document.querySelector(`input[value="${zone}"]`);
					if (checkbox) {
						checkbox.checked = selectedZones.includes(zone);
					}
				});
			}
			
			// Charger les calendriers sélectionnés
			if (typeof window.selectedCalendars !== 'undefined' && data.selected_calendars) {
				window.selectedCalendars = data.selected_calendars;
			}
		}
		return data;
	}
	return { success: false };
}

async function fetchHolidays(year) {
	try {
		const response = await fetch(`https://calendrier.api.gouv.fr/jours-feries/metropole/${year}.json`);
		const data = await response.json();
		const holidays = {};
		Object.entries(data).forEach(([date, name]) => {
			const [year, month, day] = date.split('-').map(Number);
			holidays[`${month}-${day}`] = name;
		});
		return holidays;
	} catch (error) {
		console.error('Erreur lors de la récupération des jours fériés :', error);
		return {};
	}
}

async function fetchSchoolHolidays(year, zone) {
	try {
		const response = await fetch(`https://data.education.gouv.fr/api/records/1.0/search/?dataset=fr-en-calendrier-scolaire&q=&rows=1000&sort=-start_date&facet=start_date&facet=end_date&facet=description&refine.zones=Zone+${zone}`);
		const data = await response.json();
		const vacations = {};
		data.records.forEach(record => {
			const start = new Date(record.fields.start_date);
			const end = new Date(record.fields.end_date);
			const recordYear = start.getFullYear();
			if (recordYear === year || recordYear === year + 1) {
				let current = new Date(start);
				while (current <= end) {
					if (current.getFullYear() === year) {
						vacations[`${current.getMonth() + 1}-${current.getDate()}`] = zone;
					}
					current.setDate(current.getDate() + 1);
				}
			}
		});
		return vacations;
	} catch (error) {
		console.error('Erreur lors de la récupération des vacances :', error);
		return {};
	}
}

async function renderVisibleMonths() {
	// Vérifier si l'élément calendar existe
	const calendar = document.getElementById('calendar');
	if (!calendar) {
			console.log("Élément calendar non trouvé, probablement sur une autre page");
			return; // Sortir de la fonction si l'élément n'existe pas
	}
	
	calendar.innerHTML = '';
	const centerMonth = centralDate.getMonth();
	const centerYear = parseInt(yearSelect.value);
	yearSelect.value = centerYear;

	const holidays = await fetchHolidays(centerYear);
	window.holidays = holidays; // Rendre les jours fériés disponibles globalement
	
	const schoolHolidays = { A: {}, B: {}, C: {} };
	for (const zone of ['A', 'B', 'C']) {
		if (selectedZones.includes(zone)) {
			schoolHolidays[zone] = await fetchSchoolHolidays(centerYear, zone);
		}
	}
	window.schoolHolidays = schoolHolidays; // Rendre les vacances scolaires disponibles globalement

	for (let offset = -2; offset <= 2; offset++) {
		const date = new Date(centerYear, centerMonth + offset, 1);
		const month = date.getMonth();
		const year = date.getFullYear();
		const monthDiv = document.createElement('div');
		monthDiv.className = 'month';
		monthDiv.id = `month-${year}-${month}`;
		monthDiv.innerHTML = `<h2>${months[month]} ${year}</h2>`;

		const daysDiv = document.createElement('div');
		daysDiv.className = 'days';

		const lastDate = new Date(year, month + 1, 0).getDate();

		for (let i = 1; i <= lastDate; i++) {
			const currentDate = new Date(year, month, i);
			const dayIndex = currentDate.getDay();
			const adjustedDayIndex = dayIndex === 0 ? 6 : dayIndex - 1;
			const dayLetter = dayLetters[adjustedDayIndex];

			const isToday = currentDate.toDateString() === new Date().toDateString();
			const isHoliday = holidays[`${month + 1}-${i}`];
			const isSchoolHoliday = ['A', 'B', 'C'].some(zone => schoolHolidays[zone][`${month + 1}-${i}`]);
			const isSaturday = dayIndex === 6;
			const isSunday = dayIndex === 0;

			const displayText = isHoliday ? isHoliday : '';
			let vacationBars = '';
			['A', 'B', 'C'].forEach(zone => {
				const isActive = selectedZones.includes(zone) && schoolHolidays[zone][`${month + 1}-${i}`];
				vacationBars += `<div class="vacation-bar zone-${zone.toLowerCase()} ${isActive ? 'active' : ''}"></div>`;
			});

			const dayDiv = document.createElement('div');
			dayDiv.className = `day ${isToday ? 'today' : ''} ${isHoliday ? 'holiday' : ''} ${isSchoolHoliday ? 'school-holiday' : ''} ${isSaturday ? 'saturday' : ''} ${isSunday ? 'sunday' : ''}`;
			
			// Créer un conteneur pour les informations du jour (numéro et lettre)
			const dayInfo = document.createElement('div');
			dayInfo.className = 'day-info';
			
			// Ajouter le numéro du jour
			const dayNumber = document.createElement('span');
			dayNumber.className = 'day-number';
			dayNumber.textContent = i;
			dayNumber.onclick = function(event) {
				event.stopPropagation();
				showDayPopup(currentDate);
			};
			dayInfo.appendChild(dayNumber);
			
			// Ajouter la lettre du jour
			const dayLetterSpan = document.createElement('span');
			dayLetterSpan.className = 'day-letter';
			dayLetterSpan.textContent = dayLetter;
			dayInfo.appendChild(dayLetterSpan);
			
			// Ajouter le conteneur supérieur au jour
			dayDiv.appendChild(dayInfo);
			
			// Ajouter la lettre du jour
			const dayEvents = document.createElement('div');
			dayEvents.className = 'day-events';

			// Ajouter le nom du jour férié si présent
			if (displayText) {
				const holidayName = document.createElement('div');
				holidayName.className = 'holiday-name';
				holidayName.title = displayText; // Ajouter un titre pour afficher le texte complet au survol
				holidayName.textContent = displayText;
				dayEvents.appendChild(holidayName);
			}
			
			// Ajouter les événements Google Calendar si disponibles
			if (typeof renderGoogleEvents === 'function') {
				renderGoogleEvents(currentDate, dayEvents);
			}
			
			// Ajouter le conteneur supérieur au jour
			dayDiv.appendChild(dayEvents);
			
			// Ajouter les barres de vacances
			const vacationBarsDiv = document.createElement('div');
			vacationBarsDiv.className = 'vacation-bars';
			vacationBarsDiv.innerHTML = vacationBars;
			dayDiv.appendChild(vacationBarsDiv);
			
			daysDiv.appendChild(dayDiv);
		}

		monthDiv.appendChild(daysDiv);
		calendar.appendChild(monthDiv);
	}
	
	// Faire défiler jusqu'au mois central
	setTimeout(() => {
		const centerMonthElement = document.getElementById(`month-${centerYear}-${centerMonth}`);
		if (centerMonthElement) {
			centerMonthElement.scrollIntoView({ behavior: 'smooth', inline: 'center' });
		}
	}, 100);
}

function updateYear(year) {
	currentYear = parseInt(year);
	centralDate.setFullYear(currentYear);
	renderVisibleMonths();
	// Mettre à jour les événements Google Calendar si connecté
	if (typeof googleConnected !== 'undefined' && googleConnected) {
		fetchGoogleEvents(0);
	}
}

function updateZones(zone, checked) {
	if (checked) {
		if (!selectedZones.includes(zone)) selectedZones.push(zone);
	} else {
		selectedZones = selectedZones.filter(z => z !== zone);
	}
	if (selectedZones.length === 0) selectedZones.push('A');
	
	// Sauvegarder les préférences si l'utilisateur est connecté
	const userDropdown = document.getElementById('user-dropdown');
	if (userDropdown) {
		savePreferences();
	}
	
	renderVisibleMonths();
}

function prevYear() {
	currentYear--;
	yearSelect.value = currentYear;
	centralDate.setFullYear(currentYear);
	renderVisibleMonths();
}

function prevMonths() {
	centralDate.setMonth(centralDate.getMonth() - 2);
	renderVisibleMonths();
	// Mettre à jour les événements Google Calendar si connecté
	if (typeof googleConnected !== 'undefined' && googleConnected) {
		fetchGoogleEvents(-2);
	}
}

function nextMonths() {
	centralDate.setMonth(centralDate.getMonth() + 2);
	renderVisibleMonths();
	// Mettre à jour les événements Google Calendar si connecté
	if (typeof googleConnected !== 'undefined' && googleConnected) {
		fetchGoogleEvents(2);
	}
}

// Ajouter la navigation avec les touches fléchées du clavier
document.addEventListener('keydown', function(event) {
	if (event.key === 'ArrowLeft') {
		prevMonths();
	} else if (event.key === 'ArrowRight') {
		nextMonths();
	}
});

function goToToday() {
	centralDate = new Date();
	currentYear = centralDate.getFullYear();
	yearSelect.value = currentYear;
	renderVisibleMonths();
	// Mettre à jour les événements Google Calendar si connecté
	if (typeof googleConnected !== 'undefined' && googleConnected) {
		fetchGoogleEvents(0);
	}
}

function goToDate() {
	const datePicker = document.getElementById('date-picker');
	if (datePicker && datePicker.value) {
		const selectedDate = new Date(datePicker.value);
		centralDate = selectedDate;
		currentYear = centralDate.getFullYear();
		yearSelect.value = currentYear;
		renderVisibleMonths();
		// Mettre à jour les événements Google Calendar si connecté
		if (typeof googleConnected !== 'undefined' && googleConnected) {
			fetchGoogleEvents(0);
		}
	}
}

function nextYear() {
	currentYear++;
	yearSelect.value = currentYear;
	centralDate.setFullYear(currentYear);
	renderVisibleMonths();
}

async function forceLogout() {
	const response = await fetch('./modules/logout.php');
	const data = await response.json();
	if (data.success) {
		// Au lieu de recharger la page, utiliser forceLogout
		// Définir un cookie de déconnexion
		document.cookie = 'logged_out=1; Path=/;';
		
		// Supprimer le cookie PHPSESSID
		document.cookie = 'PHPSESSID=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
		
		// Rediriger vers la page d'accueil
		window.location.href = 'calendrier';
	}
}

loadPreferences();
renderVisibleMonths();

// Initialiser Google Calendar après le chargement de la page
setTimeout(() => {
	if (typeof initGoogleCalendar === 'function') {
		initGoogleCalendar();
	}
}, 1000);