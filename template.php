<?php

class Template {

    private $templateDir = "";

    private $languageDir = "";

    /**
     * Der linke Delimter für einen Standard-Platzhalter
     */
    private $leftDelimiter = '{$';

    /**
     * Der rechte Delimter für einen Standard-Platzhalter
     */
    private $rightDelimiter = '}';

    /**
     * Der linke Delimter für eine Funktion
     */
    private $leftDelimiterF = '{';

    /**
     * Der rechte Delimter für eine Funktion
     */
    private $rightDelimiterF = '}';

    /**
     * Der linke Delimter für ein Kommentar. Sonderzeichen müssen escapt werden => wegen Regex Parser
     */
    private $leftDelimiterC = '\{\*';

    /**
     * Der rechte Delimter für ein Kommentar. Sonderzeichen müssen escapt werden => wegen Regex Parser
     */
    private $rightDelimiterC = '\*\}';

    /**
     * Der linke Delimter für eine Sprachvariable. Sonderzeichen müssen escapt werden => wegen Regex Parser
     */
    private $leftDelimiterL = '\{L_';

    /**
     * Der rechte Delimter für eine Sprachvariable. Sonderzeichen müssen escapt werden => wegen Regex Parser
     */
    private $rightDelimiterL = '\}';

    /**
     * Der komplette Pfad der Templatedatei
     */
    private $templateFile = "";

    /**
     * Der komplette Pfad der Sprachdatei
     */
    private $languageFile = "";

    /**
     * Der Dateiname der Templatedatei
     */
    private $templateName = "";

    /**
     * Der Inhalt des Templates
     */
    private $template = "";


    /**
     * Pfade festlegen
     */
    public function __construct($tpl_dir = "", $lang_dir = "") {
        if ( !empty($tpl_dir) ) {
            $this->templateDir = $tpl_dir;
        }
        if ( !empty($lang_dir) ) {
            $this->languageDir = $lang_dir;
        }
    }

    /**
     * Template öffnen
     */
    public function load($file)    {
        $this->templateName = $file;
        $this->templateFile = $this->templateDir.$file;
        if( !empty($this->templateFile) ) {
            if( file_exists($this->templateFile) ) {
                $this->template = file_get_contents($this->templateFile);
            } else {
                return false;
            }
        } else {
           return false;
        }
        $this->parseFunctions();
    }

    /**
     * Einen Standard-Platzhalter ersetzen
     */
    public function assign($replace, $replacement) {
        $this->template = str_replace( $this->leftDelimiter .$replace.$this->rightDelimiter,
                                       $replacement, $this->template );
    }

    /**
     * Sprachdateien öffnen und Sprachvariablem im Template ersetzen
     */
    public function loadLanguage($files) {
        $this->languageFiles = $files;
        for( $i = 0; $i < count( $this->languageFiles ); $i++ ) {
            if ( !file_exists( $this->languageDir .$this->languageFiles[$i] ) ) {
                return false;
            } else {
                 include_once( $this->languageDir .$this->languageFiles[$i] );
                 // Jetzt steht das Array $lang zur Verfügung
            }
        }
        $this->replaceLangVars($lang);
        return $lang;
    }

    /**
     * Sprachvariablen im Template ersetzen
     */
    private function replaceLangVars($lang) {
        $this->template = preg_replace("/\{L_(.*)\}/isUe", "\$lang[strtolower('\\1')]", $this->template);
    }

    /**
     * Includes parsen und Kommentare aus dem Template entfernen
     */
    private function parseFunctions() {
        while( preg_match( "/" .$this->leftDelimiterF . "include file=\"(.*)\.(.*)\"" . $this->rightDelimiterF . "/isUe", $this->template ) ) {
            $this->template = preg_replace( "/" . $this->leftDelimiterF . "include file=\"(.*)\.(.*)\"" . $this->rightDelimiterF . "/isUe", "file_get_contents(\$this->templateDir.'\\1'.'.'.'\\2')", $this->template );
        }
        $this->template = preg_replace( "/" .$this->leftDelimiterC ."(.*)" .$this->rightDelimiterC ."/isUe", "", $this->template );
    }

    /**
     * Das fertige Template ausgeben
     */
    public function display() {
        echo $this->template;
    }
}

?>