# Teamanzeige im Header
Dieses Plugin erweitert das Board um die Funktion bestimmte User (die Teammitglieder) im Header zu präsentieren. Die Teammitglieder werden mit ihrem Spielernamen, Avatar, Charaktername und ihrer letzten Aktivität angezeigt. Diese Infos beziehen sich immer auf den Charakter, mit dem das Teammitglied das letzte mal aktiv war. Es kann ausgewählt werden, ob die letzte Aktivität, sowie in der Mitgliederliste oder im Profil angezeigt wird, sprich zB zuletzt online: 23.01.2022, 14:25 oder ob dort dann offline stehen soll, wenn das Teammitglied nicht mehr aktiv im Board ist. In den meisten Fällen nach 15 Minuten. Auch kann eingestellt werden, ob Abwesenheiten von den Teammitgliedern angezeigt werden sollen. Diese Notiz ersetzt dann die zuletzt online Anzeige. Sprich ist ein Teammitglied abwesend, aber dennoch online wird es nicht angezeigt, sondern die Abwesenheitsnotiz bleibt bestehen. Auch kann gewählt werden, ob auch angezeigt werden soll, wann das Teammitglied wieder zurück ist.<br>
Auf Wunsch können die Avatare in grau dargestellt werden, wenn ein Teammitglied offline (auch wenn man die zuletzt online Anzeige aktiviert hat) und abwesend gemeldet ist. Avatare können für Gäste versteckt werden. Diese bekommen dann den festgelegten Standardavatar angezeigt. Dieser wird auch angezeigt, wenn das Teammitglied mit einem Account noch kein Avatar besitzt und online ist bzw war. <br>
Wichtig bei dem Plugin ist, dass im ACP die UIDs der Teammitglieder angegeben werden und das es sich dabei bei um die Hauptuids handelt. Also an den der Accountswitcher die anderen Accounts von dem User anhängt.

# Vorrausetzung
- Der <a href="https://www.mybb.de/erweiterungen/18x/plugins-verschiedenes/enhanced-account-switcher/" target="_blank">Accountswitcher</a> von doylecc <b>muss</b> installiert sein.

# Neue Templates
- header_teamheader
- header_teamheader_bit
(da ich es fürs Plugin in einem default Style gecodet habe, bestehen die tpls aus Tabellen. Doch kann man das alles problemlos ersetzen mit divs oder was weiß ich)

# Template Änderungen - neue Variablen
- header - {$teamheader} 

# ACP-Einstellungen - Teamanzeige im Header
- Standard-Avatar
- Gäste Ansicht
- Spielername-ID
- Teamuser
- Zuletzt online-Anzeige
- Grauer Teamavatar
- Abwesenheitsnotiz
- Abwesenheitsnotiz - Rückkehrdatum

# Demo
 Teamanzeige - Standard<p>
 <img src="https://stormborn.at/plugins/teamheader_default.png" />
 
 Teamanzeige - Graue Avatarfunktion<p>
 <img src="https://stormborn.at/plugins/teamheader_grau.png" />
 
 Teamanzeige - mit Abwesenheitsnotiz & ohne Rückkehrdatum<p>
 <img src="https://stormborn.at/plugins/teamheader_away.png" />
 
 Teamanzeige - mit Abwesenheitsnotiz & mit Rückkehrdatum<p>
 <img src="https://stormborn.at/plugins/teamheader_offline.png" />
 
 Teamanzeige - Offline-Anzeige<p>
 <img src="https://stormborn.at/plugins/teamheader_offlineaway.png" />
 
 Teamanzeige - Offline-Anzeige mit Abwesenheitsnotiz & ohne Rückkehrdatum<p>
 <img src="https://stormborn.at/plugins/teamheader_default.png" />
