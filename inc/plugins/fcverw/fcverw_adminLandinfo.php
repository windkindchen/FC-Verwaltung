<?php
    // Disallow direct access to this file for security reasons
    if (!defined('IN_MYBB')) {
        die('Direct initialization of this file is not allowed.<br /><br />
            Please make sure IN_MYBB is defined.');
    }


/* *******************************************************************************************************************************************************************
       Inhalt Dokument d. Länderinfos 
******************************************************************************************************************************************************************* */

    // 0. Unabhängige Variablen
    // 1. show_landinfo
    // 2. add_landinfo
    // 3. control_landinfo
    // 4. free_landinfo
    // 5. new_landinfo
    // 6. vergl_landinfo
    // 7. edit_landinfo



/* *******************************************************************************************************************************************************************
       d0. Unabhängige Variablen
******************************************************************************************************************************************************************* */

    $themen = array(
        "allgemein" => "Allgemeine Informationen",
        "hauptstadt" => "Hauptstadt",
        "sprache" => "Amtssprache und verbreitete Sprachen",
        "royal" => "K&ouml;nigliche Familie und Thronfolge",
        "regierung" => "Landespolitik",
        "diplomatie" => "Diplomatische Grunds&auml;tze",
        "volk" => "Bev&ouml;lkerungsdaten",
        "religion" => "Religion und Glaube", 
        "einwanderung" => "Einwanderungspolitik",
        "wirtschaft" => "Wirtschaft",
        "militaer" => "Milit&auml;r",
        "medien" => "Medien und Pressefreiheit",
        "rebellen" => "Rebellen",
        "sonstiges" => "Sonstige Informationen"
    );




/* *******************************************************************************************************************************************************************
       d1. Aktuelle freigegebene Länderinformation anzeigen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'show_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        $archive = (int)$mybb->input['archive'];
        
        
        $archivet = "";
        if ($archive == '1')
        {
            $archivet = "_archive";
        }
        
        $select = $db->write_query("
            SELECT 
                li.*, l.lname, l.lart 
            FROM 
                ".TABLE_PREFIX."laender_info li 
            LEFT JOIN 
                ".TABLE_PREFIX."laender".$archivet." l 
            ON 
                li.landid = l.landid 
            WHERE 
                li.landid = ".$landid." 
                    AND 
                li.lifreigabe = '1' 
            ORDER BY 
                li.lidatum DESC 
            LIMIT 1
        ");
        
        $data = $db->fetch_array($select);
        
        // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
        $sub_tabs['show_landinfo'] = array(
            'title' => 'L&auml;nderinfo anzeigen',
            'link' => 'index.php?module=config-fcverw&amp;action=show_landinfo&amp;archive='.$archive.'&amp;landid='.$landid,
            'description' => 'Anzeige der aktuellsten, freigegebenen L&auml;nderinformation von <b>'.$data['lart'].' '.$data['lname'].'</b>.'
        );
        
        $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Aktuelle L&auml;nderbeschreibung anzeigen');
        $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Aktuelle L&auml;nderbeschreibung anzeigen');
        
        $page->output_nav_tabs($sub_tabs, 'show_landinfo');
        
        
        // Jetzt die Daten anzeigen
        $form = new Form("index.php?module=config-fcverw&amp;action=edit_kontinent", "post", "", 1);
        $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
        
        
        foreach ($themen AS $thema => $lititel)
        {
            $form_container->output_row(
                $lititel,
                '',
                '<div style="width: 75%; max-height: 300px; overflow: auto; margin-left: 60px; line-height: 1.6; padding: 10px;">'.nl2br($data[$thema]).'</div><br />'
            );
        }
        
        $form_container->end();
        $form->end();
        
        
    } // Ende Länderinfos anzeigen
    
    
    
    
/* *******************************************************************************************************************************************************************
       d2. Neue Länderinformation anlegen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'add_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        
        
        if ($mybb->request_method == 'post')
        {
            // Eintragen
            $insert = array();
            $insert['landid'] = $landid;
            
            foreach ($themen AS $thema => $lititel)
            {
                $insert[$thema] = $mybb->input[$thema];
            }
            
            if ($db->insert_query("laender_info", $insert))
            {
                redirect("admin/index.php?module=config-fcverw");
            }
            
        }
        else 
        {
            // Formular
            $select = $db->simple_select("laender", "lname, lart", "landid = ".$landid);
        
            $data = $db->fetch_array($select);
            
            // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
            $sub_tabs['add_landinfo'] = array(
                'title' => 'L&auml;nderinfo anlegen',
                'link' => 'index.php?module=config-fcverw&amp;action=add_landinfo&amp;landid='.$landid,
                'description' => 'Anlegen einer L&auml;nderinformation f&uuml;r: <b>'.$data['lart'].' '.$data['lname'].'</b>.'
            );
            
            $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Neue L&auml;nderinformation anlegen');
            $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Neue L&auml;nderinformation anlegen');
            
            $page->output_nav_tabs($sub_tabs, 'add_landinfo');
            
            
            // Jetzt die Daten anzeigen
            $form = new Form("index.php?module=config-fcverw&amp;action=add_landinfo", "post", "", 1);
            $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
            
            echo $form->generate_hidden_field('landid', $landid);
            
            foreach ($themen AS $thema => $lititel)
            {            
                $form_container->output_row(
                    $lititel,
                    $form->generate_text_area(
                        $thema,
                        $mybb->input[$thema],
                        array('style' => 'width: 80%; height: 300px;')
                    )
                );
            }
            
            
            $form_container->end();
            $button[] = $form->generate_submit_button('Anlegen');
            $form->output_submit_wrapper($button);
            $form->end();
            
        }
        
        
    } // Ende Länderinfos anzeigen



/* *******************************************************************************************************************************************************************
       d3. Aktuelle noch nicht freigegebene Länderinformation anzeigen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'control_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        
        // Wenn Formular abgesendet, dann ...
        if ($mybb->request_method == 'post')
        {
            $linfoid = (int)$mybb->input['linfoid'];
            
            $update = array("lifreigabe" => "1");
            
            if ($db->update_query("laender_info", $update, "linfoid = ".$linfoid))
            {
                redirect("admin/index.php?module=config-fcverw");
            }
        }
        else
        {
            $select = $db->write_query("
                SELECT 
                    li.*, l.lname, l.lart 
                FROM 
                    ".TABLE_PREFIX."laender_info li 
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    li.landid = l.landid 
                WHERE 
                    li.landid = ".$landid." 
                        AND 
                    li.lifreigabe = '0' 
                ORDER BY 
                    li.lidatum DESC 
                LIMIT 1
            ");
            
            $data = $db->fetch_array($select);
            
            // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
            $sub_tabs['control_landinfo'] = array(
                'title' => 'L&auml;nderinfo kontrollieren',
                'link' => 'index.php?module=config-fcverw&amp;action=control_landinfo&amp;landid='.$landid,
                'description' => 'Anzeige der aktuellsten, noch nicht freigegebenen L&auml;nderinformation von <b>'.$data['lart'].' '.$data['lname'].'</b>.'
            );
            
            $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Noch nicht freigegebene L&auml;nderbeschreibung anzeigen');
            $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Noch nicht freigegebene L&auml;nderbeschreibung anzeigen');
            
            $page->output_nav_tabs($sub_tabs, 'control_landinfo');
            
            
            // Jetzt die Daten anzeigen
            $form = new Form("index.php?module=config-fcverw&amp;action=control_landinfo", "post", "", 1);
            $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
            
            echo $form->generate_hidden_field('linfoid', $data['linfoid']);
            
            foreach ($themen AS $thema => $lititel)
            {
                $form_container->output_row(
                    $lititel,
                    '',
                    '<div style="width: 75%; max-height: 300px; overflow: auto; margin-left: 60px; line-height: 1.6; padding: 10px;">'.nl2br($data[$thema]).'</div><br />'
                );
            }
            
            $form_container->end();
            $button[] = $form->generate_submit_button('Freigeben');
            $form->output_submit_wrapper($button);
            $form->end();
        
        }
        
    } // Ende Länderinfos kontrollieren
    
    
    

/* *******************************************************************************************************************************************************************
       d4. Schnelle Freigabe
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'free_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $linfoid = (int)$mybb->input['linfoid'];
        
        $update = array("lifreigabe" => "1");
            
        if ($db->update_query("laender_info", $update, "linfoid = ".$linfoid))
        {
            redirect("admin/index.php?module=config-fcverw");
        }
       
        
    } // Ende schnell freigeben
    
    


/* *******************************************************************************************************************************************************************
       d5. Neue Version
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'new_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        
        
        if ($mybb->request_method == 'post')
        {
            $landid = (int)$mybb->input['landid'];
            
            // Eintragen
            $insert = array();
            $insert['landid'] = $landid;
            
            foreach ($themen AS $thema => $lititel)
            {
                $insert[$thema] = $mybb->input[$thema];
            }
            
            if ($db->insert_query("laender_info", $insert))
            {
                redirect("admin/index.php?module=config-fcverw");
            }
        }
        else
        {
            
            $select = $db->write_query("
                SELECT 
                    li.*, l.lname, l.lart 
                FROM 
                    ".TABLE_PREFIX."laender_info li 
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    li.landid = l.landid 
                WHERE 
                    li.landid = ".$landid." 
                        AND 
                    li.lifreigabe = '1' 
                ORDER BY 
                    li.lidatum DESC 
                LIMIT 1
            ");
            
            $data = $db->fetch_array($select);
            
            // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
            $sub_tabs['new_landinfo'] = array(
                'title' => 'L&auml;nderinfo - Neue Version',
                'link' => 'index.php?module=config-fcverw&amp;action=new_landinfo&amp;landid='.$landid,
                'description' => 'Erstellung einer neuen Version der L&auml;nderinformation von <b>'.$data['lart'].' '.$data['lname'].'</b>.'
            );
            
            $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Neue Version der L&auml;nderbeschreibung anlegen');
            $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Neue Version der L&auml;nderbeschreibung anlegen');
            
            $page->output_nav_tabs($sub_tabs, 'new_landinfo');
            
            
            // Jetzt die Daten anzeigen
            $form = new Form("index.php?module=config-fcverw&amp;action=new_landinfo", "post", "", 1);
            $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
            
            echo $form->generate_hidden_field('landid', $landid);
            
            foreach ($themen AS $thema => $lititel)
            {
                $form_container->output_row(
                    $lititel,
                    $form->generate_text_area(
                        $thema,
                        $data[$thema],
                        array('style' => 'width: 80%; height: 300px;')
                    )
                );
            }
            
            $form_container->end();
            $button[] = $form->generate_submit_button('Neue Version einreichen');
            $form->output_submit_wrapper($button);
            $form->end();
            
        }
        
       
    } // Ende neue Version Länderinfo anlegen
    
    
    

/* *******************************************************************************************************************************************************************
       d6. Alte Version und neue Version vergleichen
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'vergl_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        
        // Aktive raussuchen
        $select = $db->write_query("
            SELECT 
                li.*, l.lname, l.lart 
            FROM 
                ".TABLE_PREFIX."laender_info li 
            LEFT JOIN 
                ".TABLE_PREFIX."laender".$archivet." l 
            ON 
                li.landid = l.landid 
            WHERE 
                li.landid = ".$landid." 
                    AND 
                li.lifreigabe = '1' 
            ORDER BY 
                li.lidatum DESC 
            LIMIT 1
        ");
        
        // Neue raussuchen
        $select2 = $db->simple_select("laender_info", "*", "lifreigabe = '0' AND landid = ".$landid, array("order_by" => "lidatum DESC", "limit" => "1"));
        
        $data = $db->fetch_array($select);
        $data2 = $db->fetch_array($select2);
        
        
        // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
        $sub_tabs['vergl_landinfo'] = array(
            'title' => 'L&auml;nderinfo vergleichen',
            'link' => 'index.php?module=config-fcverw&amp;action=show_landinfo&amp;landid='.$landid,
            'description' => 'Vergleich der letzten freigegebenen Version und der neusten, noch freizugebenden Version der L&auml;nderinfo von <b>'.$data['lart'].' '.$data['lname'].'</b>.'
        );
        
        $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Vergleich der L&auml;nderbeschreibung');
        $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Vergleich der L&auml;nderbeschreibung');
        
        $page->output_nav_tabs($sub_tabs, 'vergl_landinfo');
        
        
        // Jetzt die Daten anzeigen
        $form = new Form("index.php?module=config-fcverw&amp;action=vergl_landinfo", "post", "", 1);
        $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
        
        $form_container->output_row_header('<b>Alte Version</b>', array("width" => "50%", "class" => "align_center"));
        $form_container->output_row_header('<b>Neue Version</b>', array("class" => "align_center"));
        
        
        foreach ($themen AS $thema => $lititel)
        {
            $diff = str_word_count($data2[$thema]) - str_word_count($data[$thema]);
            
            $form_container->output_cell('<b>'.$lititel.'</b> ('.$diff.' W&ouml;rter Unterschied)', array("colspan" => "2", "class" => "align_center"));
            $form_container->construct_row();
            
            
            $form_container->output_cell('<div style="width: 95%; max-height: 300px; overflow: auto; line-height: 1.6; padding: 10px;">'.nl2br($data[$thema]).'</div>');
            $form_container->output_cell('<div style="width: 95%; max-height: 300px; overflow: auto; line-height: 1.6; padding: 10px;">'.nl2br($data2[$thema]).'</div>');
            $form_container->construct_row();
            
        }
        
        $form_container->end();
        $form->end();
       
    } // Ende Versionen vergleichen



/* *******************************************************************************************************************************************************************
       d7. Aktuelle Arbeitsversion editieren
******************************************************************************************************************************************************************* */

    if ($mybb->input['action'] == 'edit_landinfo')
    {
        // Daten der aktuell freigegebenen Version auslesen
        $landid = (int)$mybb->input['landid'];
        
        
        if ($mybb->request_method == 'post')
        {
            $linfoid = (int)$mybb->input['linfoid'];
            
            // Eintragen
            $update = array();
            
            foreach ($themen AS $thema => $lititel)
            {
                $update[$thema] = $mybb->input[$thema];
            }
            
            if ($db->update_query("laender_info", $update, "linfoid = ".$linfoid))
            {
                redirect("admin/index.php?module=config-fcverw");
            }
        }
        else
        {
            
            $select = $db->write_query("
                SELECT 
                    li.*, l.lname, l.lart 
                FROM 
                    ".TABLE_PREFIX."laender_info li 
                LEFT JOIN 
                    ".TABLE_PREFIX."laender l 
                ON 
                    li.landid = l.landid 
                WHERE 
                    li.landid = ".$landid." 
                        AND 
                    li.lifreigabe = '0' 
                ORDER BY 
                    li.lidatum DESC 
                LIMIT 1
            ");
            
            $data = $db->fetch_array($select);
            
            // Neues Tab kreieren, das nur während des Anzeigens vorhanden ist.
            $sub_tabs['edit_landinfo'] = array(
                'title' => 'L&auml;nderinfo - Arbeitsversion editieren',
                'link' => 'index.php?module=config-fcverw&amp;action=edit_landinfo&amp;landid='.$landid,
                'description' => 'Editieren der Arbeitsversion der L&auml;nderinformation von <b>'.$data['lart'].' '.$data['lname'].'</b>.'
            );
            
            $page->add_breadcrumb_item('['.$data['lart'].' '.$data['lname'].'] Editierung der Arbeitsversion L&auml;nderbeschreibung');
            $page->output_header('L&auml;nderverwaltung - ['.$data['lart'].' '.$data['lname'].'] Editierung der Arbeitsversion L&auml;nderbeschreibung');
            
            $page->output_nav_tabs($sub_tabs, 'edit_landinfo');
            
            
            // Jetzt die Daten anzeigen
            $form = new Form("index.php?module=config-fcverw&amp;action=edit_landinfo", "post", "", 1);
            $form_container = new FormContainer('L&auml;nderinformation von '.$data['lart'].' '.$data['lname'].'</b>.');
            
            echo $form->generate_hidden_field('linfoid', $data['linfoid']);
            
            foreach ($themen AS $thema => $lititel)
            {
                $form_container->output_row(
                    $lititel,
                    $form->generate_text_area(
                        $thema,
                        $data[$thema],
                        array('style' => 'width: 80%; height: 300px;')
                    )
                );
            }
            
            $form_container->end();
            $button[] = $form->generate_submit_button('Arbeitsversion bearbeiten');
            $form->output_submit_wrapper($button);
            $form->end();
            
        }
        
       
    } // Ende neue Version Länderinfo anlegen
