<?php
class Generovacia_trieda {
    private $text, $typ, $domena, $pridanie, $pocet_vysledkov, $cisla, $hocijake;
    private $samohl, $vysoke, $nizke, $dvojite, $samohlasky, $spoluhlasky;
            
    function __construct($text, $domena1, $typ, $pridanie, $pocet_vysledkov, $cisla, $hocijake) {
        $this->text = $text;
        
        $this->typ = $typ; 
        $this->domena1 = $domena1; 
        $this->pridanie = $pridanie;
        $this->pocet_vysledkov = $pocet_vysledkov;
        $this->cisla = $cisla; 
        $this->hocijake = $hocijake; 
        
        $this->slabikotvorne = array('a', 'e', 'i', 'o', 'u', 'y', 'r', 'l'); //'ia', 'ie', 'io', 'iu',
        $this->vysoke = array('d', 'l', 'h', 'k', 'b', 'f', 'p', 'g', 'j', 'q', 'y');
        $this->nizke = array('t', 'n', 'c', 'z', 's', 'm', 'v', 'r', 'w', 'x', 'a', 'e', 'i', 'o', 'u');         
        $this->samohlasky = array('a', 'e', 'i', 'o', 'u', 'y');
        $this->spoluhlasky = array('d', 'l', 'h', 'k', 'b', 'f', 't', 'n', 'g', 'c', 'z', 's', 'j', 'm', 'p', 'v', 'r', 'w', 'q', 'x');
        $this->dvojite = array('ch', 'dz');
    }
   
    //Jedina verejna metoda triedy
    public function vygeneruj() {
        //$this->typ = "Cele slovo";
        //$this->text = "";        
        //$this->domena = ".com"; 
        
        $this->vracajVysledkyPostupne($this->typ, $this->text, $this->domena1);
                
        /*        
        $domeny;
        //$domeny = $this->urobKombinacie(); 
        $domeny = $this->urob_kombinacie1($this->typ, $this->text);
        $domeny = $this->overRegistraciu($domeny);                
        $this->vypisVysledky($domeny, "green", "red");         
        */
    }    
    
    private function patriDoSkupiny($polozka, $pole) { //zistuje ci pismeno sa v danom poli nachadza
        for($i=0; $i<count($pole); $i++){
            if(strcmp($pole[$i], $polozka) == 0) return TRUE;            
        }                
        return FALSE;
    }
    /*
    private function testSlabiky($pism1, $pism2, $pism3) { //zistuje ci dvojica pismen je povolena kombinacia        
        //Dve neslabikotvorne hlasky za sebou nesmu byt
        if(!$this->patriDoSkupiny($pism2, $this->slabikotvorne)) { //Splnena podmienka nastane iba ked je prostredne neslabikotvorne
            if(!$this->patriDoSkupiny($pism1, $this->slabikotvorne) || !$this->patriDoSkupiny($pism3, $this->slabikotvorne)) return FALSE;            
        }         
        return TRUE;
    }
    */
    
    //Zistuje ci dvojica pismen je povolena kombinacia
    //Ak nie je povolena kombinacia, vrati FALSE
    private function testSamohlasky($pism1, $pism2) {        
        //Spoluhlasky su iba so samohlaskou ==> dve spoluhlasky za sebou nesmu byt
        if($this->patriDoSkupiny($pism1, $this->spoluhlasky) && $this->patriDoSkupiny($pism2, $this->spoluhlasky)) { //Splnena podmienka nastane iba ked je prostredne neslabikotvorne
            return FALSE;            
        }         
        return TRUE;
    }    
    
    //Prave jedno pismeno musi byt vysoke
    //Testuje, ci je v dvojici predel s vysokym pismenom
    //Prave jedno z pismen musi byt vysoke
    //Vrati TRUE ak sa hranica nasla
    private function testHranice($pism1, $pism2) {        
        if($this->patriDoSkupiny($pism1, $this->vysoke) && $this->patriDoSkupiny($pism2, $this->nizke)) return TRUE;
        if($this->patriDoSkupiny($pism2, $this->vysoke) && $this->patriDoSkupiny($pism1, $this->nizke)) return TRUE;                     
        return FALSE;
    }
    
