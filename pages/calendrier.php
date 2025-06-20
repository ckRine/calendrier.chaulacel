<?php 
	include(TEMPLATES_PATH.'/header/header.php');
	include(TEMPLATES_PATH.'/auth/auth-form.php');
	include(TEMPLATES_PATH.'/controls/controls.php');
	include(TEMPLATES_PATH.'/day-popup.php');
	$page_name="calendrier";
?>

<div class="calendar-container">
	
	<div class="nav">
		<button onclick="prevMonths()">◄ Reculer</button>
		<select id="year" onchange="updateYear(this.value)">
			<?php
			$currentYear = date('Y');
			for ($i = $currentYear - 5; $i <= $currentYear + 5; $i++) {
				$selected = ($i == $currentYear) ? 'selected' : '';
				echo "<option value=\"$i\" $selected>$i</option>";
			}
			?>
		</select>
		<button onclick="nextMonths()">Avancer ►</button>
		<button onclick="goToToday()">Aujourd'hui</button>
		<div class="goto-date">
			<input type="date" id="date-picker" aria-label="Aller à une date">
			<button onclick="goToDate()">Aller à</button>
		</div>
	</div>
    <div class="calendar-nav prev" onclick="prevMonths()">&lt;</div>
    <div class="calendar" id="calendar"></div>
    <div class="calendar-nav next" onclick="nextMonths()">&gt;</div>
</div>