// Fonctions pour la popup d'agenda

// Variable pour stocker la date sélectionnée
let selectedPopupDate = null;

// Rendre la fonction showDayPopup disponible globalement
window.showDayPopup = function(date) {
	selectedPopupDate = date;
	
	// Formater la date pour l'affichage
	const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
	const formattedDate = date.toLocaleDateString('fr-FR', options);
	
	// Mettre à jour le titre
	const popupTitle = document.getElementById('day-popup-title');
	popupTitle.textContent = `Agenda du ${formattedDate}`;

	// Récupérer les informations pour cette date
	const dateInfo = getEventsForDate(date);
	const events = dateInfo.events;
	const holidayName = dateInfo.holidayName;
	const schoolHolidayZones = dateInfo.schoolHolidayZones;
	
	// Séparer les événements sur toute la journée des événements avec horaire
	const allDayEvents = events.filter(event => event.startTime === 'Toute la journée');
	const timedEvents = events.filter(event => event.startTime !== 'Toute la journée');
	
	// Trier les événements avec horaire par heure de début
	timedEvents.sort((a, b) => a.startTime.localeCompare(b.startTime));
	
	// Mettre à jour les informations du jour
	const popupInfo = document.getElementById('day-popup-info');
	let infoHtml = '';
	
	// Ajouter le jour de la semaine et la date
	infoHtml += `<div class="day-info-date">${formattedDate}</div>`;
	
	// Ajouter le numéro de semaine
	const weekNumber = getWeekNumber(date);
	infoHtml += `<div class="day-info-week">Semaine ${weekNumber}</div>`;
	
	// Ajouter le jour de l'année
	const dayOfYear = getDayOfYear(date);
	const daysLeft = getDaysLeftInYear(date);
	infoHtml += `<div class="day-info-yearday">Jour ${dayOfYear} de l'année</div>`;
	infoHtml += `<div class="day-info-daysleft">${daysLeft} jours restants</div>`;
	
	popupInfo.innerHTML = infoHtml;
	
	// Mettre à jour les événements
	const popupEvents = document.getElementById('day-popup-events');
	
	if (events.length === 0) {
			popupEvents.innerHTML = '<div class="no-events">Aucun événement pour cette journée</div>';
	} else {
			let contentHtml = '';
			
			// Ajouter le jour férié s'il existe
			if (holidayName) {
					contentHtml += `
							<div class="holiday-event">
									<div class="event-title">${holidayName}</div>
									<div class="event-location">Jour férié</div>
							</div>
					`;
			}
			
			// Section des événements sur toute la journée
			if (allDayEvents.length > 0) {
					contentHtml += '<div class="all-day-section">';
					contentHtml += '<div class="all-day-header">Toute la journée</div>';
					contentHtml += '<div class="all-day-events">';
					
					allDayEvents.forEach(event => {
							// Ne pas afficher à nouveau le jour férié qui est déjà affiché
							if (!(event.location === 'Jour férié' && holidayName === event.title)) {
									contentHtml += `
											<div class="all-day-event" style="background-color: ${getLighterColor(event.color)}">
													<div class="event-details">
															<div class="event-title">${event.title}</div>
															${event.location ? `<div class="event-location">${event.location}</div>` : ''}
													</div>
											</div>
									`;
							}
					});
					
					contentHtml += '</div></div>';
			}
			
			// Section des événements avec horaire
			if (timedEvents.length > 0) {
					contentHtml += '<div class="timed-events">';
					
					timedEvents.forEach(event => {
							contentHtml += `
									<div class="event-item" style="--event-color: ${event.color}">
											<div class="event-time">${formatTime(event.startTime)}${event.endTime ? ` - ${formatTime(event.endTime)}` : ''}</div>
											<div class="event-details">
													<div class="event-title">${event.title}</div>
													${event.location ? `<div class="event-location">${event.location}</div>` : ''}
											</div>
									</div>
							`;
					});
					
					contentHtml += '</div>';
			}
			
			popupEvents.innerHTML = contentHtml;
			
			// Appliquer les couleurs aux événements
			document.querySelectorAll('.event-item').forEach(item => {
					const color = item.style.getPropertyValue('--event-color');
					item.querySelector('.event-time').style.color = getDarkerColor(color);
					item.style.removeProperty('--event-color');
					item.style.setProperty('border-left', `4px solid ${color}`);
			});
			
			document.querySelectorAll('.all-day-event').forEach(item => {
					const bgColor = item.style.backgroundColor;
					const baseColor = getBaseColor(bgColor);
					item.style.setProperty('border-left', `4px solid ${baseColor}`);
			});
	}
	
	// Mettre à jour les zones de vacances scolaires
	const popupZones = document.getElementById('day-popup-zones');
	let zonesHtml = '';
	
	if (schoolHolidayZones.length > 0) {
			zonesHtml += '<div class="school-holiday-info">';
			zonesHtml += '<div class="school-holiday-title">Vacances scolaires</div>';
			zonesHtml += '<div class="school-holiday-zones">';
			
			schoolHolidayZones.forEach(zone => {
					zonesHtml += `<div class="zone-badge zone-${zone}">Zone ${zone}</div>`;
			});
			
			zonesHtml += '</div></div>';
	} else {
			zonesHtml = '<div class="no-zones">Pas de vacances scolaires ce jour</div>';
	}
	
	popupZones.innerHTML = zonesHtml;
	
	// Afficher la popup
	const popup = document.getElementById('day-popup');
	popup.classList.add('show');
	
	// Ajouter un gestionnaire d'événement pour fermer la popup en cliquant en dehors
	document.addEventListener('click', closePopupOnClickOutside);
};

