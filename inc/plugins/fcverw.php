<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

// Informationen, die unter Plugins angezeigt werden
function fcverw_info()
{
    return array(
        "name"          => "L&auml;nderverwaltung",
        "description"   => "Dieser Plugin erlaubt die Verwaltung von L&auml;ndern inkl. Erstellung, Diplomatie, Informationen.",
        "website"       => "https://github.com/windkindchen/FC-Verwaltung",
        "author"        => "May (windkindchen)",
        "authorsite"    => "https://github.com/windkindchen",
        "version"       => "2.0",
        "guid"          => "",
        "codename"      => "",
        "compatibility" => "18*"
    );
}

function fcverw_install()
{
    global $db, $mybb;

    // Erstellen der Datenbanktabellen
    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender (
            `landid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `lkid` int(15) NOT NULL,
            `lrid` int(15) NOT NULL,
            `lname` varchar(255) NOT NULL,
            `lkuerzel` varchar(10) NOT NULL,
            `lart` varchar(255) NOT NULL,
            `lreal` text NOT NULL,
            `lbesp` int(1) NOT NULL DEFAULT 0,
            `lstat` int(1) NOT NULL DEFAULT 0,
            `lparent` int(15) NOT NULL,
            `lverantwortl` int(15) NOT NULL,
            `ldelete` int(1) NOT NULL DEFAULT 0
            PRIMARY KEY (`landid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_archive (
            `lid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `landid` int(15) NOT NULL,
            `lkid` int(15) NOT NULL,
            `lrid` int(15) NOT NULL,
            `lname` varchar(255) NOT NULL,
            `lkuerzel` varchar(10) NOT NULL,
            `lart` varchar(255) NOT NULL,
            `lreal` text NOT NULL,
            `lbesp` int(1) NOT NULL DEFAULT 0,
            `lstat` int(1) NOT NULL DEFAULT 0,
            `lparent` int(15) NOT NULL,
            `lverantwortl` int(15) NOT NULL,
            `ldelete` int(1) NOT NULL DEFAULT 1
            PRIMARY KEY (`lid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_diplomatie (
            `dipid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `diplandid` int(15) NOT NULL,
            `dippartid` int(15) NOT NULL,
            `dipstatus` int(2) NOT NULL,
            `dipbeschr` text NOT NULL,
            `diptime` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`dipid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_info (
            `linfoid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `landid` int(15) NOT NULL,
            `lidatum` timestamp NOT NULL DEFAULT current_timestamp(),
            `lifreigabe` int(1) NOT NULL DEFAULT 0,
            `sprache` longtext NOT NULL,
            `hauptstadt` longtext NOT NULL,
            `allgemein` longtext NOT NULL,
            `royal` longtext NOT NULL,
            `diplomatie` longtext NOT NULL,
            `militaer` longtext NOT NULL,
            `volk` longtext NOT NULL,
            `rebellen` longtext NOT NULL,
            `wirtschaft` longtext NOT NULL,
            `einwanderung` longtext NOT NULL,
            `medien` longtext NOT NULL,
            `sonstiges` longtext NOT NULL,
            `regierung` longtext NOT NULL,
            `religion` longtext NOT NULL,
            PRIMARY KEY (`linfoid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_kontinente (
            `kid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `kname` varchar(255) NOT NULL,
            `kbeschr` text NOT NULL,
            PRIMARY KEY (`kid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_regionen (
            `rid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `rkid` int(15) NOT NULL,
            `rname` varchar(255) NOT NULL,
            `rbeschr` text NOT NULL,
            PRIMARY KEY (`rid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_verwandt (
            `lvid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `landid` int(15) NOT NULL,
            `verwid` int(15) NOT NULL,
            `lvbeschr` text NOT NULL,
            PRIMARY KEY (`lvid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
        CREATE TABLE ".TABLE_PREFIX."laender_personen (
            `lpid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
            `landid` int(15) NOT NULL,
            `uid` int(15) NOT NULL,
            `name` varchar(255) NOT NULL,
            `titel` varchar(255) NOT NULL,
            `rang` int(15) NOT NULL,
            `lpfreigabe` int(1) NULL DEFAULT 0,
            PRIMARY KEY (`lpid`)
        )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    // Erstellen der Einstellungen

    // Erstellen der templates

    // Erstellen des CSS
}


// Prüfen, ob Plugin installiert
function fcverw_is_installed()
{
    global $db;

    if ($db->table_exists("laender"))
    {
        return true;
    }
}


// Plugin deinstallieren
function fcverw_uninstall()
{
    global $db;
    
    // Löschen der Datenbanktabellen
    if ($db->table_exists("laender"))
    {
        $db->drop_table("laender");
    }
    if ($db->table_exists("laender_diplomatie"))
    {
        $db->drop_table("laender_diplomatie");
    }
    if ($db->table_exists("laender_info"))
    {
        $db->drop_table("laender_info");
    }
    if ($db->table_exists("laender_kontinente"))
    {
        $db->drop_table("laender_kontinente");
    }
    if ($db->table_exists("laender_regionen"))
    {
        $db->drop_table("laender_regionen");
    }
    if ($db->table_exists("laender_verwandt"))
    {
        $db->drop_table("laender_verwandt");
    }
    if ($db->table_exists("laender_personen"))
    {
        $db->drop_table("laender_personen");
    }


    // Löschen der Einstellungen

    // Löschen der templates

    // Löschen des CSS
}


// Aktivieren und damit veröffentlichen
function fcverw_activate()
{
    global $db, $cache;

    // Links einbinden etc.
    
    // im UserCP
    // im Header zur Liste
    // ...
}

// deaktivieren und damit verstecken
function fcverw_deactivate()
{
    global $db, $cache;

    // Änderungen an Templates rückgängig machen
}


/* **********************************************************************************************
   **********************************************************************************************
                                          Let the magic begin
   **********************************************************************************************
   ********************************************************************************************** */
// Load the Globals that are for everything
require_once MYBB_ROOT.'inc/plugins/fcverw/fcverw_global.php';



/* ***********************************************
          Part 01: ADMIN-CP
   *********************************************** */



#######################################
### // creates link in acp -> users ###
#######################################

$plugins->add_hook('admin_config_menu', 'fcverw_admin_config_menu');
function fcverw_admin_config_menu(&$sub_menu)
{
    $sub_menu[] = array(
        'id' => 'fcverw',
        'title' => 'L&auml;nderverwaltung',
        'link' => 'index.php?module=config-fcverw'
    );
}



#############################################
### // hook actions for fcverw management ###
#############################################

$plugins->add_hook('admin_config_action_handler', 'fcverw_admin_config_action_handler');
function fcverw_admin_config_action_handler(&$actions)
{
    $actions['fcverw'] = array('active' => 'fcverw', 'file' => 'fcverw');
}


###################################
### // Admin-Funktionen basteln ###
### // a. Kontinente            ###
### // b. Regionen              ###
### // c. Länder                ###
###################################


$plugins->add_hook('admin_load', 'fcverw_admin');
function fcverw_admin()
{
    global $mybb, $db, $lang, $page, $action_file, $run_module, $form, $land, $i;

    if ($page->active_action != 'fcverw')
    {
   	    return false;
    }

// Wenn Das Module config ist und das Action File fcverw, 
// dann beginnt die Magic.
    if ($run_module == 'config' && $action_file == 'fcverw')
    {
        $page->add_breadcrumb_item('L&auml;nderverwaltung', 'index.php?module=config-fcverw');


// Die Standard-Tabs (permanent) des Moduls konfigurieren.
// Für die Bearbeitung und einzelne Unterpunkte gibt es extra welche
        $sub_tabs['laender'] = array(
            'title'	=> 'Alle L&auml;nder',
            'link'	=> 'index.php?module=config-fcverw',
            'description'   => '&Uuml;bersicht aller L&auml;nder, aufgeteilt nach aktiven und archivierten Ländern. <br />
            Wichtig: Wenn viele Länder wiederhergestellt wurden, dann zur Sicherheit <b><a href="index.php?module=config-fcverw&amp;action=ber_laender">Daten bereinigen</a></b>.<br /><br />
  			<img src="fcverw/frei.png"> Land frei; <img src="fcverw/vergeben.png"> Land vergeben; <img src="fcverw/error.png"> Land vergeben ohne Verantwortlichen'
        );
        $sub_tabs['add_land'] = array(
            'title' => 'Land anlegen',
            'link' => 'index.php?module=config-fcverw&amp;action=add_land',
            'description' => 'Anlegen eines neuen Landes'
        );
        $sub_tabs['regionen'] = array(
            'title' => 'Regionen',
            'link' => 'index.php?module=config-fcverw&amp;action=regionen',
            'description' => 'Übersicht der Regionen'
        );
        $sub_tabs['add_region'] = array(
            'title' => 'Region anlegen',
            'link' => 'index.php?module=config-fcverw&amp;action=add_region',
            'description' => 'Anlegen eines neuen Landes'
        );
        $sub_tabs['kontinente'] = array(
            'title' => 'Kontinente',
            'link' => 'index.php?module=config-fcverw&amp;action=kontinente',
            'description' => 'Übersicht der Kontinente'
        );
        $sub_tabs['add_kontinent'] = array(
            'title' => 'Kontinent anlegen',
            'link' => 'index.php?module=config-fcverw&amp;action=add_kontinent',
            'description' => 'Anlegen eines neuen Kontinents'
        );



// a. Kontinente
// a1. Alle Kontinente anzeigen lassen
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



// a. Kontinente
// a2. Neuen Kontinent anlegen
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



// a. Kontinente
// a3. Kontinent editieren
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



// a. Kontinente
// a4. Kontinent löschen
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



// a. Kontinente
// a5. Kontinent wiederherstellen
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

        } // Ende Löschen Kontinent





// b. Regionen
// b1. Alle Regionen anzeigen
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


// b. Regionen
// b2. Region anlegen
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


// b. Regionen
// b3. Region editieren
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


// b. Regionen
// b4. Region löschen
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


// b. Regionen
// b5. Region wiederhierstellen
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
            
           

        } // Ende Löschen Region





// c. Länder
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
                    $query = fcverw_LandList($data['rid'], 0);
                    
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
                        
                        // Optionen-Fach basteln
                        //erst pop up dafür bauen - danke an @Risuena
                        $popup = new PopupMenu("fcverw_".$landdata['landid'], "Optionen");
                        $popup->add_item(
                            "Editieren",
                            "index.php?module=config-fcverw&amp;action=edit_land&amp;landid=".$landdata['landid']
                        );
                        $popup->add_item(
                            "Archivieren",
                            "index.php?module=config-fcverw&amp;action=del_land&amp;landid=".$landdata['landid']."&amp;path=".$landdata['PathID']
                        );
                        $form_container->output_cell($popup->fetch(), array("class" => "align_center"));
        
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
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
                    $query2 = fcverw_LandList($data2['rid'], 1);

                    while ($landdata2 = $db->fetch_array($query2))
                    {
                        $trenner2 = str_repeat("-", $landdata2['Ebene']);
                        
                        $hinweis = "";
                        if ($landdata2['ldelete'] == '0')
                        {
                            $landdata2['lname'] = "<span style=\"opacity: .5;\">".$landdata2['lname']."</span>";
                            $hinweis = "(aktiv; hier nur für &Uuml;bersicht)";
                        }
                        
                        $form_container->output_cell($landdata2['landid'], array("class" => "align_center"));
                        $form_container->output_cell($trenner2." ".$landdata2['lname']." (".$landdata2['lart'].") ".$hinweis);
                        
                        // Optionen-Fach basteln
                        //erst pop up dafür bauen - danke an @Risuena
                        $popup2 = new PopupMenu("fcverw2_".$landdata2['landid'], "Optionen");
                        $popup2->add_item(
                            "Editieren",
                            "index.php?module=config-fcverw&amp;action=edit_land&amp;landid=".$landdata2['landid']
                        );
                        $popup2->add_item(
                            "Wiederherstellen",
                            "index.php?module=config-fcverw&amp;action=re_land&amp;landid=".$landdata2['landid']."&amp;path=".$landdata2['PathID']
                        );
                        $form_container->output_cell($popup2->fetch(), array("class" => "align_center"));
        
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->construct_row(); // Reihe erstellen
                    }       
                } 
            } 
            $form_container->end();
            
            
            
            // Nächstes: Land, Inaktive Region, Kontinent
            $form_container = new FormContainer('Alle aktiven L&auml;nder (archivierte Region, aktiver Kontinent)');
            
            
            
            $form->end();
        } // Ende der Startseite



// c. Länder
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

                echo $l_fehler."<br>".$r_fehler."<br>".$lu_fehler;

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
                    'Wie wird das Land abgek&uuml;rzt? Z.B. Russland - RUS. Bitte alle Buchstaben groß schreiben.',
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
                    $query = fcverw_LandList($regdata['rid'], 0);
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
                $form_container->output_row(
                    'Ist das Land bereits bespielt?',
                    'Gibt es bereits politisch relevante Charaktere?',
                    $form->generate_yes_no_radio(
                        'lbesp',
                        '0',
                        $db->escape_string($mybb->input['lbesp']),
                        array('id' => 'lbesp')
                    ),
                    'lbesp'
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


// c. Länder
// c3. Land editieren



// c. Länder
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
                    
                    $newID = 0;
                    
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
                            "lparent" => $newID,
                            "lverantw" => $parent['lverantw'],
                            "ldelete" => "0"
                        );
                        $db->insert_query("laender_archive", $insert);
                        
                        $newID = $db->insert_id();
                    }
                    // Wenn es bereits einen Eintrag gibt, dann LandID auslesen und entsprechend die NewID speichern für die nächste Runde.
                    else 
                    {
                        $newID = $db->fetch_field($db->simple_select("laender_archive", "*", "lname = '".$parent['lname']."' AND lstat = '".$parent['lstat']."' AND lrid = '".$parent['lrid']."'", array("limit" => "1")), "landid");                  
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
                        "lparent" => $newID,
                        "lverantw" => $landdata['lverantw']
                    );
                    $db->insert_query("laender_archive", $insert);
                }
                // Wenn ein Eintrag vorhanden, dann updaten
                else 
                {
                    $update = array(
                        "ldelete" => "1",
                        "lparent" => $newID
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

// c. Länder
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
                        
                        if ($db->delete_query("laender_archive", "landid = ".$land['land']))
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



// c. Länder
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


// c. Länder
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


// c. Länder
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


        $page->output_footer();
        exit;
    } // Ende der Prüfung, ob das richtige Modul aktiv ist
} // Ende der Admin-Funktion
