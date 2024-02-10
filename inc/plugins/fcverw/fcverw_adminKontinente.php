<?php

// Disallow direct access to this file for security reasons
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />
        Please make sure IN_MYBB is defined.');
}

/* *******************************************************************************************************************************************************************
       a1. Alle Kontinente anzeigen lassen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "kontinente")
        {

            $page->add_breadcrumb_item('&Uuml;bersicht aller Kontinente');
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht aller Kontinente');
        
            // Welches Tab ist ausgewählt?
            $page->output_nav_tabs($sub_tabs, 'kontinente');
            
            // Tabelle alle aktiven Kontinente
            // Tabelle kreieren - Headerzeile
            $form = new Form("index.php?module=config-fcverw", "post");
            $form_container = new FormContainer('Alle aktiven Kontinente');
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Kontinentname', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('Regionen<br /> aktiv (archiviert)', array("class" => "align_center", "width" => "8%"));
            $form_container->output_row_header('L&auml;nder<br /> aktiv (archiviert)', array("class" => "align_center", "width" => "8%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));
        
            // Hier werden die aktiven Kontinente ausgelesen
            $fc_kontsel = $db->simple_select("laender_kontinente", "*", "kdelete = '0'", array('order_by' => 'kname'));
            while ($row = $db->fetch_array($fc_kontsel))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $regionena = $db->num_rows($db->simple_select("laender_regionen", "rid", "rdelete = '0' AND rkid = ".$row['kid']));
                $regionenb = $db->num_rows($db->simple_select("laender_regionen", "rid", "rdelete = '1' AND rkid = ".$row['kid']));
                $laendera = $db->num_rows($db->simple_select("laender", "landid", "ldelete = '0' AND lkid = ".$row['kid']));
                $laenderb = $db->num_rows($db->simple_select("laender_archive", "landid", "ldelete = '1' AND lkid = ".$row['kid']));
                
        
                $form_container->output_cell($row['kid'], array("class" => "align_center"));
                $form_container->output_cell("<b>".$row['kname']."</b>");
                $form_container->output_cell($row['kbeschr']);
                $form_container->output_cell($regionena." (".$regionenb.")", array("class" => "align_center"));
                $form_container->output_cell($laendera." (".$laenderb.")", array("class" => "align_center"));
        
                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup = new PopupMenu("fcverw_".$row['kid'], "Optionen");
                $popup->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_kontinent&amp;kid=".$row['kid']
                );
                $popup->add_item(
                    "Archivieren",
                    "index.php?module=config-fcverw&amp;action=del_kontinent&amp;kid=".$row['kid']
                );
                $form_container->output_cell($popup->fetch(), array("class" => "align_center"));
        
                $form_container->construct_row(); // Reihe erstellen
            }
            $form_container->end();
            
            // Tabelle alle aktiven Kontinente
            // Tabelle kreieren - Headerzeile
            $form = new Form("index.php?module=config-fcverw", "post");
            $form_container = new FormContainer('Alle archivierten Kontinente');
            $form_container->output_row_header('ehem. ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Kontinentname', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('Regionen<br /> aktiv (archiviert)', array("class" => "align_center", "width" => "8%"));
            $form_container->output_row_header('L&auml;nder<br /> aktiv (archiviert)', array("class" => "align_center", "width" => "8%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));
        
            // Hier werden die gelöschten Kontinente ausgelesen
            $fc_kontsel2 = $db->simple_select("laender_kontinente", "*", "kdelete = '1'", array('order_by' => 'kname'));
            while ($row2 = $db->fetch_array($fc_kontsel2))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $regionena2 = $db->num_rows($db->simple_select("laender_regionen", "rid", "rdelete = '0' AND rkid = ".$row2['kid'])); // aktive regionen
                $regionenb2 = $db->num_rows($db->simple_select("laender_regionen", "rid", "rdelete = '1' AND rkid = ".$row2['kid'])); // gelöschte regionen
                $laendera2 = $db->num_rows($db->simple_select("laender", "landid", "ldelete = '0' AND lkid = ".$row2['kid'])); // aktive regionen
                $laenderb2 = $db->num_rows($db->simple_select("laender_archive", "landid", "ldelete = '1' AND lkid = ".$row2['kid'])); // gelöschte regionen
        
                $form_container->output_cell($row2['kid'], array("class" => "align_center"));
                $form_container->output_cell("<b>".$row2['kname']."</b>");
                $form_container->output_cell($row2['kbeschr']);
                $form_container->output_cell($regionena2." (".$regionenb2.")", array("class" => "align_center"));
                $form_container->output_cell($laendera2." (".$laenderb2.")", array("class" => "align_center"));
        
                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup2 = new PopupMenu("fcverw_".$row2['kid'], "Optionen");
                $popup2->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_kontinent&amp;kid=".$row2['kid']
                );
                $popup2->add_item(
                    "Wiederherstellen",
                    "index.php?module=config-fcverw&amp;action=re_kontinent&amp;kid=".$row2['kid']
                );
                $form_container->output_cell($popup2->fetch(), array("class" => "align_center"));
        
                $form_container->construct_row(); // Reihe erstellen
            }
            $form_container->end();
            
            $form->end();
            
        } // Ende der Kontinentübersicht
        
        
        

/* *******************************************************************************************************************************************************************
       a2. Neuen Kontinent erstellen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "add_kontinent")
        {
            // Wenn alle Pflichtangaben abgeschickt wurden, dann eintragen
            if ($mybb->request_method == 'post' && $mybb->input['kname'] != '')
            {
                $insert_query = array(
                    'kname' => htmlspecialchars_uni($mybb->input['kname']),
                    'kbeschr' => htmlspecialchars_uni($mybb->input['kbeschr'])
                );

                if ($db->insert_query("laender_kontinente", $insert_query))
                {
                    redirect("admin/index.php?module=config-fcverw&action=kontinente");
                }

            }
            else
            {
                // Wenn Kontinentname leer, dann Fehldermeldung generieren!
                if ((!$mybb->input['kname'] || $mybb->input['kname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der Kontinentname muss ausgef&uuml;llt sein!</font></b>";
                }

                $page->add_breadcrumb_item('Kontinent anlegen');
                $page->output_header('L&auml;nderverwaltung - Kontinent anlegen');

                // which tab is selected? hier: add_kontinent
                $page->output_nav_tabs($sub_tabs, 'add_kontinent');

                // Neues Formular erstellen
                $form = new Form("index.php?module=config-fcverw&amp;action=add_kontinent", "post", "", 1);
                $form_container = new FormContainer('Neuen Kontinent anlegen');

                // der name
                $form_container->output_row(
                    'Name des Kontinents'.$l_fehler,
                    'Vollst&auml;ndiger Name des Kontinents',
                    $form->generate_text_box(
                        'kname',
                        htmlspecialchars_uni($mybb->input['kname']),
                        array('style' => 'width: 200px;')
                    )
                );

                // Informationstext
                $form_container->output_row(
                    'Beschreibung',
                    'Gibt es interessante Informationen &uuml;ber den Kontinent?',
                    $form->generate_text_area(
                        'kbeschr',
                        $db->escape_string($mybb->input['kbeschr'])
                    )
                );


                $form_container->end();
                $button[] = $form->generate_submit_button('Kontinent anlegen');
                $form->output_submit_wrapper($button);
                $form->end();
            }   
        }




/* *******************************************************************************************************************************************************************
       a3. Kontinent editieren
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "edit_kontinent")
        {
            // Eintrag machen
            if ($mybb->request_method == 'post' && $mybb->input['kname'] != '')
            {
                if ($mybb->input['kdelete'] == '1')
                {
                    // Länder kopieren und löschen
                    $landselect = $db->simple_select("laender", "*", "lkid = ".$mybb->input['kid'], array("order_by" => 'landid'));
                    while ($landdata = $db->fetch_array($landselect))
                    {
                        $insert = array(
                            "landid" => $landdata['landid'],
                            "lkid" => $landdata['lkid'],
                            "lrid" => $landdata['lrid'],
                            "lname" => $landdata['lname'],
                            "lkuerzel" => $landdata['lkuerzel'],
                            "lart" => $landdata['lart'],
                            "lreal" => $landdata['lreal'],
                            "lbesp" => $landdata['lbesp'],
                            "lstat" => $landdata['lstat'],
                            "lparent" => $landdata['lparent'],
                            "lverantw" => $landdata['lverantw']
                        );
                        
                        // Prüfen, ob das Land mit der LandID bereits vorhanden ist
                        $probe = $db->simple_select("laender_archive", "*", "landid = ".$landdata['landid']);
                        // Wenn Ergebnis = 0, dann eintragen - ansonsten nicht.
                        if ($db->num_rows($probe) == '0')
                        {
                            $db->insert_query("laender_archive", $insert);
                        }
                        
                        // Hier in jedem Fall alle löschen.
                        $db->delete_query("laender", "landid = ".$landdata['landid']);
                    }
                    
        
                    // Regionen updaten
                    $update_regionen = array(
                        'rdelete' => "1"
                    );
                    $db->update_query("laender_regionen", $update_regionen, "rkid = ".(int)$mybb->input['kid']);
                }
                
                $update_query = array(
                    'kname' => htmlspecialchars_uni($mybb->input['kname']),
                    'kbeschr' => htmlspecialchars_uni($mybb->input['kbeschr']),
                    'kdelete' => (int)$mybb->input['kdelete']
                );

                if ($db->update_query("laender_kontinente", $update_query, "kid = ".(int)$mybb->input['kid']))
                {
                    redirect("admin/index.php?module=config-fcverw&action=kontinente");
                }

            }
            else
            {
                // Formular anzeigen
                // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
                $sub_tabs['edit_kontinent'] = array(
                    'title' => 'Kontinent editieren',
                    'link' => 'index.php?module=config-fcverw&amp;action=edit_kontinent&amp;kid='.$mybb->input['kid'],
                    'description' => 'Editieren eines bestehenden Kontinents'
                );

                $page->add_breadcrumb_item('Kontinent editieren');
                $page->output_header('L&auml;nderverwaltung - Kontinent editieren');

                // which tab is selected? here: edit_kontinent - der ist NICHT permanent!
                $page->output_nav_tabs($sub_tabs, 'edit_kontinent');

                $form = new Form("index.php?module=config-fcverw&amp;action=edit_kontinent", "post", "", 1);
                $form_container = new FormContainer('Kontinent editieren');
                
                // ID mitgeben über verstecktes Feld
                echo $form->generate_hidden_field('kid', $mybb->input['kid']);

                // Daten holen
                $dataget = $db->simple_select("laender_kontinente", "*", "kid = ".$mybb->input['kid']);
                $data = $db->fetch_array($dataget);

                // Fehlermeldung ausgeben, wenn Name nicht ausgefüllt
                if ((!$mybb->input['kname'] || $mybb->input['kname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der Kontinentname muss ausgef&uuml;llt sein!</font></b>";
                    // Daten überschreiben
                    $data['kname'] = $mybb->input['kname'];
                    $data['kbeschr'] = $mybb->input['kbeschr'];
                }

                // der name
                $form_container->output_row(
                    'Name des Kontinents'.$l_fehler,
                    'Vollst&auml;ndiger Name des Kontinents',
                    $form->generate_text_box(
                        'kname',
                        htmlspecialchars_uni($data['kname']),
                        array('style' => 'width: 200px;')
                    )
                );

                // Informationstext
                $form_container->output_row(
                    'Beschreibung',
                    'Gibt es interessante Informationen &uuml;ber den Kontinent?',
                    $form->generate_text_area(
                        'kbeschr',
                        $db->escape_string($data['kbeschr'])
                    )
                );
                
                
                if ($data['kdelete'] == '1')
                {
                    $kdelete[0] = "Wiederherstellen";
                    $kdelete[1] = "Archivierung beibehalten";
                    
                    if ($mybb->input['kdelete'] == '')
                    {
                        $mybb->input['kdelete'] = $data['kdelete'];
                    }
                    
                    // Gelöschtes Land wiederherstellen?
                    $form_container->output_row(
                        'Wiederherstellen',
                        'Soll der archivierte Kontinent wiederhergestellt werden?',
                        $form->generate_select_box(
                            'kdelete',
                            $kdelete,
                            $mybb->input['kdelete'], 
                            array('style' => 'width: 200px;')
                        )
                    ); 
                }
                else 
                {
                    $kdelete[0] = "Nein";
                    $kdelete[1] = "Ja";
                    
                    if ($mybb->input['kdelete'] == '')
                    {
                        $mybb->input['kdelete'] = $data['kdelete'];
                    }
                    
                    // Gelöschtes Land wiederherstellen?
                    $form_container->output_row(
                        'Archivieren?',
                        'Soll der Kontinent archiviert werden?',
                        $form->generate_select_box(
                            'kdelete',
                            $kdelete,
                            $mybb->input['kdelete'], 
                            array('style' => 'width: 200px;')
                        )
                    ); 
                }

                $form_container->end();
                $button[] = $form->generate_submit_button('Kontinent editieren');
                $form->output_submit_wrapper($button);
                $form->end();
            }
        } // Ende Editieren Kontinent



/* *******************************************************************************************************************************************************************
       a4. Kontinent archivieren
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "del_kontinent")
        {
            $kid = (int)$mybb->input['kid'];

            // Länder updaten
            // Länder kopieren und löschen
            $landselect = $db->simple_select("laender", "*", "lkid = ".$mybb->input['kid'], array("order_by" => 'landid'));
            while ($landdata = $db->fetch_array($landselect))
            {
                $insert = array(
                    "landid" => $landdata['landid'],
                    "lkid" => $landdata['lkid'],
                    "lrid" => $landdata['lrid'],
                    "lname" => $landdata['lname'],
                    "lkuerzel" => $landdata['lkuerzel'],
                    "lart" => $landdata['lart'],
                    "lreal" => $landdata['lreal'],
                    "lbesp" => $landdata['lbesp'],
                    "lstat" => $landdata['lstat'],
                    "lparent" => $landdata['lparent'],
                    "lverantw" => $landdata['lverantw']
                );
                            
                // Prüfen, ob das Land mit der LandID bereits vorhanden ist
                $probe = $db->simple_select("laender_archive", "*", "landid = ".$landdata['landid']);
                // Wenn Ergebnis = 0, dann eintragen - ansonsten nicht.
                if ($db->num_rows($probe) == '0')
                {
                    $db->insert_query("laender_archive", $insert);
                }
                            
                // Hier in jedem Fall alle löschen.
                $db->delete_query("laender", "landid = ".$landdata['landid']);
            }

            // Regionen updaten
            $update_regionen = array(
                'rdelete' => "1"
            );
            $db->update_query("laender_regionen", $update_regionen, "rkid = ".$kid);


            // Eintrag abändern - Delete = 1
            $update = array(
                'kdelete' => '1'
            );
            
            if ($db->update_query("laender_kontinente", $update, "kid = ".$kid))
            {
                redirect("admin/index.php?module=config-fcverw&action=kontinente");
            }

        } // Ende Löschen Kontinent



/* *******************************************************************************************************************************************************************
       a5. Kontinent wiederherstellen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "re_kontinent")
        {
            $kid = (int)$mybb->input['kid'];

            // Eintrag abändern - Delete = 0
            $update = array(
                'kdelete' => '0'
            );
            
            if ($db->update_query("laender_kontinente", $update, "kid = ".$kid))
            {
                redirect("admin/index.php?module=config-fcverw&action=kontinente");
            }

        } // Ende Wiederherstellen Kontinent
