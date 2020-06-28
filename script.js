
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
    if (chosen<1) loadFromCookie();
    
    document.getElementById("fc_info").innerHTML = "Frage "+(Math.round(chosen/2))+"/"+Math.round(karten/2);
    
    if (chosen>0) {
        //console.log("Save: \n"+cookieText());
        setCookie("flashcard", cookieText(), 2);
    }
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
	
	for (var c=1;c<karten+1;c++) {
        pre = "<div class='plugin__flashcards_fp_border'><div class='plugin__flashcards_fp_header'>" + "Karte " + c + "</div>";
        document.getElementById("card"+c).innerHTML = pre + document.getElementById("card"+c).innerHTML + "</div>";

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
        setCookie("flashcard", "", 0);
		document.getElementById("card0").innerHTML = "Du hast alle Fragen richtig beantwortet! <br><br> <button onClick='window.location.reload();'>Refresh Page</button>";
		chosen=0;
		showPage();
		return;
	}
	
    msg = '';
	do {
		chosen++;
		if (chosen>karten) {
            chosen = 1;
            msg = 'Neue Runde. Du hast bisher <b>' + percentage() + '%</b> richtig.';
        }
	} while (awnsered[chosen]);
	
	// display chosen card
    message(msg);
    
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

function message(m){
    var msg = document.getElementById('plugin__flashcard_message');
    if (m=="") {
        msg.style.display = 'none';
    } else {
        msg.innerHTML = m;
        msg.style.display = 'block';
    }
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
	if (n==1) {
		document.getElementById("card"+chosen).style.display = "none";
		chosen = chosen-2; nextQuestion();
	}
}

// Cookie functions from: https://www.w3schools.com/js/js_cookies.asp
function setCookie(cname, cvalue, exdays) {
    user = JSINFO['user_id'];
    if (user != 'false') {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + '_' + JSINFO['user_id'] + "=" + cvalue + ";" + expires + ";path=/";
    }
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
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
    if (document.getElementById("plugin__flashcard_edit_btn")!=null) {
        if (JSINFO['access']=="editor") document.getElementById("plugin__flashcard_edit_btn").style.display = "inline-block";
    }
    
}

function isOdd(num) {
	if (num === 0) return false;
	return (num & -num) === 1;
}

function progressBar(n){
	document.getElementById("fc_progress_fill").style.width = n+"%";
}

function TextToEditor(){
	if (chosen>0) document.getElementById("cardtext").value=decodeURIComponent(card[chosen-1]);
	//console.log(card);
}

function editMode(){
	TextToEditor();
	
	document.getElementById("card"+chosen).style.display = "none";
	document.getElementById("steuerung").style.display = "none";
    if (JSINFO['access']=="editor") document.getElementById("plugin__flashcard_edit_btn").style.display = "none";
	
	document.getElementById("editor").style.display = "block";
}

function readMode(){
	document.getElementById("card"+chosen).style.display = "block";
	document.getElementById("steuerung").style.display = "block";
    if (JSINFO['access']=="editor") document.getElementById("plugin__flashcard_edit_btn").style.display = "block";
	
	document.getElementById("editor").style.display = "none";
}


function switchAjaxLoad(n=0){
	if (n==0) {
		document.getElementById("editor_ribbon").style.display = "none";
		document.getElementById("ajax_wait").style.display = "block";
	} else {
		document.getElementById("ajax_wait").style.display = "none";
		document.getElementById("editor_ribbon").style.display = "block";
		readMode();
	}
}
	

function saveCard(){
	if (chosen==0) return; // starting menu
	
	switchAjaxLoad();
		
	var m = encodeURIComponent(document.getElementById("cardtext").value);
	
	jQuery.post(
    DOKU_BASE + 'lib/exe/ajax.php',
    {
        call: 'editcard',
        newtext : m,
        nr : chosen-1,
		id: JSINFO["id"],
    },
    function(data) {
		data = decodeURIComponent(data); 
        //console.log(data);
		
        if (data.substr(0,6) == 'Error:') {
            alert(data);
        } else {
            card[chosen-1] = m;
            document.getElementById("card"+chosen).innerHTML = data; // load to present div-container
        }
		
		switchAjaxLoad(1);

    },
    'html'
	);
	
}


function getPre(){
    if (chosen>1){
        document.getElementById("cardtext").value=decodeURIComponent(card[chosen-2]);
    } else {
        alert("No card before this one");
    }
}

/* Cookie Text which is saved */
function cookieText() {
    if (awnsered.length == 0) return "None.";
    var res = JSINFO['id'] + "/" + chosen + "/";
    for (c=1;c<awnsered.length;c=c+2) {
        if (awnsered[c]) {res = res + "1";} else {res = res + "0";}
    }
    return res;
}

function loadFromCookie(){
    user = JSINFO['user_id'];
    
    if (user == "false") return; // Cookies werden per user gespeichert
    
    var urlParams = new URLSearchParams(window.location.search);
    
    var data = getCookie('flashcard_'+user).split("/");
    
    console.log("Cookie 'flashcard_"+user+"' = "+data);
    
    // GET-Parameter "flashcard=continue" as marker
    if (JSINFO["id"]==data[0]) {
        
        message("Letzte Sitzung fortsetzen: <a style='cursor:pointer' onclick='window.location.href=window.location.href+\"&flashcard=continue\"' >hier klicken</a>");
        
        // User has to be on correct page
        if (urlParams.get('flashcard')=="continue") {
            
            awn = data[2];
            awnsered.push(false); // Initial Menu
            for (c=0;c<awn.length;c++) {
                if (awn.charAt(c)=="0") {
                    awnsered.push(false);awnsered.push(false);
                } else {
                    awnsered.push(true);awnsered.push(true);
                }
            }
            
            //console.log('Loaded Awnsers: '+awnsered.length);
            
            progressBar(0);
            chosen = parseInt(data[1])-1;
            
            document.getElementById("fc_progress_fill").style.backgroundColor = "green";
            nextQuestion();
            
            Menu((chosen % 2)+2);

        }
    }
    
}

// This card is displayed at present
var chosen = 0;
var awnsered = new Array();

// All cards in WikiMarkup for the Editor
var card = new Array();
