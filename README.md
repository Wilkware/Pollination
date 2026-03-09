# Pollenflug

[![Version](https://img.shields.io/badge/Symcon-PHP--Modul-red.svg?style=flat-square)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Product](https://img.shields.io/badge/Symcon%20Version-6.4-blue.svg?style=flat-square)](https://www.symcon.de/produkt/)
[![Version](https://img.shields.io/badge/Modul%20Version-4.0.20260309-orange.svg?style=flat-square)](https://github.com/Wilkware/Pollination)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg?style=flat-square)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
[![Actions](https://img.shields.io/github/actions/workflow/status/wilkware/Pollination/ci.yml?branch=main&label=CI&style=flat-square)](https://github.com/Wilkware/Pollination/actions)

Das Modul nutzt den von Deutschen Wetterdienst (DWD) bereitgestellten Pollenflug-Gefahrenindex (opendata.dwd.de) zur graphischen Aufbereitung und
erzeugt über alle Meldungen einen kurzen Tageshinweis für die entsprechenden Gefahren.

## Inhaltverzeichnis

1. [Funktionsumfang](#user-content-1-funktionsumfang)
2. [Voraussetzungen](#user-content-2-voraussetzungen)
3. [Installation](#user-content-3-installation)
4. [Einrichten der Instanzen in IP-Symcon](#user-content-4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen](#user-content-5-statusvariablen)
6. [Visualisierung](#user-content-6-visualisierung)
7. [PHP-Befehlsreferenz](#user-content-7-php-befehlsreferenz)
8. [Versionshistorie](#user-content-8-versionshistorie)

### 1. Funktionsumfang

Dieses Modul ruft den Pollenflug-Gefahrenindex von DWD ab und stellt ihn textuell bzw. graphisch dar.

* Auswahl des gewüschten Bundeslandes
* Auswahl der gewünschten Region
* Auswahl der darzustellenden Tage (max. 3 Tage)
* Auswahl der darzustellenden Gefahren/Pollenarten
* Meldungen an Visualisierung und/oder Meldungsdashboard ab einen bestimmten Gefahrenlevel
* Möglichleit der Medieneinbindung von Pollenflugkalender(n) (www.pollenstiftung.de)
* Tägliche Aktualisierung der Daten (siehe Einrichten der Instanz)

Der Pollen-Gefahrenindex kann auch über die Methode [POLLEN_IndexInfo](#7-php-befehlsreferenz) als JSON abgerufen werden.

### 2. Voraussetzungen

* IP-Symcon ab Version 8.1

### 3. Installation

* Über den Modul Store das Modul _Pollenflug_ bzw. _Pollination_ installieren.
* Alternativ Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/Wilkware/Pollination` oder `git://github.com/Wilkware/Pollination.git`

### 4. Einrichten der Instanzen in IP-Symcon

* Unter "Instanz hinzufügen" ist das _Pollenflug_-Modul (_Pollination_) unter dem Hersteller '(Geräte)' aufgeführt.

__Konfigurationsseite__:

Einstellungsbereich:

> 📆 Pollenflugkalender ...

Name                                                          | Beschreibung
------------------------------------------------------------- | ---------------------------------
Gesamtdeutscher Pollenflugkalender (2016–2021)                | Aktiv, wird entsprechender Kalender als Bild (Medien) angelegt
Pollenflugkalender für Norddeutschland (2016–2021)            | Aktiv, wird entsprechender Kalender als Bild (Medien) angelegt
Pollenflugkalender für Mittel- und Ostdeutschland (2016–2021) | Aktiv, wird entsprechender Kalender als Bild (Medien) angelegt
Pollenflugkalender für Süddeutschland (2016–2021)             | Aktiv, wird entsprechender Kalender als Bild (Medien) angelegt
Pollenflugkalender für Westdeutschland (2016–2021)            | Aktiv, wird entsprechender Kalender als Bild (Medien) angelegt

> ✨ Visualisierung ...

Name                        | Beschreibung
--------------------------- | ---------------------------------
Ambrosia                    | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Beifuß                      | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Birke                       | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Erle                        | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Esche                       | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Gräser                      | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Hasel                       | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt
Roggen                      | Wenn Aktiv, wird dazugehöriger Gefahrindex in der Visualisierung angezeigt

> 📢 Meldungsverwaltung ...

Name                               | Beschreibung
---------------------------------- | ---------------------------------
Meldung an Anzeige senden          | Auswahl ob Eintrag in die Meldungsverwaltung erfolgen soll oder nicht (Ja/Nein)
Ab Stufe                           | Auswahl ab welcher Stufe (1-4) die Nachricht erfolgen soll
Nachricht an Visualiserung senden  | Auswahl ob Push-Nachricht gesendet werden soll oder nicht (Ja/Nein)
Ab Stufe                           | Auswahl ab welcher Stufe (1-4) die Meldung erfolgen soll
Visualisierungs-Instanz            | ID der Visualisierung, an welches die Push-Nachrichten für Geburts-, Hochzeits- und Todestage gesendet werden soll (WebFront oder TileVisu Instanz)
Meldsungsskript                    | Skript ID des Meldungsverwaltungsskripts, weiterführende Infos im Forum: [Meldungsanzeige im Webfront](https://community.symcon.de/t/meldungsanzeige-im-webfront/23473)


> ⚙️ Erweiterte Einstellungen ...

Name                                        | Beschreibung
------------------------------------------- | ---------------------------------
Variable für Tageshinweis anlegen?          | Schalter, ob der tägliche Tageshinweis (Text) angelegt und aktualisiert werden soll.

Aktionsbereich:

Aktion         | Beschreibung
-------------- | ------------------------------------------------------------
AKTUALISIEREN  | Ermittelt für das aktuelle Datum den Gefahrenindex (Update)

### 5. Statusvariablen

Die Statusvariablen werden je nach Einstellung automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

Name                   | Typ       | Beschreibung
---------------------- | --------- | ----------------
Letzte Aktualisierung  | Integer   | von DWD gelieferter Zeitstempel der letzten Aktualisierung
Nächste Aktualisierung | Integer   | von DWD gelieferter Zeitstempel für die nächste Aktualisierung
Bundesland             | Integer   | Bundesland für welchen man die den Gefahrenindex haben möchte
Region                 | Integer   | Teilregion innerhalb des Bundeslandes
Tage                   | Integer   | Anzahl der vorhergesagten Tage
Tageshinweis           | String    | Textuelle Zusammenfassung der Vorhersage
{Pollenflugkalender}   | Medien    | n-Bild(er) der aktivieren Pollenflugkalender (Gesamtdeutsch, Nord, West, Ost, Süd)

### 6. Visualisierung

Man kann die gesamte Instanz direkt in die Visualisierung verlinken.

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
> 6: "hoch",  
> 5": "mittel bis hoch",  
> 4: "mittel",  
> 3: "gering bis mittel",  
> 2: "gering",  
> 1: "keine bis gering",  
> 0: "keine",  
> },  
> "next": 1614506400,  
> "last": 1614420000  
> }  

### 8. Versionshistorie

v4.0.20260309

* _FIX_: HTML optimiert (weniger Platzbedarf)
* _FIX_: Fehler in Tageslabel gefixt
* _FIX_: Legende wird wieder mit ausgeliefert (neues Format)
* _FIX_: Rechtschreibfehler korriegiert

v4.0.20260208

* _NEU_: Umstellung auf IPSModuleStrict
* _NEU_: Umstellung von Profilen auf Darstellungen
* _NEU_: Modulversion wird in Quellcodesektion angezeigt
* _NEU_: Projektumstrukturierung hin zu einer globalen CI/CD-Pipeline
* _NEU_: Kompatibilität auf IPS 8.1 hoch gesetzt
* _NEU_: Direkte Unterstützung der TileVisu, keine HTML-Boxen mehr
* _NEU_: Senden von Nachrichten ab definiertem Gefahrenindex
* _NEU_: Version der Pollenflugkalender nachgezogen und als echtes Bild(Medien) realisiert
* _FIX_: Gefahrenindex wird jetzt korrekt abgebildet, konnte unter gewissen Umständen um eins zu niedrig gewesen sein
* _FIX_: Übersetzungen vollständig nachgezogen
* _FIX_: Bibliotheksfunktionen angeglichen

v3.0.20221007

* _NEU_: Örtliche und zeitliche Einstellungen per Webfront ermöglicht
* _FIX_: Vereinheitlichungen des Konfigurationsformulars
* _FIX_: Libs nachgezogen

v2.0.20210227

* _NEU_: Gefahrenindex kann als JSON Objekt abgerufen werden.
* _NEU_: Tabellarische Ausgabe kann mittels CSS angepasst werden.
* _FIX_: Gefahrenindex wird jetzt immer alphabetisch sortiert dargestellt.
* _FIX_: Vereinheitlichungen des Konfigurationsformulars
* _FIX_: Vereinheitlichungen der Libs

v1.0.20190821

* _NEU_: Initialversion (Migration vom Script _Online.Pollination.ips.php_ v1.2.20181012).

## Danksagung

Die verwendeten Icons sind vom Autor "Freepik" von www.flaticon.com (Icon made by Freepik from <https://www.flaticon.com>).  
Vielen Dank für die hervorragende und tolle Arbeit! Thanks!

## Entwickler

Seit nunmehr über 10 Jahren fasziniert mich das Thema Haussteuerung. In den letzten Jahren betätige ich mich auch intensiv in der IP-Symcon Community und steuere dort verschiedenste Skript und Module bei. Ihr findet mich dort unter dem Namen @pitti ;-)

[![GitHub](https://img.shields.io/badge/GitHub-@wilkware-181717.svg?style=for-the-badge&logo=github)](https://wilkware.github.io/)

## Spenden

Die Software ist für die nicht kommerzielle Nutzung kostenlos, über eine Spende bei Gefallen des Moduls würde ich mich freuen.

[![PayPal](https://img.shields.io/badge/PayPal-spenden-00457C.svg?style=for-the-badge&logo=paypal)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8816166)

## Lizenz

Namensnennung - Nicht-kommerziell - Weitergabe unter gleichen Bedingungen 4.0 International

[![Licence](https://img.shields.io/badge/License-CC_BY--NC--SA_4.0-EF9421.svg?style=for-the-badge&logo=creativecommons)](https://creativecommons.org/licenses/by-nc-sa/4.0/)
