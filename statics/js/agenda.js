// Script pour la vue agenda hebdomadaire

// Variables globales
let currentDate = new Date();
let notes = {}; // Stockage des notes par jour

// Déclarer la variable une seule fois en haut du fichier
let upcomingEventsOffset = 0;
let initialUpcomingOffset = 0;

// Initialisation
document.addEventListener('DOMContentLoaded', async function() {
	// Charger d'abord les données nécessaires
	await loadData();
	
	// Initialiser l'agenda
	renderAgenda(currentDate);
	
	// Gestionnaires d'événements
	document.getElementById('prev-week').addEventListener('click', function() {
			currentDate.setDate(currentDate.getDate() - 7);
			renderAgenda(currentDate);
	});
	
	document.getElementById('next-week').addEventListener('click', function() {
			currentDate.setDate(currentDate.getDate() + 7);
			renderAgenda(currentDate);
	});
	
	document.getElementById('go-to-date').addEventListener('click', function() {
			const selectedDate = document.getElementById('agenda-date').value;
			if (selectedDate) {
					currentDate = new Date(selectedDate);
					renderAgenda(currentDate);
			}
	});
	
	// Les préférences utilisateur sont chargées depuis le serveur
	
	// Charger les notes depuis le localStorage
	loadNotes();
});

// Fonction pour charger les données nécessaires (jours fériés, vacances scolaires)
async function loadData() {
	const year = currentDate.getFullYear();
	
	// Charger les jours fériés
	const holidays = await fetchHolidays(year);
	window.holidays = holidays;
	
	// Charger les vacances scolaires
	const schoolHolidays = { A: {}, B: {}, C: {} };
	
	// Utiliser le cache pour les préférences utilisateur
	const selectedZones = await getUserPreferences();
	console.log("Zones sélectionnées:", selectedZones);
	
	for (const zone of selectedZones) {
		schoolHolidays[zone] = await fetchSchoolHolidays(year, zone);
	}
	window.schoolHolidays = schoolHolidays;
	console.log("Données de vacances scolaires:", window.schoolHolidays);
	
	// Si Google Calendar est connecté, charger les événements
	if (typeof googleConnected !== 'undefined' && googleConnected) {
		if (typeof fetchGoogleEvents === 'function') {
			await fetchGoogleEvents(0);
		}
	}
}

// Ajout d'un cache pour les préférences utilisateur
let userPreferencesCache = null;
let userPreferencesPromise = null;

// Fonction pour récupérer les préférences utilisateur (optimisée, un seul appel réseau)
async function getUserPreferences() {
	if (userPreferencesCache) {
		return userPreferencesCache;
	}
	if (userPreferencesPromise) {
		return userPreferencesPromise;
	}
	userPreferencesPromise = fetch('./modules/get_user_preferences.php')
		.then(response => {
			if (!response.ok) throw new Error('Erreur réseau');
			return response.json();
		})
		.then(data => {
			if (data.success && data.preferences && data.preferences.zones) {
				userPreferencesCache = data.preferences.zones;
				return userPreferencesCache;
			}
			return ['A', 'B'];
		})
		.catch(() => ['A', 'B'])
		.finally(() => { userPreferencesPromise = null; });
	return userPreferencesPromise;
}

// Fonction pour récupérer les zones sélectionnées
function getSelectedZones() {
	const zones = [];
	document.querySelectorAll('.zone-checkbox:checked').forEach(checkbox => {
			zones.push(checkbox.value);
	});
	return zones.length > 0 ? zones : ['A'];
}

// Fonction pour mettre à jour les zones sélectionnées
function updateZones(zone, checked) {
	if (checked) {
			if (!window.selectedZones) window.selectedZones = ['A'];
			if (!window.selectedZones.includes(zone)) window.selectedZones.push(zone);
	} else {
			if (window.selectedZones) {
					window.selectedZones = window.selectedZones.filter(z => z !== zone);
					if (window.selectedZones.length === 0) window.selectedZones.push('A');
			}
	}
	
	// Recharger les données et mettre à jour l'agenda
	loadData().then(() => renderAgenda(currentDate));
}

// Fonction pour récupérer les jours fériés
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