    //testuje ci znak je cislo
    private function jeCislo($znak) {
        if(48 <= ord($znak) && ord($znak) <= 57 ) return true;
        else return false;
    }
    
    //Vrati TRUE ak dvojica pismen pasuje, inak vrati FALSE
    //"index je na pism1 ak prechadzame retazec    
    private function otestujDvojicu($domena, $pism1, $pism2, $index, $zaciatokSlova, $koniecSlova) {

        if($zaciatokSlova <= $index && ($index) <= $koniecSlova-1) return true; //slovo nijako nekontrolujeme
                                
        if($this->cisla == false) { //Kontroluje ci obsahuje cisla ak nie su pouzivatelom povolene
            if($this->jeCislo($pism1) || $this->jeCislo($pism2)) return false;
            //else echo "Nie je cislo: $pism1 - $pism2  <br>";
        }
        
        if($this->hocijake == true)  return true;
        
        if(! ($this->jeCislo($pism1) || $this->jeCislo($pism2)) ) { //dvojicu s cislom/cislami netestujeme
            if($this->testSamohlasky($pism1, $pism2) == false) return false; 
        }
        
        if(($index+1) == $zaciatokSlova) { //Ak sme priamo pred slovom, "$pism2" je slovo, "$pism1" este nie
            if(strcmp($this->typ, "Cele slovo") == 0) {                
                if($this->testHranice($pism1, $pism2) == false) {                    
                    return false;
                }
            }
        }           

        if(($index) == $koniecSlova) { //Ak sme jedno pismeno za slovom, ak je "$index" "$pism1" 
            if(strcmp($this->typ, "Cele slovo") == 0) {                    
                if($this->testHranice($pism1, $pism2) == false) {                    
                    return false;
                }    
            }                                    
        }          
        return true;
    }    
    
    private function otestujDomenu($domena, $dlzkaSlova, $pozicia, $dlzkaDomeny) {        
        //Kontroluje kazdu dvojicu
        for($i=0; $i<$dlzkaDomeny-1; $i++) {
            //echo "domena: " . $domena . " index: ". $i . ": " . $domena[$i] . " - " . $domena[$i+1] . " pozicia: " . $pozicia . " - " . ($pozicia + $dlzkaSlova - 1) . "<br>";            
            if($this->otestujDvojicu($domena, $domena[$i], $domena[$i+1], $i, $pozicia, ($pozicia + $dlzkaSlova - 1)) == false) return '';            
        }                
        
        return $domena;
    }
    
    //Vrati nahodne vygenerovany znak, bud pismeno alebo cislo
    private function vratZnak() {
        $znak = 60;

        while(57 < $znak && $znak < 97 ) {
           //97 - 122 - pismena abecedy podla ASCII
           //48 - 57 - cisla podla ASCII
           $znak = rand(48, 122);  
        }

        return chr($znak);
    }
    
    private function urob_kombinaciu($slovo, $dlzkaKombinacii) {                     
        //Nahodne vyberie pismena ku slovu
        if(strlen($slovo) == 0) { //Ak je na vstupe prazdne slovo
            
            $slovo = $this->vratZnak(); 
            //$dlzka = $dlzka + 1;            
        }

        $domena = $slovo;        
        $dlzka = strlen($slovo) + $dlzkaKombinacii;
        $pozicia = rand(0, $dlzkaKombinacii); //Nahodne urci poziciu pouzivatelom zadaneho slova  
        
        for($i=0; $i<$pozicia; $i++) { //zapisujeme pred slovo
            $znak = $this->vratZnak();            
            $domena = $znak . $domena;            
        }
        for($i=$pozicia+strlen($slovo); $i<$dlzka; $i++) { //zapisujeme za slovo
            $znak = $this->vratZnak();            
            $domena = $domena . $znak;               
        }                
        $domena = $this->otestujDomenu($domena, strlen($slovo), $pozicia, $dlzka);
        
        return $domena;
    }        
    
