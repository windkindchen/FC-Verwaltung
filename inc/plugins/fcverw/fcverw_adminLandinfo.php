<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }


/* *******************************************************************************************************************************************************************
       Inhalt Dokument b. Regionen
******************************************************************************************************************************************************************* */

    // 1. regionen - Anzeige aller Regionen
    // 2. add_region - Neue Region anlegen
    // 3. edit_region - Region editieren
    // 4. del_region - Region archivieren
    // 5. re_region - Region wiederherstellen



/* *******************************************************************************************************************************************************************
       b1. Regionen anzeigen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "regionen")
        {
            $page->add_breadcrumb_item('&Uuml;bersicht aller Regionen');
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht aller Regionen');

            // Welches Tab ist ausgewählt?
            $page->output_nav_tabs($sub_tabs, 'regionen');

            // Tabelle kreieren - Headerzeile
            $form = new Form("index.php?module=config-fcverw", "post");
            $form_container = new FormContainer('Alle aktiven Regionen (in aktiven Kontinenten)');
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Regionenname', array("class" => "align_center", "width" => "25%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('L&auml;nder<br>aktiv (archiviert)', array("class" => "align_center", "width" => "10%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));

            // Hier werden die aktiven Regionen ausgelesen
            $fc_regsel = fcverw_KonReg(0, 0);
            while ($row = $db->fetch_array($fc_regsel))
            {
                // Auslesen der Anzahl der Länder
                $laendera = $db->num_rows($db->simple_select("laender", "landid", "ldelete = '0' AND lrid = ".$row['rid']));
                $laenderb = $db->num_rows($db->simple_select("laender_archive", "landid", "ldelete = '1' AND lrid = ".$row['rid']));

                $form_container->output_cell($row['rid'], array("class" => "align_center"));
                $form_container->output_cell("[".$row['kname']."] <b>".$row['rname']."</b>");
                $form_container->output_cell($row['rbeschr']);
                $form_container->output_cell($laendera." (".$laenderb.")", array("class" => "align_center"));

                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup = new PopupMenu("fcverw_".$row['rid'], "Optionen");
                $popup->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_region&amp;rid=".$row['rid']
                );
                $popup->add_item(
                    "Archivieren",
                    "index.php?module=config-fcverw&amp;action=del_region&amp;rid=".$row['rid']
                );
                $form_container->output_cell($popup->fetch(), array("class" => "align_center"));

                $form_container->construct_row(); // Reihe erstellen
            }
            $form_container->end();
            
            
            $form_container = new FormContainer('Alle archivierten Regionen (in aktiven Kontinenten)');
            $form_container->output_row_header('ehem. ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Regionenname', array("class" => "align_center", "width" => "25%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('L&auml;nder<br>aktiv (archiviert)', array("class" => "align_center", "width" => "10%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));

            // Hier werden die aktiven Regionen ausgelesen
            $fc_regsel2 = fcverw_KonReg(0, 1);
            while ($row2 = $db->fetch_array($fc_regsel2))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $laendera2 = $db->num_rows($db->simple_select("laender", "landid", "lrid = ".$row2['rid']));
                $laenderb2 = $db->num_rows($db->simple_select("laender_archive", "landid", "lrid = ".$row2['rid']));

                $form_container->output_cell($row2['rid'], array("class" => "align_center"));
                $form_container->output_cell("[".$row2['kname']."] <b>".$row2['rname']."</b>");
                $form_container->output_cell($row2['rbeschr']);
                $form_container->output_cell($laendera2." (".$laenderb2.")", array("class" => "align_center"));

                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup2 = new PopupMenu("fcverw_".$row2['rid'], "Optionen");
                $popup2->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_region&amp;rid=".$row2['rid']
                );
                $popup2->add_item(
                    "Wiederherstellen",
                    "index.php?module=config-fcverw&amp;action=re_region&amp;rid=".$row2['rid']
                );
                $form_container->output_cell($popup2->fetch(), array("class" => "align_center"));

                $form_container->construct_row(); // Reihe erstellen
            }
            $form_container->end();
            
            
            $form_container = new FormContainer('Alle Regionen (in archivierten Kontinenten)');
            $form_container->output_row_header('ehem. ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Regionenname', array("class" => "align_center", "width" => "25%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('L&auml;nder<br>aktiv (archiviert)', array("class" => "align_center", "width" => "10%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));


            // Hier werden die Regionen in gelöschten Kontinenten ausgelesen
            $fc_regsel3 = fcverw_KonReg(1, 2);
            while ($row3 = $db->fetch_array($fc_regsel3))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $laendera3 = $db->num_rows($db->simple_select("laender", "landid", "lrid = ".$row3['rid']));
                $laenderb3 = $db->num_rows($db->simple_select("laender_archive", "landid", "lrid = ".$row3['rid']));

                $form_container->output_cell($row3['rid'], array("class" => "align_center"));
                $form_container->output_cell("[".$row3['kname']."] <b>".$row3['rname']."</b>");
                $form_container->output_cell($row3['rbeschr']);
                $form_container->output_cell($laendera3." (".$laenderb3.")", array("class" => "align_center"));

                // Optionen-Fach basteln
                $form_container->output_cell("Bitte erst den Kontinent aktivieren!", array("class" => "align_center"));

                $form_container->construct_row(); // Reihe erstellen
            }
            $form_container->end();
            
            
            
            $form->end();
        } // Ende der Regionenübersicht




/* *******************************************************************************************************************************************************************
       b2. Neue Region anlegen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "add_region")
        {
            // Wenn alle Pflichtangaben abgeschickt wurden, dann eintragen
            if ($mybb->request_method == 'post' && $mybb->input['rname'] != '' && $mybb->input['rkid']!= '0')
            {
                $insert_query = array(
                    'rkid' => (int)$mybb->input['rkid'],
                    'rname' => htmlspecialchars_uni($mybb->input['rname']),
                    'rbeschr' => htmlspecialchars_uni($mybb->input['rbeschr'])
                );

                if ($db->insert_query("laender_regionen", $insert_query))
                {
                    redirect("admin/index.php?module=config-fcverw&action=regionen");
                }

            }
            else
            {
                // Wenn Regionenname leer, dann Fehldermeldung generieren!
                if ((!$mybb->input['rname'] || $mybb->input['rname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der Regionenname muss ausgef&uuml;llt sein!</font></b>";
                }
                // Wenn Regionenkontinent leer, dann Fehlermeldung generieren!
                if ((!$mybb->input['rkid'] || $mybb->input['rkid'] == '' || $mybb->input['rkid'] == '0') && $mybb->request_method == 'post')
                {
                    $k_fehler = " <b><font color='#ff000'>Es muss ein Kontinent zugeordnet werden!</font></b>";
                }

                $page->add_breadcrumb_item('Region anlegen');
                $page->output_header('L&auml;nderverwaltung - Region anlegen');

                // which tab is selected? hier: add_region
                $page->output_nav_tabs($sub_tabs, 'add_region');

                // Neues Formular erstellen
                $form = new Form("index.php?module=config-fcverw&amp;action=add_region", "post", "", 1);
                $form_container = new FormContainer('Neue Region anlegen');

                // der name
                $form_container->output_row(
                    'Name der Region'.$l_fehler,
                    'Vollst&auml;ndiger Name der Region',
                    $form->generate_text_box(
                        'rname',
                        htmlspecialchars_uni($mybb->input['rname']),
                        array('style' => 'width: 200px;')
                    )
                );
                
                // der zugeordnete Kontinent
                // Kontinente auslesen
                $kontsel = $db->simple_select("laender_kontinente", "*");
                $kontinente = array();
                $kontinente[0] = "Bitte w&auml;hlen!";
                
                while ($kontdata = $db->fetch_array($kontsel))
                {
                    $kontinente[$kontdata['kid']] = htmlspecialchars_uni($kontdata['kname']);
                }
                
                $form_container->output_row(
                    'Kontinent'.$k_fehler,
                    'Zu welchem Kontinent geh&ouml;rt die Region?',
                    $form->generate_select_box(
                        'rkid',
                        $kontinente,
                        $mybb->input['rkid'], 
                        array('style' => 'width: 200px;')
                    )
                );

                // Informationstext
                $form_container->output_row(
                    'Beschreibung',
                    'Gibt es interessante Informationen &uuml;ber die Region?',
                    $form->generate_text_area(
                        'rbeschr',
                        $db->escape_string($mybb->input['rbeschr'])
                    )
                );

                $form_container->end();
                $button[] = $form->generate_submit_button('Region anlegen');
                $form->output_submit_wrapper($button);
                $form->end();
            }   
        } // Ende Region anlegen




/* *******************************************************************************************************************************************************************
       b3. Region bearbeiten
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "edit_region")
        {
            // Eintrag machen
            if ($mybb->request_method == 'post' && $mybb->input['rname'] != '')
            {
                // Länder updaten
                if ($mybb->input['rdelete'] == '1')
                {
                    // Länder updaten
                    // Länder kopieren und löschen
                    $landselect = $db->simple_select("laender", "*", "lrid = ".$mybb->input['rid'], array("order_by" => 'landid'));
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
        
                }
                
                $update_query = array(
                    'rkid' => (int)$mybb->input['rkid'],
                    'rname' => htmlspecialchars_uni($mybb->input['rname']),
                    'rbeschr' => htmlspecialchars_uni($mybb->input['rbeschr']),
                    'rdelete' => (int)$mybb->input['rdelete']
                );

                if ($db->update_query("laender_regionen", $update_query, "rid = ".(int)$mybb->input['rid']))
                {
                    redirect("admin/index.php?module=config-fcverw&action=regionen");
                }

            }
            else
            {
                // Formular anzeigen
                // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
                $sub_tabs['edit_region'] = array(
                    'title' => 'Region editieren',
                    'link' => 'index.php?module=config-fcverw&amp;action=edit_region&amp;rid='.$mybb->input['rid'],
                    'description' => 'Editieren einer bestehenden Region'
                );

                $page->add_breadcrumb_item('Region editieren');
                $page->output_header('L&auml;nderverwaltung - Region editieren');

                // which tab is selected? here: edit_region - der ist NICHT permanent!
                $page->output_nav_tabs($sub_tabs, 'edit_region');

                $form = new Form("index.php?module=config-fcverw&amp;action=edit_region", "post", "", 1);
                $form_container = new FormContainer('Region editieren');
                
                // ID mitgeben über verstecktes Feld
                echo $form->generate_hidden_field('rid', $mybb->input['rid']);

                // Daten holen
                $dataget = $db->simple_select("laender_regionen", "*", "rid = ".$mybb->input['rid']);
                $data = $db->fetch_array($dataget);

                // Fehlermeldung ausgeben, wenn Name nicht ausgefüllt
                if ((!$mybb->input['rname'] || $mybb->input['rname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der Regionenname muss ausgef&uuml;llt sein!</font></b>";
                    // Daten überschreiben
                    $data['rname'] = $mybb->input['rname'];
                    $data['rbeschr'] = $mybb->input['rbeschr'];
                    $data['rkid'] = $mybb->input['rkid'];
                }
                // Fehlermeldung, wenn Kontinent nicht augefüllt
                if ((!$mybb->input['rkid'] || $mybb->input['rkid'] == '' || $mybb->input['rkid'] == '0') && $mybb->request_method == 'post')
                {
                    $k_fehler = " <b><font color='#ff0000'>Es muss ein Kontinent ausgew&auml;hlt werden!</font></b>";
                    // Daten überschreiben
                    $data['rname'] = $mybb->input['rname'];
                    $data['rbeschr'] = $mybb->input['rbeschr'];
                    $data['rkid'] = $mybb->input['rkid'];
                }


                // der name
                $form_container->output_row(
                    'Name der Region'.$l_fehler,
                    'Vollst&auml;ndiger Name der Region',
                    $form->generate_text_box(
                        'rname',
                        htmlspecialchars_uni($data['rname']),
                        array('style' => 'width: 200px;')
                    )
                );

                // der zugeordnete Kontinent
                // Kontinente auslesen
                $kontsel = $db->simple_select("laender_kontinente", "*");
                $kontinente = array();
                $kontinente[0] = "Bitte w&auml;hlen!";
                
                while ($kontdata = $db->fetch_array($kontsel))
                {
                    $kontinente[$kontdata['kid']] = htmlspecialchars_uni($kontdata['kname']);
                }
                
                $form_container->output_row(
                    'Kontinent'.$k_fehler,
                    'Zu welchem Kontinent geh&ouml;rt die Region?',
                    $form->generate_select_box(
                        'rkid',
                        $kontinente,
                        $data['rkid'], 
                        array('style' => 'width: 200px;')
                    )
                );

                // Informationstext
                $form_container->output_row(
                    'Beschreibung',
                    'Gibt es interessante Informationen &uuml;ber die Region?',
                    $form->generate_text_area(
                        'rbeschr',
                        $db->escape_string($data['rbeschr'])
                    )
                );
                
                if ($data['rdelete'] == '1')
                {
                    $rdelete[0] = "Wiederherstellen";
                    $rdelete[1] = "Archivierung beibehalten";
                    
                    if ($mybb->input['rdelete'] == '')
                    {
                        $mybb->input['rdelete'] = $data['rdelete'];
                    }
                    
                    // Gelöschtes Region wiederherstellen?
                    $form_container->output_row(
                        'Wiederherstellen',
                        'Soll die archivierte Region wiederhergestellt werden?',
                        $form->generate_select_box(
                            'rdelete',
                            $rdelete,
                            $mybb->input['rdelete'], 
                            array('style' => 'width: 200px;')
                        )
                    ); 
                }
                else 
                {
                    $rdelete[0] = "Nein";
                    $rdelete[1] = "Ja";
                    
                    if ($mybb->input['rdelete'] == '')
                    {
                        $mybb->input['rdelete'] = $data['rdelete'];
                    }
                    
                    // Gelöschtes Region wiederherstellen?
                    $form_container->output_row(
                        'Archivieren?',
                        'Soll die Region archiviert werden?',
                        $form->generate_select_box(
                            'rdelete',
                            $rdelete,
                            $mybb->input['rdelete'], 
                            array('style' => 'width: 200px;')
                        )
                    ); 
                }
                

                $form_container->end();
                $button[] = $form->generate_submit_button('Region editieren');
                $form->output_submit_wrapper($button);
                $form->end();
            }
        } // Ende Editieren Region




/* *******************************************************************************************************************************************************************
       b4. Region archivieren
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "del_region")
        {
            $rid = (int)$mybb->input['rid'];

            // Länder updaten
            // Länder kopieren und löschen
            $landselect = $db->simple_select("laender", "*", "lrid = ".$mybb->input['rid'], array("order_by" => 'landid'));
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

            // Eintrag abändern - Delete = 1
            $update = array(
                'rdelete' => '1'
            );
            
            if ($db->update_query("laender_regionen", $update, "rid = ".$rid))
            {
                redirect("admin/index.php?module=config-fcverw&action=regionen");
            }
            
        } // Ende Löschen Region



/* *******************************************************************************************************************************************************************
       b5. Region wiederherstellen
******************************************************************************************************************************************************************* */

        if ($mybb->input['action'] == "re_region")
        {
            $rid = (int)$mybb->input['rid'];

            // Eintrag abändern - Delete = 0
            $update = array(
                'rdelete' => '0'
            );

            // Eintrag wiederherstellen
            if ($db->update_query("laender_regionen", $update, "rid = ".$rid)) 
            {
                redirect("admin/index.php?module=config-fcverw&action=regionen");
            }
            
           

        } // Ende Wiederherstellen Region