// Fonction pour récupérer les vacances scolaires
async function fetchSchoolHolidays(year, zone) {
	try {
			// Utiliser des données statiques pour les vacances scolaires au lieu de l'API
			// car l'API peut être instable ou ne pas fonctionner correctement
			const vacations = {};
			
			// Ajouter quelques dates de vacances pour test
			// Ces dates sont fictives pour tester l'affichage
			const currentMonth = new Date().getMonth() + 1;
			const currentDay = new Date().getDate();
			
			// Ajouter le jour courant et les 3 jours suivants comme vacances pour la zone
			for (let i = 0; i < 4; i++) {
				const day = currentDay + i;
				vacations[`${currentMonth}-${day}`] = zone;
			}
			
			console.log(`Vacances chargées pour la zone ${zone}:`, vacations);
			return vacations;
	} catch (error) {
			console.error('Erreur lors de la récupération des vacances :', error);
			return {};
	}
}

// Fonction pour charger les notes depuis le localStorage
function loadNotes() {
	const savedNotes = localStorage.getItem('agenda-notes');
	if (savedNotes) {
			notes = JSON.parse(savedNotes);
	}
}

// Fonction pour sauvegarder les notes dans le localStorage
function saveNotes() {
	localStorage.setItem('agenda-notes', JSON.stringify(notes));
}

// Fonction pour formater une date au format YYYY-MM-DD
function formatDate(date) {
	const year = date.getFullYear();
	const month = String(date.getMonth() + 1).padStart(2, '0');
	const day = String(date.getDate()).padStart(2, '0');
	return `${year}-${month}-${day}`;
}

// Fonction pour obtenir le jour courant comme premier jour
function getFirstDayOfWeek(date) {
	// Retourne simplement la date actuelle comme premier jour
	return new Date(date);
}

// Fonction pour mettre à jour l'affichage de l'agenda
function renderAgenda(date) {
	const agendaGrid = document.getElementById('agenda-grid');
	const today = new Date(date);
	const days = [];
	
	// Générer le jour courant et les 6 jours suivants
	for (let i = 0; i < 7; i++) {
		const currentDay = new Date(today);
		currentDay.setDate(today.getDate() + i);
		days.push(currentDay);
	}
	
	// Mettre à jour le titre de la période
	const lastDay = new Date(today);
	lastDay.setDate(today.getDate() + 6);
	document.getElementById('current-week-range').textContent = `Du ${today.getDate()}/${today.getMonth() + 1} au ${lastDay.getDate()}/${lastDay.getMonth() + 1}/${lastDay.getFullYear()}`;
	
	// Vider la grille
	agendaGrid.innerHTML = '';
	
	// Créer l'en-tête avec les jours
	createHeader(agendaGrid, days);
	
// Fonction pour créer une ligne d'heure
	function createHourRow(grid, hour, days) {
		// Cellule d'heure
		const hourCell = document.createElement('div');
		hourCell.className = 'agenda-cell time';
		hourCell.textContent = `${hour}:00`;
		grid.appendChild(hourCell);
		
		// Cellules pour chaque jour à cette heure
		days.forEach(day => {
			const cell = document.createElement('div');
			cell.className = `agenda-cell ${isToday(day) ? 'today' : ''} ${isWeekend(day) ? (day.getDay() === 6 ? 'saturday' : 'sunday') : ''}`;
			
			// Ajouter la classe holiday si c'est un jour férié
			if (getHolidayName(day)) {
					cell.classList.add('holiday');
			}
			
			// Ajouter la classe school-holiday si c'est un jour de vacances scolaires
			if (isSchoolHoliday(day)) {
					cell.classList.add('school-holiday');
			}
			
			// Si c'est l'heupeof goore "Notes" (heure 23), ajouter un textarea
			if (hour === 23) {
				const dateStr = formatDate(day);
				const textarea = document.createElement('textarea');
				textarea.className = 'agenda-note';
				textarea.placeholder = 'Notes...';
				textarea.dataset.date = dateStr;
				
				// Charger les notes existantes
				if (notes[dateStr]) {
						textarea.value = notes[dateStr];
				}
				
				cell.appendChild(textarea);
			}
			
			grid.appendChild(cell);
		});
	}
	
	// Créer les lignes pour chaque heure
	for (let hour = 0; hour < 24; hour++) {
			createHourRow(agendaGrid, hour, days);
	}
	
	// Ajouter les événements
	addEvents(days);
	
	// Ajouter les gestionnaires d'événements pour les notes
	setupNoteHandlers();
	// Initialiser l'offset à -1 pour forcer le calcul sur le prochain renderUpcomingEvents
	upcomingEventsOffset = -1;
	renderUpcomingEvents();
}

