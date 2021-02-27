# Pollenflug

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-5.2%20%3E-blue.svg)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-2.0.20210227-orange.svg)](https://github.com/Wilkware/IPSymconPollination)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://github.com/Wilkware/IPSymconPollination/workflows/Check%20Style/badge.svg)](https://github.com/Wilkware/IPSymconPollination/actions)

Das Modul nutzt den von Deutschen Wetterdienst (DWD) bereitgestellten Pollenflug-Gefahrenindex (opendata.dwd.de) zur graphischen Aufbereitung und
erzeugt über alle Meldungen einen kurzen Tageshinweis für die entsprechenden Gefahren.

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

Dieses Modul ruft den Pollenflug-Gefahrenindex von DWD ab und stellt ihn textuell bzw. graphisch dar.

* Auswahl des gewüschten Bundeslandes
* Auswahl der gewünschten Region
* Auswahl der darzustellenden Tage (max. 3 Tage)
* Bearbeitung der Tabellen-Formatvorlagen (Stylesheets)
* Einbindung des gesamtdeutschen Pollenflugkalenders (Bildlink)
* Tägliche Aktualisierung der Daten (siehe Einrichten der Instanz)

Der Pollen-Gefahrenindex kann auch über die Methode [POLLEN_IndexInfo](#7-php-befehlsreferenz) als JSON abgerufen werden.

Folgende Informationen stehen als key => value Paare zur Verfügung:

Folgende Informationen stehen als key => value Paare zur Verfügung:

Schlüssel(key)        | Typ     | Beschreibung
--------------------- | ------- | ----------------
last                  | int     | Timestamp, letze Aktualisierung
next                  | int     | Timestamp, nächaste Aktualisierung
legend                | array   | #0-#7, textuelle Beschreibung der Belastungsstufen  
index                 | array   | Belastung für die nächsten 3 Tage (siehe Legende)

### 2. Voraussetzungen

* IP-Symcon ab Version 5.2

### 3. Installation

* Über den Modul Store das Modul *Pollenflug* installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/IPSymconPollination` oder `git://github.com/Wilkware/IPSymconPollination.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter "Instanz hinzufügen" ist das 'Pollen Count'-Modul (Alias: *Pollenflug*) unter dem Hersteller '(Geräte)' aufgeführt.

__Konfigurationsseite__:

Einstellungsbereich:

> Örtliche und zeitliche Einstellungen ...

Name                        | Beschreibung
--------------------------- | ---------------------------------
Bundesland                  | Auswahl des Bundesland für welchen man die den Gefahrenindex haben möchte.
Region                      | Auswahl der Teilregion innerhalb des Bundeslandes.
Anzahl darzustellende Tage  | Anzahl der vorhergesagten Tage (1-3 Tage). **HINWEIS:** Viele Regionen liefern nur Daten für 2 Tage!

> Formatvorlagen (CSS) ...

Name                                                      | Beschreibung
--------------------------------------------------------- | ---------------------------------
3D Farbverlauf für Tabellenkopf verwenden?                | Verwendet einen transparenten Gradienten zur 3D Darstellung
Tabelle \[table\]                                         | Allgemeine Tabellenstyle (Schrift, Farbe, Hintergrund)
Tabllenkopf \[thead\]                                     | Style des Tabellenkopfs (Schrift, Farbe, Hintergrund)
Tabellenzellen \[th,td\]                                  | Allgemeiner Style für alle Zellen
Letzte Zeile \[tr:last-child\]                            | Style für letzte Zeile (z.b: Rahmen links, rechts, unten)
Alternierende Zeile \[tr:nth-child(even)\]                | Style für gerade Zeile (z.b: Hintergrundfarbe)
Erste Zelle \[td:first-child\]                            | Style für erste Zelle einer Zeile (z.b: Rahmen links)
Mittlere Zellen \[td:not(:first-child):not(:last-child)\] | Style für mittlere Zellen einer Zeile (z.b: keinen Rahmen)
Letzte Zelle \[td:last-child\]                            | Style für letzte Zelle einer Zeile (z.b: Rahmen rechts)
Wochentag \[div.day\]                                     | Style für den Wochentagsnamen
Tag des Monats \[div.num\]                                | Style für den Monatstag (z.b. groß & farbig)
Monatsname \[div.mon\]                                    | Style für den Monatsnamen (z.b. alles Grossbuschtaben)

> Erweiterte Einstellungen ...

Name                                        | Beschreibung
------------------------------------------- | ---------------------------------
Variable für Tageshinweis anlegen?          | Schalter, ob der tägliche Tageshinweis (Text) angelegt und aktualisiert werden soll.
Variable für Graphische Vorhersage anlegen? | Schalter, ob die graphische Vorhersage (HTMLBox) angelegt und aktualisiert werden soll.
Variable für Bildlink auf gesamtdeutschen Pollenflugkalender anlegen? | Schalter, ob ein statischer Link (HTMLBox) zum Übersichtsbild erzeugt werden soll.
Tägliche Aktualisierung aktivieren?         | Schalter, ob das tägliche Update aktiv oder inaktiv ist. DWD aktualisiert die Daten immer 11:00 Uhr. Das Modul holt die Daten immer 15 Minuten später (11:15 Uhr) ab.

Aktionsbereich:

> Gefahrenindex ...

Aktion         | Beschreibung
-------------- | ------------------------------------------------------------
AKTUALISIEREN  | Ermittelt für das aktuelle Datum den Gefahrenindex (Update)

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

```php
void POLLEN_Update(int $InstanzID):
```

Holt entsprechend der Konfiguration die gewählten Daten.
Die Funktion liefert keinerlei Rückgabewert.

__Beispiel__: `POLLEN_Update(12345);`

```php
string POLLEN_IndexInfo(int $InstanzID);
```

Holt den aktuellen Gefahrenindex ab und gibt diese als JSON zurück.

__Beispiel__: `POLLEN_IndexInfo(12345);`

> {  
> "index": { "Roggen": [1,1,0], "Graeser": [1,1,0], "Beifuss": [1,1,0], "Ambrosia": [1,1,0], "Hasel": [6,7,0], "Esche": [1,1,0], "Erle": [7,7,0],"Birke": [1,1,0]},  
> "legend": {  
> "#7": "hoch",  
> "#6": "mittel bis hoch",  
> "#5": "mittel",  
> "#4": "gering bis mittel",  
> "#3": "gering",  
> "#2": "keine bis gering",  
> "#1": "keine",  
> "#0": "nicht bekannt"  
> },  
> "next": 1614506400,  
> "last": 1614420000  
> }  

### 8. Versionshistorie

v2.0.20210227

* _NEU_: Gefahrenindex kann als JSON Objekt abgerufen werden.
* _NEU_: Tabellarische Ausgabe kann mittels CSS angepasst werden.
* _FIX_: Gefahrenindex wird jetzt immer alphabetisch sortiert dargestellt.
* _FIX_: Vereinheitlichungen des Konfigurationsformulars
* _FIX_: Vereinheitlichungen der Libs

v1.0.20190821

* _NEU_: Initialversion (Migration vom Script *Online.Pollination.ips.php* v1.2.20181012).

## Danksagung

Die verwendeten Icons sind vom Autor "Freepik" von www.flaticon.com (Icon made by Freepik from <https://www.flaticon.com>).  
Vielen Dank für die hervorragende und tolle Arbeit! Thanks!

## Entwickler

* Heiko Wilknitz ([@wilkware](https://github.com/wilkware))

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, Schenkungen als Unterstützung für den Entwickler bitte hier:

[![License](https://img.shields.io/badge/Einfach%20spenden%20mit-PayPal-blue.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

### Lizenz

[![Licence](https://licensebuttons.net/i/l/by-nc-sa/transparent/00/00/00/88x31-e.png)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
