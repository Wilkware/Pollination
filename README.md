[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-5.2%20%3E-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-1.0.20190825-orange.svg)](https://github.com/Wilkware/IPSymconPollination)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![StyleCI](https://github.styleci.io/repos/203404838/shield?style=flat)](https://github.styleci.io/repos/203404838)

# Pollenflug

Dieses Modul ruft den Pollenflug-Gefahrenindex von DWD ab und stellt ihn textuell bzw. graphisch dar.

## Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#3-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [Versionshistorie](#8-versionshistorie)

### 1. Funktionsumfang

Das Modul nutzt den von Deutschen Wetterdienst (DWD) bereitgestellten Pollenflug-Gefahrenindex (opendata.dwd.de) zur grapghisch Aufbereitung und  
erzeugt über alle Meldungen einen kurzen Tageshinweis für die ensprechenden Gefahren.

* Auswahl des gewüschten Bundeslandes
* Auswahl der gewünschten Region
* Auswahl der darzustellenden Tage (max. 3 Tage)
* Tägliche Aktualisierung der Daten (siehe Einrichten der Instanz)

### 2. Voraussetzungen

* IP-Symcon ab Version 5.2

### 3. Installation

* Über den Modul Store das Modul *Pollenflug* installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPollination` oder `git://github.com/Wilkware/IPSymconPollination.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter "Instanz hinzufügen" ist das 'Pollen Count*-Modul (Alias: *Pollenflug*) unter dem Hersteller '(Sonstige)' aufgeführt.

__Konfigurationsseite__:

Name                        | Beschreibung
--------------------------- | ---------------------------------
Bundesland                  | Auswahl des Bundesland für welchen man die den Gefahrenindex haben möchte.
Region                      | Auswahl der Teilregion innerhalb des Bundeslandes.
Anzahl darzustellende Tage  | Anzahl der vorhergesagten Tage (1-3 Tage). **HINWEIS:** Viele Regionen liefern nur Daten für 2 Tage!
Variable für Tageshinweis anlegen? | Schalter, ob der tägliche Tageshinweis (Text) angelegt und aktualisiert werden soll.
Variable für Graphische Vorhersage anlegen? | Schalter, ob die graphische Vorhersage (HTMLBox) angelegt und aktualisiert werden soll.
Variable für Bildlink auf gesamtdeutschen Pollenflugkalendar anlegen? | Schalter, ob ein statischer Link (HTMLBox) zum Übersichtsbild erzeugt werden soll.
Tägliche Aktualisierung aktivieren? | Schalter, ob das tägliche Update aktiv oder inaktiv ist. DWD aktualisiert die Daten immer 11:00 Uhr. Das Modul holt die Daten immer 15 Minuten später (11:15 Uhr) ab.

### 5. Statusvariablen und Profile

Die Statusvariablen werden je nach Einstellung automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
Letzte Aktualisierung  | Integer   | von DWD gelieferter Zeitstempel der letzten Aktualisierung
Nächste Aktualisierung | Integer   | von DWD gelieferter Zeitstempel für die nächste Aktualisierung
Tageshinweis           | String    | Textuelle Zusammenfassung der Vorhersage
Vorhersage             | String    | Graphische Repräsentation des Gefahrenindex in Abhängigkeit der gewählten darzustellenden Tage
Jahreskalender         | String    | statischer Link zum Übersichtsbild

### 6. WebFront

Man kann die Statusvariablen direkt im WF verlinken.

### 7. PHP-Befehlsreferenz

`void POLLEN_Update(int $InstanzID);`
Holt entsprechend der Konfiguration die gewählten Daten.
Die Funktion liefert keinerlei Rückgabewert.

Beispiel:
`POLLEN_Update(12345);`

### 8. Versionshistorie

v1.0.20190825

* _NEU_: Initialversion (Migration vom Script *Online.Pollination.ips.php* v1.2.20181012).

## Danksagung

Die verwendeten Icons sind vom Autor "Freepik" von www.flaticon.com (Icon made by Freepik from <https://www.flaticon.com>).  
Vielen Dank für die hervorragende und tolle Arbeit! Thanks!

## Entwickler

* Heiko Wilknitz ([@wilkware](https://github.com/wilkware))

## Spenden

Die Software ist für die nicht kommzerielle Nutzung kostenlos, Schenkungen als Unterstützung für den Entwickler bitte hier:  
<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166" target="_blank"><img src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_donate_LG.gif" border="0" /></a>

### Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
