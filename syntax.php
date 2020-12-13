<?php
/**
 * Plugin Flashcard: Creates Flashcards for a quiz
 *
 * @license    GPL2
 * @author     Gero Gothe <gero.gothe@medizindoku.de>
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
    
    function getSort() { return 136; }
    
    function getPType() { return 'normal'; }
    
    function getAllowedTypes() { return array('formatting','substition'); }


    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<cards>.*?</cards>',$mode,'plugin_flashcards');
        $this->Lexer->addSpecialPattern('~~countcards~~',$mode,'plugin_flashcards');
    }


    function handle($match, $state, $pos, Doku_Handler $handler){
        return $match;
    }


    /* Returns all files in a directory
     * 
     * Function is needed for counting the amount of flashcards in a namespace
     * 
     * @return: Array = list of files in a directory
     */
    function getFileListAsArray(string $dir, bool $recursive = true, string $basedir = ''): array {
        if ($dir == '') {
            return array();
        } else {
            $results = array();
            $subresults = array();
        }
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        } // so a files path can be sent
        if ($basedir == '') {
            $basedir = realpath($dir) . DIRECTORY_SEPARATOR;
        }

        $files = scandir($dir);
        foreach ($files as $key => $value) {
            if (($value != '.') && ($value != '..')) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                if (is_dir($path)) { // do not combine with the next line or..
                    if ($recursive) { // ..non-recursive list will include subdirs
                        $subdirresults = self::getFileListAsArray($path, $recursive, $basedir);
                        $results = array_merge($results, $subdirresults);
                    }
                } else { // strip basedir and add to subarray to separate file list
                    $subresults[str_replace($basedir, '', $path)] = $value;
                }
            }
        }
        // merge the subarray to give the list of files then subdirectory files
        if (count($subresults) > 0) {
            $results = array_merge($subresults, $results);
        }
        return $results;
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
        
        /* Count cards recursively
         * 
         * Data is cached into cache.txt so that the files are not counted every time
         * Cache must be refreshed be purge
         */
        if ($data == '~~countcards~~') {
            $dir = DOKU_INC . 'data/pages/' .  str_replace(":","/",$INFO['namespace']) . "/";
            
            $cachefile = 'lib/plugins/flashcards/cache.txt';
            if (file_exists($cachefile)) {
                $cache = json_decode(file_get_contents($cachefile),true);
            }
            
            # Return data in cache if available
            if (!isset($_GET['purge']) && isset($cache[$dir])) {
                $renderer->doc .= $cache[$dir];
                return;
            }
            
            $files = array_keys($this->getFileListAsArray($dir));
            $sum = 0;
            
            for ($c=0;$c<count($files);$c++) {
                $content = file_get_contents($dir.$files[$c]);
                
                preg_match("'<cards>(.*?)</cards>'si",$content,$match);
                
                if ($match == false) {
                    $count = -1;
                } else {
                    $count = (substr_count($match[0],"----")+1) / 2;
                    $sum += $count;
                }

            }
            
            $renderer->doc .= "$sum (counted)";
            
            $cache[$dir] = $sum;
            file_put_contents($cachefile,json_encode($cache));
            
            return;
        }
        
        
        /* Output playable flashcard quiz 
         * 
         * All flashcards are put in divs with an id="card<number>"
         * "card0" contains the main menu
         * 
         * The css of these containers a set to "display=none" and only
         * the present card is set to be displayed
         */
        if($format == 'xhtml') {
            global $conf;
            
            /* defer_js-option must be off */
            if ($conf['defer_js'] == 1) {
                msg($this->getLang("defer js msg"),2);
                $data = str_replace('<cards>','',$data);
                $data = str_replace('</cards>','',$data);
                $renderer->doc .= p_render('xhtml',p_get_instructions($data),$info);
                return;
            }
            
            $replacements = Array('%QUIZ%'       => $this->getLang('menu start'),
                                  '%QUIZ SUB%'   => $this->getLang('menu start sub'),
                                  '%SCROLL%'     => $this->getLang('menu scroll'),
                                  '%SCROLL SUB%' => $this->getLang('menu scroll sub'),
                                  '%ALL%'        => $this->getLang('menu all'),
                                  '%ALL SUB%'    => $this->getLang('menu all sub')
                                  );
            $renderer->doc .= strtr(file_get_contents("lib/plugins/flashcards/inc/header.html"),$replacements);

            $m = "";
            $data = str_replace('<cards>','',$data);
            $data = str_replace('</cards>','',$data);
            
            $pages = array_map('trim', explode("----",$data));

            # Generate flashcard div containers
            for ($c=1;$c<count($pages)+1;$c++) {
                $p = $pages[$c-1];
                $t= p_render('xhtml',p_get_instructions($p),$info);
                $m .= "<div class='flashcard' id='card$c'>".($t)."</div>";
            }
            
            
            # Show first page on start
            $c--; # Dont count start page
            $m .= "<script>karten=$c;showPage();</script>";

            /* The content of the cards are also placed into a js-variable in order
             * to be sent to an ajax script for editing
             */
            $m .= "<script>";
            for ($c=0;$c<Count($pages);$c++) $m .= "card[$c]='".rawurlencode($pages[$c])."';\n";
            $m .= "</script>";
            
            $renderer->doc .= $m;
            
            # Hide all cards per js
            $renderer->doc .= "<script type='text/javascript'>hideAll();</script>";
            $renderer->doc .= "<input type='button' id='plugin__flashcard_edit_btn' onclick='editMode();' value='".$this->getLang('edit btn')."'>";

            return true;
        }
        
        return false;
    }
}