// Fonction pour créer l'en-tête de l'agenda
function createHeader(grid, days) {
	// Cellule vide dans le coin supérieur gauche
	const cornerCell = document.createElement('div');
	cornerCell.className = 'agenda-cell header';
	cornerCell.textContent = 'Heure / Jour';
	grid.appendChild(cornerCell);
	
	// En-têtes des jours
	days.forEach(day => {
			const dayHeader = document.createElement('div');
			dayHeader.className = `agenda-cell header day-header ${isToday(day) ? 'today' : ''} ${isWeekend(day) ? (day.getDay() === 6 ? 'saturday' : 'sunday') : ''}`;
			
			const dayName = document.createElement('div');
			dayName.className = 'day-name';
			dayName.textContent = getDayName(day.getDay());
			
			const dayDate = document.createElement('div');
			dayDate.className = 'day-date';
			dayDate.textContent = day.getDate();
			
			const dayInfo = document.createElement('div');
			dayInfo.className = 'day-info';
			
			// Ajouter la classe holiday si c'est un jour férié
			if (getHolidayName(day)) {
					dayHeader.classList.add('holiday');
			}
			
			// Ajouter la classe school-holiday si c'est un jour de vacances scolaires
			if (isSchoolHoliday(day)) {
					dayHeader.classList.add('school-holiday');
			}
			
			dayHeader.appendChild(dayName);
			dayHeader.appendChild(dayDate);
			
			// Ajouter un gestionnaire d'événement pour afficher la popup du jour
			dayHeader.addEventListener('click', function() {
					if (typeof showDayPopup === 'function') {
							showDayPopup(day);
					}
			});
			
			grid.appendChild(dayHeader);
	});
	
	// Ajouter une ligne pour les événements de toute la journée
	createAllDayRow(grid, days);
}

// Fonction pour créer une ligne pour les événements de toute la journée
function createAllDayRow(grid, days) {
    // Cellule d'étiquette pour les événements de toute la journée
    const labelCell = document.createElement('div');
    labelCell.className = 'agenda-cell all-day-row';
    labelCell.textContent = 'Journée';
    grid.appendChild(labelCell);

    // Cellules pour chaque jour
    days.forEach(day => {
      const cell = document.createElement('div');
      cell.className = `agenda-cell all-day-row ${isToday(day) ? 'today' : ''} ${isWeekend(day) ? (day.getDay() === 6 ? 'saturday' : 'sunday') : ''}`;

      // Ajouter la classe holiday si c'est un jour férié
      if (getHolidayName(day)) {
        cell.classList.add('holiday');
      }

      // Ajouter la classe school-holiday si c'est un jour de vacances scolaires
      if (isSchoolHoliday(day)) {
        cell.classList.add('school-holiday');
      }

      const dayInfoContainer = document.createElement('div');

      // Ajouter les événements Google Calendar de toute la journée ou sur plusieurs jours
      if (typeof googleConnected !== 'undefined' && googleConnected) {
        if (typeof googleEvents !== 'undefined' && googleEvents && googleEvents.length > 0) {
          const current = new Date(day.getFullYear(), day.getMonth(), day.getDate());
          const allDayEvents = googleEvents.filter(event => {
            const eventStart = new Date(event.start.substring(0, 10));
            const eventEnd = event.end
              ? new Date(new Date(event.end.substring(0, 10)).getTime() - 24 * 60 * 60 * 1000)
              : eventStart;
            const isCalendarSelected = !window.selectedCalendars || window.selectedCalendars.length === 0 || window.selectedCalendars.includes(event.calendarId);
            return isCalendarSelected
              && !event.id.startsWith('Weeknum')
              && (!event.startTime || event.startTime === 'Toute la journée')
              && current >= eventStart && current <= eventEnd;
          });

          allDayEvents.forEach(event => {
            const eventElement = document.createElement('div');
            eventElement.className = 'agenda-event all-day';
            eventElement.textContent = event.title || 'Sans titre';
            if (event.backgroundColor) {
              eventElement.style.backgroundColor = event.backgroundColor;
            }
            dayInfoContainer.appendChild(eventElement);
          });
        }
      }

      cell.appendChild(dayInfoContainer);
      grid.appendChild(cell);
    });
}

