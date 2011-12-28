<?php
/**
    Script zur einfachen Erstellung und Pr�fung von Passw�rtern

    Copyright (C) 2006 - Mathias Bank

    Dieses Programm ist freie Software. Sie k�nnen es unter den Bedingungen der GNU General Public License, wie von der Free Software Foundation
    ver�ffentlicht, weitergeben und/oder modifizieren, gem�� Version 2 der Lizenz.

    Die Ver�ffentlichung dieses Programms erfolgt in der Hoffnung, da� es Ihnen von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne
    die implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT F�R EINEN BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.

    Zum heutigen Stand ist die Lizenz zu finden unter http://www.gnu.de/gpl-ger.html

 �nderungen
 - String-Zugriff laut Doku nicht mit eckigen, sondern mit geschweiften Klammern
 - Bug in Security-Test behoben
*/

/**
 * Password helper.
 *
 * @package		Core
 * @subpackage	Security
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Password {

    const CHECK_TOO_SMALL = 1;
    const CHECK_NO_UPPERCASE_CHAR = 2;
    const CHECK_NO_LOWERCASE_CHAR = 4;
    const CHECK_NO_NUMBERIC_CHAR = 8;
    const CHECK_NO_SPECIAL_CHAR = 16;
    const CHECK_SAME_CHAR_SEQUENCE = 32;
    const CHECK_ALPHABETICAL_SEQUENCE = 64;
    const CHECK_KEYBOARD_SEQUENCE = 128;
    const CHECK_NUMERICAL_SEQUENCE = 256;
    const CHECK_LEXICON_WORD = 512;

    private static $vowels  = "aeiou";
    private static $consonants = "bcdfghjklmnprstvwxz";
    private static $specialchars = '!#$%&*+-/<=>?@^_~';

    private static $alphabet = "abcdefghijklmnopqrstuvqxyz0123456789";
    private static $keybordSequences = array("qwe","wer","ert","asd","sdf","dfg","yxc",'xcv','cvb','trz','tzu','yui','uio','iop','fgh','ghj','hjk','jkl','vbn','bnm','!"�','"�$','�$%','$%&','%&/','&/(','/()','()=',')=?');
    private static $numberSequences = array("137","379","973","731","246","468","159","357","753","951","846","461");

    /**
     * Erstellt ein mnemonisches Passwort, welches m�glichst einfach zu merken sein sollte.
     *
     * @param int $minpairs Gibt an, aus wieviel Vokal-/Konsonant-Paaren das Passwort mindestens bestehen soll
     * @param int $maxpairs Gibt an, aus wieviel Vokal-/Konsonant-Paaren das Passwort maximal bestehen soll
     * @param int $minnumbers Gibt an, aus wieviel Zahlen das Passwort mindestens bestehen soll
     * @param int $maxnumbers Gibt an, aus wieviel Zahlen das Passwort maximal bestehen soll
     * @return string Generiertes Passwort
     */
    public static function generate($minpairs = 2, $maxpairs = 5, $minnumbers = 1, $maxnumbers = 3){
            $password = "";

            $pairs = mt_rand($minpairs, $maxpairs);
            $lenv = strlen(self::$vowels)-1;
            $lenc = strlen(self::$consonants)-1;

            $usedBig = false; //Speichert, ob schon mal ein Gro�buchstabe eingef�gt wurde
            for($i = 1; $i <= $pairs; $i++) {
                //Per Zufall ein Gro�buchstaben als Konsonant einf�gen
                $password .= self::$vowels{mt_rand(0, $lenv)};

                if (mt_rand(0,1)==0 && !$usedBig) {
                    $password .= strtoupper(self::$consonants[mt_rand(0, $lenc)]);
                    if (mt_rand(0,1)==0) $usedBig=true;
                } else {
                    $password .= self::$consonants{mt_rand(0, $lenc)};
                }
            }

            //zuf�lliges Sonderzeichen einf�gen
            $password .= self::$specialchars{mt_rand(0, strlen(self::$specialchars)-1)};

            //Zuf�llige Anzahl an Zahlen einf�gen
            $sizeNumbers = mt_rand($minnumbers, $maxnumbers);
            for($i = 1; $i <= $sizeNumbers; $i++) {
                $password .= mt_rand(0,9);
            }
            return $password;
    }

    /**
     * Pr�ft die Sicherheit eines Passworts.
     *
     * Dabei wird gepr�ft ob,<br />
     * - Das Passwort in einem Lexikon steht (sofern pspell installiert)<br />
     * - Das Passwort eine Mindestl�nge erreicht<br />
     * - Das Passwort aus Gro�-/Keinbuchstaben, Zahlen und Sonderzeichen besteht<br />
     * - identische Zahlenfolgen enth�lt (mind.3)<br />
     * - Tastatur-Sequenzen enth�lt (mind. 3)<br />
     * - Zahlen-Sequenzen enth�lt (mind. 3)<br />
     * - Alphabet-Elemente enth�lt (mind. 3)<br />
     *
     * @param string $password: zu pr�fendes Passwort
     * @param array $failureArray: Array zur Ermittlung, warum Punkte abezogen wurden
     * @param array $language: Array mit Sprach-K�rzeln, die mittels pspell gepr�ft werden sollen
     * @param int $optimalPasswordLength: optimale L�nge des Passworts
     * @return int: 0=sehr schlecht, 100=sehr gut
     */
    public static function check($password, &$failureArray=array(), $language=array("en","de"), $optimalPasswordLength=10) {
        //Rating initialisieren
        $rating = 100;
        $passwordLength = strlen($password);
        $smallPassword = strtolower($password); //Zum Vergleich mit Reihen

        // passwort k�rzer als 4 Zeichen? NICHT sicher => Rating: 0
        if ($passwordLength < 4) {
        	return 0;
        }

        //passwort ist deutsches oder englisches wort => aspell
        if(function_exists("pspell_new")) {
            foreach($language as $lang) {
                $pspellLink = pspell_new($lang);
                if (pspell_check($pspellLink, $password)) {
                    $failureArray[] = Password::CHECK_LEXICON_WORD;
                    $rating -= 50;
                }
            }
        }

        //pro fehlendem Zeichen auf Passwort-L�nge: 5 Punkte abziehen
        $dif = $optimalPasswordLength-$passwordLength;
        if ($dif>0) {
            $failureArray[] = Password::CHECK_TOO_SMALL;
            $rating = $rating - ($dif*5);
        }

        //wenn kein Kleinbuchstaben/Gro�buchstaben/Zahlen/Sonderzeichen
        //besondere Zeichen werden nicht ber�cksichtigt
        $smallChar = false;
        $bigChar=false;
        $numericChar=false;
        $specialChar=false;
        for($i=0;$i<$passwordLength;$i++) {
            $ascii = ord($password[$i]);
            if ($ascii>=48 && $ascii<=57) $numericChar=true;
            elseif ($ascii>=65 && $ascii<=90) $bigChar=true;
            elseif ($ascii>=97 && $ascii<=122) $smallChar=true;
            elseif ($ascii>=32 && $ascii<=126) $specialChar=true;
        }

        if(!$smallChar) {
            $failureArray[] = Password::CHECK_NO_LOWERCASE_CHAR;
            $rating -=15;
        }
        if(!$bigChar) {
            $failureArray[] = Password::CHECK_NO_UPPERCASE_CHAR;
            $rating -=15;
        }
        if(!$numericChar) {
            $failureArray[] = Password::CHECK_NO_NUMBERIC_CHAR;
            $rating -=20;
        }
        if(!$specialChar) {
            $failureArray[] = Password::CHECK_NO_SPECIAL_CHAR;
            $rating -=10;
        }

        //identische Zeichenfolgen suchen (ab 3 Buchstaben)
        for ($i=0;$i<=$passwordLength-3;$i++) {
            $excerpt = substr($smallPassword,$i,3);
            if ($excerpt[0]==$excerpt[1] && $excerpt[1] == $excerpt[2]) {
                $failureArray[] = Password::CHECK_SAME_CHAR_SEQUENCE;
                $rating -=20;
                break;
            }
        }

        //Zeichenfolgen auf der Tastatur (ab 3 Buchstaben)
        foreach(self::$keybordSequences as &$sequence) {
            if (strstr($smallPassword, $sequence)) {
                $failureArray[] = Password::CHECK_KEYBOARD_SEQUENCE;
                $rating -=15;
                break;
            }
        }

        //Zahlenmuster
        foreach(self::$numberSequences as &$sequence) {
            if (strstr($smallPassword, $sequence)) {
                $failureArray[] = Password::CHECK_NUMERICAL_SEQUENCE;
                $rating -= 15;
                break;
            }
        }

        //ABC oder Zahlenreihen (ab 3 Buchstaben) => 20 Punkte abziehen
        for ($i=0;$i<=$passwordLength-3;$i++) {
            $excerpt = substr($smallPassword,$i,3);
            if(strstr(self::$alphabet,$excerpt)) {
                $failureArray[] = Password::CHECK_ALPHABETICAL_SEQUENCE;
                $rating -=15;
                break;
            }
        }

        return ($rating > 0) ? $rating: 0;
    }

}
?>