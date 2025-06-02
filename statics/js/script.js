const calendar = document.getElementById('calendar');
const yearSpan = document.getElementById('year');
let currentYear = new Date().getFullYear();
let centralDate = new Date(); // Utilisé comme point de référence

gapi.load('client:auth2', () => {
		gapi.client.init({
				apiKey: API_KEY,
				clientId: CLIENT_ID,
				discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest'],
				scope: SCOPES
		}).then(() => {
				// Ensure auth2 is initialized
				gapi.auth2.init({
						client_id: CLIENT_ID,
						scope: SCOPES
				}).then(() => {
						initClient();
				});
		}).catch(error => {
				console.error('Error initializing Google API client:', error);
		});
});

const months = [
		'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
		'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
];
const dayLetters = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];
const fixedHolidays = {
		'1-1': 'Jour de l\'An',
		'5-1': 'Fête du Travail',
		'5-8': 'Victoire 1945',
		'7-14': 'Fête Nationale',
		'8-15': 'Assomption',
		'11-1': 'Toussaint',
		'11-11': 'Armistice',
		'12-25': 'Noël'
};

function getEasterSunday(year) {
		const a = year % 19;
		const b = Math.floor(year / 100);
		const c = year % 100;
		const d = Math.floor(b / 4);
		const e = b % 4;
		const f = Math.floor((b + 8) / 25);
		const g = Math.floor((b - f + 1) / 3);
		const h = (19 * a + b - d - g + 15) % 30;
		const i = Math.floor(c / 4);
		const k = c % 4;
		const l = (32 + 2 * e + 2 * i - h - k) % 7;
		const m = Math.floor((a + 11 * h + 22 * l) / 451);
		const month = Math.floor((h + l - 7 * m + 114) / 31);
		const day = ((h + l - 7 * m + 114) % 31) + 1;
		return new Date(year, month - 1, day);
}

const CLIENT_ID = '1007241733203-p87a3bce6v47ut1i28ul2sup1r86emmm.apps.googleusercontent.com';
const API_KEY = 'AIzaSyCVG0b41GRFSR3WVgnrzpDHGiMz6EKG-G0'; // Non nécessaire si OAuth
const SCOPES = 'https://www.googleapis.com/auth/calendar.events';

function initClient() {
		gapi.client.init({
				clientId: CLIENT_ID,
				scope: SCOPES
		}).then(() => {
				gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);
				updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
				renderVisibleMonths(); // Move initial render here
		}).catch(error => {
				console.error('Error initializing Google API client:', error);
		});
}

function getVariableHolidays(year) {
		const easter = getEasterSunday(year);
		const holidays = { ...fixedHolidays };

		const easterMonday = new Date(easter);
		easterMonday.setDate(easter.getDate() + 1);
		holidays[`${easterMonday.getMonth() + 1}-${easterMonday.getDate()}`] = 'Lundi de Pâques';

		const ascension = new Date(easter);
		ascension.setDate(easter.getDate() + 39);
		holidays[`${ascension.getMonth() + 1}-${ascension.getDate()}`] = 'Ascension';

		const pentecostMonday = new Date(easter);
		pentecostMonday.setDate(easter.getDate() + 50);
		holidays[`${pentecostMonday.getMonth() + 1}-${pentecostMonday.getDate()}`] = 'Lundi de Pentecôte';

		return holidays;
}