    private function vratDomenu($slovo, $dlzkaKombinacii) {        
        $domena = null;
        while(strlen($domena) == 0) { //vrati 0 ked vygeneruje nevhodnu kombinaciu
            $domena = $this->urob_kombinaciu($slovo, $dlzkaKombinacii);
        }        
        return $domena;
    }
    
    private function vracajVysledkyPostupne($typ, $slovo, $domena1){
        $pocetSkupinV = 0;
        
        if($this->pridanie != 0) {
            $pocetSkupinV = $this->pridanie; //kolko skupin vysledkov to vrati
            $pocetSkupin =  $this->pridanie + 1;            
        }    
        else $pocetSkupin = 4;
        
        if($this->pocet_vysledkov != 0) {
            $pocetVysledkov = $this->pocet_vysledkov;//kolko vysledkov v skupine to vytvori            
        }
        else $pocetVysledkov = 50;
               
        $domena;                     
                
        if(strlen($slovo) == 0) $dlzka = 1;
        else $dlzka = strlen($slovo);
        
        for($i=$pocetSkupinV; $i<$pocetSkupin; $i++) {
            //echo "<br>" . $i . "<br>";
            echo "<br><br><b> Dĺžka domény je " . ($dlzka + $i) . " znakov</b> <br>";
            $domenyArr = array();
            $domenyReg = array();
            for($j=0; $j<$pocetVysledkov; $j++) {
                $domena = $this->vratDomenu($slovo, $i);
                //U - kontrolovat, ci domena sa uz nenachadza (s funkciou overDuplicity)
                if($this->patriDoSkupiny($domena, $domenyArr) == false) {
                    flush();
                    array_push($domenyArr, $domena);
                    //if(true) {
                    if($this->is_domain_available($domena . $domena1) == true) {
                        echo "<font face=\" monospace\" color=\"green\">" . $domena . $domena1 . "&thinsp;&thinsp;&thinsp;" . "</font>";                     
                        
                    }
                    else { //Domena je registrovana
                        array_push($domenyReg, $domena);
                    }
                }                
            }
            //Vypise aj zaregistrovane domeny, su ulozene v poli
            if(count($domenyReg) != 0) {
                for($ii=0; $ii<count($domenyReg); $ii++) {                    
                    echo "<font face=\" monospace\" color=\"red\">" . $domenyReg[$ii] . $domena1 . "&thinsp;&thinsp;&thinsp;" . "</font>";                    
                }                
            }
        } 
    }
    
    //Vrati TRUE ak pismeno pasuje, inak vrati FALSE
    //"index je na pismene, ktore chceme vlozit do pola "domena"
    private function otestujPismeno($domena, $pism, $index, $hraniceSlova) {
        
        if($index != 0) { //ked je index 0, tak pismeno predtym neexistuje a teda niet co porovnavat
            if($this->testSamohlasky($domena[$index-1], $pism) == false) return false; 
            
            if(($index + 1) == $hraniceSlova[0]) { //Ak sme priamo pred slovom
                if($this->testSamohlasky($pism, $domena[$hraniceSlova[0]]) == false) return false;   
                if(strcmp($this->typ, "Cele slovo") == 0) {
                    if($this->testHranice($pism, $domena[$hraniceSlova[0]]) == false) return false;
                }
            }           
        }
        
        if(($index == 0)) {            
            if(($index + 1) == $hraniceSlova[0]) { //Ak sme priamo pred slovom
                if($this->testSamohlasky($pism, $domena[$index+1]) == false) return false; 
                if(strcmp($this->typ, "Cele slovo") == 0) {
                    if($this->testHranice($pism, $domena[$hraniceSlova[0]]) == false) return false;
                }                
            }                        
        }
        
        if(($index) == $hraniceSlova[1]) { //Ak sme priamo za slovom
            if($this->testSamohlasky($domena[$hraniceSlova[1]], $pism) == false) return false;
                if(strcmp($this->typ, "Cele slovo") == 0) {
                    if($this->testHranice($pism, $domena[$hraniceSlova[1]]) == false) return false;
                }                                    
        }                
        
        //U - osetrit situaciu ked je slovo na konci
        
        return TRUE;
    }
    
