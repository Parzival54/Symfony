<?php

namespace OC\PlatformBundle\Antispam;

class OCAntispam {
    
    private $mailer;
    private $locale;
    private $minLenght;

    public function __construct(\Swift_Mailer $mailer, $minLenght) {
        $this->mailer = $mailer;
        $this->minLenght = (int) $minLenght;
    }
    
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    
        /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text) {
        return strlen($text) < $minLenght;
    }

}
