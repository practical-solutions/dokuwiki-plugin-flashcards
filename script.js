
/* n = move from present card n steps to next one */
function showPage(n=0){
	
	// Hide present card
	document.getElementById("card"+chosen).style.display = "none";
	
	// move on
	chosen += n;
	
	// check if valid; karten = amount of cards
	if (chosen < 0) chosen = 0;
	if (chosen > karten) chosen = karten;
	
	presentQ();
	progressBar(Math.round((chosen/karten*100)));
	
	// display chosen card
	document.getElementById("card"+chosen).style.display = "block";
	
	if (chosen==0) Menu(0);
	
}

// Display present question nr.
function presentQ(){
	document.getElementById("fc_info").innerHTML = "Frage "+(Math.round(chosen/2))+"/"+Math.round(karten/2);
}

/* start reading mode */
function startread(){
	document.getElementById("steuerung").style.display = "block";
	showPage(1);
	Menu(1);
}

/* show all questions and awnsers while hiding the menu */
function showall(){
	document.getElementById("card0").style.display = "none"; /* start page with options */
	
	for (var c=1;c<karten;c++) {
		document.getElementById("card"+c).style.display = "block";
		document.getElementById("card"+c).style.minHeight = 0;
	}
}

function startquiz() {
	for (var c=0;c<karten+1;c++) awnsered[c] = false;
	
	document.getElementById("fc_progress_fill").style.backgroundColor = "green";
	nextQuestion();
}

/* move on in quiz mode */
function nextQuestion(){
	// Hide present card
	document.getElementById("card"+chosen).style.display = "none";
	
	if (awnsered.lastIndexOf(false) < 1) {
		document.getElementById("card0").innerHTML = "Du hast alle Fragen richtig beantwortet! <br><br> <button onClick='window.location.reload();'>Refresh Page</button>";
		chosen=0;
		showPage();
		return;
	}
	
	do {
		chosen++;
		if (chosen>karten) chosen = 1;
	} while (awnsered[chosen]);
	
	// display chosen card
	document.getElementById("card"+chosen).style.display = "block";
	if (isOdd(chosen)) {Menu(3);} else {Menu(2);}
	
	progressBar(percentage());
	presentQ();
	
	
}

/* percent correct awnsers */
function percentage(){
	var c=0;
	for (i=1;i<awnsered.length;i++) {
		if (awnsered[i]) c++;
	}
	
	return Math.round((c/(i-1))*100);
}


function correct() {
	awnsered[chosen-1] = true;
	awnsered[chosen] = true;
	nextQuestion();
}

function wrong(){
	nextQuestion();
}

function showawnser(){
	nextQuestion();
}

function flip(n=0){
	if (n==1) {chosen = chosen-2; nextQuestion();}
}

/* 
0 = hide menu completely, 
1 = show scrolling arrows, 
2 = show quiz evaluations (correct/wrong-buttons),
3 = show quiz turn card button
*/
function Menu(n=0) {
	
	switch (n) {
		case 1: var btn = Array(1,5);break;
		case 2: var btn = Array(2,4,6);break;
		case 3: var btn = Array(0,3);break;
		default: document.getElementById("steuerung").style.display = "none";
				 return;
	}
	
	/* hide all */
	for (var c=1;c<7;c++) {document.getElementById("btn"+c).style.display = "none";}
	
	
	// display chosen buttons
	for (c=0;c<btn.length;c++) {
		if (btn[c] > 0)
			document.getElementById("btn"+btn[c]).style.display = "inline-block";
	}
	
	document.getElementById("steuerung").style.display = "block";
	
}

function isOdd(num) {
	if (num === 0) return false;
	return (num & -num) === 1;
}

function progressBar(n){
	document.getElementById("fc_progress_fill").style.width = n+"%";
}

// This card is displayed at present
var chosen = 0;
var awnsered = new Array();
