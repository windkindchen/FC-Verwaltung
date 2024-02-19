<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }

/* *******************************************************************************************************************************************************************
       Inhalt Dokument e. Bewohner
******************************************************************************************************************************************************************* */

    // 1. Bewohner anzeigen + eintragen
    // 2. Bewohner löschen


/* *******************************************************************************************************************************************************************
       e1. Bewohner anzeigen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "show_chars")
    { 
        $landid = (int)$mybb->input['landid'];
        $uselect = (int)$mybb->input['uselect'];
        // Daten des Anzeigelandes heraussuchen
        $land = $db->simple_select("laender", "lname, lart", "landid = ".$landid);
        $landdata = $db->fetch_array($land);
        
        if ($mybb->request_method == 'post')
        {
            // Prüfen, ob es bereits den Bewohner gibt
            $pruef = $db->simple_select("laender_personen", "*", "uid = ".$uselect." AND landid = ".$landid);
            
            // Ist der User bereits eingetragen, dann ...
            if ($db->num_rows($pruef) >= "1")
            {
                redirect("admin/index.php?module=config-fcverw&action=show_chars&amp;landid=".$landid);
            }
            // Ansonsten eintragen
            else 
            {
                $insert = array(
                    "landid" => $landid,
                    "uid" => $uselect
                );
                
                $db->insert_query("laender_personen", $insert);
                redirect("admin/index.php?module=config-fcverw&action=show_chars&amp;landid=".$landid);
            } 
        } 
        else
        {
            // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
            $sub_tabs['show_chars'] = array(
                'title' => 'Bewohner verwalten',
                'link' => 'index.php?module=config-fcverw&amp;action=show_chars&amp;landid='.$landid,
                'description' => 'Anzeige der Bewohner von '.$landdata['lart'].' '.$landdata['lname']
            );
            
            $page->add_breadcrumb_item('&Uuml;bersicht der Bewohner von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht der Bewohner von '.$landdata['lart'].' '.$landdata['lname']);
            $page->output_nav_tabs($sub_tabs, 'show_chars');
            
            
            // Hier kommt die neue Eintragung.
            $form = new Form("index.php?module=config-fcverw&amp;action=show_chars", "post", "", 1);
            $form_container = new FormContainer('Bewohner von '.$landdata['lart'].' '.$landdata['lname'].' bearbeiten');
            
            echo $form->generate_hidden_field('landid', $landid);
                  
            // Aktive Länder auslesen
            $ugroups = "'2', '3', '4', '6', '8', '9', '10'";
            $userselect = fcverw_UserSelect($ugroups);
            
            $uselect = array();
            
            while ($userdata = $db->fetch_array($userselect))
            {
                // Prüfen, ob bereits in dem Land eingetragen
                $pruef = $db->simple_select("laender_personen", "*", "uid = ".$userdata['uid']." AND landid = ".$landid);
                
                if ($db->num_rows($pruef) >= '1')
                {
                    $uselect[$userdata['uid']] = "(bereits eingetragen) ".$userdata['username'];
                }
                else
                {
                    $uselect[$userdata['uid']] = $userdata['username'];
                }
            }
            // Reihe formen
            $form_container->output_cell('<b>neuer Bewohner</b>:', array("width" => "15%"));
            $form_container->output_cell(
                $form->generate_select_box(
                    'uselect',
                    $uselect,
                    $mybb->input['uselect'], 
                    array('id' => 'uselect', 'style' => 'width: 200px; padding: 10px;')
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
                    lp.*, u.username  
                FROM 
                    ".TABLE_PREFIX."laender_personen lp 
                    
                LEFT JOIN 
                    ".TABLE_PREFIX."users u 
                ON 
                    u.uid = lp.uid  
                    
                WHERE 
                    lp.landid = ".$landid."
                ORDER BY 
                    u.username
            "); 
        
            
            $form = new Form("", "post", "", 1);
            $form_container = new FormContainer('Zeige alle Bewohner');
            
            $form_container->output_row_header('Bewohner', array("class" => "align_center", "width" => "30%"));
            $form_container->output_row_header('Option', array("class" => "align_center"));
            
            while ($data = $db->fetch_array($select))
            {
                $form_container->output_cell("<b>".$data['username']."</b>");
                $form_container->output_cell('<a href="index.php?module=config-fcverw&action=del_bewohner&amp;landid='.$landid."&amp;uid=".$data['uid'].'">L&ouml;schen</a>');
                $form_container->construct_row(); // Reihe erstellen
            } 
            
            $form_container->end();
            $form->end(); 
             
            
        }
        
    }


/* *******************************************************************************************************************************************************************
       f2. Bewohner löschern
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == "del_bewohner")
    {
        $landid = (int)$mybb->input['landid'];
        $uid = (int)$mybb->input['uid'];
        
        $db->delete_query("laender_personen", "landid = ".$landid." AND uid = ".$uid);
        redirect("admin/index.php?module=config-fcverw&action=show_chars&amp;landid=".$landid);
    }