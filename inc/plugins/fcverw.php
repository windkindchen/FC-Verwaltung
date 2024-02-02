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
          Part 00: Get Global
   *********************************************** */

function fcverw_KonReg()
{
    global $db, $mybb, $konreg;
    
    $konreg  = $db->write_query("
        SELECT 
            r.rid, r.rname, r.rkid, k.kname, k.kid
        FROM 
            ".TABLE_PREFIX."laender_regionen r 
        LEFT JOIN 
            ".TABLE_PREFIX."laender_kontinente k 
        ON 
            k.kid = r.rkid 
        ORDER BY 
            k.kname, r.rname
    ");
        
    return $konreg;
}


function fcverw_LandList($rid)
{
    global $db, $mybb, $landliste;
    
    $landliste = $db->query("
        WITH RECURSIVE 
            LandListe
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
                Ebene, 
                Path
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
                0 AS Ebene, 
                CAST(lname AS CHAR(2000))
            FROM 
                ".TABLE_PREFIX."laender
            WHERE 
                lrid = '".$rid."'
                    AND 
                lparent IS NULL 
            
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
                la.Ebene + 1, 
                CONCAT(la.path, ',', l.lname)
            FROM 
                LandListe AS la 
            JOIN 
                ".TABLE_PREFIX."laender AS l
            ON 
                la.landid = l.lparent
        )
        SELECT 
            * 
        FROM 
            LandListe 
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
            $form_container->output_row_header('Regionenname', array("class" => "align_center", "width" => "25%"));
            $form_container->output_row_header('Beschreibung', array("class" => "align_center"));
            $form_container->output_row_header('L&auml;nder', array("class" => "align_center", "width" => "5%"));
            $form_container->output_row_header('Optionen', array("class" => "align_center", "width" => "15%"));

            // Hier werden die Regionen ausgelesen
            $fc_regsel = fcverw_KonReg();
                
                
            while ($row = $db->fetch_array($fc_regsel))
            {
                // Auslesen der Anzahl der Regionen und Länder
                $laender = $db->num_rows($db->simple_select("laender", "landid", "lrid = ".$row['rid']));

                $form_container->output_cell($row['rid'], array("class" => "align_center"));
                $form_container->output_cell("[".$row['kname']."] <b>".$row['rname']."</b>");
                $form_container->output_cell($row['rbeschr']);
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
        } // Ende Editieren Region


// b. Regionen
// b4. Region löschen
        if ($mybb->input['action'] == "del_region")
        {
            $rid = (int)$mybb->input['rid'];

            // Länder updaten
            $update_laender = array(
                'lrid' => "0"
            );
            $db->update_query("laender", $update_laender, "lrid = ".$rid);

            // Eintrag löschen
            if ($db->delete_query("laender_regionen", "rid = ".$rid))
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
            $form_container = new FormContainer('Alle L&auml;nder');
            
            $form_container->output_row_header('ID', array("class" => "align_center", "width" => "2%"));
            $form_container->output_row_header('Landname', array("class" => "align_center", "colspan" => "2"));
            $form_container->output_row_header('Landart', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('L&auml;nderinfos', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Diplomatie', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Verwandtschaften', array("class" => "align_center", "width" => "15%"));
            $form_container->output_row_header('Bewohner', array("class" => "align_center", "width" => "15%"));
            
            // Zunächst Kontinente und Regionen auslesen - um dann die jeweiligen Länder und Unterländer zu bekommen.
            $select_query = fcverw_KonReg();
            
            while ($data = $db->fetch_array($select_query))
            {
                // Prüfen, ob überhaupt Ausgabe erforderlich
                $lands = $db->simple_select("laender", "*", "lrid = ".$data['rid']." AND lstat = '0'", array('order_by' => 'lname'));
                
                if ($db->num_rows($lands) > '0')
                {
                    $form_container->output_cell($data['kname'].' &raquo; <b>'.$data['rname'].'</b>', array("colspan" => "8"));
                    $form_container->construct_row(); // Reihe erstellen
                    
                    // Funktion der Länderauflistung aufrufen und nutzen
                    $query = fcverw_LandList($data['rid']);
                    
                    while ($landdata = $db->fetch_array($query))
                    {
                        $trenner = str_repeat("-", $landdata['Ebene']);
                        
                        // Prüfen, ob Land bespielt
                        if ($landdata['lbesp'] == '1')
                        {
                            // Prüfen, ob Spieler noch da
                            $use = $db->simple_select("users", "username", "uid = ".$landdata['lverantw']);
                            $count = $db->num_rows($use);
                                
                            if ($count == '1')
                            {
                                $image = '<img src="fcverw/vergeben.png" />';
                            } 
                            else
                            {
                                $image = '<img src="fcverw/error.png" />';
                            } 
                        }
                        else 
                        {
                            $image = '<img src="fcverw/frei.png" />';
                        }
                        
                        $form_container->output_cell($landdata['landid'], array("class" => "align_center"));
                        $form_container->output_cell($image, array("width" => "1%"));
                        $form_container->output_cell($trenner." ".$landdata['lname']." (".$landdata['lart'].")");
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->output_cell('ID');
                        $form_container->construct_row(); // Reihe erstellen
                    }
                    
                }
            } 
            $form_container->end();
            $form->end();
        } // Ende der Startseite



// c. Länder
// c2. Land anlegen

        if ($mybb->input['action'] == "add_land")
        {
            // Wenn alle Pflichtangaben abgeschickt wurden, dann eintragen
            if ($mybb->request_method == 'post' && $mybb->input['lname'] != '' && $mybb->input['lrid']!= '0' && ($mybb->input['lstat'] == '0' || ($mybb->input['lstat'] == '1' && $mybb->input['lparent'] != '0')))
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
                if (($mybb->input['lstat'] == '1' && $mybb->input['lparent'] == '0') && $mybb->request_method == 'post')
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
                $reg = fcverw_KonReg();
                while ($regdata = $db->fetch_array($reg))
                {
                    //Regionen definieren für Variante 1 (nicht untergeordnet)
                    $regionen[$regdata['rid']] = "[".$regdata['kname']."] ".$regdata['rname'];
                    
                    // Angelegte Länder definieren für Variante 2 (untergeordnet)
                    // Aufruf Funktion für die Darstellung des Landes
                    $query = fcverw_LandList($regdata['rid']);
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


// c. Länder
// c5. Land - Diplomatie verwalten

// c. Länder
// c6. Land - Verwandtschaft verwalten

// c. Länder
// c7. Land - Informationen vewalten

// c. Länder
// c8. Land - Familien verwalten


        $page->output_footer();
        exit;
    } // Ende der Prüfung, ob das richtige Modul aktiv ist
} // Ende der Admin-Funktion