// Fonction pour formater l'heure au format 24h
function formatTime(timeStr) {
	if (timeStr === 'Toute la journée') return timeStr;
	
	// Si le format est déjà HH:MM, le retourner tel quel
	if (/^\d{1,2}:\d{2}$/.test(timeStr)) return timeStr;
	
	try {
			// Convertir le format ISO (HH:MM:SS) en HH:MM
			if (timeStr.includes('T')) {
					const timePart = timeStr.split('T')[1];
					return timePart.substring(0, 5);
			}
			
			// Sinon, essayer de parser comme une heure
			const date = new Date(timeStr);
			if (!isNaN(date.getTime())) {
					return date.getHours().toString().padStart(2, '0') + ':' + 
								date.getMinutes().toString().padStart(2, '0');
			}
	} catch (e) {
			console.error("Erreur lors du formatage de l'heure:", e);
	}
	
	// En cas d'échec, retourner la chaîne originale
	return timeStr;
}

// Fonction pour fermer la popup d'agenda
window.hideDayPopup = function() {
	const popup = document.getElementById('day-popup');
	popup.classList.remove('show');
	
	// Supprimer le gestionnaire d'événement
	document.removeEventListener('click', closePopupOnClickOutside);
};

// Fonction pour fermer la popup en cliquant en dehors
function closePopupOnClickOutside(event) {
	const popup = document.getElementById('day-popup');
	if (!popup.contains(event.target) && !event.target.closest('.day-number')) {
			hideDayPopup();
	}
}

// Fonction pour récupérer les événements pour une date donnée
function getEventsForDate(date) {
	// Formater la date au format YYYY-MM-DD
	const formattedDate = date.toISOString().split('T')[0];
	let events = [];
	
	// Récupérer les événements Google Calendar si disponibles
	if (typeof googleEvents !== 'undefined' && googleEvents && googleEvents.length > 0) {
			const dateStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
			
			// Filtrer les événements pour cette date
			const eventsForDay = googleEvents.filter(event => {
					const eventDate = event.start.substring(0, 10);
					// Vérifier si le calendrier est sélectionné ou si aucun calendrier n'est sélectionné
					const isCalendarSelected = window.selectedCalendars.length === 0 || window.selectedCalendars.includes(event.calendarId);
					return eventDate === dateStr && isCalendarSelected && !event.id.startsWith('Weeknum');
			});
			
			events = eventsForDay.map(event => {
					return {
							title: event.title || 'Sans titre',
							startTime: event.startTime || '00:00',
							endTime: event.endTime || '',
							location: event.calendarName || '',
							color: event.backgroundColor || '#4285f4'
					};
			});
	}
	
	// Vérifier si c'est un jour férié
	const month = date.getMonth() + 1;
	const day = date.getDate();
	const year = date.getFullYear();
	const holidays = window.holidays || {};
	const holidayName = holidays[`${month}-${day}`];
	
	if (holidayName) {
			events.push({
					title: holidayName,
					startTime: 'Toute la journée',
					endTime: '',
					location: 'Jour férié',
					color: '#d60000'
			});
	}
	
	// Vérifier si c'est un jour de vacances scolaires
	const schoolHolidays = window.schoolHolidays || { A: {}, B: {}, C: {} };
	const zones = [];
	
	['A', 'B', 'C'].forEach(zone => {
			if (schoolHolidays[zone] && schoolHolidays[zone][`${month}-${day}`]) {
					zones.push(zone);
			}
	});
	
	return {
			events: events,
			holidayName: holidayName,
			schoolHolidayZones: zones
	};
}

