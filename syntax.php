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
			
			$renderer->doc .= file_get_contents("lib/plugins/flashcards/inc/header.html");
			
			$m = "";
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
			
			$m .= "<script>";
			
			for ($c=0;$c<Count($pages);$c++) $m .= "card[$c]='".rawurlencode($pages[$c])."';\n";
			
			$m .= "</script>";
						
			$renderer->doc .= $m;
			
			$renderer->doc .= "<div id='plugin__flashcard_edit_btn' onclick='editMode()'>Bearbeiten</div>";
			
			return true;
		}
        
        return false;
    }
}