// Fonction pour ajouter les événements à l'agenda
function addEvents(days) {
	// Ajouter les événements Google Calendar avec horaire si disponibles
	if (typeof googleConnected !== 'undefined' && googleConnected) {
			if (typeof googleEvents !== 'undefined' && googleEvents && googleEvents.length > 0) {
					days.forEach((day, index) => {
							const dateStr = `${day.getFullYear()}-${String(day.getMonth() + 1).padStart(2, '0')}-${String(day.getDate()).padStart(2, '0')}`;
							console.log(`Recherche d'événements pour ${dateStr} (jour ${index})`);
							
							// Filtrer les événements pour cette date avec horaire
							const timedEvents = googleEvents.filter(event => {
									const eventDate = event.start.substring(0, 10);
									const isCalendarSelected = !window.selectedCalendars || window.selectedCalendars.length === 0 || window.selectedCalendars.includes(event.calendarId);
									return eventDate === dateStr && isCalendarSelected && !event.id.startsWith('Weeknum') && 
											event.startTime && event.startTime !== 'Toute la journée';
							});
							
							console.log(`Trouvé ${timedEvents.length} événements pour ${dateStr}`);
							
							// Ajouter chaque événement avec horaire
							timedEvents.forEach(event => {
									const startHour = event.startTime ? parseInt(event.startTime.split(':')[0]) : 0;
									const endHour = event.endTime ? parseInt(event.endTime.split(':')[0]) : startHour + 1;
									
									// Utiliser l'index du jour dans le tableau days plutôt que le jour lui-même
									// pour garantir le bon placement dans la grille
									addEvent(day, startHour, endHour, {
											title: event.title || 'Sans titre',
											color: event.backgroundColor || '#4285F4',
											dayIndex: index // Passer l'index du jour
									});
							});
					});
			}
	}
}

// Fonction pour ajouter un événement à l'agenda
function addEvent(day, startHour, endHour, eventData) {
	// Trouver l'index de la colonne correspondant au jour dans la grille
	// Nous devons trouver l'index relatif par rapport au premier jour affiché
	const firstDay = new Date(currentDate);
	const daysDiff = Math.floor((day - firstDay) / (24 * 60 * 60 * 1000));
	const columnIndex = daysDiff + 1; // +1 car la première colonne est pour les heures
	
	// S'assurer que endHour est supérieur à startHour
	if (endHour <= startHour) {
			endHour = startHour + 1;
	}
	
	// Limiter à 24 heures maximum
	if (endHour > 24) {
			endHour = 24;
	}
	
	// Créer l'élément d'événement une seule fois
	const eventElement = document.createElement('div');
	eventElement.className = 'agenda-event multi-hour';
	eventElement.textContent = eventData.title;
	eventElement.title = `${eventData.title} (${startHour}h-${endHour}h)`;
	
	if (eventData.color) {
			eventElement.style.backgroundColor = eventData.color;
	}
	
	// Ajouter un gestionnaire d'événement pour afficher les détails
	eventElement.addEventListener('click', function(e) {
			e.stopPropagation();
			alert(`Événement: ${eventData.title} (${startHour}h-${endHour}h)`);
	});
	
	// Ajouter l'événement à chaque cellule horaire concernée
	const cells = document.querySelectorAll('.agenda-cell');
	const gridColumns = 8; // 1 colonne d'heures + 7 jours
	
	for (let hour = startHour; hour < endHour; hour++) {
			const rowIndex = hour + 2; // +2 car la première ligne est l'en-tête et la deuxième est pour les événements de toute la journée
			const cellIndex = rowIndex * gridColumns + columnIndex;
			
			if (cells[cellIndex]) {
					// Cloner l'élément pour chaque cellule
					const clone = eventElement.cloneNode(true);
					
					// Ajouter une classe spéciale pour la première et dernière cellule
					if (hour === startHour) {
							clone.classList.add('event-start');
					} else {
							// Ne pas afficher le texte pour les cellules qui ne sont pas le début de l'événement
							clone.textContent = '';
					}
					if (hour === endHour - 1) {
							clone.classList.add('event-end');
					}
					
					// Ajouter le même gestionnaire d'événement au clone
					clone.addEventListener('click', function(e) {
							e.stopPropagation();
							alert(`Événement: ${eventData.title} (${startHour}h-${endHour}h)`);
					});
					
					// Ajouter une classe à la cellule pour indiquer qu'elle contient un événement
					cells[cellIndex].classList.add('has-event');
					cells[cellIndex].appendChild(clone);
			}
	}
}

