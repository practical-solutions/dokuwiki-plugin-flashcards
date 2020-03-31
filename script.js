/* n = move from present card n steps to next one */
function showPage(n=0){
	
	// Hide present card
	document.getElementById("card"+chosen).style.display = "none";
	
	// mov on
	chosen += n;
	
	// check if valid; karten = amount of cards
	if (chosen < 0) chosen = 0;
	if (chosen > karten-1) chosen = karten-1;
	
	// display chosen card
	document.getElementById("card"+chosen).style.display = "block";
	
}

// This card is displayed at present
var chosen = 0;
