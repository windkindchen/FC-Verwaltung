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
	        "description"	=> "Dieser Plugin erlaubt die Verwaltung von L&auml;ndern inkl. Erstellung, Diplomatie, Gesuchen etc..",
		"website"		=> "https://epic.quodvide.de/member.php?action=profile&uid=75",
		"author"		=> "#rivers @ EPIC [May-Britt Thie&szlig;en]",
		"authorsite"	=> "https://epic.quodvide.de/member.php?action=profile&uid=75",
	        "version"		=> "2.0",
	        "guid" 			=> "",
	        "codename"		=> "",
	        "compatibility" => "18*"
	    );
	}
