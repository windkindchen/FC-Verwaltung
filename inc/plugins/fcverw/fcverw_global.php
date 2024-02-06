<?php

// Disallow direct access to this file for security reasons
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />
        Please make sure IN_MYBB is defined.');
}


/* ***********************************************
          Part 00: Get Global
   *********************************************** */

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


function fcverw_LandList($rid, $del)
{
    global $db, $mybb, $landliste, $table;
    
    $table = "";
    if ($del == '1')
    {
        $table = "_archive";
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
                    lrid = '".$rid."'
                        AND 
                    lparent = '0' 
                
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