    private function pridajOkoliePostupne($domena, $hraniceSlova) {  
        $pismeno;
        //Vygenerovanie znakov po slovo
        for($i=0; $i<$hraniceSlova[0]; $i++) {

            $pismeno = chr(rand(97, 122));
             
            if($this->otestujPismeno($domena, $pismeno, $i, $hraniceSlova)) { //Hned testuje aby nerobilo operacie zbytocne
               
               $domena[$i] = $pismeno;
            }
            else { $i--; } //a sa nepodarilo, tak opakujeme cyklus
        }
        
        //Vygenerovanie znakov od konca slova az po koniec buducej domeny
        for($i= ($hraniceSlova[1] + 1); $i<count($domena); $i++) {

            $pismeno = chr(rand(97, 122));
             
            if($this->otestujPismeno($domena, $pismeno, $i, $hraniceSlova)) { //Hned testuje aby nerobilo operacie zbytocne  
               //echo $domena[$i] . " - " . $pismeno . "<br>";
               $domena[$i] = $pismeno;
               
            }
            else { $i--; } //a sa nepodarilo, tak opakujeme cyklus
            
        }        
        
        $domenaText = '';
        for($i=0; $i<count($domena); $i++) {
            $domenaText = $domenaText . $domena[$i];            
        }        
        return $domenaText;
    }
    
    //POZN. Nie je to dokoncene, nema to testovanie pismen
    private function pridajOkolieNaraz($domena, $hraniceSlova) { 
        $pismeno;
        //Vygenerovanie znakov po slovo
        for($i=0; $i<$hraniceSlova[0]; $i++) {
            $pismeno = chr(rand(97, 122));
            $domena[$i] = $pismeno;
        }
        
        //Vygenerovanie znakov od konca slova az po koniec buducej domeny
        for($i= ($hraniceSlova[1] + 1); $i<count($domena); $i++) {
            $pismeno = chr(rand(97, 122));
            $domena[$i] = $pismeno;
        }        
        
        $domenaText = '';
        for($i=0; $i<count($domena); $i++) {
            $domenaText = $domenaText . $domena[$i];            
        }        
        return $domenaText;                
    }
    
    //Vrati pole potencionalnych domen
    private function urob_kombinacie1($typ, $slovo) {                        
        //tato premenna je ako jedina subjektivne urcena. 
        //Musi sa nastavit ale tak aby bola co najvyssia a zaroven aby to dokayal v rzchlom case spracovat server
        //Je to maximalna dlzka pridanych pismen ku slovu na vstupe
        $dlzkaKom = 5;  
        $dlzkaSlova = strlen($slovo); //dlzka slova        
        $pole = array(); //Tu ukladame potencionalne domeny
          
        
        while(count($pole) < 100) {
        $domena = array(); //pola na zapisanie kombinacie
        $hraniceS = array(); //uklada zaciatocny a posledny index kde je v poli "domena" zapisane slovo      
        
        //Slovo ulozi do pola na urcene pozicie
        for($i=0; $i<$dlzkaKom+1; $i++) { //cyklus bezi pre kazdu poziciu
                     
            //Vytvori pole podla pozadovanej dlzky s prazdnymi polickami
            for($ii=0; $ii<($dlzkaKom+$dlzkaSlova); $ii++) {
                $domena[$ii] = '|';                        
            }              
            
            for($j=0; $j<$dlzkaSlova; $j++) { //zapiseme slovo do pola na priradene pozicie
                $domena[$i + $j] = $slovo[$j];             
            }
            
            $domenaText = '';
            for($ii=0; $ii<count($domena); $ii++) {
                $domenaText = $domenaText . $domena[$ii];            
            }
            
            $hraniceS[0] = $i;
            $hraniceS[1] = $i + $dlzkaSlova - 1;
            
            //U - vyriesit situaciu ak sa slovo nezada
            
            //$domenaText = $this->pridajOkoliePostupne($domena, $hraniceS);//Nahodne vygeneruje k slovu vhodne pismena            
            $domenaText = $this->pridajOkolieNaraz($domena, $hraniceS);//Nahodne vygeneruje k slovu vhodne pismena
            array_push($pole, $domenaText);  
                                     
        }
        }
                                       
        //Cyklus sa opakuje pokym sa nedosiahne potrebny pocet slov
        
        return $pole;
    }    
    
