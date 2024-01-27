<?php
	// Disallow direct access to this file for security reasons
	if(!defined("IN_MYBB"))
	{
	    die("Direct initialization of this file is not allowed.");
	}


	//hooks
		$plugins->add_hook('misc_start', 'fcverw');
	 	$plugins->add_hook('global_intermediate', 'fcverw_header');
	// $plugins->add_hook('global_intermediate', 'reservieren_alert_global');

	function fcverw_info()
	{
	    return array(
	        "name"			=> "L&auml;nderverwaltung",
	        "description"	=> "Dieser Plugin erlaubt die Verwaltung von L&auml;ndern inkl. Erstellung, Diplomatie, Informationen.",
	        "website"		=> "",
	        "author"		=> "May/winterkind",
	        "authorsite"	=> "",
	        "version"		=> "2.0",
	        "guid" 			=> "",
	        "codename"		=> "",
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
        `dipcom` text NOT NULL,
        `diptime` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`dipid`)
      )
      ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
      CREATE TABLE ".TABLE_PREFIX."laender_info (
        `linfoid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
        `lid` int(15) NOT NULL,
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
        `kbeschr` text,
        PRIMARY KEY (`kid`)
      )
      ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
      CREATE TABLE ".TABLE_PREFIX."laender_regionen (
        `rid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
        `rkid` int(15) NOT NULL,
        `rname` varchar(255) NOT NULL,
        `kbeschr` text,
        PRIMARY KEY (`rid`)
      )
      ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    $db->write_query("
      CREATE TABLE ".TABLE_PREFIX."laender_verwandt (
        `lvid` int(15) UNSIGNED NOT NULL AUTO_INCREMENT,
        `landid` int(15) NOT NULL,
        `verwid` int(15) NOT NULL,
        `lvbeschr` text,
        PRIMARY KEY (`lvid`)
      )
      ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci AUTO_INCREMENT=1
    ");

    // Erstellen der Einstellungen

    // Erstellen der templates

    // Erstellen des CSS
  }

  function fcverw_is_installed()
  {
    global $db;

    if ($db->table_exists("laender"))
    {
      return true;
    }
  }


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


    // Löschen der Einstellungen

    // Löschen der templates

    // Löschen des CSS
  }


  function fcverw_activate()
  {
    global $db, $cache;

    // Links einbinden etc.

  }


  function fcverw_deactivate()
  {
    global $db, $cache;

    // Änderungen an Templates rückgängig machen
  }


/* ********************************************
            Let the magic begin
******************************************** */