async function renderVisibleMonths() {
	 calendar.innerHTML = '';
		const centerMonth = centralDate.getMonth();
		const centerYear = centralDate.getFullYear();
		yearSpan.textContent = centerYear;

		const holidays = getVariableHolidays(centerYear);
		let googleEvents = [];

		// Check if auth2 is available and user is signed in
		if (gapi.auth2 && gapi.auth2.getAuthInstance && gapi.auth2.getAuthInstance().isSignedIn.get()) {
				try {
						const response = await gapi.client.calendar.events.list({
								calendarId: 'primary',
								timeMin: new Date(centerYear, centerMonth - 3, 1).toISOString(),
								timeMax: new Date(centerYear, centerMonth + 4, 0).toISOString(),
								singleEvents: true,
								orderBy: 'startTime'
						});
						googleEvents = response.result.items;
				} catch (error) {
						console.error('Error fetching Google Calendar events:', error);
				}
		}

		for (let offset = -3; offset <= 3; offset++) {
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

						const isToday = currentDate.toDateString() === new Date().toDateString();
						const isHoliday = holidays[`${month + 1}-${i}`];
						const isSaturday = dayIndex === 6;
						const isSunday = dayIndex === 0;

						const displayText = isHoliday ? isHoliday : '';
						daysDiv.innerHTML += `<div class="day ${isToday ? 'today' : ''} ${isHoliday ? 'holiday' : ''} ${isSaturday ? 'saturday' : ''} ${isSunday ? 'sunday' : ''}">
								${i} ${dayLetters[adjustedDayIndex]} ${displayText}
						</div>`;
				}

				monthDiv.appendChild(daysDiv);
				calendar.appendChild(monthDiv);
		}
}

function goToToday() {
		currentYear = new Date().getFullYear();
		renderVisibleMonths();
		const today = new Date();
		const currentMonth = today.getMonth();
		const monthElement = document.getElementById(`month-${currentMonth}`);
		if (monthElement) {
				monthElement.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'center' });
		}
}

function prevYear() {
		currentYear--;
		renderVisibleMonths();
}
function prevMonths() {
		centralDate.setMonth(centralDate.getMonth() - 2);
		renderVisibleMonths();
}

function nextMonths() {
		centralDate.setMonth(centralDate.getMonth() + 2);
		renderVisibleMonths();
}

function goToToday() {
		centralDate = new Date();
		renderVisibleMonths();
}

function nextYear() {
		currentYear++;
		renderVisibleMonths();
}

renderVisibleMonths();

function handleCredentialResponse(response) {
		const syncBtn = document.getElementById("sync-button");
		if (syncBtn) {
				syncBtn.disabled = !response.credential;
		}
		// Décodez le jeton JWT et stockez-le
		gapi.client.setToken({ access_token: response.credential });
		renderVisibleMonths();
}

function updateSigninStatus(isSignedIn) {
		const syncBtn = document.getElementById("sync-button");
		if (syncBtn) {
				syncBtn.disabled = !isSignedIn;
		}
}

function handleAuthClick() {
	gapi.auth2.getAuthInstance().signIn()
		.then(() => {
			console.log("Connexion réussie");
		})
		.catch(error => {
		if (error && error.error === 'popup_closed_by_user') {
		console.warn("Connexion annulée par l'utilisateur.");
		} else {
		console.error("Erreur d'authentification :", error);
		}
	});
}

function handleSignOutClick() {
		gapi.auth2.getAuthInstance().signOut();
}

function syncToGoogleCalendar() {
		const holidays = Object.entries(getVariableHolidays(centralDate.getFullYear()));
		const events = [];

		holidays.forEach(([key, title]) => {
				const [month, day] = key.split('-');
				const date = new Date(centralDate.getFullYear(), month - 1, day);
				events.push({
						summary: title,
						start: { date: date.toISOString().split('T')[0] },
						end: { date: date.toISOString().split('T')[0] }
				});
		});

		schoolVacations.forEach(vac => {
				events.push({
						summary: vac.name,
						start: { date: vac.start.toISOString().split('T')[0] },
						end: { date: new Date(vac.end.getTime() + 86400000).toISOString().split('T')[0] }
				});
		});

		events.forEach(event => {
				gapi.client.calendar.events.insert({
						calendarId: 'primary',
						resource: event
				}).then(() => {
						console.log(`Événement ajouté : ${event.summary}`);
				});
		});

		alert("Événements synchronisés dans votre agenda Google.");
}