    //vrati pole stringov
    //Hned testuje na spoluhlasky
    public function vratPodKombinacie($dlzka) { 
        $pole = array();
        
        //V cykle urobi vsetky mozne kombinacie podla ASCII tabulky
        //interval 97-122 su male pismena v ASCII tabulke 
        $pocitadlo = 0;
        for($i=0; $i<100; $i++) { //50 sme si urcili ako pocet navrhov
            if($pocitadlo == 100) break; //poistka proti nekonecnemu cyklu
            $kombinacia = chr(rand(97, 122)); //prve pismeno            
            $pismeno1 = $kombinacia;            
            $j=0;            
            while($j < $dlzka){               
                $pismeno2 = chr(rand(97, 122));
                if($this->testSamohlasky($pismeno1, $pismeno2)) { //$this->testSamohlasky($pismeno1, $pismeno2)
                    $kombinacia = $kombinacia . $pismeno2;
                    $j = $j + 1;    
                    $pismeno1 = $pismeno2;                   
                } 
            }            
            $kombinacia = substr($kombinacia, 0, $dlzka); //odstrani posledne pismena navyse            
            /*
            if($this->jeDuplicita($pole, $kombinacia)) { //$this->jeDuplicita($pole, $kombinacia)
                $i--;
                $pocitadlo++;
                continue;
            }
            */
            
            array_push($pole, $kombinacia);
            $pocitadlo = 0;
        }               
        //$this->vypisVysledky($pole);        
        return $pole;
    }
    
    //Overi ci sa prvok v poli uz nachadza 
    //Ak je duplicita, vrati TRUE
    private function jeDuplicita($pole, $element) { //testuje ci sa element v poli STRINGov uz nenachadza
        for($i=0; $i<count($pole); $i++) {
            if(strcmp($pole[$i], $element) == 0) {
                return TRUE;            
            }    
        }   
        return FALSE;
    }    
    
    //Vrati pole vsetkych potencialnych domen
    private function zapasujPodslovo($retazec, $slovo) {
        $domeny = array();
        
        //Podla pozicie rozdeli retazec na prednu a koncovu cast
        $zaciatok;
        $koniec;
        for($i=0; $i<strlen($retazec); $i++) { 
            $domena = '';
            
            //Zapis prvej casti
            for($j=0; $j<$i; $j++) {
                $domena = $domena . $retazec[$j];                                
            }                        
            
            //Kontrola na spoluhlasky
            if($this->testSamohlasky(substr($domena, -1), $slovo[0]) == FALSE) { 
                //echo substr($domena, -1). " - " . $slovo[0];
                continue; //taka kombinacia neplati, ideme dalej
            }
            
            //test na vysoke pismeno ako hranicu so slovom
            if(strcmp($this->typ, "Cele slovo") == 0) { 
                if($i > 0)
                    if($this->testHranice($retazec[$i-1], $slovo[0]) == FALSE) {
                        //echo substr($domena, -1). " - " . $slovo[0] . "<br>";
                        continue;
                    }                
            }
               
            //Zapis slova
            for($j=0; $j<strlen($slovo); $j++) {
                $domena = $domena . $slovo[$j];   
            }            
           
            //Kontrola na spoluhlasky
            if($this->testSamohlasky(substr($slovo, -1),  $retazec[strlen($domena) - strlen($slovo)] ) == FALSE) {                    
                continue; //taka kombinacia neplati, ideme dalej
            }
                        
            //test na vysoke pismeno ako hranicu za slovom
            if(strcmp($this->typ, "Cele slovo")  == 0) { 
                //echo (strlen($domena) - strlen($slovo)) . " - " . (strlen($retazec)) . "<br>";
                if( ($i) < strlen($retazec)) {
                    if($this->testHranice(substr($slovo, -1),  $retazec[$i]) == FALSE) {
                        //echo substr($slovo, -1) . " - " . $retazec[strlen($domena) - strlen($slovo)] . "<br>";
                        continue;
                    }
                }    
            }            
            
            //Zapis zvysku
            for($j=strlen($domena) - strlen($slovo); $j< strlen($retazec); $j++) {
                $domena = $domena . $retazec[$j];                  
            }              
            
            array_push($domeny, $domena);
        }
        
        //Kontrola na spoluhlasky a na hranicu pri poslednej situacii
        if($this->testSamohlasky(substr($retazec, -1),  $slovo[0] ) == TRUE) {                    
            if(strcmp($this->typ, "Cele slovo")  == 0) { //test na vysoke pismeno ako hranicu so slovom
                if($this->testHranice(substr($retazec, -1),  $slovo[0]) == TRUE) {
                    array_push($domeny, $retazec . $slovo);
                }                
            }
            else if($this->testHranice(substr($retazec, -1),  $slovo[0]) == TRUE) {
                array_push($domeny, $retazec . $slovo);
            }     
        }           

        return $domeny;
    }
    
