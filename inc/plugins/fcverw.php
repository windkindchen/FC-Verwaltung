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
require_once MYBB_ROOT.'inc/class_parser.php';



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
### // c. Länder  Allgemein     ###
###################################


$plugins->add_hook('admin_load', 'fcverw_admin');
function fcverw_admin()
{
    global $mybb, $db, $lang, $page, $action_file, $run_module, $form, $parser;
    
    if ($page->active_action != 'fcverw')
    {
   	    return false;
    }
    
    
    // Parser für Texte
    $parser = new postParser;
    // Do something, for example I'll create a page using the hello_world_template
    $options = array(
        "allow_html" => 1,
        "allow_mycode" => 1,
        "allow_smilies" => 1,
        "allow_imgcode" => 1,
        "filter_badwords" => 0,
        "nl2br" => 1,
        "allow_videocode" => 1
    );
    

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
  			<b>Allgemein</b>: <img src="fcverw/frei.png"> Land frei; <img src="fcverw/vergeben.png"> Land vergeben; <img src="fcverw/error.png"> Land vergeben ohne Verantwortlichen<br /><br />
            <b>L&auml;nderinformation</b>: <img src="fcverw/frei.png"> L&auml;nderinfo freigegeben ; <img src="fcverw/vergeben.png"> keine L&auml;nderinfo; <img src="fcverw/error.png"> L&auml;nderinfo muss freigegeben werden'
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
        require_once MYBB_ROOT.'inc/plugins/fcverw/fcverw_adminKontinente.php';
                    
        
        // b. Regionen
        require_once MYBB_ROOT.'inc/plugins/fcverw/fcverw_adminRegionen.php';
        
        
        // c. Länder - Allgemein
        require_once MYBB_ROOT.'inc/plugins/fcverw/fcverw_adminLaender.php';
        
        
        // d. Länder - Allgemein
        require_once MYBB_ROOT.'inc/plugins/fcverw/fcverw_adminLandinfo.php';




        $page->output_footer();
        exit;
    } // Ende der Prüfung, ob das richtige Modul aktiv ist
} // Ende der Admin-Funktion
