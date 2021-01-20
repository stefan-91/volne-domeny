     
<form action="generator_domen.php" method="get">
    <h3>Generátor domén</h3>
    <p><strong>UPOZORNENIE:</strong> kvôli funkcii <a href="https://www.php.net/manual/en/function.checkdnsrr.php" target="_blank">checkdnsrr</a> program môže bežať, resp. čakať na odpoveď z externého zdroja dohromady aj niekoľko desiatok minút. 
    Na demonštráciu a pre rýchle zbehnutie programu zvoľte malý počet vygenerovaných domén na preverenie (napr. s malým počtom znakov).</p>
    
    <i>Generátor domén umožňuje nájsť nezaregistrované domény. Vychádza z podmienok, pri ktorých sú skoro všetky rozumné slová v najpoužívanejších doménach, ako .com, .info. .net a .org, už dávno zaregistrované.
       <br>
       <br>
       Preto sa k požadovanému slovu zvyknú pridávať ďalšie znaky navyše, takým spôsobom, aby celá doména vyzerala aj naďalej použitelne.
       Konkrétne Generátor domén pridáva ku kľúčovému slovu znaky, ktoré tvoria slabiky, vytvára hranice okolo kľúčového slova pomocou vysokých a nízkych písmen, pridáva čísla atď. Nové kombinácie následne overí, či nie sú takisto zaregistrované.
       <br>
       <br>
       Vďaka tomu používateľ nemusí namáhavo vymýšľať varianty nových domén a náledne ich overovať.
    </i>
    <br>
    <br>
    Kľúčové slovo: <input type="text" name="kSlovo" value ="<?php if(isset($_GET["kSlovo"])) echo $_GET["kSlovo"]; ?>" required title="Napíšte slovo, ktoré by Ste chceli, aby obsahovala Vaša doména.">
    <br>
    Doména:
    <input type="radio" name="domena1" value=".com" <?php if(!isset($_GET["domena1"]) ||  $_GET["domena1"] == ".com") echo 'CHECKED'; ?> >.com
    <input type="radio" name="domena1" value=".net" <?php if(isset($_GET["domena1"])) { if($_GET["domena1"] == ".net") echo 'CHECKED';} ?> >.net 
    <input type="radio" name="domena1" value=".org" <?php if(isset($_GET["domena1"])) {if($_GET["domena1"] == ".org") echo 'CHECKED'; } ?> >.org
    <input type="radio" name="domena1" value=".info" <?php if(isset($_GET["domena1"])) {if($_GET["domena1"] == ".info") echo 'CHECKED'; } ?> >.info    
    <input type="radio" name="domena1" value="ina_domena" <?php if(isset($_GET["domena1"])) { if($_GET["domena1"] == "ina_domena") echo 'CHECKED';} ?> >
    iná doména <input type="text" name="ina_domena1" value = "<?php if(isset($_GET["ina_domena1"])) echo $_GET["ina_domena1"]; ?>" />​
    (napríklad ".uk")
    <br><br>
    <b>Pokročilé nastavenia (nepovinné)</b>
    
    <br>
    Kľúčové slovo má byť:
    <input type="radio" name="sInterpr" value="Cele slovo" <?php if(!isset($_GET["sInterpr"]) ||  $_GET["sInterpr"] == "Cele slovo") echo 'CHECKED'; ?> 
           title="Pri tejto možnosti sa berú do úvahy iba znaky okolo kľúčového slova, ktoré by ho ohraničovali svojou výškou. 
Príklad: slovol (áno), slovos (nie)" 
    >celé slovo
    
    <input type="radio" name="sInterpr" value="Cast buduceho slova" <?php if(isset($_GET["sInterpr"])) {if($_GET["sInterpr"] == "Cast buduceho slova") echo 'CHECKED'; } ?> 
           title="Pri tejto možnosti sa ohraničenie kľúčového slova výškou okolitých písmen neberie do úvahy. 
Príklad: slovol (áno), slovos (áno)" 
    >čast budúceho slova
    
    <br>
    Pridané znaky môžu byť:
    <input type="checkbox" name="hocijakeKom" value="Hocijake kombinacie" <?php if(isset($_GET["hocijakeKom"])) echo 'CHECKED'; ?> 
           title="Táto možnosť vygeneruje úplne ľubovoľné znaky okolo kľúčového slova. Na jednej strane ponúne viac návrhov na doménu ale na druhej strane vygeneruje aj menej vhodné návrhy domén.
           