    public function urobKombinacie() {
        $kombinacie = array();
        
        if($this->dlzka == 10) { return $this->text; }
        /*
        $prvePism = substr($this->text, 0, 0); //prve pismeno slova
        $poslednePism = substr($this->text, (strlen($this->text)-1), (strlen($this->text)-1)); //posledne pismeno slova
        */
        $volneMiest = $this->dlzka - strlen($this->text); //urci na kolko miest ma generovat        
        
        while(count($kombinacie) < 10) { //dokym nebude ziskany dany pocet hodnot
            $genHodnoty = $this->vratPodKombinacie($volneMiest); //vygeneruje do pola scasti nahodne hodnoty                
            for($i=0; $i<count($genHodnoty); $i++) {
                    $kombinaciePom = $this->zapasujPodslovo($genHodnoty[$i], $this->text);
                    $kombinacie = array_merge($kombinacie, $kombinaciePom);
                    //$this->vypisVysledky($kombinaciePom);
                }                  
        }
        
        //$this->vypisVysledky($kombinacie);

        
        return $kombinacie;
    }
    
    //Vrati TRUE ak je domena volna
    private function is_domain_available($domain) { //zisti, ci domena je zaregistrovana
        //Check DNS records corresponding to a given Internet host name or IP address
        
        return !checkdnsrr($domain, 'ANY');
    }
   
    //Overi ci vysledky su este nezarezervovane a vrati tie zarezervovane
    private function overRegistraciu($poleDomen) { 
        //Navrh - Vylucit vsetky duplicity
        $volneDomeny = array();
        $registrovaneDomeny = array();        
        for($i=0; $i<count($poleDomen); $i++) {
            if($this->is_domain_available($poleDomen[$i] . ".com")) {
                array_push($volneDomeny, $poleDomen[$i] . ".com - volne");                               
            }            
            else {
                array_push($registrovaneDomeny, $poleDomen[$i] . ".com - zaregistrovane"); 
            }
        }
        
        $vystup = array();
        array_push($vystup, count($volneDomeny)); //na zaciatok vlozi pocet volnych domen        
        $vystup = array_merge($vystup, $volneDomeny);                
        $vystup = array_merge($vystup, $registrovaneDomeny);
        
        return $vystup;
    }       
    
    private function vypisVysledky($pole, $farba1, $farba2) { //Vypise len prvych 100 k je toho viac
        echo "<br> pocet volnych domen: " . $pole[0] . " z " . (count($pole) - 1); //Na zaciatok napise pocet kombinacii
        echo "<br>";
        for($i=1; $i<count($pole); $i++) {
            if($i <= intval($pole[0])) echo "<font color=\"$farba1\">$pole[$i]</font>";
            else echo "<font color=\"$farba2\">$pole[$i]</font>";
            echo "<br>";
        }
    }        
}

