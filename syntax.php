<?php
/**
 * Plugin Search Form: Inserts a search form in any page
 *
 * @license    MIT
 * @author     Gero Gothe <practical@medizin-lernen.de>
 */
 

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_flashcards extends DokuWiki_Syntax_Plugin {

    /**
     * Syntax Type
     *
     * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
     * @return string
     */
    public function getType() {
        return 'substition';
    }

	
	function connectTo($mode) {
		$this->Lexer->addEntryPattern('<cards>',$mode,'plugin_flashcards');
    }

   
    function postConnect() {      
      $this->Lexer->addExitPattern('</cards>', 'plugin_flashcards');
    }
	

    function handle($match, $state, $pos, Doku_Handler $handler){
		
		if ($state == DOKU_LEXER_UNMATCHED) {
			return $match;
		}
          
        return false;
    }

    /**
     * The actual output creation.
     *
     * @param   $format   string        output format being rendered
     * @param   $renderer Doku_Renderer reference to the current renderer object
     * @param   $data     array         data created by handler()
     * @return  boolean                 rendered correctly?
     */
    public function render($format, Doku_Renderer $renderer, $data) {
        global $lang, $INFO, $ACT, $QUERY;
		
		if ($data === false) return;
		
        if($format == 'xhtml') {
			
		
		
			$m  = "<div id='steuerung' class='steuerung'>
				   <img id='btn1' class='fc_button' onclick='showPage(-1)' src='lib/plugins/flashcards/img/left.png'>
				   <img id='btn2' class='fc_button' onclick='correct()' src='lib/plugins/flashcards/img/yes.png'>
				   <img id='btn3' class='fc_button' onclick='showawnser()' src='lib/plugins/flashcards/img/show.png'>
				   <img id='btn4' class='fc_button' onclick='wrong()' src='lib/plugins/flashcards/img/no.png'>
				   <img id='btn5' class='fc_button' onclick='showPage(1)' src='lib/plugins/flashcards/img/right.png'>
				   <img id='btn6' class='fc_button' onclick='flip(1)' src='lib/plugins/flashcards/img/redo.png'>
				   <div id='fc_info'></div>
				   
				   <div class='fc_progressbar'>
				   <div id='fc_progress_fill'></div>
				   </div>
				   
				   </div>";
				   
				   
			
			$t = "<div class='fc_button' onclick='startquiz()'>";
			$t .= "<img src='lib/plugins/flashcards/img/quiz.png'> <div class='fc-btn-caption'>Quiz starten</div>";
			$t .= "<span>Antworten evaluieren und wiederholen, bis sie im Gedächtnis sind</span>";
			$t .= "</div>";
			$t .= "<br><hr>";
			$t .= "<div class='fc_button' onclick='startread()'>";
			$t .= "<img src='lib/plugins/flashcards/img/info.png'> <div class='fc-btn-caption'>Fragen durchscrollen</div>";
			$t .= "<span>Fragen und Antworten durchblätten</span>";
			$t .= "</div>";
			$t .= "<br><hr>";
			$t .= "<div class='fc_button' onclick='showall()'>";
			$t .= "<img src='lib/plugins/flashcards/img/read.png'> <div class='fc-btn-caption'>Alles zeigen</div>";
			$t .= "<span>Fragen und Antworten hintereinander anzeigen</span>";
			$t .= "</div>";
			
			# card0 = Starting area
			$m .= "<div class='flashcard' id='card0'>".($t)."</div>";

				
			$pages = array_map('trim', explode("----",$data));
			
			# Generate flashcard containers
			for ($c=1;$c<count($pages)+1;$c++) {				
				$p = $pages[$c-1];
				$t= p_render('xhtml',p_get_instructions($p),$info);				
				$m .= "<div class='flashcard' id='card$c'>".($t)."</div>";		
			}
			
			# Show first page on start
			$c--; # Dont count start page
			$m .= "<script>karten=$c;showPage();</script>";
						
			$renderer->doc .= $m;
			
			return true;
		}
        
        return false;
    }
}
