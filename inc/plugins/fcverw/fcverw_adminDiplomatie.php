<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }
    
/* *******************************************************************************************************************************************************************
       Inhalt Dokument g. Diplomatie
******************************************************************************************************************************************************************* */

    // 1. show_diplo - Anzeige der aktuellen Diplomatien
    // 2. edit_diplo - Diplomatie anlegen, editieren etc.
    // 3. del_diplo - Diplomatie archivieren
    


/* *******************************************************************************************************************************************************************
       g1. Anzeige der aktuellen Diplomatien
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "show_diplo")
    {
        $landid = (int)$mybb->input['landid'];
        // Daten des Anzeigelandes heraussuchen
        $land = $db->simple_select("laender", "lname, lart", "landid = ".$landid);
        $landdata = $db->fetch_array($land);
        

        // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
        $sub_tabs['show_diplo'] = array(
            'title' => 'Diplomatie ansehen',
            'link' => 'index.php?module=config-fcverw&amp;action=show_diplo&amp;landid='.$landid,
            'description' => 'Anzeige der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']
        );
        
        $page->add_breadcrumb_item('&Uuml;bersicht der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']);
        $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']);
        $page->output_nav_tabs($sub_tabs, 'show_diplo');
        
        // Hier kommt nun die Übersicht
        $form = new Form("index.php?module=config-fcverw&amp;action=show_diplo", "post", "", 1);
        $form_container = new FormContainer('Diplomatie von '.$landdata['lart'].' '.$landdata['lname']);
        
        $i = 0;
        foreach ($diplostatus AS $statusid => $statusname)
        {
            $i++;
            
            // Alle Länder auswählen, wo Diplomatie mit dem Status ist
            $selectd = $db->write_query("
                SELECT 
                    ld.*, l.lname, l.lart, la.lname AS landname, la.lart AS landart 
                FROM 
                    ".TABLE_PREFIX."laender_diplomatie ld 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    l.landid = ld.dippartid 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender_archive la 
                ON 
                    la.landid = ld.dippartid 
                    
                WHERE 
                    ld.diplandid = ".$landid." 
                        AND 
                    ld.dipstatus = ".$statusid." 
                        AND 
                    ld.dipdelete = '0' 
                ORDER BY 
                    l.lname, landname 
            ");
            
            $diplos = "";
            
            while ($diplo = $db->fetch_array($selectd))
            {
                // Prüfen, ob bereits bestätigt oder nicht.
                $ok = "";
                if ($diplo['dipok'] == '0')
                {
                    $ok = "(Best&auml;tigung ausstehend)";
                }
                
                // Prüfen, ob es aus der Haupttabelle kommt oder aus dem Archiv
                if ($diplo['lname'] == "")
                {
                    $diplo['lart'] = "<i>[".$diplo['landart'];
                    $diplo['lname'] = $diplo['landname']."]</i>";
                }
                
                $diplos .= "<li>".$diplo['lart']." ".$diplo['lname']." ".$ok;
            }
            
            $form_container->output_cell('
                <b>'.$statusname.'</b>
                <div style="height: 200px; overflow: auto;">
                    <ul>
                        '.$diplos.'
                    </ul>
                </div>
            ', array("width" => "33%"));

             if ($i % 3 == "0")
             {
                $form_container->construct_row(); // Reihe erstellen
             }
        }
        
        
        $form_container->end();
        $form->end();
    }




/* *******************************************************************************************************************************************************************
       g2. Diplomatie anlegen, editieren etc.
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "edit_diplo")
    {
        $landid = (int)$mybb->input['landid'];
        $diplostat = (int)$mybb->input['diplostat'];
        $newLand = $mybb->input['new'];
        // Daten des Anzeigelandes heraussuchen
        $land = $db->simple_select("laender", "lname, lart", "landid = ".$landid);
        $landdata = $db->fetch_array($land);
        
        
        
        if ($mybb->request_method == 'post')
        { 
            // Prüfen, ob es bereits eine Beziehung gibt
            $pruef = $db->simple_select("laender_diplomatie", "dipstatus", "dippartid = ".$newLand." AND diplandid = ".$landid);
            
            // Wenn es das eigene Land ist, dann einfach wieder zurückleiten
            if ($newLand == $landid)
            {
                redirect("admin/index.php?module=config-fcverw&action=edit_diplo&amp;landid=".$landid."&amp;diplostat=".$diplostat);
            }
            elseif ($db->num_rows($pruef) >= '1') 
            {
                // Hier die Änderung
                $update = array(
                    "dipstatus" => $diplostat,
                    "dipdelete" => 0
                );
                
                $update1 = $db->update_query("laender_diplomatie", $update, "diplandid = ".$landid." AND dippartid = ".$newLand);
                $update1 = $db->update_query("laender_diplomatie", $update, "diplandid = ".$newLand." AND dippartid = ".$landid);
                
                redirect("admin/index.php?module=config-fcverw&action=edit_diplo&amp;landid=".$landid."&amp;diplostat=".$diplostat);
            } 
            else 
            {
                // Hier der Neueintrag
                // Ersteinmal in die Hauptrichtung - mit dem Path
                $insert[] = array(
                    "diplandid" => $landid,
                    "dippartid" => $newLand,
                    "dipstatus" => $diplostat,
                    "dipok" => "1"
                );
                
                
                // Dann aber anders herum - da muss das letzte Child ausgewählt werden!
                $elements = explode(',', $newLand);
                $celements = count($elements) - 1;
                
                $insert[] = array(
                    "diplandid" => $elements[$celements],
                    "dippartid" => $landid,
                    "dipstatus" => $diplostat,
                    "dipok" => "1"
                );
                
                $db->insert_query_multiple("laender_diplomatie", $insert);
                
                redirect("admin/index.php?module=config-fcverw&action=edit_diplo&amp;landid=".$landid."&amp;diplostat=".$diplostat);
            }
            
        }
        else 
        {
            // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
            $sub_tabs['edit_diplo'] = array(
                'title' => 'Diplomatie '.$landdata['lname'].' ['.$diplostatus[$diplostat].']',
                'link' => 'index.php?module=config-fcverw&amp;action=edit_diplo&amp;landid='.$landid,
                'description' => 'Bearbeiten der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']
            );
            
            $page->add_breadcrumb_item('Bearbeiten der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_header('L&auml;nderverwaltung - Bearbeiten der Diplomatie von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_nav_tabs($sub_tabs, 'edit_diplo');
            
            // Hier kommt die neue Eintragung.
            $form = new Form("index.php?module=config-fcverw&amp;action=edit_diplo", "post", "", 1);
            $form_container = new FormContainer('Diplomatie von '.$landdata['lart'].' '.$landdata['lname'].' bearbeiten');
            
            echo $form->generate_hidden_field('landid', $landid);
            echo $form->generate_hidden_field('diplostat', $diplostat);
            
            // Erst einmal alle möglichen Länder auslesen
            $laender = array();
            
            // Aktive Länder auslesen
            $laendersel = fcverw_LandList(0, 0, 0);
            $trenner = "";
            while ($laenderdata = $db->fetch_array($laendersel))
            {
                $trenner = str_repeat("-", $laenderdata['Ebene']);
                if ($trenner != "")
                {
                    $trenner .= " ";
                }
                
                
                // Prüfen, ob es bereits eine Beziehung gibt
                $pruef = $db->simple_select("laender_diplomatie", "dipstatus", "dippartid = ".$laenderdata['landid']." AND dipdelete = '0'");
                
                // Sicherstellen, dass das Land nicht das eigentliche ist UND dass es nicht bereits als Bündnis o.ä. da ist                  
                if ($laenderdata['landid'] == $landid) 
                {
                    $laender[$laenderdata['landid']] = $trenner."(nicht w&auml;hlbar) ".$laenderdata['lart']." ".$laenderdata['lname'];
                }
                // Wenn eine Beziehung besteht, dann ...
                elseif ($db->num_rows($pruef) == '1')
                {
                        $select = $db->fetch_field($pruef, 'dipstatus');
                        $laender[$laenderdata['landid']] = $trenner."(bereits: ".$diplostatus[$select].") ".$laenderdata['lart']." ".$laenderdata['lname'];
                } 
                // Ansonsten normal anzeigen
                else 
                {
                    $laender[$laenderdata['landid']] = $trenner."".$laenderdata['lart']." ".$laenderdata['lname'];
                }
                
            }
            // Reihe formen
            $form_container->output_cell('<b>'.$diplostatus[$diplostat].' neu</b>:', array("width" => "15%"));
            $form_container->output_cell(
                $form->generate_select_box(
                    'new',
                    $laender,
                    $mybb->input['new'], 
                    array('id' => 'new', 'style' => 'width: 200px; padding: 10px;')
                )
            );
            $form_container->construct_row();

            $form_container->end();
            $buttons[] = $form->generate_submit_button('eintragen');
            $form->output_submit_wrapper($buttons);
            $form->end();
            
            echo "<br />";
            
            // Daten auslesen
            $select = $db->write_query("
                SELECT 
                    ld.*, l.lname, l.lart, l.ldelete, la.lname AS landname, la.lart AS landart, la.ldelete AS landdelete 
                FROM 
                    ".TABLE_PREFIX."laender_diplomatie ld 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    l.landid = ld.dippartid 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender_archive la 
                ON 
                    la.landid = ld.dippartid 
                    
                WHERE 
                    ld.diplandid = ".$landid." 
                        AND 
                    ld.dipstatus = ".$diplostat." 
                        AND 
                    ld.dipdelete = '0' 
                ORDER BY 
                    l.lname, landname 
            ");
        
            
            $form = new Form("", "post", "", 1);
            $form_container = new FormContainer('Zeige alle Diplomatien der Art '.$diplostatus[$diplostat]);
            
            $form_container->output_row_header('Land', array("class" => "align_center", "width" => "30%"));
            $form_container->output_row_header('Option', array("class" => "align_center"));
            
            while ($data = $db->fetch_array($select))
            {
                // Prüfen, ob bereits bestätigt oder nicht.
                $ok = "";
                if ($data['dipok'] == '0')
                {
                    $ok = " (Best&auml;tigung ausstehend)";
                }
                
                // Prüfen, ob es aus der Haupttabelle kommt oder aus dem Archiv
                // Wenn Haupttabelle leer, dann ...
                if ($data['lname'] == "")
                {
                    $data['lart'] = "<i>[".$data['landart'];
                    $data['lname'] = $data['landname']."]</i>";
                }
                
                // Wenn ldelete = 1, dann auch durchstreichen
                if ($data['ldelete'] == "1")
                {
                    $data['lart'] = "<i>[".$data['lart'];
                    $data['lname'] = $data['lname']."]</i>";
                }
                
                $form_container->output_cell("<b>".$data['lart']." ".$data['lname']."</b>".$ok);
                $form_container->output_cell('<a href="index.php?module=config-fcverw&action=del_diplo&amp;landid='.$landid."&amp;dipland=".$data['dippartid'].'&amp;diplostat='.$diplostat.'">L&ouml;schen</a>');
                $form_container->construct_row(); // Reihe erstellen
            }
            
            $form_container->end();
            $form->end();
        }
        
    }


/* *******************************************************************************************************************************************************************
       g2. Diplomatie löschen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "del_diplo")
    {
        $landid = $mybb->input['landid'];
        $partid = $mybb->input['dipland'];
        
        $update = array(
            "dipdelete" => 1
        );
        
        $db->update_query("laender_diplomatie", $update, "diplandid = ".$landid." AND dippartid = ".$partid);
        $db->update_query("laender_diplomatie", $update, "diplandid = ".$partid." AND dippartid = ".$landid);
        
        redirect("admin/index.php?module=config-fcverw&action=edit_diplo&amp;landid=".$landid."&amp;diplostat=".$mybb->input['diplostat']);
    }
    
    