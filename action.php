<?php
/**
 * DokuWiki Plugin 
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author: Gero Gothe <gero.gothe@medizindoku.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();
define('DEBUG',false);

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
        $controller->register_hook('DOKUWIKI_STARTED', 'AFTER',  $this, 'acl_info');
        $controller->register_hook('TPL_CONTENT_DISPLAY', 'BEFORE', $this,'mediacleanup');

    }

    # Adds the Ajax-Call "editcard"
    public function _ajax_call(Doku_Event $event, $param) {

        if ($event->data !== 'editcard') {
            return;
        }

        # No other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();
        
        global $ID;
        if (auth_quickaclcheck($ID) < AUTH_CREATE) {echo "Error: No editing rights granted.";return;};
 
        global $INPUT;

        # receive new card text
        $newtext = $INPUT->post->str('newtext');
        if(empty($newtext)) $query = $INPUT->get->str('newtext');
        if(DEBUG && empty($newtext)) {echo "Error: No content received";return;}
    
        # receive page id
        $id = $INPUT->post->str('id');
        if(empty($id)) $query = $INPUT->get->str('id');
        if(DEBUG && empty($id)) {echo "Error: No id received";return;}
        
        # receive card nr
        $cardnr = $INPUT->post->str('nr');
        if(empty($cardnr)) $query = $INPUT->get->str('nr');
        if(DEBUG && empty($cardnr)) {echo "Error: No cardnr received";return;}

        $newtext = rawurldecode($newtext);

        # Process Page Data
        $page = rawWiki($id); # Get the page
        
        $pre = substr($page,0,strpos($page,'<cards>')+7); # Get part before the quiz
        $post = substr($page,strpos($page,'</cards>')); # Get part after the quiz
        $main = str_replace(Array($pre,$post),'',$page); # only the questions
        
        $questions = explode('----',$main); # Get the questions
        
        
        $questions[$cardnr] = DOKU_LF . $newtext . DOKU_LF;
        
        $done = $pre . implode('----',$questions) . $post;
        
        
        # save changes to wikifile
        saveWikiText($id, $done, "Flashcard nr. $cardnr edited");
        

        # Return rendered text
        
        # add namespace to image if relativ path is used
        $r = p_render('xhtml',p_get_instructions($newtext),$info);

        # add namespace to media files with relative paths
        $ns = substr($id,0,strrpos($id,":")+1);

        $p = strpos($r,"media=");

        do {

            $p2 = strpos($r,'"',$p);
            $f = substr($r,$p+6,$p2-$p-6);

            if (strpos($f,":") === false) {
                $new = "media=".$ns.$f;		
                $r = str_replace("media=$f",$new,$r);
            }

            $p = strpos($r,"media=",$p+10+strlen($ns));

        } while ($p !== false);

		# Send html response
		$response = $r; 
		echo trim($response);
				
	}
    
    # Function to tell javascript function wether to display editing contents
    public function acl_info(&$event, $param) {
        global $JSINFO;        
        global $ID;
        global $USERINFO;
        
        if (auth_quickaclcheck($ID) < AUTH_CREATE) {
            $JSINFO['access'] = 'reader';
        } else $JSINFO['access'] = 'editor';
        
        if (isset($USERINFO)) {
            $JSINFO['user_id'] = $_SERVER['REMOTE_USER'];
        } else $JSINFO['user_id'] = 'false';
        
        $JSINFO['plugin_flashcard__scrolltop'] = intval($this->getConf('scroll_top'));
        
    }
    
    # Check for unused mediafiles
    public function mediacleanup (){
        global $INFO;
        
        if (!isset($_GET['mediacleanup'])) return;
        
        $mediapath = DOKU_INC . 'data/media/' . str_replace(':','/',$INFO['namespace']);
        $datapath = DOKU_INC . 'data/pages/' . str_replace(':','/',$INFO['namespace']);
        
        echo '<div style="font-family:Courier;font-weight:bold;color:white;background-color:black;">';
        
        echo "$datapath<br><br>";
        
        $pages = scandir($datapath);
        $pages = array_diff(scandir($datapath), array('.', '..'));
        foreach ($pages as $f) {
            $p = $INFO['namespace'] . ':' . str_replace('.txt','',$f);
            echo "$p<br>";
            $data[] = str_replace(' ','_',strtolower(rawWiki($p)));
        }
        
        echo "<br><hr>$mediapath<br><br><hr>";
        
        echo '<div style="column-count:3">';
        $files = scandir($mediapath);
        $files = array_diff(scandir($mediapath), array('.', '..'));
        #print_r($files);
        
        foreach ($files as $f) {
            if (strpos($data[0],$f)>0) {
                echo "<span style='color:lightgreen'>$f</span>";
            } else if (strpos($data[1],$f)>0) {
                echo "<span style='color:yellow'>$f</span>";
            } else echo "<span style='color:red'>$f</span>";
            echo "<br>";
        }
        
        echo '</div></div>';
        
    }
}

// vim:ts=4:sw=4:et:
