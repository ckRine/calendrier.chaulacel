<?php
/**
 * Module d'exportation du calendrier
 * Permet d'exporter le calendrier au format iCalendar (.ics)
 */

// Inclure la configuration
require_once('../common/conf.php');

// Vérifier si l'utilisateur est connecté
session_start();
$is_logged_in = isset($_SESSION['user_id']);

// Récupérer les paramètres
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$zones = isset($_GET['zones']) ? $_GET['zones'] : ['A'];
if (!is_array($zones)) {
    $zones = explode(',', $zones);
}

// Valider l'année
if ($year < 2000 || $year > 2050) {
    $year = date('Y');
}

// Valider les zones
$valid_zones = [];
foreach ($zones as $zone) {
    if (in_array($zone, ['A', 'B', 'C'])) {
        $valid_zones[] = $zone;
    }
}
if (empty($valid_zones)) {
    $valid_zones = ['A'];
}

// Fonction pour récupérer les jours fériés
function get_holidays($year) {
    $url = "https://calendrier.api.gouv.fr/jours-feries/metropole/{$year}.json";
    $json = file_get_contents($url);
    if ($json === false) {
        return [];
    }
    
    $data = json_decode($json, true);
    if (!$data) {
        return [];
    }
    
    $holidays = [];
    foreach ($data as $date => $name) {
        $holidays[] = [
            'date' => $date,
            'name' => $name
        ];
    }
    
    return $holidays;
}

// Fonction pour récupérer les vacances scolaires
function get_school_holidays($year, $zone) {
    $url = "https://data.education.gouv.fr/api/records/1.0/search/?dataset=fr-en-calendrier-scolaire&q=&rows=1000&sort=-start_date&facet=start_date&facet=end_date&facet=description&refine.zones=Zone+{$zone}";
    $json = file_get_contents($url);
    if ($json === false) {
        return [];
    }
    
    $data = json_decode($json, true);
    if (!isset($data['records'])) {
        return [];
    }
    
    $vacations = [];
    foreach ($data['records'] as $record) {
        $start = new DateTime($record['fields']['start_date']);
        $end = new DateTime($record['fields']['end_date']);
        $description = $record['fields']['description'];
        
        // Filtrer par année
        $record_year = intval($start->format('Y'));
        if ($record_year == $year || $record_year == $year - 1 || $record_year == $year + 1) {
            $vacations[] = [
                'start' => $start->format('Y-m-d'),
                'end' => $end->format('Y-m-d'),
                'description' => $description,
                'zone' => "Zone {$zone}"
            ];
        }
    }
    
    return $vacations;
}

// Générer le fichier iCalendar
function generate_ical($year, $zones) {
    // Début du fichier iCalendar
    $ical = "BEGIN:VCALENDAR\r\n";
    $ical .= "VERSION:2.0\r\n";
    $ical .= "PRODID:-//ChronoGestCal//Calendrier Scolaire//FR\r\n";
    $ical .= "CALSCALE:GREGORIAN\r\n";
    $ical .= "METHOD:PUBLISH\r\n";
    $ical .= "X-WR-CALNAME:Calendrier Scolaire " . implode(', ', $zones) . " - {$year}\r\n";
    $ical .= "X-WR-TIMEZONE:Europe/Paris\r\n";
    
    // Ajouter les jours fériés
    $holidays = get_holidays($year);
    foreach ($holidays as $holiday) {
        $date = new DateTime($holiday['date']);
        $date_str = $date->format('Ymd');
        $next_day = clone $date;
        $next_day->modify('+1 day');
        $next_day_str = $next_day->format('Ymd');
        
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "DTSTART;VALUE=DATE:{$date_str}\r\n";
        $ical .= "DTEND;VALUE=DATE:{$next_day_str}\r\n";
        $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ical .= "UID:holiday-" . md5($holiday['date'] . $holiday['name']) . "@ChronoGestCal.fr\r\n";
        $ical .= "CREATED:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ical .= "DESCRIPTION:Jour férié en France\r\n";
        $ical .= "LAST-MODIFIED:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ical .= "LOCATION:France\r\n";
        $ical .= "SEQUENCE:0\r\n";
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "SUMMARY:Jour férié: " . $holiday['name'] . "\r\n";
        $ical .= "TRANSP:TRANSPARENT\r\n";
        $ical .= "END:VEVENT\r\n";
    }
    
    // Ajouter les vacances scolaires pour chaque zone
    foreach ($zones as $zone) {
        $vacations = get_school_holidays($year, $zone);
        foreach ($vacations as $vacation) {
            $start = new DateTime($vacation['start']);
            $start_str = $start->format('Ymd');
            $end = new DateTime($vacation['end']);
            $end->modify('+1 day'); // iCal uses exclusive end date
            $end_str = $end->format('Ymd');
            
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "DTSTART;VALUE=DATE:{$start_str}\r\n";
            $ical .= "DTEND;VALUE=DATE:{$end_str}\r\n";
            $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ical .= "UID:vacation-" . md5($vacation['start'] . $vacation['end'] . $zone) . "@ChronoGestCal.fr\r\n";
            $ical .= "CREATED:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ical .= "DESCRIPTION:Vacances scolaires " . $vacation['zone'] . "\r\n";
            $ical .= "LAST-MODIFIED:" . gmdate('Ymd\THis\Z') . "\r\n";
            $ical .= "LOCATION:France\r\n";
            $ical .= "SEQUENCE:0\r\n";
            $ical .= "STATUS:CONFIRMED\r\n";
            $ical .= "SUMMARY:" . $vacation['description'] . " - " . $vacation['zone'] . "\r\n";
            $ical .= "TRANSP:TRANSPARENT\r\n";
            $ical .= "END:VEVENT\r\n";
        }
    }
    
    // Fin du fichier iCalendar
    $ical .= "END:VCALENDAR\r\n";
    
    return $ical;
}

// Générer le contenu iCalendar
$ical_content = generate_ical($year, $valid_zones);

// Définir les en-têtes pour le téléchargement
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="calendrier_scolaire_' . implode('_', $valid_zones) . '_' . $year . '.ics"');

// Envoyer le contenu
echo $ical_content;