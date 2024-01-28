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
            `luebergeordnet` int(15) NOT NULL,
            `lverantwortl` int(15) NOT NULL,
            PRIMARY KEY (`landid`)
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
            'description'   => '&Uuml;bersicht aller L&auml;nder<br />
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

            // Tabelle kreieren - Headerzeile
            $form = new Form("index.php?module=config-fcverw", "post");
            $form_container = new FormContainer('Alle Kontinente');
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "3%"));
            $form_container->output_row_header('Kontinentname', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('Regionen', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('L&auml;nder', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));

            // Hier werden die Kontinente ausgelesen
            $fc_kontsel = $db->write_query("SELECT * FROM ".TABLE_PREFIX."laender_kontinente ORDER BY kname");
            while ($row = $db->fetch_array($fc_kontsel))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $regionen = $db->num_rows($db->simple_select("laender_regionen", "rid", "rkid = ".$row['kid']));
                $laender = $db->num_rows($db->simple_select("laender", "landid", "lkid = ".$row['kid']));

                $form_container->output_cell($row['kid'], array("class" => "align_center"));
                $form_container->output_cell("<b>".$row['kname']."</b>");
                $form_container->output_cell($row['kbeschr']);
                $form_container->output_cell($regionen, array("class" => "align_center"));
                $form_container->output_cell($laender, array("class" => "align_center"));

                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup = new PopupMenu("fcverw_".$row['kid'], "Optionen");
                $popup->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_kontinent&amp;kid=".$row['kid']
                );
                $popup->add_item(
                    "L&ouml;schen",
                    "index.php?module=config-fcverw&amp;action=del_kontinent&amp;kid=".$row['kid']
                );
                $form_container->output_cell($popup->fetch(), array("class" => "align_center"));

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
                $update_query = array(
                    'kname' => htmlspecialchars_uni($mybb->input['kname']),
                    'kbeschr' => htmlspecialchars_uni($mybb->input['kbeschr'])
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
            $update_laender = array(
                'lkid' => "0"
            );
            $db->update_query("laender", $update_laender, "lkid = ".$kid);

            // Regionen updaten
            $update_regionen = array(
                'rkid' => "0"
            );
            $db->update_query("laender_regionen", $update_regionen, "rkid = ".$kid);

            // Eintrag löschen
            if ($db->delete_query("laender_kontinente", "kid = ".$kid))
            {
                redirect("admin/index.php?module=config-fcverw&action=kontinente");
            }

        } // Ende Löschen Kontinent





// b. Regionen
// b1. Alle Regionen anzeigen
        if ($mybb->input['action'] == "regionen")
        {
            $page->add_breadcrumb_item('&Uuml;bersicht aller Regionen');
            $page->output_header('L&auml;nderverwaltung - &Uuml;bersicht aller regionen');

            // Welches Tab ist ausgewählt?
            $page->output_nav_tabs($sub_tabs, 'regionen');

            // Tabelle kreieren - Headerzeile
            $form = new Form("index.php?module=config-fcverw", "post");
            $form_container = new FormContainer('Alle Regionen');
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "3%"));
            $form_container->output_row_header('Regionenname', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('Kontinent', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('L&auml;nder', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));

            // Hier werden die Regionen ausgelesen
            $fc_regsel = $db->write_query("
                SELECT 
                    r.*, k.* 
                FROM 
                    ".TABLE_PREFIX."laender_regionen r 
                LEFT JOIN 
                    ".TABLE_PREFIX."laender_kontinente k 
                ON 
                    k.kid = r.rkid 
                ORDER BY 
                    k.kname, r.rname
            ");
                
                
            while ($row = $db->fetch_array($fc_regsel))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $laender = $db->num_rows($db->simple_select("laender", "landid", "lrid = ".$row['rid']));

                $form_container->output_cell($row['rid'], array("class" => "align_center"));
                $form_container->output_cell("<b>".$row['rname']."</b>");
                $form_container->output_cell($row['rbeschr']);
                $form_container->output_cell($row['kname']);
                $form_container->output_cell($laender, array("class" => "align_center"));

                // Optionen-Fach basteln
                //erst pop up dafür bauen - danke an @Risuena
                $popup = new PopupMenu("fcverw_".$row['rid'], "Optionen");
                $popup->add_item(
                    "Editieren",
                    "index.php?module=config-fcverw&amp;action=edit_region&amp;rid=".$row['rid']
                );
                $popup->add_item(
                    "L&ouml;schen",
                    "index.php?module=config-fcverw&amp;action=del_region&amp;rid=".$row['rid']
                );
                $form_container->output_cell($popup->fetch(), array("class" => "align_center"));

                $form_container->construct_row(); // Reihe erstellen
            }

            $form_container->end();
            $form->end();
        } // Ende der Kontinentübersicht


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
                $update_query = array(
                    'rkid' => (int)$mybb->input['rkid'],
                    'rname' => htmlspecialchars_uni($mybb->input['rname']),
                    'rbeschr' => htmlspecialchars_uni($mybb->input['rbeschr'])
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

                $form_container->end();
                $button[] = $form->generate_submit_button('Region editieren');
                $form->output_submit_wrapper($button);
                $form->end();
            }
        } // Ende Editieren Kontinent


// b. Regionen
// b4. Region löschen




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
            $form_container = new FormContainer('Alle L&auml;nder');
            $form_container->output_row_header('ID');
            $form_container->output_row_header('Landname');
            $form_container->output_row_header('Landart');
            $form_container->output_row_header('L&auml;nderinfos');
            $form_container->output_row_header('Diplomatie');
            $form_container->output_row_header('Verwandtschaften');
            $form_container->output_row_header('Bewohner');
            $form_container->output_row_header('Bearbeiten?');

            // Hier werden die Lander ausgelesen
            // und weitere Details


          $form_container->end();
          $form->end();
        } // Ende der Startseite


// c. Länder
// c2. Land editieren

// c. Länder
// c3. Land löschen


// c. Länder
// c4. Land - Diplomatie verwalten

// c. Länder
// c5. Land - Verwandtschaft verwalten

// c. Länder
// c6. Land - Informationen vewalten

// c. Länder
// c7. Land - Familien verwalten



        $page->output_footer();
        exit;
    } // Ende der Prüfung, ob das richtige Modul aktiv ist
} // Ende der Admin-Funktion
