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
			
		
			$m  = "<div class='steuerung'>
				   <img class='fc_button' onclick='showPage(-1)' src='".DOKU_BASE."lib/plugins/flashcards/left.png'>
				   <img class='fc_button' onclick='showPage(1)' src='".DOKU_BASE."lib/plugins/flashcards/right.png'>
				   </div>";
				
			$pages = array_map('trim', explode("----",$data));
			
			$c=0;
			foreach ($pages as $p){				
				$t= p_render('xhtml',p_get_instructions($p),$info);
				$m .= "<div class='flashcard' id='card$c'>".($t)."</div>";
				$c++;			
			}
			
			# Show first page on start
			$m .= "<script>karten=$c;showPage();</script>";
						
			$renderer->doc .= $m;
			
			return true;
		}
        
        return false;
    }
}
