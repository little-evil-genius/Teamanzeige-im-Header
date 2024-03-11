<?php
// Direktzugriff auf die Datei aus Sicherheitsgründen sperren
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// HOOKS
$plugins->add_hook("global_intermediate", "teamheader_global");

// Die Informationen, die im Pluginmanager angezeigt werden
function teamheader_info(){
	return array(
		"name"		=> "Teamanzeige im Header",
		"description"	=> "Listet die Teammitglieder im Header mit ihrer letzter Aktivität auf. Auf Wunsch kann angezeigt werden, dass das Teammitglied abwesend oder offline ist, statt der letzten Aktivität.",
		"website"	=> "https://github.com/little-evil-genius/Teamanzeige-im-Header",
		"author"	=> "little.evil.genius",
		"authorsite"	=> "https://storming-gates.de/member.php?action=profile&uid=1712",
		"version"	=> "1.1.1",
		"compatibility" => "18*"
	);
}
 
// Diese Funktion wird aufgerufen, wenn das Plugin installiert wird (optional).
function teamheader_install(){
    global $db, $cache, $mybb;

    // EINSTELLUNGEN 
    $setting_group = array(
        'name' => 'teamheader',
        'title' => 'Teamanzeige im Header',
        'description' => 'Einstellungen für die Teamanzeige im Header',
        'disporder' => 5, // The order your setting group will display
        'isdefault' => 0  
    );

    $gid = $db->insert_query("settinggroups", $setting_group);
    $setting_array = array(

        // Default Avatar
        'teamheader_default_avatar' => array(
            'title' => 'Standard-Avatar',
            'description' => 'Wie heißt die Bilddatei für die Standard-Avatare? Diese Grafik wird falls ein Teammitglied noch kein Avatar hochgeladen hat stattdessen angezeigt. Damit der Avatar für jedes Design angepasst wird, sollte der Dateiname in allen Designs gleich sein.',
            'optionscode' => 'text',
            'value' => 'default_avatar.png', // Default
            'disporder' => 1
        ),

        // Gäste
        'teamheader_guest' => array(
            'title' => 'Gäste Ansicht',
            'description' => 'Sollen die Teamavatar vor Gästen versteckt werden? Statt dem Avatar wird der festgelegte Standard-Avatar angezeigt.',
            'optionscode' => 'yesno',
            'value' => '1', // Default
            'disporder' => 2
        ),
       
        // Profilfeld
        'teamheader_name_fid' => array(
            'title' => 'Spielername-ID',
            'description' => 'Wie lautet die FID von dem Profilfeld, wo der Spielername gespeichert wird?',
            'optionscode' => 'text',
            'value' => '5', // Default
            'disporder' => 3
        ),
     
        // Teamuser
        'teamheader_users' => array(
            'title' => 'Teamuser',
            'description' => 'Welcher Uids haben die Hauptcharakter der Teammitglieder? In der Reihenfolge wie die Uids angegeben werden, werden die Teammitglieder auch angezeigt im Header.',
            'optionscode' => 'text',
            'value' => '2, 3, 4',
            'disporder' => 4
        ),
     
        // Zuletzt online Anzeige
        'teamheader_lastvisit' => array(
            'title' => 'Zuletzt online-Anzeige',
            'description' => 'Soll angezeigt werden, wann das Teammitglied zuletzt online war? Wenn nein, dann wird angezeigt, dass dieses Teamie aktuell offline ist.',
            'optionscode' => 'yesno',
            'value' => '1', // Default
            'disporder' => 5
        ),
     
        // Grauer Teamavatar
        'teamheader_greyava' => array(
            'title' => 'Grauer Teamavatar',
            'description' => 'Soll der Avatar vom Teammitglied grau sein in der Headeranzeige, wenn das Teammitglied offline ist?',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 6
        ),
     
        // Abwesenheitsnotiz
        'teamheader_away' => array(
            'title' => 'Abwesenheitsnotiz',
            'description' => 'Soll angezeigt werden, wenn ein Teammitglied abwesend ist? Sprich sich über die Abwesenheitsfunktion im Profil abmeldet.',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 7
        ),
     
        // Abwesenheitsnotiz - Bis wann
        'teamheader_awayreturn' => array(
            'title' => 'Abwesenheitsnotiz - Rückkehrdatum',
            'description' => 'Soll sogar angezeigt werden, bis wann das Teammitglied abwesend gemeldet ist?',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 8
        ),
    );
    
    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;
        $db->insert_query('settings', $setting);
    }

    // TEMPLATES ERSTELLEN
    // Template Gruppe für jedes Design erstellen
    $templategroup = array(
        "prefix" => "teamheader",
        "title" => $db->escape_string("Teamanzeige im Header"),
    );

    $db->insert_query("templategroups", $templategroup);

    // TEMPLATES ERSTELLEN
    $insert_array = array(
        'title'		=> 'teamheader',
        'template'	=> $db->escape_string('<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
        <tr class="tcat">
           <td colspan="2">Unsere Teammitglieder</td>
        </tr>
           {$teamheader_bit}
     </table>'),
        'sid'		=> '-2',
        'version'	=> '',
        'dateline'	=> TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'		=> 'teamheader_bit',
        'template'	=> $db->escape_string('<tr>
        <td>
            {$teamavatar}
        </td>
        <td>
            {$spitzname} • {$charaname}
            <br>
            {$lastvisit}
        </td>   
   </tr>'),
        'sid'		=> '-2',
        'version'	=> '',
        'dateline'	=> TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

}
 
// Funktion zur Überprüfung des Installationsstatus; liefert true zurürck, wenn Plugin installiert, sonst false (optional).
function teamheader_is_installed(){
    global $mybb;

    if(isset($mybb->settings['teamheader_default_avatar']))
    {
        return true;
    }
    return false;
} 
 
// Diese Funktion wird aufgerufen, wenn das Plugin deinstalliert wird (optional).
function teamheader_uninstall(){
    global $db;
    
    // EINSTELLUNGEN LÖSCHEN
    $db->delete_query('settings', "name LIKE 'teamheader%'");
    $db->delete_query('settinggroups', "name = 'teamheader'");

    rebuild_settings();

    // TEMPLATGRUPPE LÖSCHEN
    $db->delete_query("templategroups", "prefix = 'teamheader'");

    // TEMPLATES LÖSCHEN
    $db->delete_query("templates", "title LIKE '%teamheader%'");
}
 
// Diese Funktion wird aufgerufen, wenn das Plugin aktiviert wird.
function teamheader_activate(){
    
    // VARIABLE EINFÜGEN
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('<div id="content">')."#i", '<div id="content">{$teamheader}');
}
 
// Diese Funktion wird aufgerufen, wenn das Plugin deaktiviert wird.
function teamheader_deactivate(){

    // VARIABLE ENTFERNEN
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$teamheader}')."#i", '', 0);
}

