# Teamanzeige im Header
Dieses Plugin erweitert das Forum um die Funktion, bestimmte Teammitglieder im Header zu präsentieren. Die Teammitglieder werden mit ihrem Spielernamen, einer Teamrafik, Charaktername und ihrer letzten Aktivität angezeigt. Diese Informationen beziehen sich immer auf den Charakter, mit dem das Teammitglied zuletzt aktiv war.<br>
Sollte ein Teammitglied nicht mehr aktiv im Board ist (in den meisten Fällen nach 15 Minuten oder ausgeloggt), kann ausgewählt werden ob die konkrete letzte Aktivität wie in der Mitgliederliste oder im Profil angezeigt werden soll oder ob dort "offline" stehen soll. Ebenso kann eingestellt werden, ob Abwesenheiten von den Teammitgliedern angezeigt werden sollen. Diese Notiz ersetzt dann die "zuletzt online"-Anzeige. Wenn ein Teammitglied abwesend ist, aber dennoch online, wird es nicht angezeigt und die Abwesenheitsnotiz bleibt bestehen. Es kann auch gewählt werden, ob angezeigt werden soll, wann das Teammitglied wieder zurück ist.<br>
Auf Wunsch können die Teamgrafiken in grau dargestellt werden, wenn ein Teammitglied offline und/oder abwesend gemeldet ist. Die Avatare können für Gäste versteckt werden; diese sehen dann den festgelegten Standardavatar. Dieser wird auch angezeigt, wenn das Teammitglied noch keinen Avatar besitzt.<br>
Für die Grafik der Teammitglieden stehen folgende Optionen zur Verfügung: Es kann ausgewählt werden, ob der Avatar, ein Element aus dem Plugin "Uploadsystem", der Inhalt Profilfeld oder ein Steckbrieffeld von dem Steckbrief-Plugin von risuena verwendet werden soll.<br>
<br>
Bei den Optionen Steckifeld/Profilfeld gibt es eine "spezielle" Funktion. Anstelle eines vollständigen Links, der auf eine Grafik verweist, können die Teammitglieder auch nur den Dateinamen angeben. Um diese Option zu nutzen, muss im Template die Variable {$graphic_link} durch {$graphic_link_theme} ersetzt werden. Damit sucht das Plugin nach einer Datei mit dem angegebenen Namen aus dem Steckifeld/Profilfeld im entsprechenden Bildordner des Designs, der im ACP unter den einzelnen Themen im Pfad zu den Bildern festgelegt wurde. Wenn eine Datei mit diesem Namen vorhanden ist, wird diese Grafik angezeigt. Andernfalls wird die Standardgrafik verwendet.

# Vorrausetzung
- Der <a href="https://www.mybb.de/erweiterungen/18x/plugins-verschiedenes/enhanced-account-switcher/" target="_blank">Accountswitcher</a> von doylecc <b>muss</b> installiert sein.

# ACP-Einstellungen - Teamanzeige im Header
- Teammitglieder
- Grafiktyp
- Identifikator von dem Upload-Element
- FID von dem Profilfeld
- Identifikator von dem Steckbrieffeld
- Standard-Grafik
- Gäste Ansicht
- Spielername-ID
- Zuletzt online-Anzeige
- Graue Teamgrafik
- Abwesenheitsnotiz
- Abwesenheitsnotiz - Rückkehrdatum<br>
<br>
<b>HINWEIS:</b><br>
Das Plugin ist kompatibel mit den klassischen Profilfeldern von MyBB und dem <a href="https://github.com/katjalennartz/application_ucp">Steckbrief-Plugin</a> von <a href="https://github.com/katjalennartz">risuena</a>.

# Neues Templates (nicht global!)
- teamheader
- teamheader_bit

# Neue Variable
- header: {$teamheader}

# Neues CSS - postinggoal.css
Es wird automatisch in jedes bestehende und neue Design hinzugefügt. Man sollte es einfach einmal abspeichern - auch im Default. Sonst kann es passieren, dass es bei einem Update von MyBB entfernt wird.
<blockquote> .teamheader-container {
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
}</blockquote>

# extra Variabeln
<b>Charaktername:</b>
- nur der Name: {$charactername}
- als Link: {$charactername_link}
- mit Gruppenfarbe: {$charactername_formated}
- mit Gruppenfarben als Link: {$charactername_formated_link}
- Vorname: {$charactername_first} & Nachname: {$charactername_last} ({$uid} ist die entsprechende UID)
<br>
<b>design spezifische Teamgrafiken:</b><br><br>
Bei den Optionen Steckifeld/Profilfeld gibt es eine "spezielle" Funktion. Anstelle eines vollständigen Links, der auf eine Grafik verweist, können die Teammitglieder auch nur den Dateinamen angeben. Um diese Option zu nutzen, muss im Template die Variable {$graphic_link} durch {$graphic_link_theme} ersetzt werden. Damit sucht das Plugin nach einer Datei mit dem angegebenen Namen aus dem Steckifeld/Profilfeld im entsprechenden Bildordner des Designs, der im ACP unter den einzelnen Themen im Pfad zu den Bildern festgelegt wurde. Wenn eine Datei mit diesem Namen vorhanden ist, wird diese Grafik angezeigt. Andernfalls wird die Standardgrafik verwendet.

# Demo
 <img src="https://stormborn.at/plugins/teamheader.png" />