Konkrétne:           
1) Neberie sa do úvahy existencia slabík.
Príklad slova so slabikou: slovoles.com (les je slabika)
Príklad slova bez slabiky: slovolll.com (lll nie je slabika)

2) Neberie sa do úvahy ohraničenie kľúčového slova.
Príklad ohraničeného slova: slovol.com (l je vyššie písmeno ako o)
Príklad neohraničeného slova: slovos.com (s nie je vyššie písmeno ako o)" 

    >bez obmedzení
    
    <input type="checkbox" name="cisla" value="cisla1" <?php if(isset($_GET["cisla"])) echo 'CHECKED'; ?> title="Berie do úvahy aj domény s číslami. Napríklad slovo123.com" >aj čísla
        
    <br>
    Počet pridaných znakov: <input type="number" name="pridanie" value = "<?php if(isset($_GET["pridanie"])) echo $_GET["pridanie"]; ?>" title="Napríklad pre voľbu 3 vygeneruje návrhy domén soyslovo.com, aayslovo.com (slovo tu má pridané 3 znaky)"> 
    &nbsp;
    a počet výsledkov: <input type="number" name="pocet_vysledkov" value = "<?php if(isset($_GET["pocet_vysledkov"])) echo $_GET["pocet_vysledkov"]; ?>" title="Počet výsledkov pre zvolený počet pridaných znakov." >
    <br><br>
    <input type="submit" name="btnSpust" value = "Generuj" >    
</form>

<?php
error_reporting(E_ALL);
ini_set("display_errors","On");

if (isset($_GET['btnSpust'])) {

    //==== Spracovanie klucoveho slova ====
    if(empty($_GET["kSlovo"])) {
        $slovo = '';
    }
    else {
        $slovo = $_GET["kSlovo"];
        $slovo = strtolower($slovo);
    }
    
  
    //====== Spracovanie domeny 1. stupna ======
    $domena1 = "" . $_GET["domena1"];
    
    if(strcmp($domena1, "ina_domena") == 0) {
        if(!empty($_GET["ina_domena1"])) { 
            $domena1 = $_GET["ina_domena1"];
            if($domena1[0] != '.') $domena1 = '.' . $domena1;

            for($i=1; $i< strlen($domena1); $i++) { //Osetrenie pred nespravnymi vstupmi, pouzivatel tam zada nanajvys zlu domenu ale ta musi mat spravny format
                if(((97 <= $domena1[$i] && $domena1[$i] <= 122) || ($domena1[$i] != '.')) && ($domena1[1] != '.')) {}  
                else { //Ak to najde chybu, tak to nastavi domenu ".com"
                   $domena1 = ".com";                
                }                            
            }         
        }    
    }
    
    //===== Spracovanie typu klucoveho slova =====
    if($slovo == '') { 
        $typ = "Cast buduceho slova"; 
    }    
    else $typ = $_GET["sInterpr"];                    

    //===== Soracovanie povolenych znakov =====
    $hocijake;
    if(isset($_GET["hocijakeKom"])) {
        $hocijake = true;
    }
    else $hocijake = false;
    
    $cisla;
    if(isset($_GET["cisla"])) {
        $cisla = true;
    }
    else $cisla = false;    
    
    //===== Spracovanie kvantitativnych vlastnosti vysledkov =======
    if(empty($_GET["pridanie"]) || empty($_GET["pocet_vysledkov"])) {
        $pridanie = 0;
        $pocet_vysledkov = 0;    
    }
    else { //ak pole nie je prazdne
        $pridanie = $_GET["pridanie"];     
        $pocet_vysledkov = $_GET["pocet_vysledkov"];

        if(! ($pridanie > 0 && $pocet_vysledkov > 0) ) {
            $pridanie = 0;
            $pocet_vysledkov = 0;
        }

        if($pocet_vysledkov > 500) { //aby sme nepretazili server
            $pocet_vysledkov = 500;     
        }    

        if($pridanie > 10) { //aby sme nepretazili server
            $pridanie = 10;     
        }     
    }

    //echo $slovo . " - " . $domena1 . " - " . $typ . " - " . $pridanie . " - " . $pocet_vysledkov;
    
    //======== Volanie triedy =========
    
    require_once('Generovacia_trieda.php');
    $myGen = new Generovacia_trieda($slovo, $domena1, $typ, $pridanie, $pocet_vysledkov, $cisla, $hocijake);
    //$myGen->urobKombinacie();
    $myGen->vygeneruj();           
}
 
?>