// Fonction pour obtenir une version plus claire d'une couleur
function getLighterColor(color) {
	// Convertir la couleur en RGB
	let r, g, b;
	
	if (color.startsWith('#')) {
			// Format hexadécimal
			const hex = color.substring(1);
			r = parseInt(hex.substring(0, 2), 16);
			g = parseInt(hex.substring(2, 4), 16);
			b = parseInt(hex.substring(4, 6), 16);
	} else {
			// Couleur par défaut
			return '#e8f0fe'; // Bleu clair par défaut
	}
	
	// Éclaircir la couleur (mélanger avec du blanc)
	r = Math.floor(r * 0.2 + 255 * 0.8);
	g = Math.floor(g * 0.2 + 255 * 0.8);
	b = Math.floor(b * 0.2 + 255 * 0.8);
	
	// Convertir en hexadécimal
	return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
}

// Fonction pour obtenir une version plus foncée d'une couleur
function getDarkerColor(color) {
	// Convertir la couleur en RGB
	let r, g, b;
	
	if (color.startsWith('#')) {
			// Format hexadécimal
			const hex = color.substring(1);
			r = parseInt(hex.substring(0, 2), 16);
			g = parseInt(hex.substring(2, 4), 16);
			b = parseInt(hex.substring(4, 6), 16);
	} else {
			// Couleur par défaut
			return '#1a73e8'; // Bleu foncé par défaut
	}
	
	// Assombrir la couleur
	r = Math.floor(r * 0.7);
	g = Math.floor(g * 0.7);
	b = Math.floor(b * 0.7);
	
	// Convertir en hexadécimal
	return `#${r.toString(16).padStart(2, '0')}${g.toString(16).padStart(2, '0')}${b.toString(16).padStart(2, '0')}`;
}

// Fonction pour obtenir la couleur de base à partir d'une couleur claire
function getBaseColor(lightColor) {
	// Si la couleur est au format rgba
	if (lightColor.startsWith('rgba') || lightColor.startsWith('rgb')) {
			const rgbValues = lightColor.match(/\d+/g);
			if (rgbValues && rgbValues.length >= 3) {
					const r = parseInt(rgbValues[0]);
					const g = parseInt(rgbValues[1]);
					const b = parseInt(rgbValues[2]);
					
					// Rendre la couleur plus foncée
					const darkerR = Math.floor(r * 0.5);
					const darkerG = Math.floor(g * 0.5);
					const darkerB = Math.floor(b * 0.5);
					
					return `rgb(${darkerR}, ${darkerG}, ${darkerB})`;
			}
	}
	
	// Par défaut, retourner une couleur bleue
	return '#1a73e8';
}

// Fermer la popup avec la touche Escape
document.addEventListener('keydown', function(event) {
	if (event.key === 'Escape') {
			const popup = document.getElementById('day-popup');
			if (popup && popup.classList.contains('show')) {
					hideDayPopup();
			}
	}
});
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

// Fonction pour obtenir le nombre de jours restants dans l'année
function getDaysLeftInYear(date) {
	const yearEnd = new Date(date.getFullYear(), 11, 31);
	const diff = yearEnd - date;
	const oneDay = 1000 * 60 * 60 * 24;
	return Math.floor(diff / oneDay);
}