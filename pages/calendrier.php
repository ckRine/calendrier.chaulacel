<?php 
	include(TEMPLATES_PATH.'/header/header.php');
	include(TEMPLATES_PATH.'/auth/auth-form.php');
	include(TEMPLATES_PATH.'/controls/controls.php');
?>

<div class="calendar-container">
	
	<div class="nav">
		<button onclick="prevMonths()">◄ 1 mois</button>
		<select id="year" onchange="updateYear(this.value)">
			<option value="2020">2020</option>
			<option value="2021">2021</option>
			<option value="2022">2022</option>
			<option value="2023">2023</option>
			<option value="2024">2024</option>
			<option value="2025" selected>2025</option>
		</select>
		<button onclick="nextMonths()">1 mois ►</button>
		<button onclick="goToToday()">Aujourd'hui</button>
	</div>
    <div class="calendar-nav prev" onclick="prevMonths()">&lt;</div>
    <div class="calendar" id="calendar"></div>
    <div class="calendar-nav next" onclick="nextMonths()">&gt;</div>
</div>