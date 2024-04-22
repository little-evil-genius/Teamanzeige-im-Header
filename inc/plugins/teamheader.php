<?php
//error_reporting ( -1 );
//ini_set ( 'display_errors', true );
// Direktzugriff auf die Datei aus Sicherheitsgründen sperren
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// HOOKS
$plugins->add_hook('admin_config_settings_change', 'teamheader_settings_change');
$plugins->add_hook('admin_settings_print_peekers', 'teamheader_settings_peek');
$plugins->add_hook("global_intermediate", "teamheader_global");

// Die Informationen, die im Pluginmanager angezeigt werden
function teamheader_info(){
	return array(
		"name"		=> "Teamanzeige im Header",
		"description"	=> "Listet die Teammitglieder im Header mit ihrer letzter Aktivität auf. Auf Wunsch kann angezeigt werden, dass das Teammitglied abwesend oder offline ist, statt der letzten Aktivität.",
		"website"	=> "https://github.com/little-evil-genius/Teamanzeige-im-Header",
		"author"	=> "little.evil.genius",
		"authorsite"	=> "https://storming-gates.de/member.php?action=profile&uid=1712",
		"version"	=> "2.0",
		"compatibility" => "18*"
	);
}
 
// Diese Funktion wird aufgerufen, wenn das Plugin installiert wird (optional).
function teamheader_install(){

    global $db, $cache, $mybb;

    // Accountswitcher muss vorhanden sein
    if (!function_exists('accountswitcher_is_installed')) {
		flash_message("Das Plugin <a href=\"http://doylecc.altervista.org/bb/downloads.php?dlid=26&cat=2\" target=\"_blank\">\"Enhanced Account Switcher\"</a> muss installiert sein!", 'error');
		admin_redirect('index.php?module=config-plugins');
	}

    // EINSTELLUNGEN 
    $maxdisporder = $db->fetch_field($db->query("SELECT MAX(disporder) FROM ".TABLE_PREFIX."settinggroups"), "MAX(disporder)");
    $setting_group = array(
        'name' => 'teamheader',
        'title' => 'Teamanzeige im Header',
        'description' => 'Einstellungen für die Teamanzeige im Header',
        'disporder' => $maxdisporder+1, // The order your setting group will display
        'isdefault' => 0  
    );

    $gid = $db->insert_query("settinggroups", $setting_group);
    $setting_array = array(
        'teamheader_users' => array(
            'title' => 'Teammitglieder',
            'description' => 'Welcher UIDs haben die Hauptcharakter der Teammitglieder? In der Reihenfolge wie die Uids angegeben werden, werden die Teammitglieder auch angezeigt im Header.',
            'optionscode' => 'text',
            'value' => '2, 3, 4',
            'disporder' => 1
        ),
		'teamheader_graphic' => array(
			'title' => 'Grafiktyp',
            'description' => 'Welche Grafik soll vom Teamie angezeigt werden? Zur Auswahl steht klassisch der Avatar, ein Element aus dem Uploadsystem von little.evil.genius, ein klassisches Profilfeld oder ein Feld aus dem Steckbriefplugin von risuena.',
            'optionscode' => 'select\n0=Avatar\n1=Upload-Element\n2=Profilfeld\n3=Steckbrieffeld',
            'value' => '0', // Default
            'disporder' => 2
		),
		'teamheader_graphic_uploadsystem' => array(
			'title' => 'Identifikator von dem Upload-Element',
            'description' => 'Wie lautet der Identifikator von dem Upload-Element, welches genutzt werden soll als Grafik vom zitierten Charakter?',
            'optionscode' => 'text',
            'value' => 'index', // Default
            'disporder' => 3
		),
		'teamheader_graphic_profilefield' => array(
			'title' => 'FID von dem Profilfeld',
            'description' => 'Wie lautet die FID von dem Profilfeld, welches genutzt werden soll als Grafikvom zitierten Charakter?',
            'optionscode' => 'numeric',
            'value' => '6', // Default
            'disporder' => 4
		),
		'teamheader_graphic_characterfield' => array(
			'title' => 'Identifikator von dem Steckbrieffeld',
            'description' => 'Wie lautet der Identifikator von dem Steckbrieffeld, welches genutzt werden soll als Grafik vom zitierten Charakter?',
            'optionscode' => 'text',
            'value' => 'index_pic', // Default
            'disporder' => 5
		),
		'teamheader_graphic_defaultgraphic' => array(
			'title' => 'Standard-Grafik',
            'description' => 'Wie heißt die Bilddatei für die Standard-Grafik? Diese Grafik wird, falls ein Charakter noch keine entsprechende Grafik besitzt oder es sich um ein gelöschten Charakter handelt, stattdessen angezeigt. Damit die Grafik für jedes Design angepasst wird, sollte der Dateiname in allen Ordner für die Designs gleich heißen.',
            'optionscode' => 'text',
            'value' => 'default_avatar.png', // Default
            'disporder' => 6
		),
        'teamheader_graphic_guest' => array(
            'title' => 'Gäste Ansicht',
            'description' => 'Sollen die Teamagrafiken vor Gästen versteckt werden? Statt dem Avatar wird der festgelegte Standard-Avatar angezeigt.',
            'optionscode' => 'yesno',
            'value' => '1', // Default
            'disporder' => 7
        ),
        'teamheader_playername' => array(
            'title' => 'Spielername-ID',
            'description' => 'Wie lautet die FID / der Identifikator von dem Profilfeld/Steckbrieffeld für den Spielernamen?<br>
            <b>Hinweis:</b> Bei klassischen Profilfeldern muss eine Zahl eintragen werden. Bei dem Steckbrief-Plugin von Risuena muss der Name/Identifikator des Felds eingetragen werden.',
            'optionscode' => 'text',
            'value' => '5', // Default
            'disporder' => 8
        ),
        'teamheader_lastvisit' => array(
            'title' => 'Zuletzt online-Anzeige',
            'description' => 'Soll angezeigt werden, wann das Teammitglied zuletzt online war? Wenn nein, dann wird angezeigt, dass dieses Teamie aktuell offline ist.',
            'optionscode' => 'yesno',
            'value' => '1', // Default
            'disporder' => 9
        ),
        'teamheader_greyava' => array(
            'title' => 'Grauer Teamavatar',
            'description' => 'Soll der Avatar vom Teammitglied grau sein in der Headeranzeige, wenn das Teammitglied offline ist?',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 10
        ),
        'teamheader_away' => array(
            'title' => 'Abwesenheitsnotiz',
            'description' => 'Soll angezeigt werden, wenn ein Teammitglied abwesend ist? Sprich sich über die Abwesenheitsfunktion im Profil abmeldet.',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 11
        ),
        'teamheader_awayreturn' => array(
            'title' => 'Abwesenheitsnotiz - Rückkehrdatum',
            'description' => 'Soll sogar angezeigt werden, bis wann das Teammitglied abwesend gemeldet ist?',
            'optionscode' => 'yesno',
            'value' => '0', // Default
            'disporder' => 12
        ),
    );
    
    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;
        $db->insert_query('settings', $setting);
    }
	rebuild_settings();

    // TEMPLATES ERSTELLEN
    // Template Gruppe für jedes Design erstellen
    $templategroup = array(
        "prefix" => "teamheader",
        "title" => $db->escape_string("Teamanzeige im Header"),
    );
    $db->insert_query("templategroups", $templategroup);

    $insert_array = array(
        'title'		=> 'teamheader',
        'template'	=> $db->escape_string('<div class="teamheader-container">
        <div class="teamheader-headline">Unsere Teammitglieder</div>
        <div class="teamheader-bit">
            {$teamheader_bit}
        </div>
    </div>'),
        'sid'		=> '-2',
        'version'	=> '',
        'dateline'	=> TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'		=> 'teamheader_bit',
        'template'	=> $db->escape_string('<div class="teamheader-bit_teamie">
        <div class="teamheader-bit_avatar">
            <img src="{$graphic_link}" {$grey_graphic}>
        </div>
        <div class="teamheader-bit_infos">
            {$playername} • {$charactername_link}
            <br>
            {$lastvisit}
        </div>
    </div>'),
        'sid'		=> '-2',
        'version'	=> '',
        'dateline'	=> TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    require_once MYBB_ADMIN_DIR."inc/functions_themes.php";

    // STYLESHEET HINZUFÜGEN
    $css = array(
        'name' => 'teamheader.css',
        'tid' => 1,
        'attachedto' => '',
        "stylesheet" => '.teamheader-container {
            background: #fff;
            width: 100%;
            margin: auto auto;
            border: 1px solid #ccc;
            padding: 1px;
            -moz-border-radius: 7px;
            -webkit-border-radius: 7px;
            border-radius: 7px;
            margin-bottom: 10px;
        }
        
        .teamheader-headline {
            background: #0066a2 url(../../../images/thead.png) top left repeat-x;
            color: #ffffff;
            border-bottom: 1px solid #263c30;
            padding: 8px;
            -moz-border-radius-topleft: 6px;
            -moz-border-radius-topright: 6px;
            -webkit-border-top-left-radius: 6px;
            -webkit-border-top-right-radius: 6px;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }
        
        .teamheader-bit {
            background: #f5f5f5;
            border: 1px solid;
            border-color: #fff #ddd #ddd #fff;
            -moz-border-radius-bottomleft: 6px;
            -webkit-border-bottom-left-radius: 6px;
            border-bottom-left-radius: 6px;
            -moz-border-radius-bottomright: 6px;
            -webkit-border-bottom-right-radius: 6px;
            border-bottom-right-radius: 6px;
            padding: 5px;
        }
        
        .teamheader-bit_teamie {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 10px;
        }
        
        .teamheader-bit_teamie:last-child {
            margin-bottom: 0;
        }
        
        .teamheader-bit_avatar {
            width: 12%;
        }
        
        .teamheader-bit_avatar img {
            width: 152px;
        }
        
        .teamheader-bit_infos {
            width: 87%;
        }',
        'cachefile' => $db->escape_string(str_replace('/', '', 'teamheader.css')),
        'lastmodified' => time()
    );
    
    $sid = $db->insert_query("themestylesheets", $css);
    $db->update_query("themestylesheets", array("cachefile" => "teamheader.css"), "sid = '".$sid."'", 1);

    $tids = $db->simple_select("themes", "tid");
    while($theme = $db->fetch_array($tids)) {
        update_theme_stylesheet_list($theme['tid']);
    }
}
 
// Funktion zur Überprüfung des Installationsstatus; liefert true zurürck, wenn Plugin installiert, sonst false (optional).
function teamheader_is_installed(){
    global $mybb;

    if(isset($mybb->settings['teamheader_awayreturn']))
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
    $db->delete_query("templates", "title LIKE 'teamheader%'");
}
 
// Diese Funktion wird aufgerufen, wenn das Plugin aktiviert wird.
function teamheader_activate(){
    
    // VARIABLE EINFÜGEN
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("header", "#".preg_quote('{$pm_notice}')."#i", '{$pm_notice}{$teamheader}');

}
 
// Diese Funktion wird aufgerufen, wenn das Plugin deaktiviert wird.
function teamheader_deactivate(){

    // VARIABLE ENTFERNEN
    include MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$teamheader}')."#i", '', 0);
}

