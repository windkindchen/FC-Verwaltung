<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }

/* *******************************************************************************************************************************************************************
       Inhalt Dokument f. Verwandtschaften
******************************************************************************************************************************************************************* */

    // 1. Verwandtschaften anzeigen
    // 2. Verwandtschaften löschen
    

/* *******************************************************************************************************************************************************************
       f1. Verwandtschaften anzeigen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "show_verwandt")
    {
        $landid = (int)$mybb->input['landid'];
        $verwid = (int)$mybb->input['verwid'];
        // Daten des Anzeigelandes heraussuchen
        $land = $db->simple_select("laender", "lname, lart", "landid = ".$landid);
        $landdata = $db->fetch_array($land);
        
        if ($mybb->request_method == 'post')
        {
            // Prüfen, ob es bereits eine Beziehung gibt
            $pruef = $db->write_query("
                SELECT 
                    * 
                FROM 
                    ".TABLE_PREFIX."laender_verwandt 
                WHERE 
                    (landid = ".$landid." AND verwid = ".$verwid.") 
                        OR 
                    (landid = ".$verwid." AND verwid = ".$landid.") 
            ");
            
            // Ist das Land das, das bereits da ist, dann einfach weiterleiten
            if ($verwid == $landid)
            {
                redirect("admin/index.php?module=config-fcverw&action=show_verwandt&amp;landid=".$landid);
            }
            // Beziehung besteht bereits - dann auch einfach weiterleiten
            elseif ($db->num_rows($pruef) >= "1")
            {
                redirect("admin/index.php?module=config-fcverw&action=show_verwandt&amp;landid=".$landid);
            }
            // Ansonsten eintragen
            else 
            {
                $insert[] = array(
                    "landid" => $landid,
                    "verwid" => $verwid
                );
                
                $insert[] = array(
                    "landid" => $verwid,
                    "verwid" => $landid
                );
                
                $db->insert_query_multiple("laender_verwandt", $insert);
                redirect("admin/index.php?module=config-fcverw&action=show_verwandt&amp;landid=".$landid);
            }
        } 
        else
        {
            // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
            $sub_tabs['show_verwandt'] = array(
                'title' => 'Verwandtschaften ansehen',
                'link' => 'index.php?module=config-fcverw&amp;action=show_verwandt&amp;landid='.$landid,
                'description' => 'Anzeige der famili&auml;ren Beziehungen von '.$landdata['lart'].' '.$landdata['lname']
            );
            
            $page->add_breadcrumb_item('&Uuml;bersicht der famili&auml;ren Beziehungen von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht der famili&auml;ren Beziehungen von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_nav_tabs($sub_tabs, 'show_verwandt');
            
            
            // Hier kommt die neue Eintragung.
            $form = new Form("index.php?module=config-fcverw&amp;action=show_verwandt", "post", "", 1);
            $form_container = new FormContainer('Famili&auml;re Beziehungen von '.$landdata['lart'].' '.$landdata['lname'].' bearbeiten');
            
            echo $form->generate_hidden_field('landid', $landid);
            
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
                $pruef = $db->simple_select("laender_verwandt", "*", "(verwid = ".$laenderdata['landid']." OR landid = ".$laenderdata['landid'].")");
                
                // Sicherstellen, dass das Land nicht das eigentliche ist UND dass es nicht bereits als Bündnis o.ä. da ist                  
                if ($laenderdata['landid'] == $landid ) 
                {
                    $laender[$laenderdata['landid']] = $trenner."(nicht w&auml;hlbar) ".$laenderdata['lart']." ".$laenderdata['lname'];
                }
                // Wenn eine Beziehung besteht, dann ...
                elseif ($db->num_rows($pruef) >= '1')
                {
                        $laender[$laenderdata['landid']] = $trenner."(bereits verwandt) ".$laenderdata['lart']." ".$laenderdata['lname'];
                } 
                // Ansonsten normal anzeigen
                else 
                {
                    $laender[$laenderdata['landid']] = $trenner."".$laenderdata['lart']." ".$laenderdata['lname'];
                }
                
            }
            // Reihe formen
            $form_container->output_cell('<b>neue Verwandtschaft</b>:', array("width" => "15%"));
            $form_container->output_cell(
                $form->generate_select_box(
                    'verwid',
                    $laender,
                    $mybb->input['verwid'], 
                    array('id' => 'verwid', 'style' => 'width: 200px; padding: 10px;')
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
                    lv.*, l.lname, l.lart, l.ldelete, la.lname AS landname, la.lart AS landart, la.ldelete AS landdelete 
                FROM 
                    ".TABLE_PREFIX."laender_verwandt lv 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    l.landid = lv.verwid  
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."laender_archive la 
                ON 
                    la.landid = lv.verwid 
                    
                WHERE 
                    lv.landid = ".$landid."
                ORDER BY 
                    l.lname, landname 
            ");
        
            
            $form = new Form("", "post", "", 1);
            $form_container = new FormContainer('Zeige alle bestehenden Verwandtschaften');
            
            $form_container->output_row_header('Land', array("class" => "align_center", "width" => "30%"));
            $form_container->output_row_header('Option', array("class" => "align_center"));
            
            while ($data = $db->fetch_array($select))
            {
       
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
                $form_container->output_cell('<a href="index.php?module=config-fcverw&action=del_verwandt&amp;landid='.$landid."&amp;verwid=".$data['verwid'].'">L&ouml;schen</a>');
                $form_container->construct_row(); // Reihe erstellen
            }
            
            $form_container->end();
            $form->end(); 
            
            
        }
        
    }


/* *******************************************************************************************************************************************************************
       f2. Verwandtschaften anzeigen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "del_verwandt")
    {
        $landid = (int)$mybb->input['landid'];
        $verwid = (int)$mybb->input['verwid'];
        
        $db->delete_query("laender_verwandt", "(landid = ".$landid." AND verwid = ".$verwid.") OR (landid = ".$verwid." AND verwid = ".$landid.")");
        redirect("admin/index.php?module=config-fcverw&action=show_verwandt&amp;landid=".$landid);
    }