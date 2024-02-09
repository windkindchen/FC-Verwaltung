<?php

// Disallow direct access to this file for security reasons
if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.<br /><br />
        Please make sure IN_MYBB is defined.');
}


// c1. Alle Länder anzeigen

        if ($mybb->input['action'] == "" || !$mybb->input['action'] || $mybb->input['action'] == 'laender')
        {
            $page->add_breadcrumb_item('&Uuml;bersicht aller L&auml;nder');
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht aller L&auml;nder');

            // which tab is selected?
            $page->output_nav_tabs($sub_tabs, 'laender');

            // Grundgerüst
            $form = new Form("index.php?module=config-fcverw", "post");
            
            // Zunächst Kontinente und Regionen auslesen - um dann die jeweiligen Länder und Unterländer zu bekommen.
            // Aktive Kontinente und Regionen
            $select_query = fcverw_KonReg(0, 0);
            
            // Aktive Länder
            $form_container = new FormContainer('Alle aktiven L&auml;nder (aktive Region, aktiver Kontinent)');
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Landname', array("class" => "align_center", "colspan" => "2"));
            $form_container->output_row_header('Allgemeines', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('L&auml;nderinfos', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Diplomatie', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Verwandtschaften', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Bewohner', array("class" => "align_center", "width" => "13%"));
            
            while ($data = $db->fetch_array($select_query))
            {
                // Prüfen, ob überhaupt Ausgabe erforderlich
                $lands = $db->simple_select("laender", "*", "lrid = ".$data['rid']." AND lstat = '0'");
                
                if ($db->num_rows($lands) > '0')
                {
                    $form_container->output_cell($data['kname'].' &raquo; <b>'.$data['rname'].'</b>', array("colspan" => "8"));
                    $form_container->construct_row(); // Reihe erstellen
                    
                    // Funktion der Länderauflistung aufrufen und nutzen
                    $query = fcverw_LandList($data['rid'], 0, 0);
                    
                    while ($landdata = $db->fetch_array($query))
                    {
                        $trenner = str_repeat("-", $landdata['Ebene']);
                        
                        $image = "";
                        $hinweis = "";
                        
                        // Prüfen, ob Land bespielt
                        if ($landdata['lbesp'] == '1')
                        {
                            // Prüfen, ob Spieler noch da
                            $use = $db->simple_select("users", "username", "uid = ".$landdata['lverantw']);
                            $count = $db->num_rows($use);
                            $userl = $db->fetch_field($use, "username");
                            
                            $userlink = build_profile_link($userl, $landdata['lverantw']);
                            
                            
                            if ($count == '1')
                            {
                                $image = '<a href="index.php?module=config-fcverw&action=free_land&landid='.$landdata['landid'].'"><img src="fcverw/vergeben.png" /></a>';
                                $hinweis = "<br /><i>L&auml;nderverantwortung:</i> ".$userlink;
                            } 
                            else
                            {
                                $image = '<a href="index.php?module=config-fcverw&action=edit_land&landid='.$landdata['landid'].'"><img src="fcverw/error.png" /></a>';
                            } 
                        }
                        else 
                        {
                            $image = '<a href="index.php?module=config-fcverw&action=take_land&landid='.$landdata['landid'].'"><img src="fcverw/frei.png" /></a>';
                        }
                        
                        
                        
                        if ($landdata['ldelete'] == '1')
                        {
                            $landdata['lname'] = "<span style=\"opacity: .5;\">".$landdata['lname']."</span>";
                            $hinweis = "(archiviert; hier nur für &Uuml;bersicht)";
                            $image = "-";
                        }
                        
                        $form_container->output_cell($landdata['landid'], array("class" => "align_center"));
                        $form_container->output_cell($image, array("width" => "1%"));
                        $form_container->output_cell($trenner." ".$landdata['lname']." (".$landdata['lart'].") ".$hinweis);
                        
                        
                        // Childs auslesen
                        $childi = $db->num_rows($db->simple_select("laender", "*", "lparent = ".$landdata['landid']));
                        
                        // Optionen-Fach basteln
                        //erst pop up dafür bauen - danke an @Risuena
                        // ALLGEMEINES
                        if ($landdata['ldelete'] == '0')
                        {
                            $popup = new PopupMenu("fcverw_".$landdata['landid'], "Optionen");
                            $popup->add_item(
                                "editieren",
                                "index.php?module=config-fcverw&amp;action=edit_land&amp;landid=".$landdata['landid']
                            );
                            $popup->add_item(
                                "archivieren",
                                "index.php?module=config-fcverw&amp;action=del_land&amp;landid=".$landdata['landid']."&amp;path=".$landdata['PathID']
                            );
                            $form_container->output_cell($popup->fetch(), array("class" => "align_center"));
            
            
                            // Länderinfos
                            $popup4 = new PopupMenu("fcverw4_".$landdata['landid'], "Optionen");
                            // Differenzieren, ob es Länderinfos gibt oder nicht - und ob es noch nicht freigegebene gibt.
                            // Gibt es allgemein Länderinfos + aktuellste Auslesen
                            $landinfosel = $db->simple_select("laender_info", "linfoid, lidatum, lifreigabe", "landid = ".$landdata['landid'], array("order_by" => "lidatum DESC"));
                            $landinfo = $db->fetch_array($landinfosel);
                            
                            // Wenn keine Info da ist ...
                            if ($db->num_rows($landinfosel) == "0")
                            {
                                $popup4->add_item(
                                    "anlegen",
                                    "index.php?module=config-fcverw&amp;action=add_landinfo&amp;landid=".$landdata['landid']
                                );
                            }
                            // Wenn eine Info da ist und diese noch nicht freigegeben wurde
                            elseif ($db->num_rows($landinfosel) == "1" && $landinfo['lifreigabe'] == "0")
                            {
                                $popup4->add_item(
                                    "anzeigen",
                                    "index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "editieren",
                                    "index.php?module=config-fcverw&amp;action=edit_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "freigeben",
                                    "index.php?module=config-fcverw&amp;action=free_landinfo&amp;linfoid=".$landinfo['linfoid']
                                );
                            }
                            elseif ($db->num_rows($landinfosel) == "1" && $landinfo['lifreigabe'] == "1")
                            {
                                $popup4->add_item(
                                    "anzeigen",
                                    "index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "editieren",
                                    "index.php?module=config-fcverw&amp;action=edit_landinfo&amp;landid=".$landdata['landid']
                                );
                            }
                            elseif ($db->num_rows($landinfosel) > "1" && $landinfo['lifreigabe'] == '0')
                            {
                                $popup4->add_item(
                                    "aktuelle anzeigen",
                                    "index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "mit vorheriger vergleichen",
                                    "index.php?module=config-fcverw&amp;action=vergl_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "editieren",
                                    "index.php?module=config-fcverw&amp;action=edit_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "freigeben",
                                    "index.php?module=config-fcverw&amp;action=free_landinfo&amp;linfoid=".$landinfo['linfoid']
                                );
                            }
                            elseif ($db->num_rows($landinfosel) > "1" && $landinfo['lifreigabe'] == '1')
                            {
                                $popup4->add_item(
                                    "aktuelle anzeigen",
                                    "index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "mit vorheriger vergleichen",
                                    "index.php?module=config-fcverw&amp;action=vergl_landinfo&amp;landid=".$landdata['landid']
                                );
                                $popup4->add_item(
                                    "editieren",
                                    "index.php?module=config-fcverw&amp;action=edit_landinfo&amp;landid=".$landdata['landid']
                                );
                            }
                            $form_container->output_cell($popup4->fetch(), array("class" => "align_center"));
                            
                            
                            
                            // Diplomatie
                            $popup5 = new PopupMenu("fcverw5_".$landdata['landid'], "Optionen");
                            $popup5->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_diplo&amp;landid=".$landdata['landid']
                            );
                            $popup5->add_item(
                                "bearbeiten",
                                "index.php?module=config-fcverw&amp;action=edit_diplo&amp;landid=".$landdata['landid']
                            );
                            $popup5->add_item(
                                "l&ouml;schen",
                                "index.php?module=config-fcverw&amp;action=del_diplo&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup5->fetch(), array("class" => "align_center"));
                            
                            // Verwandtschaften
                            $popup6 = new PopupMenu("fcverw6_".$landdata['landid'], "Optionen");
                            $popup6->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_verwandt&amp;landid=".$landdata['landid']
                            );
                            $popup6->add_item(
                                "bearbeiten",
                                "index.php?module=config-fcverw&amp;action=edit_verwandt&amp;landid=".$landdata['landid']
                            );
                            $popup6->add_item(
                                "l&ouml;schen",
                                "index.php?module=config-fcverw&amp;action=del_verwandt&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup6->fetch(), array("class" => "align_center"));
                            
                            // Bewohner
                            $popup7 = new PopupMenu("fcverw7_".$landdata['landid'], "Optionen");
                            $popup7->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_chars&amp;landid=".$landdata['landid']
                            );
                            $popup7->add_item(
                                "bearbeiten",
                                "index.php?module=config-fcverw&amp;action=edit_chars&amp;landid=".$landdata['landid']
                            );
                            $popup7->add_item(
                                "l&ouml;schen",
                                "index.php?module=config-fcverw&amp;action=del_chars&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup7->fetch(), array("class" => "align_center"));
                        }
                        elseif ($landdata['ldelete'] == '1' AND $childi == '0')
                        {
                            $popup11 = new PopupMenu("fcverw11_".$landdata['landid'], "Optionen");
                            $popup11->add_item(
                                "vollständig archivieren",
                                "index.php?module=config-fcverw&amp;action=del_land&amp;landid=".$landdata['landid']."&amp;path=".$landdata['PathID']
                            );
                            $form_container->output_cell($popup11->fetch(), array("class" => "align_center"));
                            
                            $form_container->output_cell("keine weiteren Optionen verf&uuml;gbar", array("class" => "align_center", "colspan" => "4"));
                        }
                        else 
                        {
                            $form_container->output_cell("keine Optionen verf&uuml;gbar", array("class" => "align_center", "colspan" => "5"));
                        }
                        
                        $form_container->construct_row(); // Reihe erstellen
                    }
                    
                }
            } 
            $form_container->end();
            
            
            
            
            // Nächstes: Archiviertes Land, Aktive Region, Aktiver Kontinent
            $form_container = new FormContainer('Alle archivierten L&auml;nder (aktive Region, aktiver Kontinent)');
            
            $form_container->output_row_header('ehem. ID', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Landname', array("class" => "align_center"));
            $form_container->output_row_header('Allgemeines', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('L&auml;nderinfos', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Diplomatie', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Verwandtschaften', array("class" => "align_center", "width" => "13%"));
            $form_container->output_row_header('Bewohner', array("class" => "align_center", "width" => "13%"));
            
            $select_query2 = fcverw_KonReg(0, 0);
            
            while ($data2 = $db->fetch_array($select_query2))
            {
                // Prüfen, ob überhaupt Ausgabe erforderlich
                $landcount = $db->num_rows($db->simple_select("laender_archive", "*", "lrid = ".$data2['rid']));
                
                if ($landcount > '0')
                {
                    $form_container->output_cell($data2['kname'].' &raquo; <b>'.$data2['rname'].'</b>', array("colspan" => "8"));
                    $form_container->construct_row(); // Reihe erstellen

                    
                    // Funktion der Länderauflistung aufrufen und nutzen
                    $query2 = fcverw_LandList($data2['rid'], 1, 0);
                    
                    while ($landdata2 = $db->fetch_array($query2))
                    {
                        $trenner2 = str_repeat("-", $landdata2['Ebene']);
                        
                        $hinweis = "";
                        if ($landdata2['ldelete'] == '0')
                        {
                            $landdata2['lname'] = "<span style=\"opacity: .5;\">".$landdata2['lname']."</span>";
                            $hinweis = "(aktiv; hier nur für &Uuml;bersicht)";
                        }
                        
                        // Childs auslesen
                        $childi2 = $db->num_rows($db->simple_select("laender_archive", "*", "lparent = ".$landdata2['landid']));
                        
                        $form_container->output_cell($landdata2['landid'], array("class" => "align_center"));
                        $form_container->output_cell($trenner2." ".$landdata2['lname']." (".$landdata2['lart'].") ".$hinweis);
                        
                        if ($landdata2['ldelete'] == "1")
                        {
                            // Optionen-Fach basteln
                            //erst pop up dafür bauen - danke an @Risuena
                            $popup2 = new PopupMenu("fcverw2_".$landdata2['landid'], "Optionen");
                            $popup2->add_item(
                                "Wiederherstellen",
                                "index.php?module=config-fcverw&amp;action=re_land&amp;landid=".$landdata2['landid']."&amp;path=".$landdata2['PathID']
                            );
                            $form_container->output_cell($popup2->fetch(), array("class" => "align_center"));
            
                            // Länderinfos
                            $landinfo = $db->num_rows($db->simple_select("laender_info", "*", "landid = ".$landdata2['landid']));
                            if ($landinfo > 0)
                            {
                                $popup3 = new PopupMenu("fcverw3_".$landdata2['landid'], "Optionen");
                                $popup3->add_item(
                                    "Anzeigen",
                                    "index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid=".$landdata2['landid']
                                );
                                $form_container->output_cell($popup3->fetch(), array("class" => "align_center"));
                            }
                            else 
                            {
                                $form_container->output_cell("-", array("class" => "align_center"));
                            }
                            
                            
                            
                            // Diplomatie
                            $popup5 = new PopupMenu("fcverw5_".$landdata2['landid'], "Optionen");
                            $popup5->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_diplo&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup5->fetch(), array("class" => "align_center"));
                            
                            // Verwandtschaften
                            $popup6 = new PopupMenu("fcverw6_".$landdata2['landid'], "Optionen");
                            $popup6->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_verwandt&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup6->fetch(), array("class" => "align_center"));
                            
                            // Bewohner
                            $popup7 = new PopupMenu("fcverw7_".$landdata2['landid'], "Optionen");
                            $popup7->add_item(
                                "anzeigen",
                                "index.php?module=config-fcverw&amp;action=show_chars&amp;landid=".$landdata['landid']
                            );
                            $form_container->output_cell($popup7->fetch(), array("class" => "align_center"));
                        }
                        elseif ($landdata2['ldelete'] == '0' AND $childi2 == '0')
                        {
                            $popup13 = new PopupMenu("fcverw13_".$landdata2['landid'], "Optionen");
                            $popup13->add_item(
                                "vollständig wiederherstellen",
                                "index.php?module=config-fcverw&amp;action=re_land&amp;landid=".$landdata2['landid']."&amp;path=".$landdata2['PathID']
                            );
                            $form_container->output_cell($popup13->fetch(), array("class" => "align_center"));
                            
                            
                            $form_container->output_cell("keine weiteren Optionen verf&uuml;gbar", array("class" => "align_center", "colspan" => "4"));
                        }
                        else 
                        {
                            $form_container->output_cell("keine Optionen verf&uuml;gbar", array("class" => "align_center", "colspan" => "5"));
                        }
                        
                        
                        $form_container->construct_row(); // Reihe erstellen
                    }       
                } 
            } 
            $form_container->end();
            
            
            // Nächstes: Land, Inaktive Region, Kontinent
            $form_container = new FormContainer('Alle aktiven L&auml;nder (archivierte Region, aktiver Kontinent)');
            
            
            
            $form->end();
        } // Ende der Startseite



// c. Länder - Allgemein
// c2. Land anlegen
        if ($mybb->input['action'] == "add_land")
        {
            // Wenn alle Pflichtangaben abgeschickt wurden, dann eintragen - oder Fehler eingeben.
            if (
                $mybb->request_method == 'post' && 
                $mybb->input['lname'] != '' && 
                (($mybb->input['lrid']!= '0' && $mybb->input['lstat'] == '0') || ($mybb->input['lstat'] == '1' && $mybb->input['lparent'] != '0')))
            {
                // bei untergeordneten Ländern zur Sicherheit die richtige Region-ID raussuchen:
                if ($mybb->input['lstat'] == '1')
                {
                    $rsele = $db->simple_select("laender", "lrid", "landid = ".$mybb->input['lparent']);
                    $mybb->input['lrid'] = $db->fetch_field($rsele, 'lrid');
                }
                
                // Wähle die Kontinent-ID:
                $rsel = $db->simple_select("laender_regionen", "rkid", "rid = ".$mybb->input['lrid'], array('limit' => '1'));
                $lkid = $db->fetch_field($rsel, 'rkid');
            
            
                $insert_query = array(
                    'lkid' => (int)$lkid,
                    'lrid' => (int)$mybb->input['lrid'], 
                    'lname' => htmlspecialchars_uni($mybb->input['lname']),
                    'lkuerzel' => htmlspecialchars_uni($mybb->input['lkuerzel']),
                    'lart' => htmlspecialchars_uni($mybb->input['lart']),
                    'lreal' => htmlspecialchars_uni($mybb->input['lreal']),
                    'lbesp' => (int)$mybb->input['lbesp'],
                    'lstat' => (int)$mybb->input['lstat'],
                    'lparent' => (int)$mybb->input['lparent'],
                    'lverantw' => (int)$mybb->input['lverantw']
                ); 
                
                if ($db->insert_query("laender", $insert_query))
                {
                    redirect("admin/index.php?module=config-fcverw&action=laender");
                }
 
            }
            else
            {
                // Wenn Ländername leer, dann Fehldermeldung generieren!

                if ((!$mybb->input['lname'] || $mybb->input['lname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der L&auml;ndername muss ausgef&uuml;llt sein!</font></b>";
                }
                // Wenn untergeordnete Region = 0 und Länderregion leer, dann Fehlermeldung generieren!
                if (($mybb->input['lstat'] == '0' && $mybb->input['lrid'] == '0') && $mybb->request_method == 'post')
                {
                    $r_fehler = " <b><font color='#ff000'>Es muss eine Region zugeordnet werden!</font></b>";
                }
                
                // Wenn Untergeordnete Region = 1 und Übergeordnetes Land = 0, dann Fehlermeldung
                if (($mybb->input['lstat'] == '1' && ($mybb->input['lparent'] == '0' || $mybb->input['lparent'] == '')) && $mybb->request_method == 'post')
                {
                    $lu_fehler = " <b><font color='#ff000'>Es muss ein &uuml;bergeordnetes Land zugeordnet werden!</font></b>";
                }

                $page->add_breadcrumb_item('Land anlegen');
                $page->output_header('L&auml;nderverwaltung - Land anlegen');

                // which tab is selected? hier: add_region
                $page->output_nav_tabs($sub_tabs, 'add_land');

                // Neues Formular erstellen
                $form = new Form("index.php?module=config-fcverw&amp;action=add_land", "post", "", 1);
                $form_container = new FormContainer('Neues Land anlegen');

                // der name
                $form_container->output_row(
                    'Name des Landes'.$l_fehler,
                    'Vollst&auml;ndiger Name des Landes',
                    $form->generate_text_box(
                        'lname',
                        htmlspecialchars_uni($mybb->input['lname']),
                        array('style' => 'width: 200px;')
                    )
                );
                
                // Kürzel
                $form_container->output_row(
                    'K&uuml;rzel des Landes',
                    'Wie wird das Land abgek&uuml;rzt? Z.B. Russland - RUS. Bitte alle Buchstaben gro&szlig; schreiben.',
                    $form->generate_text_box(
                        'lkuerzel',
                        $db->escape_string($mybb->input['lkuerzel'])
                    )
                );
                
                // Art des Landes
                $form_container->output_row(
                    'Art des Landes',
                    'Handelt es sich z.B. um ein K&ouml;nigreich, ein Herzogtum oder eine Grafschaft?',
                    $form->generate_text_box(
                        'lart',
                        $db->escape_string($mybb->input['lart'])
                    )
                );
                
                // Untergeordnet?
                $lstats[0] = 'Nein, eigenst&auml;ndiges Land';
                $lstats[1] = 'Ja, untergeordnetes Land';
                $form_container->output_row(
                    'Untergeordnete Region?',
                    'Handelt es sich um einen Herrschaftsbereich, der einem anderen untergeordnet ist?',
                    $form->generate_select_box(
                        'lstat',
                        $lstats,
                        $mybb->input['lstat'],
                        array('id' => 'lstat', 'style' => 'width: 200px;')
                    ),
                    'lstat'
                );
                
                // die zugeordnete Region // Wenn Untergeordnet Nein
                // Regionen auslesen
                $regionen = array();
                $regionen[0] = "<b>Bitte Region w&auml;hlen!</b>";
                
                // Länder auslesen
                $lparent = array();
                $lparent[0] = "kein &uuml;bergeordnetes Land";
                
                
                // Funktion für das Auslesen der Kontinente und dazugehörigen Regionen
                $reg = fcverw_KonReg(0, 0);
                while ($regdata = $db->fetch_array($reg))
                {
                    //Regionen definieren für Variante 1 (nicht untergeordnet)
                    $regionen[$regdata['rid']] = "[".$regdata['kname']."] ".$regdata['rname'];
                    
                    // Angelegte Länder definieren für Variante 2 (untergeordnet)
                    // Aufruf Funktion für die Darstellung des Landes
                    $query = fcverw_LandList($regdata['rid'], 0, 0);
                    while ($landdata = $db->fetch_array($query))
                    {
                        $trenner = str_repeat("-", $landdata['Ebene']);
                        $lparent[$landdata['landid']] = "[".$regdata['kname']." - ".$regdata['rname']."] ".$trenner." ".$landdata['lname']." (".$landdata['lart'].")";
                    }
                }
                $form_container->output_row(
                    'Region'.$r_fehler,
                    'Zu welcher geopolitischen Region geh&ouml;rt das Land? Im Falle, dass real mehrere zutreffen, bitte die der gr&ouml;&szlig;ten Landmasse angeben!',
                    $form->generate_select_box(
                        'lrid',
                        $regionen,
                        $mybb->input['lrid'], 
                        array('style' => 'width: 200px;', 'id' => 'lrid')
                    ),
                    'lrid',
                    array(),
                    array('id' => 'row_lrid')
                );
 
                // Übergeordnetes Land // Wenn Untergeordnet Ja
                $form_container->output_row(
                    '&Uuml;bergeordnetes Land'.$lu_fehler,
                    'Welches Land ist &uuml;bergeordnet?',
                    $form->generate_select_box(
                        'lparent',
                        $lparent,
                        $mybb->input['lparent'], 
                        array('style' => 'width: 200px;', 'id' => 'lparent')
                    ),
                    'lparent',
                    array(),
                    array('id' => 'row_lparent')
                );

                
                // Reales Gebiet
                $form_container->output_row(
                    'Reales Gebiet',
                    'Welche realen Gebiete umfasst das fiktive Land?',
                    $form->generate_text_box(
                        'lreal',
                        $db->escape_string($mybb->input['lreal'])
                    )
                );
                
                // Bespielt?
                $bespielt = array();
                $bespielt[0] = "noch nicht bespielt";
                $bespielt[1] = "bereits bespielt";
                
                $form_container->output_row(
                    'Ist das Land bereits bespielt?',
                    'Gibt es bereits politisch relevante Charaktere?',
                    $form->generate_select_box(
                        'lbesp',
                        $bespielt,
                        $mybb->input['lbesp'], 
                        array('style' => 'width: 200px;', 'id' => 'lbesp')
                    )
                );
                
                // Länderverantwortlicher
                // User auslesen - warum gibt es keine Standardfunktion??
                $ugroups = "'2', '3', '4', '6', '8', '9', '10'";
                $userselect = fcverw_UserSelect($ugroups);
                $lverantw[0] = "kein L&auml;nderverantwortlicher";
                
                while ($lverantwortlich = $db->fetch_array($userselect))
                {
                    $lverantw[$lverantwortlich['uid']] = $lverantwortlich['username'];
                }
                
                $form_container->output_row(
                    'L&auml;nderverantwortlichkeit',
                    'Wer ist f&uuml;r das Land (insb. Verwaltung, Informationen) verantwortlich?',
                    $form->generate_select_box(
                        'lverantw',
                        $lverantw,
                        $mybb->input['lverantw'], 
                        array('style' => 'width: 200px;', 'id' => 'lverantw')
                    ),
                    'lverantw', 
                    array(), 
                    array('id' => 'row_lverantw')
                );


                $form_container->end();
                $button[] = $form->generate_submit_button('Land anlegen');
                $form->output_submit_wrapper($button);
                $form->end();
                
                echo '
                    <script type="text/javascript" src="./jscripts/peeker.js?ver=1821"></script>
                    <script type="text/javascript">
                        $(function() {
                            new Peeker($("#lstat"), $("#row_lrid"), /^0/, false);
                            new Peeker($("#lstat"), $("#row_lparent"), /^1/, false);
                        });
                    </script>
                ';
            }   
        } // Ende Land anlegen


// c. Länder - Allgemein
// c3. Land editieren
        if ($mybb->input['action'] == "edit_land")
        {   
            // Wenn alle Pflichtangaben abgeschickt wurden, dann eintragen - oder Fehler eingeben.
            if (
                $mybb->request_method == 'post' && 
                $mybb->input['lname'] != '' && 
                (($mybb->input['lrid']!= '0' && $mybb->input['lstat'] == '0') || ($mybb->input['lstat'] == '1' && $mybb->input['lparent'] != '0')))
            {
                // bei untergeordneten Ländern zur Sicherheit die richtige Region-ID raussuchen:
                if ($mybb->input['lstat'] == '1')
                {
                    $rsele = $db->simple_select("laender", "lrid", "landid = ".$mybb->input['lparent']);
                    $mybb->input['lrid'] = $db->fetch_field($rsele, 'lrid');
                }
                
                // Wähle die Kontinent-ID:
                $rsel = $db->simple_select("laender_regionen", "rkid", "rid = ".$mybb->input['lrid'], array('limit' => '1'));
                $lkid = $db->fetch_field($rsel, 'rkid');
            
                
                // Herausforderung: Änderung der Region auch bei Childs beachten.
                // Prüfen, ob überhaupt eine Änderung vorgenommen wurde. Wenn ja, dann ...
                // ALLE Optionen raussuchen, die passen
                // Ergebnisse in ein Array bringen
                // doppelte Einträge löschen
                // für alle Updaten
                $lako = $db->fetch_field($db->simple_select("laender", "lrid", "landid = ".$mybb->input['landid']), "lrid");
                
                if ($mybb->input['lstat'] == '0' AND $lako != $mybb->input['lrid'])
                {
                    $tryq = fcverw_LandList($lako, 0, $mybb->input['landid']);
                    
                    $laender = array();
                    while ($lands = $db->fetch_array($tryq))
                    {
                        $laender[] = $lands['landid'];
                    }
                    
                    array_unique($laender);
                    $count = count($laender);
                    
                    for ($i = 0; $i < $count; $i++)
                    {
                        $update = array(
                            "lkid" => $lkid,
                            "lrid" => $mybb->input['lrid']
                        );
                        $db->update_query("laender", $update, "landid = ".$laender[$i]);
                    }
                }
                
                
                $insert_query = array(
                    'lkid' => (int)$lkid,
                    'lrid' => (int)$mybb->input['lrid'], 
                    'lname' => htmlspecialchars_uni($mybb->input['lname']),
                    'lkuerzel' => htmlspecialchars_uni($mybb->input['lkuerzel']),
                    'lart' => htmlspecialchars_uni($mybb->input['lart']),
                    'lreal' => htmlspecialchars_uni($mybb->input['lreal']),
                    'lbesp' => (int)$mybb->input['lbesp'],
                    'lstat' => (int)$mybb->input['lstat'],
                    'lparent' => (int)$mybb->input['lparent'],
                    'lverantw' => (int)$mybb->input['lverantw']
                ); 
                
                if ($db->update_query("laender", $insert_query, "landid = ".$mybb->input['landid']))
                {
                    redirect("admin/index.php?module=config-fcverw&action=laender");
                }
 
            }
            else
            {
                // Wenn Ländername leer, dann Fehldermeldung generieren!
                if ((!$mybb->input['lname'] || $mybb->input['lname'] == '') && $mybb->request_method == 'post')
                {
                    $l_fehler = " <b><font color='#ff0000'>Der L&auml;ndername muss ausgef&uuml;llt sein!</font></b>";
                }
                // Wenn untergeordnete Region = 0 und Länderregion leer, dann Fehlermeldung generieren!
                if (($mybb->input['lstat'] == '0' && $mybb->input['lrid'] == '0') && $mybb->request_method == 'post')
                {
                    $r_fehler = " <b><font color='#ff000'>Es muss eine Region zugeordnet werden!</font></b>";
                }
                
                // Wenn Untergeordnete Region = 1 und Übergeordnetes Land = 0, dann Fehlermeldung
                if (($mybb->input['lstat'] == '1' && ($mybb->input['lparent'] == '0' || $mybb->input['lparent'] == '')) && $mybb->request_method == 'post')
                {
                    $lu_fehler = " <b><font color='#ff000'>Es muss ein &uuml;bergeordnetes Land zugeordnet werden!</font></b>";
                }
                
                
                // Daten Laden
                $data = $db->simple_select("laender", "*", "landid = ".$mybb->input['landid'], array("limit" => "1"));
                $land = $db->fetch_array($data);
                
                // Falls Fehler, dann die Felder manuell füllen!
                if ($mybb->request_method == 'post')
                {
                    $land['lname'] = $mybb->input['lname'];
                    $land['lrid'] = $mybb->input['lrid'];
                    $land['lkuerzel'] = $mybb->input['lkuerzel'];
                    $land['lart'] = $mybb->input['lart'];
                    $land['lreal'] = $mybb->input['lreal'];
                    $land['lbesp'] = $mybb->input['lbesp'];
                    $land['lstat'] = $mybb->input['lstat'];
                    $land['lparent'] = $mybb->input['lparent'];
                    $land['lverantw'] = $mybb->input['lverantw'];
                }

                // Neues Tab kreieren, das nur während des Editierens vorhanden ist.
                $sub_tabs['edit_land'] = array(
                    'title' => 'Land editieren',
                    'link' => 'index.php?module=config-fcverw&amp;action=edit_land&amp;landid='.$landid,
                    'description' => 'Editieren eines bestehenden Landes'
                );
                

                $page->add_breadcrumb_item('Land editieren');
                $page->output_header('L&auml;nderverwaltung - Land editieren');

                // which tab is selected? hier: edit_land
                $page->output_nav_tabs($sub_tabs, 'edit_land');

                // Neues Formular erstellen
                $form = new Form("index.php?module=config-fcverw&amp;action=edit_land", "post", "", 1);
                $form_container = new FormContainer('Land '.$land['lname'].' bearbeiten');

                // ID mitgeben über verstecktes Feld
                echo $form->generate_hidden_field('landid', $mybb->input['landid']);

                // der name
                $form_container->output_row(
                    'Name des Landes'.$l_fehler,
                    'Vollst&auml;ndiger Name des Landes',
                    $form->generate_text_box(
                        'lname',
                        htmlspecialchars_uni($land['lname']),
                        array('style' => 'width: 200px;')
                    )
                );
                
                // Kürzel
                $form_container->output_row(
                    'K&uuml;rzel des Landes',
                    'Wie wird das Land abgek&uuml;rzt? Z.B. Russland - RUS. Bitte alle Buchstaben groß schreiben.',
                    $form->generate_text_box(
                        'lkuerzel',
                        $db->escape_string($land['lkuerzel'])
                    )
                );
                
                // Art des Landes
                $form_container->output_row(
                    'Art des Landes',
                    'Handelt es sich z.B. um ein K&ouml;nigreich, ein Herzogtum oder eine Grafschaft?',
                    $form->generate_text_box(
                        'lart',
                        $db->escape_string($land['lart'])
                    )
                );
                
                // Untergeordnet?
                $lstats[0] = 'Nein, eigenst&auml;ndiges Land';
                $lstats[1] = 'Ja, untergeordnetes Land';
                $form_container->output_row(
                    'Untergeordnete Region?',
                    'Handelt es sich um einen Herrschaftsbereich, der einem anderen untergeordnet ist?',
                    $form->generate_select_box(
                        'lstat',
                        $lstats,
                        $land['lstat'],
                        array('id' => 'lstat', 'style' => 'width: 200px;')
                    ),
                    'lstat'
                );
                
                // die zugeordnete Region // Wenn Untergeordnet Nein
                // Regionen auslesen
                $regionen = array();
                $regionen[0] = "<b>Bitte Region w&auml;hlen!</b>";
                
                // Länder auslesen
                $lparent = array();
                $lparent[0] = "kein &uuml;bergeordnetes Land";
                
                
                // Funktion für das Auslesen der Kontinente und dazugehörigen Regionen
                $reg = fcverw_KonReg(0, 0);
                while ($regdata = $db->fetch_array($reg))
                {
                    //Regionen definieren für Variante 1 (nicht untergeordnet)
                    $regionen[$regdata['rid']] = "[".$regdata['kname']."] ".$regdata['rname'];
                    
                    // Angelegte Länder definieren für Variante 2 (untergeordnet)
                    // Aufruf Funktion für die Darstellung des Landes
                    $query = fcverw_LandList($regdata['rid'], 0, 0);
                    while ($landdata = $db->fetch_array($query))
                    {
                        $trenner = str_repeat("-", $landdata['Ebene']);
                        $lparent[$landdata['landid']] = "[".$regdata['kname']." - ".$regdata['rname']."] ".$trenner." ".$landdata['lname']." (".$landdata['lart'].")";
                    }
                }
                $form_container->output_row(
                    'Region'.$r_fehler,
                    'Zu welcher geopolitischen Region geh&ouml;rt das Land? Im Falle, dass real mehrere zutreffen, bitte die der gr&ouml;&szlig;ten Landmasse angeben!',
                    $form->generate_select_box(
                        'lrid',
                        $regionen,
                        $land['lrid'], 
                        array('style' => 'width: 200px;', 'id' => 'lrid')
                    ),
                    'lrid',
                    array(),
                    array('id' => 'row_lrid')
                );
 
                // Übergeordnetes Land // Wenn Untergeordnet Ja
                $form_container->output_row(
                    '&Uuml;bergeordnetes Land'.$lu_fehler,
                    'Welches Land ist &uuml;bergeordnet?',
                    $form->generate_select_box(
                        'lparent',
                        $lparent,
                        $land['lparent'], 
                        array('style' => 'width: 200px;', 'id' => 'lparent')
                    ),
                    'lparent',
                    array(),
                    array('id' => 'row_lparent')
                );

                
                // Reales Gebiet
                $form_container->output_row(
                    'Reales Gebiet',
                    'Welche realen Gebiete umfasst das fiktive Land?',
                    $form->generate_text_box(
                        'lreal',
                        $db->escape_string($land['lreal'])
                    )
                );
                

                // Bespielt?
                $bespielt = array();
                $bespielt[0] = "noch nicht bespielt";
                $bespielt[1] = "bereits bespielt";
                
                $form_container->output_row(
                    'Ist das Land bereits bespielt?',
                    'Gibt es bereits politisch relevante Charaktere?',
                    $form->generate_select_box(
                        'lbesp',
                        $bespielt,
                        $land['lbesp'], 
                        array('style' => 'width: 200px;', 'id' => 'lbesp')
                    )
                );
                
                
                // Länderverantwortlicher
                // User auslesen - warum gibt es keine Standardfunktion??
                $ugroups = "'2', '3', '4', '6', '8', '9', '10'";
                $userselect = fcverw_UserSelect($ugroups);
                $lverantw[0] = "kein L&auml;nderverantwortlicher";
                
                while ($lverantwortlich = $db->fetch_array($userselect))
                {
                    $lverantw[$lverantwortlich['uid']] = $lverantwortlich['username'];
                }
                
                $form_container->output_row(
                    'L&auml;nderverantwortlichkeit',
                    'Wer ist f&uuml;r das Land (insb. Verwaltung, Informationen) verantwortlich?',
                    $form->generate_select_box(
                        'lverantw',
                        $lverantw,
                        $land['lverantw'], 
                        array('style' => 'width: 200px;', 'id' => 'lverantw')
                    ),
                    'lverantw', 
                    array(), 
                    array('id' => 'row_lverantw')
                );


                $form_container->end();
                $button[] = $form->generate_submit_button('Land bearbeiten');
                $form->output_submit_wrapper($button);
                $form->end();
                
                echo '
                    <script type="text/javascript" src="./jscripts/peeker.js?ver=1821"></script>
                    <script type="text/javascript">
                        $(function() {
                            new Peeker($("#lstat"), $("#row_lrid"), /^0/, false);
                            new Peeker($("#lstat"), $("#row_lparent"), /^1/, false);
                        });
                    </script>
                ';
            }   
        } // Ende Land editieren




// c. Länder - Allgemein
// c4. Land löschen
        if ($mybb->input['action'] == "del_land")
        {
            $landid = $mybb->input['landid'];
            
            // Landdatan auslesen
            $landsel = $db->simple_select("laender", "*", "ldelete = '0' AND landid = ".$landid, array("limit" => "1"));
            $land = $db->fetch_array($landsel);
            
            // Wenn das Land ein Parent ist, dann ...
            if ($land['lstat'] == '0')
            {
                // Prüfen, ob es archivierte Children gibt.
                $childs = $db->simple_select("laender", "*", "lparent = ".$landid);
                // Prüfen, ob es das Land vielleicht schon gibt, weil ein anderes Child umgetragen wurde
                $probe = $db->num_rows($db->simple_select("laender_archive", "*", "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'"));
                
                // Wenn es keine (archivierten) Childs gibt, dann ...
                if ($db->num_rows($childs) == '0')
                {
                    // Wenn es das Land aktiv noch gar nicht gibt, dann eintragen und löschen
                    if ($probe == '0')
                    {
                        $insert = array(
                            "landid" => $land['landid'],
                            "lname" => $land['lname'],
                            "lkid" => $land['lkid'],
                            "lrid" => $land['lrid'],
                            "lkuerzel" => $land['lkuerzel'],
                            "lart" => $land['lart'],
                            "lreal" => $land['lreal'],
                            "lbesp" => $land['lbesp'],
                            "lstat" => $land['lstat'],
                            "lparent" => $land['parent'],
                            "lverantw" => $land['lverantw']
                        );
                        
                        $db->insert_query("laender_archive", $insert);
                        
                        if ($db->delete_query("laender", "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                    // Wenn das Land bereits existiert (warum auch immer), dann auf ganz aktiv stellen - soweit es nicht bereits der Fall ist.
                    else 
                    {
                        $update = array(
                            "ldelete" => '1'
                        );
                        
                        $db->update_query("laender_archive", $update, "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'", 1);
                        
                        if ($db->delete_query("laender", "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                }
                // Wenn das Land Childs hat, dann kopieren und entsprechend den Update-Query anpassen
                else 
                {
                    // Wenn es das Land aktiv noch gar nicht gibt, dann eintragen und löschen
                    if ($probe == '0')
                    {
                        $insert = array(
                            "landid" => $land['landid'],
                            "lname" => $land['lname'],
                            "lkid" => $land['lkid'],
                            "lrid" => $land['lrid'],
                            "lkuerzel" => $land['lkuerzel'],
                            "lart" => $land['lart'],
                            "lreal" => $land['lreal'],
                            "lbesp" => $land['lbesp'],
                            "lstat" => $land['lstat'],
                            "lparent" => $land['parent'],
                            "lverantw" => $land['lverantw']
                        );
                        $db->insert_query("laender_archive", $insert);
                        
                        $update = array(
                            "ldelete" => '1'
                        );
                        if ($db->update_query("laender", $update, "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                    // Wenn das Land bereits existiert (warum auch immer), dann auf ganz aktiv stellen - soweit es nicht bereits der Fall ist.
                    else 
                    {
                        $update = array(
                            "ldelete" => '1'
                        );
                        
                        $db->update_query("laender_archive", $update, "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'", 1);
                        
                        // Und dann entsprechend hier updaten
                        
                        $update = array(
                            "ldelete" => '1'
                        );
                        if ($db->update_query("laender", $update, "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                }
                
            }
            // Wenn es sich um ein untergeordnetes Land handelt ...
            else
            {
                $landid = $mybb->input['landid'];
                // Daten des Landes auslesen
                $landdata = $db->fetch_array($db->simple_select("laender", "*", "landid = ".$landid));
                
                
                // Wir arbeiten mit PathID - also allen IDs der Parents.
                // Nehmen diese IDs auseindner - das können wir!
                $PathID = $mybb->input['path'];
                $paths = explode(",", $PathID);
                $count = count($paths);
                
                // 1. Daten der Elter auslesen, abgleicen (ggf. eintragen mit Del = 1) -> ggf. lparent anpassen!
                for ($i = 0; $i < ($count-1); $i++)
                {
                    // Daten Auslesen
                    $parents = $db->simple_select("laender", "*", "landid = ".$paths[$i], array("limit" => "1"));
                    $parent = $db->fetch_array($parents);
                    
                    // Prüfen, ob es einen solchen Datensatz bereits hat
                    $probe = $db->num_rows($db->simple_select("laender_archive", "*", "lname = '".$parent['lname']."' AND lstat = '".$parent['lstat']."' AND lrid = '".$parent['lrid']."'"));
                    
                   
                    // Wenn nein, dann erstellen mti Del = 1 - und die ID merken!
                    if ($probe == "0")
                    {   
                        $insert = array(
                            "landid" => $parent['landid'],
                            "lname" => $parent['lname'],
                            "lkid" => $parent['lkid'],
                            "lrid" => $parent['lrid'],
                            "lkuerzel" => $parent['lkuerzel'],
                            "lart" => $parent['lart'],
                            "lreal" => $parent['lreal'],
                            "lbesp" => $parent['lbesp'],
                            "lstat" => $parent['lstat'],
                            "lparent" => $parent['lparent'],
                            "lverantw" => $parent['lverantw'],
                            "ldelete" => "0"
                        );
                        $db->insert_query("laender_archive", $insert);
                        
                    }
                    // Wenn es bereits einen Eintrag gibt, dann LandID auslesen und entsprechend die NewID speichern für die nächste Runde.
                    else 
                    {
                        $parent['lparent'] = $db->fetch_field($db->simple_select("laender_archive", "*", "lname = '".$parent['lname']."' AND lstat = '".$parent['lstat']."' AND lrid = '".$parent['lrid']."'", array("limit" => "1")), "landid");                  
                    }
                }

                
                // 2. Prüfen, ob das Child weitere Childs hat.
                $childs = $db->simple_select("laender", "*", "lparent = ".$landid);
                
                
                // Prüfen, ob entsprechender Eintrag besteht
                $pruef = $db->simple_select("laender_archive", "landid", "lname = '".$landdata['lname']."' AND lstat = '".$landdata['lstat']."' AND lrid = '".$landdata['lrid']."'");
                
                
                // Wenn noch kein Eintrag vorhanden, dann neu eintragen
                if ($db->num_rows($pruef) == "0")
                {
                    $insert = array(
                        "landid" => $landdata['landid'],
                        "lname" => $landdata['lname'],
                        "lkid" => $landdata['lkid'],
                        "lrid" => $landdata['lrid'],
                        "lkuerzel" => $landdata['lkuerzel'],
                        "lart" => $landdata['lart'],
                        "lreal" => $landdata['lreal'],
                        "lbesp" => $landdata['lbesp'],
                        "lstat" => $landdata['lstat'],
                        "lparent" => $landdata['lparent'],
                        "lverantw" => $landdata['lverantw']
                    );
                    $db->insert_query("laender_archive", $insert);
                }
                // Wenn ein Eintrag vorhanden, dann updaten
                else 
                {
                    $update = array(
                        "ldelete" => "1"
                    );
                            
                    $lid = $db->fetch_field($pruef, "landid");
                            
                    $db->update_query("laender_archive", $update, "landid = ".$lid);
                }
                    
                    
                // Wenn Childs nein, Datensatz löschen
                if ($db->num_rows($childs) == "0")
                {    
                    if ($db->delete_query("laender", "landid = ".$landdata['landid']))
                    {
                        redirect("admin/index.php?module=config-fcverw&action=laender");
                    } 
                }    
                else {
                    $update2 = array(
                        "ldelete" => "1"
                    );
                    
                    if ($db->update_query("laender", $update2, "landid = ".$landdata['landid']))
                    {
                        redirect("admin/index.php?module=config-fcverw&action=laender");
                    } 
                }
                                                                                                    
            }

        }

// c. Länder - Allgemein
// c5. Land wiederherstellen
        if ($mybb->input['action'] == "re_land")
        {
            $landid = $mybb->input['landid'];
            
            // Landdatan auslesen
            $landsel = $db->simple_select("laender_archive", "*", "ldelete = '1' AND landid = ".$landid, array("limit" => "1"));
            $land = $db->fetch_array($landsel);
            
            // Wenn das Land ein Parent ist, dann ...
            if ($land['lstat'] == '0')
            {
                // Prüfen, ob es archivierte Children gibt.
                $childs = $db->simple_select("laender_archive", "*", "lparent = ".$landid);
                // Prüfen, ob es das Land vielleicht schon gibt, weil ein anderes Child umgetragen wurde
                $probe = $db->num_rows($db->simple_select("laender", "*", "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'"));
                
                // Wenn es keine (archivierten) Childs gibt, dann ...
                if ($db->num_rows($childs) == '0')
                {
                    // Wenn es das Land aktiv noch gar nicht gibt, dann eintragen und löschen
                    if ($probe == '0')
                    {
                        $insert = array(
                            "lname" => $land['lname'],
                            "lkid" => $land['lkid'],
                            "lrid" => $land['lrid'],
                            "lkuerzel" => $land['lkuerzel'],
                            "lart" => $land['lart'],
                            "lreal" => $land['lreal'],
                            "lbesp" => $land['lbesp'],
                            "lstat" => $land['lstat'],
                            "lparent" => $land['parent'],
                            "lverantw" => $land['lverantw']
                        );
                        
                        $db->insert_query("laender", $insert);
                        
                        $newID = $db->insert_id();
                        
                        // IDs bei Länderinfos ändern
                        $linfoupdate = array(
                            "landid" => $newID
                        );
                        $db->update_query("laender_info", $linfoupdate, "landid = ".$land['landid']);
                        
                        
                        if ($db->delete_query("laender_archive", "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                    // Wenn das Land bereits existiert (warum auch immer), dann auf ganz aktiv stellen - soweit es nicht bereits der Fall ist.
                    else 
                    {
                        $update = array(
                            "ldelete" => '0'
                        );
                        
                        $db->update_query("laender", $update, "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'", 1);
                        
                        if ($db->delete_query("laender_archive", "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                }
                // Wenn das Land Childs hat, dann kopieren und entsprechend den Update-Query anpassen
                else 
                {
                    // Wenn es das Land aktiv noch gar nicht gibt, dann eintragen und löschen
                    if ($probe == '0')
                    {
                        $insert = array(
                            "lname" => $land['lname'],
                            "lkid" => $land['lkid'],
                            "lrid" => $land['lrid'],
                            "lkuerzel" => $land['lkuerzel'],
                            "lart" => $land['lart'],
                            "lreal" => $land['lreal'],
                            "lbesp" => $land['lbesp'],
                            "lstat" => $land['lstat'],
                            "lparent" => $land['parent'],
                            "lverantw" => $land['lverantw']
                        );
                        $db->insert_query("laender", $insert);
                        
                        $update = array(
                            "ldelete" => '0'
                        );
                        if ($db->update_query("laender_archive", $update, "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                    // Wenn das Land bereits existiert (warum auch immer), dann auf ganz aktiv stellen - soweit es nicht bereits der Fall ist.
                    else 
                    {
                        $update = array(
                            "ldelete" => '0'
                        );
                        
                        $db->update_query("laender", $update, "lname = '".$land['lname']."' AND lstat = '".$land['lstat']."' AND lrid = '".$land['lrid']."'", 1);
                        
                        // Und dann entsprechend hier updaten
                        
                        $update = array(
                            "ldelete" => '0'
                        );
                        if ($db->update_query("laender_archive", $update, "landid = ".$land['landid']))
                        {
                            redirect("admin/index.php?module=config-fcverw&action=laender");
                        }
                        
                    }
                }
                
            }
            // Wenn es sich um ein untergeordnetes Land handelt ...
            else
            {
                $landid = $mybb->input['landid'];
                // Daten des Landes auslesen
                $landdata = $db->fetch_array($db->simple_select("laender_archive", "*", "landid = ".$landid));
                
                
                // Wir arbeiten mit PathID - also allen IDs der Parents.
                // Nehmen diese IDs auseindner - das können wir!
                $PathID = $mybb->input['path'];
                $paths = explode(",", $PathID);
                $count = count($paths);
                
                // 1. Daten der Elter auslesen, abgleicen (ggf. eintragen mit Del = 1) -> ggf. lparent anpassen!
                for ($i = 0; $i < ($count-1); $i++)
                {
                    // Daten Auslesen
                    $parents = $db->simple_select("laender_archive", "*", "landid = ".$paths[$i], array("limit" => "1"));
                    $parent = $db->fetch_array($parents);
                    
                    // Prüfen, ob es einen solchen Datensatz bereits hat
                    $probe = $db->num_rows($db->simple_select("laender", "*", "lname = '".$parent['lname']."' AND lstat = '".$parent['lstat']."' AND lrid = '".$parent['lrid']."'"));
                    
                    $newID = 0;
                    
                    // Wenn nein, dann erstellen mti Del = 1 - und die ID merken!
                    if ($probe == "0")
                    {   
                        $insert = array(
                            "lname" => $parent['lname'],
                            "lkid" => $parent['lkid'],
                            "lrid" => $parent['lrid'],
                            "lkuerzel" => $parent['lkuerzel'],
                            "lart" => $parent['lart'],
                            "lreal" => $parent['lreal'],
                            "lbesp" => $parent['lbesp'],
                            "lstat" => $parent['lstat'],
                            "lparent" => $newID,
                            "lverantw" => $parent['lverantw'],
                            "ldelete" => "1"
                        );
                        $db->insert_query("laender", $insert);
                        
                        $newID = $db->insert_id();
                    }
                    // Wenn es bereits einen Eintrag gibt, dann LandID auslesen und entsprechend die NewID speichern für die nächste Runde.
                    else 
                    {
                        $newID = $db->fetch_field($db->simple_select("laender", "*", "lname = '".$parent['lname']."' AND lstat = '".$parent['lstat']."' AND lrid = '".$parent['lrid']."'", array("limit" => "1")), "landid");                  
                    }
                }
                

                
                // 2. Prüfen, ob das Child weitere Childs hat.
                $childs = $db->simple_select("laender_archive", "*", "lparent = ".$landid);
                
                
                // Prüfen, ob entsprechender Eintrag besteht
                $pruef = $db->simple_select("laender", "landid", "lname = '".$landdata['lname']."' AND lstat = '".$landdata['lstat']."' AND lrid = '".$landdata['lrid']."'");
                
                
                // Wenn noch kein Eintrag vorhanden, dann neu eintragen
                if ($db->num_rows($pruef) == "0")
                {
                    $insert = array(
                        "lname" => $landdata['lname'],
                        "lkid" => $landdata['lkid'],
                        "lrid" => $landdata['lrid'],
                        "lkuerzel" => $landdata['lkuerzel'],
                        "lart" => $landdata['lart'],
                        "lreal" => $landdata['lreal'],
                        "lbesp" => $landdata['lbesp'],
                        "lstat" => $landdata['lstat'],
                        "lparent" => $newID,
                        "lverantw" => $landdata['lverantw']
                    );
                    $db->insert_query("laender", $insert);
                }
                // Wenn ein Eintrag vorhanden, dann updaten
                else 
                {
                    $update = array(
                        "ldelete" => "0",
                        "lparent" => $newID
                    );
                            
                    $lid = $db->fetch_field($pruef, "landid");
                            
                    $db->update_query("laender", $update, "landid = ".$lid);
                }
                    
                    
                // Wenn Childs nein, Datensatz löschen
                if ($db->num_rows($childs) == "0")
                {    
                    if ($db->delete_query("laender_archive", "landid = ".$landdata['landid']))
                    {
                        redirect("admin/index.php?module=config-fcverw&action=laender");
                    } 
                }    
                else {
                    $update2 = array(
                        "ldelete" => "0"
                    );
                    
                    if ($db->update_query("laender_archive", $update2, "landid = ".$landdata['landid']))
                    {
                        redirect("admin/index.php?module=config-fcverw&action=laender");
                    } 
                }
                                                                                                    
            }
            
        }



// c. Länder - Allgemein
// c6. Land als vergeben kennzeichnen
        if ($mybb->input['action'] == "take_land")
        {
            $landid = $mybb->input['landid'];
            $update = array(
                'lbesp' => '1'
            );
            
            if ($db->update_query("laender", $update, "landid = ".$landid))
            {
                redirect("admin/index.php?module=config-fcverw&action=laender");
            }
        }


// c. Länder - Allgemein
// c7. Land als frei kennzeichnen
        if ($mybb->input['action'] == "free_land")
        {
            $landid = $mybb->input['landid'];
            $update = array(
                'lbesp' => '0',
                'lverantw' => '0'
            );
            
            if ($db->update_query("laender", $update, "landid = ".$landid))
            {
                redirect("admin/index.php?module=config-fcverw&action=laender");
            }
        }


// c. Länder - Allgemein
// c8. Landdaten bereinigen
        if ($mybb->input['action'] == "ber_laender")
        {
            // Unnötige Sachen löschen
            
            // 1. Hauptteil
            $active = $db->simple_select("laender", "landid", "ldelete = '1'");
            while ($act = $db->fetch_array($active))
            {
                // Prüfen, ob es aktive Children gibt
                $child = $db->simple_select("laender", "landid, lparent", "lparent = ".$act['landid']);
                
                if ($db->num_rows($child) == '0')
                {
                    $db->delete_query("laender", "landid = ".$act['landid']);
                }
            }
            
            // 1. Archiv
            $archive = $db->simple_select("laender_archive", "landid", "ldelete = '0'");
            while ($arc = $db->fetch_array($archive))
            {
                // Prüfen, ob es aktive Children gibt
                $childs = $db->simple_select("laender_archive", "landid, lparent", "lparent = ".$arc['landid']);
                
                if ($db->num_rows($childs) == '0')
                {
                    $db->delete_query("laender_archive", "landid = ".$arc['landid']);
                }
            }
            
            
            redirect("admin/index.php?module=config-fcverw&action=laender");
            
        }