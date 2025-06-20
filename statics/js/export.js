/**
 * Fonctions pour l'exportation du calendrier
 */

// Fonction pour exporter le calendrier au format iCalendar (.ics)
function exportCalendarToICS() {
    // Récupérer l'année et les zones sélectionnées
    const exportYear = document.getElementById('export-year').value;
    const zones = [];
    
    document.querySelectorAll('#export-zones input:checked').forEach(checkbox => {
        zones.push(checkbox.value);
    });
    
    if (zones.length === 0) {
        alert('Veuillez sélectionner au moins une zone.');
        return;
    }
    
    // Construire l'URL d'exportation
    const exportUrl = `./modules/export_calendar.php?year=${exportYear}&zones=${zones.join(',')}`;
    
    // Déclencher le téléchargement
    window.location.href = exportUrl;
}

// Afficher la boîte de dialogue d'exportation
function showExportDialog() {
    // Créer la boîte de dialogue si elle n'existe pas
    if (!document.getElementById('export-dialog')) {
        const dialog = document.createElement('div');
        dialog.id = 'export-dialog';
        dialog.className = 'modal';
        
        const currentYear = new Date().getFullYear();
        
        dialog.innerHTML = `
            <div class="modal-content">
                <span class="close" onclick="document.getElementById('export-dialog').style.display='none'">&times;</span>
                <h2>Exporter le calendrier</h2>
                <div class="export-options">
                    <div class="form-group">
                        <label for="export-year">Année à exporter :</label>
                        <select id="export-year">
                            ${Array.from({length: 11}, (_, i) => currentYear - 5 + i)
                                .map(year => `<option value="${year}" ${year === currentYear ? 'selected' : ''}>${year}</option>`)
                                .join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Zones à inclure :</label>
                        <div id="export-zones" class="checkbox-group">
                            <label><input type="checkbox" value="A" checked> Zone A</label>
                            <label><input type="checkbox" value="B"> Zone B</label>
                            <label><input type="checkbox" value="C"> Zone C</label>
                        </div>
                    </div>
                </div>
                <div class="export-actions">
                    <button onclick="exportCalendarToICS()">Exporter (iCalendar)</button>
                    <button onclick="document.getElementById('export-dialog').style.display='none'">Annuler</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(dialog);
    }
    
    // Mettre à jour les zones cochées en fonction des préférences actuelles
    const checkboxes = document.querySelectorAll('#export-zones input');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectedZones.includes(checkbox.value);
    });
    
    // Afficher la boîte de dialogue
    document.getElementById('export-dialog').style.display = 'flex';
}

// Ajouter un bouton d'exportation dans les contrôles
document.addEventListener('DOMContentLoaded', function() {
    const controlsDiv = document.querySelector('.controls');
    if (controlsDiv) {
        const exportButton = document.createElement('button');
        exportButton.className = 'export-button';
        exportButton.innerHTML = '<img src="' + STATICS_PATH + '/img/export-icon.svg" alt="Exporter"> Exporter';
        exportButton.onclick = showExportDialog;
        
        controlsDiv.appendChild(exportButton);
    }
});