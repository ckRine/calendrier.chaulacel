/**
 * Fonctions pour l'impression du calendrier
 */

// Fonction pour préparer le calendrier à l'impression
function printCalendar() {
    // Sauvegarder l'état actuel du calendrier
    const currentState = {
        centralDate: new Date(centralDate),
        selectedZones: [...selectedZones]
    };
    
    // Déterminer la période à imprimer
    const printPeriod = document.getElementById('print-period').value;
    const printYear = parseInt(document.getElementById('print-year').value);
    
    // Configurer le calendrier pour l'impression
    switch(printPeriod) {
        case 'current':
            // Garder la vue actuelle
            break;
        case 'year':
            // Afficher toute l'année
            preparePrintYear(printYear);
            break;
        case 'semester1':
            // Premier semestre (Janvier-Juin)
            preparePrintSemester(printYear, 0, 5);
            break;
        case 'semester2':
            // Second semestre (Juillet-Décembre)
            preparePrintSemester(printYear, 6, 11);
            break;
        case 'trimester1':
            // Premier trimestre (Janvier-Mars)
            preparePrintTrimester(printYear, 0, 2);
            break;
        case 'trimester2':
            // Deuxième trimestre (Avril-Juin)
            preparePrintTrimester(printYear, 3, 5);
            break;
        case 'trimester3':
            // Troisième trimestre (Juillet-Septembre)
            preparePrintTrimester(printYear, 6, 8);
            break;
        case 'trimester4':
            // Quatrième trimestre (Octobre-Décembre)
            preparePrintTrimester(printYear, 9, 11);
            break;
    }
    
    // Ajouter une classe pour l'impression
    document.body.classList.add('printing');
    
    // Lancer l'impression
    setTimeout(() => {
        window.print();
        
        // Restaurer l'état après l'impression
        setTimeout(() => {
            document.body.classList.remove('printing');
            centralDate = new Date(currentState.centralDate);
            selectedZones = [...currentState.selectedZones];
            renderVisibleMonths();
        }, 1000);
    }, 500);
}

// Préparer l'impression de l'année complète
function preparePrintYear(year) {
    // Créer un conteneur temporaire pour tous les mois
    const tempContainer = document.createElement('div');
    tempContainer.className = 'print-year-container';
    
    // Générer tous les mois de l'année
    for (let month = 0; month < 12; month++) {
        const monthDiv = createMonthForPrint(year, month);
        tempContainer.appendChild(monthDiv);
    }
    
    // Remplacer le contenu du calendrier
    const calendar = document.getElementById('calendar');
    calendar.innerHTML = '';
    calendar.appendChild(tempContainer);
}

// Préparer l'impression d'un semestre
function preparePrintSemester(year, startMonth, endMonth) {
    // Créer un conteneur temporaire pour les mois du semestre
    const tempContainer = document.createElement('div');
    tempContainer.className = 'print-semester-container';
    
    // Générer les mois du semestre
    for (let month = startMonth; month <= endMonth; month++) {
        const monthDiv = createMonthForPrint(year, month);
        tempContainer.appendChild(monthDiv);
    }
    
    // Remplacer le contenu du calendrier
    const calendar = document.getElementById('calendar');
    calendar.innerHTML = '';
    calendar.appendChild(tempContainer);
}

// Préparer l'impression d'un trimestre
function preparePrintTrimester(year, startMonth, endMonth) {
    // Créer un conteneur temporaire pour les mois du trimestre
    const tempContainer = document.createElement('div');
    tempContainer.className = 'print-trimester-container';
    
    // Générer les mois du trimestre
    for (let month = startMonth; month <= endMonth; month++) {
        const monthDiv = createMonthForPrint(year, month);
        tempContainer.appendChild(monthDiv);
    }
    
    // Remplacer le contenu du calendrier
    const calendar = document.getElementById('calendar');
    calendar.innerHTML = '';
    calendar.appendChild(tempContainer);
}

