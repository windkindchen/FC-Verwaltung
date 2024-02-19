<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }


/* *******************************************************************************************************************************************************************
       Inhalt Dokument
******************************************************************************************************************************************************************* */

    // 1. Funktion fcverw_KonReg - Daten der Regionen und Kontinente nach Status auswählen
    // 2. Funktion fcverw_LandList - Auswahl der Länder nach Region, Status und ggf. Landid
    // 3. Funktion fcverw_UserSelect - Daten der User nach angegebenen Usergruppen selektieren
    // 4. Arrays für Länderinfofelder
    // 5. Arrays für Diplomatie



/* *******************************************************************************************************************************************************************
       1. Funktion fcverw_KonReg - Daten der Regionen und Kontinente nach Status auswählen
******************************************************************************************************************************************************************* */

    function fcverw_KonReg($kdel, $rdel)
    {
        global $db, $mybb, $konreg;
        
        if ($kdel == '2')
        {
            $kdel = '%';
        }
        
        if ($rdel == '2')
        {
            $rdel = '%';
        }
        
        $konreg  = $db->write_query("
            SELECT 
                r.rid, r.rname, r.rkid, k.kname, k.kid
            FROM 
                ".TABLE_PREFIX."laender_regionen r 
            LEFT JOIN 
                ".TABLE_PREFIX."laender_kontinente k 
            ON 
                k.kid = r.rkid 
            WHERE 
                k.kdelete LIKE '".$kdel."' AND r.rdelete LIKE '".$rdel."'  
            ORDER BY 
                k.kname, r.rname
        ");
            
        return $konreg;
    }




/* *******************************************************************************************************************************************************************
       2. Funktion fcverw_LandList - Auswahl der Länder nach Region, Status und ggf. Landid
******************************************************************************************************************************************************************* */

    function fcverw_LandList($rid, $del, $lid)
    {
        global $db, $mybb, $landliste, $table;
        
        $table = "";
        $parent = "";
        
        if ($rid == "0")
        {
            $lrid = "";
        }
        else {
            $lrid = "lrid = '".$rid."'
                            AND ";
        }
        
        if ($del == '1')
        {
            $table = "_archive";
        }
        
        if ($lid != '0' && $lid != '')
        {
            $parent = "AND 
                landid = ".$lid;
        }
        
        $landliste = $db->query("
            WITH RECURSIVE 
                LandListe".$table."
                (
                    landid, 
                    lname, 
                    lkuerzel, 
                    lart, 
                    lreal, 
                    lbesp, 
                    lstat, 
                    lparent, 
                    lverantw, 
                    ldelete, 
                    Ebene, 
                    Path,
                    PathID
                )
                AS 
                (
                    SELECT 
                        landid, 
                        lname, 
                        lkuerzel, 
                        lart, 
                        lreal, 
                        lbesp, 
                        lstat, 
                        lparent, 
                        lverantw, 
                        ldelete, 
                        0 AS Ebene, 
                        CAST(lname AS CHAR(2000)),
                        CAST(landid AS CHAR(2000))
                    FROM 
                        ".TABLE_PREFIX."laender".$table." 
                    WHERE 
                        ".$lrid."
                        lparent = '0' 
                        ".$parent."
                    
                    UNION ALL 
                    
                    SELECT 
                        l.landid, 
                        l.lname, 
                        l.lkuerzel, 
                        l.lart, 
                        l.lreal, 
                        l.lbesp, 
                        l.lstat, 
                        l.lparent, 
                        l.lverantw, 
                        l.ldelete, 
                        la.Ebene + 1, 
                        CONCAT(la.Path, ',', l.lname),
                        CONCAT(la.PathID, ',', l.landid)
                    FROM 
                        LandListe".$table." AS la 
                    JOIN 
                        ".TABLE_PREFIX."laender".$table." AS l
                    ON 
                        la.landid = l.lparent 
                )
                
            SELECT 
                * 
            FROM 
                LandListe".$table." 
            ORDER BY 
                Path;
        ");
        
        return $landliste;
    }



/* *******************************************************************************************************************************************************************
       3. Funktion fcverw_UserSelect - Daten der User nach angegebenen Usergruppen selektieren
******************************************************************************************************************************************************************* */

    function fcverw_UserSelect($ugroups)
    {
        global $db, $mybb, $userselect;
        
        $userselect  = $db->write_query("
            SELECT 
                * 
            FROM 
                ".TABLE_PREFIX."users
            WHERE 
                usergroup IN (".$ugroups.") 
            ORDER BY 
                username
        ");
            
        return $userselect;
        
    }
    
    

/* *******************************************************************************************************************************************************************
       4. Arrays für Länderinformation
******************************************************************************************************************************************************************* */

    function fcverw_LandInfo()
    {
        global $themen; 
        
        $themen = array(
            "allgemein" => "Allgemeine Informationen",
            "hauptstadt" => "Hauptstadt",
            "sprache" => "Amtssprache und verbreitete Sprachen",
            "royal" => "K&ouml;nigliche Familie und Thronfolge",
            "regierung" => "Landespolitik",
            "diplomatie" => "Diplomatische Grunds&auml;tze",
            "volk" => "Bev&ouml;lkerungsdaten",
            "religion" => "Religion und Glaube", 
            "einwanderung" => "Einwanderungspolitik",
            "wirtschaft" => "Wirtschaft",
            "militaer" => "Milit&auml;r",
            "medien" => "Medien und Pressefreiheit",
            "rebellen" => "Rebellen",
            "sonstiges" => "Sonstige Informationen"
        );
        
        return $themen;
    }
    



/* *******************************************************************************************************************************************************************
       5. Arrays für Diplomatie
******************************************************************************************************************************************************************* */

    function fcverw_DiploStatus()
    {
        global $diplostatus;
        
        $diplostatus = array(
            "1" => "B&uuml;ndnis",
            "2" => "Ausgesetztes B&uuml;ndnis",
            "3" => "Neutralit&auml;t",
            "4" => "Ausgesetzte Feindschaft",
            "5" => "Feindschaft",
            "6" => "Milit&auml;rischer Konflikt"
        );
        
        return $diplostatus;
    }
    