// THE MAGIC
function teamheader_global() {
    
    global $db, $mybb, $templates, $theme, $teamheader, $teamheader_bit;

    // EINSTELLUNGEN ZIEHEN
    // Spielername - Profilfeld
    $team_name_fid =  $mybb->settings['teamheader_name_fid'];
    $namefid = "fid".$team_name_fid;
    // Gäste
    $guest_setting =  $mybb->settings['teamheader_guest'];
    // Standard-Avatar
    $default_avatar =  $mybb->settings['teamheader_default_avatar'];
    // Zuletzt online Anzeige
    $lastvisit_setting = $mybb->settings['teamheader_lastvisit'];
    // Ab wann offline
    $timesearch = TIME_NOW - (int)$mybb->settings['wolcutoff'];
    // Grauer Teamavatar
    $greyava = $mybb->settings['teamheader_greyava'];
    // Teamuids
    $teamheader_users_setting = $mybb->settings['teamheader_users'];
    $teamheader_users = explode (", ", $teamheader_users_setting);
    // Abwesenheitsnotiz
    $away_setting = $mybb->settings['teamheader_away'];
    // Abwesenheitsnotiz - Bis wann
    $awayreturn_setting = $mybb->settings['teamheader_awayreturn'];

    foreach ($teamheader_users as $teamheader_user) {

        $teamheader_query = $db->query("SELECT * FROM ".TABLE_PREFIX."users u
        LEFT JOIN ".TABLE_PREFIX."sessions s
        ON s.uid = u.uid
        LEFT JOIN ".TABLE_PREFIX."userfields uf
        ON uf.ufid = u.uid 
        WHERE u.uid = '".$teamheader_user."' OR u.as_uid = '".$teamheader_user."'
        ORDER BY s.time DESC
        LIMIT 1
        ");

        while($team = $db->fetch_array($teamheader_query)) {

            // LEER LAUFEN LASSEN
            $charaname = "";
            $teamavatar = "";
            $lastvisit = "";
           
            // MIT INFOS FÜLLEN
            $username = format_name($team['username'], $team['usergroup'], $team['displaygroup']);
            // Charaid ziehen
			$teamid = $db->fetch_field($db->simple_select("users", "uid", "username = '".$team['username']."'"), "uid");
            $charaname = build_profile_link($username, $teamid);
            $spitzname = $team[$namefid];
           
            // Abwesenheit abfragen
            $useraway = $db->fetch_field($db->simple_select("users", "away", "uid = '{$teamid}'"), "away");
            // Wiederda Datum abfragen
            $userreturndate = $db->fetch_field($db->simple_select("users", "returndate", "uid = '{$teamid}'"), "returndate");

            // AVATARE
            // Einstellung für Gäste Avatare ausblenden
            if ($guest_setting == 1){
                // Gäste und kein Avatar - Standard-Avatar
                if ($mybb->user['uid'] == '0' || $team['avatar'] == '') {
                    // Offline Teamies sollen grauen Avatar bekommen
                    if ($greyava == 1){
                        // Abwesenheit soll angezeigt werden - Avatar bleibt grau die ganze Zeit grau
                        if ($away_setting == 1){
                            // Teamie ist abwesend - deswegen die ganze Zeit grauer Avatar
                            if ($useraway == "1") {
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            } 
                            // Teamie ist nicht abwesend - deswegen Avatar wird nur grau, wenn offline
                            else {
                                if($team['time'] > $timesearch) {
                                    $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                                } else {   
                                    $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                                }
                            }
                        }
                        // Abwesenheit soll nicht angezeigt werden - Avatar wird nur grau, wenn offline
                        else {
                            if($team['time'] > $timesearch) {
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                            } else {   
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            }
                        }
                    } 
                    // Offline Teamies sollen keinen grauen Avatar bekommen
                    else {
                        $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                    }
                }
                // User können Avatar sehen & Teamie hat ein Avatar drin 
                else {
                    // Offline Teamies sollen grauen Avatar bekommen
                    if ($greyava == 1){
                        // Abwesenheit soll angezeigt werden - Avatar bleibt grau die ganze Zeit grau
                        if ($away_setting == 1){
                            // Teamie ist abwesend - deswegen die ganze Zeit grauer Avatar
                            if ($useraway == "1") {
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            } 
                            // Teamie ist nicht abwesend - deswegen Avatar wird nur grau, wenn offline
                            else {
                                if($team['time'] > $timesearch) {
                                    $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                                } else {   
                                    $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                                }
                            }
                        }
                        // Abwesenheit soll nicht angezeigt werden - Avatar wird nur grau, wenn offline
                        else {
                            if($team['time'] > $timesearch) {
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                            } else {   
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            }
                        }
                    } 
                    // Offline Teamies sollen keinen grauen Avatar bekommen
                    else {
                        $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                    }  
                }
            }
            // Gäste dürfen den Avatar sehen  
            else {
                // kein Avatar - Standard-Avatar
                if ($team['avatar'] == '') {
                    // Offline Teamies sollen grauen Avatar bekommen
                    if ($greyava == 1){
                        // Abwesenheit soll angezeigt werden - Avatar bleibt grau die ganze Zeit grau
                        if ($away_setting == 1){
                            // Teamie ist abwesend - deswegen die ganze Zeit grauer Avatar
                            if ($useraway == "1") {
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            } 
                            // Teamie ist nicht abwesend - deswegen Avatar wird nur grau, wenn offline
                            else {
                                if($team['time'] > $timesearch) {
                                    $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                                } else {   
                                    $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                                }
                            }
                        }
                        // Abwesenheit soll nicht angezeigt werden - Avatar wird nur grau, wenn offline
                        else {
                            if($team['time'] > $timesearch) {
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                            } else {   
                                $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            }
                        }
                    } 
                    // Offline Teamies sollen keinen grauen Avatar bekommen
                    else {
                        $teamavatar = "<img src='{$theme['imgdir']}/{$default_avatar}' width='50px'>";
                    }
                } 
                // Besitzt ein eigenen Avatar
                else {
                    // Offline Teamies sollen grauen Avatar bekommen
                    if ($greyava == 1){
                        // Abwesenheit soll angezeigt werden - Avatar bleibt grau die ganze Zeit grau
                        if ($away_setting == 1){
                            // Teamie ist abwesend - deswegen die ganze Zeit grauer Avatar
                            if ($useraway == "1") {
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            } 
                            // Teamie ist nicht abwesend - deswegen Avatar wird nur grau, wenn offline
                            else {
                                if($team['time'] > $timesearch) {
                                    $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                                } else {   
                                    $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                                }
                            }
                        }
                        // Abwesenheit soll nicht angezeigt werden - Avatar wird nur grau, wenn offline
                        else {
                            if($team['time'] > $timesearch) {
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                            } else {   
                                $teamavatar = "<img src='{$team['avatar']}' width='50px'style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\">";
                            }
                        }
                    } 
                    // Offline Teamies sollen keinen grauen Avatar bekommen
                    else {
                        $teamavatar = "<img src='{$team['avatar']}' width='50px'>";
                    }
                }
            }

            // ZULETZT ANZEIGE UND ABWESENHEIT 
            // Zuletzt online anzeigen - Zeitanzeige
            if ($lastvisit_setting == 1){
                // Abwesenheit soll angezeigt werden - ersetzt die zuletzt online Anzeige
                if ($away_setting == 1){
                    // Teamie ist abwesend - deswegen Abwesenheitsmeldung
                    if ($useraway == "1") {
                        // Wiederda-Datum soll angezeigt werden
                        if ($awayreturn_setting == 1) {
                            if($userreturndate == '') {
                                $wiederda = "";
                            } else {
                                $returnhome = explode("-", $userreturndate);
                                $wiederdamk = mktime(0, 0, 0, $returnhome[1], $returnhome[0], $returnhome[2]);
                                $wiederdate = my_date($mybb->settings['dateformat'], $wiederdamk);
                                $wiederda = " Bis zum: ".$wiederdate;
                            }
    
                            $lastvisit = "ist aktuell abwesend!".$wiederda;
                        } 
                        // Kein Wiederda-Datum
                        else {
                            $lastvisit = "ist aktuell abwesend!";
                        }
                    }
                    // Teamie ist nicht abwesen - deswegen normale zuletzt online Anzeige
                    else {
                        if($team['time'] > $timesearch) {
                            $lastvisittime = my_date('relative', $team['time']);
                            $lastvisit = "Zuletzt online: ".$lastvisittime;
                        } else {
                            $lastvisittime = my_date('relative', $team['lastactive']);
                            $lastvisit = "Zuletzt online: ".$lastvisittime;
                        }
                    }
                } 
                // Abwesenheit soll nicht angezeigt werden - normale Zuletzt online anzeige
                else {
                    if($team['time'] > $timesearch) {
                        $lastvisittime = my_date('relative', $team['time']);
                        $lastvisit = "Zuletzt online: ".$lastvisittime;
                    } else {
                        $lastvisittime = my_date('relative', $team['lastactive']);
                        $lastvisit = "Zuletzt online: ".$lastvisittime;
                    }
                }
            } 
            // Offline Anzeige
            else {
                // Abwesenheit soll angezeigt werden - ersetzt die offline Anzeige
                if ($away_setting == 1){
                    // Teamie ist abwesend - deswegen Abwesenheitsmeldung
                    if ($useraway == "1") {
                        // Wiederda-Datum soll angezeigt werden
                        if ($awayreturn_setting == 1) {
                            if($userreturndate == '') {
                                $wiederda = "";
                            } else {
                                $returnhome = explode("-", $userreturndate);
                                $wiederdamk = mktime(0, 0, 0, $returnhome[1], $returnhome[0], $returnhome[2]);
                                $wiederdate = my_date($mybb->settings['dateformat'], $wiederdamk);
                                $wiederda = " Bis zum: ".$wiederdate;
                            }
    
                            $lastvisit = "ist aktuell abwesend!".$wiederda;
                        } 
                        // Kein Wiederda-Datum
                        else {
                            $lastvisit = "ist aktuell abwesend!";
                        }
                    }
                    // Teamie ist nicht abwesen - deswegen normale zuletzt online Anzeige
                    else {
                        if($team['time'] > $timesearch) {
                            if($team['time'] > $timesearch) {
                                $lastvisittime = my_date('relative', $team['time']);
                                $lastvisit = "Zuletzt online: ".$lastvisittime;
                            } else {
                                $lastvisittime = my_date('relative', $team['lastactive']);
                                $lastvisit = "Zuletzt online: ".$lastvisittime;
                            }
                        } else {
                            $lastvisit = "ist aktuell offline!";
                        }
                    }
                } 
                // Abwesenheit soll nicht angezeigt werden - normale Zuletzt online anzeige
                else {
                    if($team['time'] > $timesearch) {
                        if($team['time'] > $timesearch) {
                            $lastvisittime = my_date('relative', $team['time']);
                            $lastvisit = "Zuletzt online: ".$lastvisittime;
                        } else {
                            $lastvisittime = my_date('relative', $team['lastactive']);
                            $lastvisit = "Zuletzt online: ".$lastvisittime;
                        }
                    } else {
                        $lastvisit = "ist aktuell offline!";
                    }
                }
            }
            
            eval('$teamheader_bit .= "'.$templates->get('teamheader_bit').'";');
        }
        
    }
    eval('$teamheader = "'.$templates->get('teamheader').'";');
}