// Fonction pour configurer les gestionnaires d'événements des notes
function setupNoteHandlers() {
	const textareas = document.querySelectorAll('.agenda-note');
	textareas.forEach(textarea => {
			textarea.addEventListener('change', function() {
					const dateStr = this.dataset.date;
					notes[dateStr] = this.value;
					saveNotes();
			});
	});
}

// Fonction pour vérifier si une date est aujourd'hui
function isToday(date) {
	const today = new Date();
	return date.getDate() === today.getDate() &&
				date.getMonth() === today.getMonth() &&
				date.getFullYear() === today.getFullYear();
}

// Fonction pour vérifier si une date est un week-end
function isWeekend(date) {
	const day = date.getDay();
	return day === 0 || day === 6; // 0 = dimanche, 6 = samedi
}

// Fonction pour obtenir le nom du jour
function getDayName(dayIndex) {
	const days = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
	return days[dayIndex];
}

// Fonction pour obtenir le nom du jour férié
function getHolidayName(date) {
	if (typeof window.holidays === 'undefined' || !window.holidays) return null;
	
	const month = date.getMonth() + 1;
	const day = date.getDate();
	return window.holidays[`${month}-${day}`] || null;
}

// Fonction pour vérifier si une date est un jour de vacances scolaires
function isSchoolHoliday(date) {
	if (typeof window.schoolHolidays === 'undefined' || !window.schoolHolidays) return false;
	
	const month = date.getMonth() + 1;
	const day = date.getDate();
	const dateStr = `${month}-${day}`;
	
	return ['A', 'B', 'C'].some(zone => {
			return window.schoolHolidays[zone] && window.schoolHolidays[zone][dateStr];
	});
}

// Fonction pour obtenir les zones de vacances scolaires pour une date
function getSchoolHolidayZones(date) {
	if (typeof window.schoolHolidays === 'undefined' || !window.schoolHolidays) return [];
	
	const month = date.getMonth() + 1;
	const day = date.getDate();
	const dateStr = `${month}-${day}`;
	
	// Pour les tests, simuler des vacances pour toutes les zones disponibles
	// sur les jours actuels et les 3 jours suivants
	const currentDay = new Date().getDate();
	if (day >= currentDay && day <= currentDay + 3) {
		// Retourner toutes les zones qui ont des données
		return Object.keys(window.schoolHolidays).filter(zone => 
			Object.keys(window.schoolHolidays[zone]).length > 0
		);
	}
	
	const zones = [];
	// Vérifier uniquement les zones qui ont des données
	Object.keys(window.schoolHolidays).forEach(zone => {
		if (window.schoolHolidays[zone] && window.schoolHolidays[zone][dateStr]) {
			zones.push(zone);
		}
	});
	
	console.log(`Zones pour ${dateStr}:`, zones);
	return zones;
}