// Créer un mois pour l'impression
async function createMonthForPrint(year, month) {
    const date = new Date(year, month, 1);
    const monthDiv = document.createElement('div');
    monthDiv.className = 'month print-month';
    monthDiv.innerHTML = `<h2>${months[month]} ${year}</h2>`;

    const daysDiv = document.createElement('div');
    daysDiv.className = 'days';

    const lastDate = new Date(year, month + 1, 0).getDate();
    
    // Récupérer les jours fériés et vacances scolaires
    const holidays = await fetchHolidays(year);
    const schoolHolidays = { A: {}, B: {}, C: {} };
    for (const zone of ['A', 'B', 'C']) {
        if (selectedZones.includes(zone)) {
            schoolHolidays[zone] = await fetchSchoolHolidays(year, zone);
        }
    }

    // Créer les jours du mois
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
        
        // Ajouter le numéro du jour
        const dayNumber = document.createElement('span');
        dayNumber.className = 'day-number';
        dayNumber.textContent = i;
        dayDiv.appendChild(dayNumber);
        
        // Ajouter la lettre du jour
        const dayLetterSpan = document.createElement('span');
        dayLetterSpan.className = 'day-letter';
        dayLetterSpan.textContent = dayLetter;
        dayDiv.appendChild(dayLetterSpan);
        
        // Ajouter le nom du jour férié si présent
        if (displayText) {
            const holidayName = document.createElement('span');
            holidayName.className = 'holiday-name';
            holidayName.textContent = displayText;
            dayDiv.appendChild(holidayName);
        }
        
        // Ajouter les barres de vacances
        const vacationBarsDiv = document.createElement('div');
        vacationBarsDiv.className = 'vacation-bars';
        vacationBarsDiv.innerHTML = vacationBars;
        dayDiv.appendChild(vacationBarsDiv);
        
        daysDiv.appendChild(dayDiv);
    }

    monthDiv.appendChild(daysDiv);
    return monthDiv;
}

// Afficher la boîte de dialogue d'impression
function showPrintDialog() {
    // Créer la boîte de dialogue si elle n'existe pas
    if (!document.getElementById('print-dialog')) {
        const dialog = document.createElement('div');
        dialog.id = 'print-dialog';
        dialog.className = 'modal';
        
        const currentYear = new Date().getFullYear();
        
        dialog.innerHTML = `
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('print-dialog').style.display='none'">&times;</span>
                <h2>Options d'impression</h2>
                <div class="print-options">
                    <div class="form-group">
                        <label for="print-period">Période à imprimer :</label>
                        <select id="print-period">
                            <option value="current">Vue actuelle</option>
                            <option value="year">Année complète</option>
                            <option value="semester1">1er semestre (Jan-Juin)</option>
                            <option value="semester2">2ème semestre (Juil-Déc)</option>
                            <option value="trimester1">1er trimestre (Jan-Mar)</option>
                            <option value="trimester2">2ème trimestre (Avr-Juin)</option>
                            <option value="trimester3">3ème trimestre (Juil-Sep)</option>
                            <option value="trimester4">4ème trimestre (Oct-Déc)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="print-year">Année :</label>
                        <select id="print-year">
                            ${Array.from({length: 11}, (_, i) => currentYear - 5 + i)
                                .map(year => `<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`)
                                .join('')}
                        </select>
                    </div>
                </div>
                <div class="print-actions">
                    <button onclick="printCalendar()">Imprimer</button>
                    <button onclick="document.getElementById('print-dialog').style.display='none'">Annuler</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(dialog);
    }
    
    // Afficher la boîte de dialogue
    document.getElementById('print-dialog').style.display = 'flex';
}

// Ajouter un bouton d'impression dans les contrôles
document.addEventListener('DOMContentLoaded', function() {
    const controlsDiv = document.querySelector('.controls');
    if (controlsDiv) {
        const printButton = document.createElement('button');
        printButton.className = 'print-button';
        printButton.innerHTML = '<img src="' + STATICS_PATH + '/img/print-icon.svg" alt="Imprimer"> Imprimer';
        printButton.onclick = showPrintDialog;
        
        controlsDiv.appendChild(printButton);
    }
});