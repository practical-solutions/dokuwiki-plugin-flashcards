<?php
/**
 * DokuWiki Plugin 
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Class action_plugin_searchform
 */
class action_plugin_flashcards extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {

        
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');

    }

    
    
    
    # Adds the Ajax-Call "editcard"
	public function _ajax_call(Doku_Event $event, $param) {
		if ($event->data !== 'editcard') {
			return;
		}
    
		# No other ajax call handlers needed
		$event->stopPropagation();
		$event->preventDefault();
 
		global $INPUT;

		# receive new card text
		$newtext = $INPUT->post->str('newtext');
		if(empty($newtext)) $query = $INPUT->get->str('newtext');
		if(empty($newtext)) return;
		
		# receive old card text
		$oldtext = $INPUT->post->str('oldtext');
		if(empty($oldtext)) $query = $INPUT->get->str('oldtext');
		if(empty($oldtext)) return;
		
		# receive page id
		$id = $INPUT->post->str('id');
		if(empty($id)) $query = $INPUT->get->str('id');
		if(empty($id)) return;

		$newtext = rawurldecode($newtext);
		$oldtext = rawurldecode($oldtext);
		
		# save changes to wikifile
		$page = str_replace($oldtext,$newtext,rawWiki($id));
		saveWikiText($id, $page, "Flashcard edited");
		
		$response = p_render('xhtml',p_get_instructions($newtext),$info); # send html response
		echo trim($response);
				
	}

}

// vim:ts=4:sw=4:et:
