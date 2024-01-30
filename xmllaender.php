<?php
/**
 * MyBB 1.8
 * Copyright 2014 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybb.com
 * License: http://www.mybb.com/about/license
 *
 */

/**
 * The deal with this file is that it handles all of the XML HTTP Requests for MyBB.
 *
 * It contains a stripped down version of the MyBB core which does not load things
 * such as themes, who's online data, all of the language packs and more.
 *
 * This is done to make response times when using XML HTTP Requests faster and
 * less intense on the server.
 */

    define("IN_MYBB", 1);
    
    // We don't want visits here showing up on the Who's Online
    define("NO_ONLINE", 1);
    
    define('THIS_SCRIPT', 'xmlhttp.php');
    
    // Load MyBB core files
    require_once dirname(__FILE__)."/inc/init.php";

    
    // Send no cache headers
    header("Expires: Sat, 1 Jan 2000 01:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    
    // Create the session
    require_once MYBB_ROOT."inc/class_session.php";
    $session = new session;
    $session->init();

    if($mybb->input['action'] == "get_countries")
    {
        $mybb->input['query'] = ltrim($mybb->get_input('query'));
        $search_type = $mybb->get_input('search_type', MyBB::INPUT_INT); // 0: starts with, 1: ends with, 2: contains
    
        // If the string is less than 2 characters, quit.
        if(my_strlen($mybb->input['query']) < 2)
        {
            exit;
        }
    
        if($mybb->get_input('getone', MyBB::INPUT_INT) == 1)
        {
            $limit = 1;
        }
        else
        {
            $limit = 15;
        }
    
        // Send our headers.
        header("Content-type: application/json; charset={$charset}");
    
        // Query for any matching users.
        $query_options = array(
            "order_by" => "lname",
            "order_dir" => "asc",
            "limit_start" => 0,
            "limit" => $limit
        );
    
        $plugins->run_hooks("xmlhttp_get_countries_start");
    
        $likestring = $db->escape_string_like($mybb->input['query']);
        if($search_type == 1)
        {
            $likestring = '%'.$likestring;
        }
        elseif($search_type == 2)
        {
            $likestring = '%'.$likestring.'%';
        }
        else
        {
            $likestring .= '%';
        }
    
        $query = $db->simple_select("laender", "landid, lname", "lname LIKE '{$likestring}'", $query_options);
        if($limit == 1)
        {
            $user = $db->fetch_array($query);
            $data = array('landid' => $user['landid'], 'id' => $user['lname'], 'text' => $user['lname']);
        }
        else
        {
            $data = array();
            while($user = $db->fetch_array($query))
            {
                $data[] = array('landid' => $user['landid'], 'id' => $user['lname'], 'text' => $user['lname']);
            }
        }
    
        $plugins->run_hooks("xmlhttp_get_countries_end");

       echo json_encode($data);
       exit;
    }