#####################################
### THE BIG MAGIC - THE FUNCTIONS ###
#####################################

// ADMIN-CP PEEKER
function teamheader_settings_change(){
    
    global $db, $mybb, $teamheader_settings_peeker;

    $result = $db->simple_select('settinggroups', 'gid', "name='teamheader'", array("limit" => 1));
    $group = $db->fetch_array($result);
    $teamheader_settings_peeker = ($mybb->get_input('gid') == $group['gid']) && ($mybb->request_method != 'post');
}
function teamheader_settings_peek(&$peekers){

    global $teamheader_settings_peeker;

    if ($teamheader_settings_peeker) {
        $peekers[] = 'new Peeker($("#setting_teamheader_graphic"), $("#row_setting_teamheader_graphic_uploadsystem"),/^1/,false)';
        $peekers[] = 'new Peeker($("#setting_teamheader_graphic"), $("#row_setting_teamheader_graphic_profilefield"),/^2/,false)';
        $peekers[] = 'new Peeker($("#setting_teamheader_graphic"), $("#row_setting_teamheader_graphic_characterfield"),/^3/,false)';
        $peekers[] = 'new Peeker($(".setting_teamheader_away"), $("#row_setting_teamheader_awayreturn"),/1/,true)';
    }
}

// ANZEIGE
function teamheader_global() {
    
    global $db, $mybb, $templates, $theme, $lang, $teamheader, $teamheader_bit;
    
    // USER-ID
    $active_uid = $mybb->user['uid'];

    // SPRACHDATEI
    $lang->load("teamheader");

    // EINSTELLUNGEN ZIEHEN
    $playername_setting =  $mybb->settings['teamheader_playername'];
    $graphic_type = $mybb->settings['teamheader_graphic'];
	$graphic_uploadsystem = $mybb->settings['teamheader_graphic_uploadsystem'];
	$graphic_profilefield = $mybb->settings['teamheader_graphic_profilefield'];
	$graphic_characterfield = $mybb->settings['teamheader_graphic_characterfield'];
	$graphic_defaultgraphic = $mybb->settings['teamheader_graphic_defaultgraphic'];
	$graphic_guest = $mybb->settings['teamheader_graphic_guest'];
    $lastvisit_setting = $mybb->settings['teamheader_lastvisit'];
    $greyava = $mybb->settings['teamheader_greyava'];
    $away_setting = $mybb->settings['teamheader_away'];
    $awayreturn_setting = $mybb->settings['teamheader_awayreturn'];

    $timesearch = TIME_NOW - (int)$mybb->settings['wolcutoff'];
    
    $teamheader_users = str_replace(", ", ",", $mybb->settings['teamheader_users']);
    $teammembers = explode (",", $teamheader_users);

    foreach ($teammembers as $teammember) {

        // Accountswitcher
        $allcharas = teamheader_get_allchars($teammember);
        $charastring = implode(",", array_keys($allcharas));

        $teamie_query = $db->query("SELECT u.* FROM ".TABLE_PREFIX."users u
        LEFT JOIN (SELECT uid, MAX(time) AS last_active_time FROM ".TABLE_PREFIX."sessions GROUP BY uid) s ON u.uid = s.uid
        WHERE u.uid IN (".$charastring.")
        ORDER BY COALESCE(s.last_active_time, 0) DESC, u.lastactive DESC
        LIMIT 1
        ");

        while($teamie = $db->fetch_array($teamie_query)) {

            // LEER LAUFEN LASSEN
            $uid = "";
            $charactername = "";
            $charactername_formated = "";
            $charactername_formated_link = "";
            $charactername_link = "";
            $charactername_fullname = "";
            $charactername_first = "";
            $charactername_last = "";
            $graphic_link = "";
            $graphic_link_theme = "";
            $online_check = "";
            $lastvisit = "";
            $away = "";
            $returndate = "";
            $lastactive = "";
           
            // MIT INFOS FÜLLEN
            $uid = $teamie['uid'];
            $away = $teamie['away'];
            $returndate = $teamie['returndate'];
            $lastactive = $teamie['lastactive'];

            // CHARACTER NAME
            // ohne alles
            $charactername = $teamie['username'];
            // mit Gruppenfarbe
            $charactername_formated = format_name($charactername, $teamie['usergroup'], $teamie['displaygroup']);	
            // mit Gruppenfarbe + Link
            $charactername_formated_link = build_profile_link(format_name($charactername, $teamie['usergroup'], $teamie['displaygroup']), $uid);	
            // Nur Link
            $charactername_link = build_profile_link($charactername, $uid);
            // Name gesplittet
            $charactername_fullname = explode(" ", $charactername);
            $charactername_first = array_shift($charactername_fullname);
            $charactername_last = implode(" ", $charactername_fullname);

            // CHARACTER GRAFIK
            // Gäste
            if ($active_uid == 0 AND $graphic_guest == 1) {
                $graphic_link = $theme['imgdir']."/".$graphic_defaultgraphic;
                $graphic_link_theme = $theme['imgdir']."/".$graphic_defaultgraphic;
            } else {
                // Avatar
                if ($graphic_type == 0) {
                    $chara_graphic = $teamie['avatar'];
                    $chara_graphic_theme = "";
                } 
                // Uploadsystem
                else if ($graphic_type == 1) {
                    $path = $db->fetch_field($db->simple_select("uploadsystem", "path", "identification = '".$graphic_uploadsystem."'"), "path");                              
                    $value = $db->fetch_field($db->simple_select("uploadfiles", $graphic_uploadsystem, "ufid = '".$uid."'"), $graphic_uploadsystem);

                    if ($value != "") {
                        $chara_graphic = $path."/".$value;
                    } else {
                        $chara_graphic = "";
                    }

                    $chara_graphic_theme = "";
                }
                // Profilfelder
                else if ($graphic_type == 2) {
                    $fid = "fid".$graphic_profilefield;
                    // Vollständiger Link
                    $chara_graphic = $db->fetch_field($db->simple_select("userfields", $fid, "ufid = '".$uid."'"), $fid);
                    // variabler Link
                    $chara_graphic_theme = $db->fetch_field($db->simple_select("userfields", $fid, "ufid = '".$uid."'"), $fid);
                    $themesdir = str_replace($mybb->settings['bburl']."/", "", $theme['imgdir']);

                    if (file_exists($themesdir."/".$chara_graphic_theme)) {
                        $found_file = $chara_graphic_theme;
                    } else {
                        $found_file = "";
                    }
                }
                // Steckifelder
                else if ($graphic_type == 3) {	
                    $fieldid = $db->fetch_field($db->simple_select("application_ucp_fields", "id", "fieldname = '".$graphic_characterfield."'"), "id");  
                    // Vollständiger Link                
                    $chara_graphic = $db->fetch_field($db->simple_select("application_ucp_userfields", "value", "uid = '".$uid."' AND fieldid = '".$fieldid."'"), "value");
                    // variabler Link
                    $chara_graphic_theme = $db->fetch_field($db->simple_select("application_ucp_userfields", "value", "uid = '".$uid."' AND fieldid = '".$fieldid."'"), "value");

                    $themesdir = str_replace($mybb->settings['bburl']."/", "", $theme['imgdir']);
                    if (file_exists($themesdir."/".$chara_graphic_theme)) {
                        $found_file = $chara_graphic_theme;
                    } else {
                        $found_file = "";
                    }
                }

                // DIREKTER LINK
                // wenn man kein Grafik hat => Default
                if ($chara_graphic == "") {
                    // Dateinamen bauen
                    $graphic_link = $theme['imgdir']."/".$graphic_defaultgraphic;
                } else {
                    // Dateinamen bauen
                    $graphic_link = $chara_graphic;
                }

                // THEMEN ORIENTIERTER LINK
                // wenn man kein Grafik hat => Default
                if ($chara_graphic_theme == "") {
                    // Dateinamen bauen
                    $graphic_link_theme = $theme['imgdir']."/".$graphic_defaultgraphic;
                } else {
                    // Dateinamen bauen
                    if (!empty($found_file)) {
                        $graphic_link_theme = $theme['imgdir']."/".$chara_graphic_theme;
                    } else {
                        $graphic_link_theme = $theme['imgdir']."/".$graphic_defaultgraphic;
                    }
                }
            }

            // SPIELERNAME
            // wenn Zahl => klassisches Profilfeld
            if (is_numeric($playername_setting)) {
                $playername = $db->fetch_field($db->simple_select("userfields", "fid".$playername_setting, "ufid = '".$uid."'"), "fid".$playername_setting);
            } else {
                $playerid = $db->fetch_field($db->simple_select("application_ucp_fields", "id", "fieldname = '".$playername_setting."'"), "id");
                $playername = $db->fetch_field($db->simple_select("application_ucp_userfields", "value", "fieldid = '".$playerid."' AND uid = '".$uid."'"), "value");
            }

            // LETZTE AKTIVITÄT
            $online_check = $db->fetch_field($db->simple_select("sessions", "time", "uid = '".$uid."'"), "time");

            // Abwesenheit beachten
            if ($away_setting == 1 AND $away == 1) {
                if ($awayreturn_setting == 1) {
                    $lastvisit = $lang->sprintf($lang->teamheader_lastvisit_returndate, my_date($mybb->settings['dateformat'], strtotime($returndate)));
                } else {
                    $lastvisit = $lang->teamheader_lastvisit_away;
                }
                    
                // Grauer Avatar
                if($greyava == 1) {
                    $grey_graphic = "style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\"";
                } else {
                    $grey_graphic = "";
                }

            } else {
                // aktuell online
                if(!empty($online_check) AND $online_check > $timesearch) {
                    $lastvisit = my_date('relative', $online_check);
                    $grey_graphic = "";
                } 
                // aktuell offline
                else {
                    if ($lastvisit_setting == 1) {
                        $lastvisit = my_date('relative', $lastactive);
                    } else {
                        $lastvisit = $lang->teamheader_lastvisit_offline;
                    }
                    // Grauer Avatar
                    if($greyava == 1) {
                        $grey_graphic = "style=\"-webkit-filter: grayscale(100%);filter: grayscale(100%);\"";
                    } else {
                        $grey_graphic = "";
                    }
                }

            }

            eval('$teamheader_bit .= "'.$templates->get('teamheader_bit').'";');
        }
    }

    eval('$teamheader = "'.$templates->get('teamheader').'";');
}

// ACCOUNTSWITCHER HILFSFUNKTION
function teamheader_get_allchars($uid) {
    global $db, $mybb;

    // Überprüfen, ob die angegebene uid die eines Hauptaccounts ist
    $as_uid = $db->fetch_field($db->simple_select("users", "as_uid", "uid='".$uid."'"), "as_uid");
    if ($as_uid != 0) {
        $uid = $as_uid;
    }

    $charas = array();

    // Abfrage für alle Benutzer mit der angegebenen uid oder as_uid des Hauptaccounts
    $get_all_users = $db->query("
        SELECT uid, username 
        FROM " . TABLE_PREFIX . "users 
        WHERE as_uid = 0 AND uid = '".$uid."'
        OR as_uid = '".$uid."'
        ORDER BY username
    ");

    while ($users = $db->fetch_array($get_all_users)) {
        $charas[$users['uid']] = $users['username'];
    }

    return $charas;
}
