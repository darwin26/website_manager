Website Manager AddOn für REDAXO 4.5+
=====================================

Ein Multidomain AddOn für REDAXO 4.5+

Features
--------

* Verwaltung mehrere Websites mit einer REDAXO-Installation
* Website-Umschalter auf oberster Ebene
* Drag und Drop Sortierung der Websites
* Der Style des Backend wird je nach ausgewählter Website angepasst (inkl. autom. generierter farbiger Favicons)
* Man kann auswählen ob man gleiche oder verschiedene Templates, Module, Medien, Clangs, Meta-Infos und Image-Types für die Websites haben will
* Ein zusätzlicher URL-Rewriter kann frei gewählt und eingesetzt werden
* Man kann aus anderen Websites ganze Artikel und Blöcke ausgeben
* Tools
* Rechtemanagement
* Theme-Plugin um pro Werte (z.B. Farbwerte) abspeichern zu können, die dann im Frontend für Farbschemas etc. verwendet werden können 

Under Construction
------------------

* Gleiche Meta-Infos für alle Websites ist noch nicht vollständig implementiert. Hier hilft aktuell nur die Meta Infos von Hand zu duplizieren pro Website.
* Gleiche Clangs sind noch nicht ausreichend getestet und damit unsupported.

API (Auszug)
------------

```php
// ausgabe des artikels mit id = 10 von website mit id = 5 
echo $REX['WEBSITE_MANAGER']->getWebsite(5)->getArticle(10);

// ausgabe des slices mit id = 40 von website mit id = 3
echo $REX['WEBSITE_MANAGER']->getWebsite(3)->getSlice(40);

// ausgabe des feldes "color1" des aktuellen themes (nur wenn "themes" plugin installiert)
echo $REX['WEBSITE_MANAGER']->getCurrentWebsite()->getTheme()->getValue('color1');
```

AddOns fitmachen für den Website Manager
----------------------------------------

Damit andere AddOns auch problemlos mit dem Website Manager zusammentun, muss man hauptsächlich folgende REDAXO Variablen einsetzen, anstelle der sonst üblichen hartcodierten Strings:

```php
$REX['MEDIA_DIR']
$REX['MEDIA_ADDON_DIR']
$REX['GENERATED_PATH']
$REX['MEDIAFOLDER']
$REX['TABLE_PREFIX']
```

Wichtig: Um abwärtskompatibilität der AddOns mit älteren REDAXO Versionen zu gewährleisten, sollten immer über `isset()` geprüft werden ob die Variablen überhaupt exisitieren. Hier mal ein Beispiel: 

```php
if (isset($REX['MEDIA_ADDON_DIR'])) {
	return $REX['MEDIA_ADDON_DIR'];
} else {
	return 'files/addons';
}
```

Hinweise
--------

* Läuft nur mit REDAXO 4.5+
* AddOn-Ordner lautet: `website_manager`
* Import/Export AddOn läuft aktuell nur für die Master-Website. Evtl. sollte man es deshalb vorerst deinstallieren.
* Meta-Infos und Image-Types werden von Haus aus unterstützt. Zusätzliche AddOns/PlugIns kann man über die entsprechenden Arrays in der `settings.inc.php` hinzufügen.
* Das Meta Info Fixer Tool erscheint nur wenn in der `settings.inc.php` die Option `identical_meta_infos` auf `true` steht
* Bei gleichen Templates/Modulen muss man den Cache global löschen für alle Websites sobald man Änderungen vorgenommen hat. Siehe dazu das entsprechende Tool.
* Die `settings.inc.php` sollte, nachdem man die zweite Website angelegt hat, nicht mehr verändert werden!
* Das Theme-Plugin ist so gedacht, dass man es für das jeweilige Projekt anpasst bevor man es installiert.
* Ein Log-File wird unter `/website_manager/generated/log/` angelegt mit Debug-Informationen, wenn man eine Website hinzufügt oder entfernt.

Wichtig: Es wird dringend angeraten eine automatische Backuplösung für alle Tabellen einzurichten, z.B. über das CronJob AddOn und dem MySQLDumper.

Changelog
---------

siehe [CHANGELOG.md](CHANGELOG.md)

Lizenz
------

siehe [LICENSE.md](LICENSE.md)

Credits
-------

* Supported by [Peter Bickel](https://github.com/polarpixel) und [Gregor Harlan](https://github.com/gharlan)
* Danke an das REDAXO-Team für die Erlaubnis die nötigen Core-Änderungen für das AddOn durchführen zu können
* Website Manager uses KLogger PHP-Class: https://github.com/katzgrau/KLogger