// Fonction pour obtenir le numéro de la semaine
function getWeekNumber(date) {
	const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
	const dayNum = d.getUTCDay() || 7;
	d.setUTCDate(d.getUTCDate() + 4 - dayNum);
	const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
	return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

// Fonction pour obtenir le jour de l'année
function getDayOfYear(date) {
	const start = new Date(date.getFullYear(), 0, 0);
	const diff = date - start;
	const oneDay = 1000 * 60 * 60 * 24;
	return Math.floor(diff / oneDay);
}

// Affiche les 10 premiers événements à venir (non passés), navigation uniquement sur les suivants
upcomingEventsOffset = 0;

function renderUpcomingEvents() {
	const container = document.querySelector('.events-list');
	if (!container) return;

	let events = [];
	if (typeof googleEvents !== 'undefined' && googleEvents && googleEvents.length > 0) {
		events = googleEvents
			.slice()
			.filter(ev => !ev.id || !ev.id.startsWith('Weeknum'))
			.sort((a, b) => new Date(a.start) - new Date(b.start));
	}

	let firstUpcomingIdx = events.findIndex(ev => new Date(ev.start) >= new Date());
	if (firstUpcomingIdx === -1) firstUpcomingIdx = events.length;

	const pageSize = 5;
	const scrollStep = 2; // Défiler de 2 événements à chaque clic
	const total = events.length;
	const maxOffset = Math.max(0, total - pageSize);

	// Initial offset: 5 prochains événements à partir d'aujourd'hui
	if (upcomingEventsOffset === -1) {
		upcomingEventsOffset = firstUpcomingIdx;
		initialUpcomingOffset = firstUpcomingIdx;
	}
	// Clamp offset
	upcomingEventsOffset = Math.max(0, Math.min(upcomingEventsOffset, maxOffset));

	const pageEvents = events.slice(upcomingEventsOffset, upcomingEventsOffset + pageSize);

	let html = '<h4>Événements</h4>';
	html += `
		<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
			<button id="upcoming-prev" class="events-list-nav-btn"${upcomingEventsOffset === 0 ? ' disabled' : ''}><span class="chevron-arrow"></span></button>
			<button id="upcoming-next" class="events-list-nav-btn"${upcomingEventsOffset + pageSize >= events.length ? ' disabled' : ''}><span class="chevron-arrow"></span></button>
		</div>
	`;

	if (pageEvents.length === 0) {
		html += '<div class="no-events">Aucun événement</div>';
	} else {
		const now = new Date();
		html += '<ul class="upcoming-events">';
		pageEvents.forEach(ev => {
			const date = ev.start ? new Date(ev.start) : null;
			const isPast = date && date < now;
			let dateStr = '';
			let timeStr = '';
			if (date) {
				dateStr = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: '2-digit' });
				if (ev.startTime && ev.startTime !== 'Toute la journée') {
					timeStr = ev.startTime;
					if (ev.endTime && ev.endTime !== ev.startTime) {
						timeStr += ' - ' + ev.endTime;
					}
				} else {
					timeStr = 'Toute la journée';
				}
			}
			html += `<li class="upcoming-event${isPast ? ' past' : ''}" style="border-left-color:${ev.backgroundColor || '#4285F4'}">
				<span style="font-weight:bold;font-size:15px;margin-bottom:2px;">${dateStr}</span>
				<span style="color:#1976d2;font-size:13px;margin-bottom:2px;">${timeStr}</span>
				<div><strong>${ev.title || 'Sans titre'}</strong></div>
			</li>`;
		});
		html += '</ul>';
	}
	container.innerHTML = html;

	// Gestionnaires pour les flèches
	const prevBtn = document.getElementById('upcoming-prev');
	const nextBtn = document.getElementById('upcoming-next');
	if (prevBtn) prevBtn.onclick = function() {
		if (upcomingEventsOffset > 0) {
			upcomingEventsOffset = Math.max(0, upcomingEventsOffset - scrollStep);
			renderUpcomingEvents();
		}
	};
	if (nextBtn) nextBtn.onclick = function() {
		if (upcomingEventsOffset + pageSize < total) {
			upcomingEventsOffset = Math.min(maxOffset, upcomingEventsOffset + scrollStep);
			renderUpcomingEvents();
		}
	};
}

// Si Google Calendar est connecté, re-render la liste des événements à venir après fetchGoogleEvents
if (typeof fetchGoogleEvents === 'function') {
	const originalFetchGoogleEvents = fetchGoogleEvents;
	window.fetchGoogleEvents = async function(...args) {
		const result = await originalFetchGoogleEvents.apply(this, args);
		upcomingEventsOffset = -1;
		renderUpcomingEvents();
		return result;
	};
}