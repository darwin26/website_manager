Website Manager AddOn für REDAXO 4.5+
=====================================

Ein Multidomain AddOn für REDAXO 4.5+

Features
--------

* Verwaltung mehrere Websites mit einer REDAXO-Installation
* Website-Umschalter auf oberster Ebene
* Der Style des Backend wird je nach ausgewählter Website angepasst
* Jede Website greift auf die gleichen Templates, Module und Aktionen zu
* Jede Website hat Ihren eigenen Medienpool und Ihren eigenen Generated-Ordner
* Ein zusätzlicher URL-Rewriter kann frei gewählt und eingesetzt werden

API (Auszug)
------------

```php
// ausgabe des artikels mit id = 10 von website mit id = 5 
echo $REX['WEBSITE_MANAGER']->getWebsite(5)->getArticle(10);

// ausgabe des slices mit id = 40 von website mit id = 3
echo $REX['WEBSITE_MANAGER']->getWebsite(3)->getSlice(40);
```

Hinweise
--------

* Läuft nur mit REDAXO 4.5+
* AddOn-Ordner lautet: `website_manager`
* Import/Export AddOn läuft aktuell nur für die Master-Website. Evtl. sollte man es deshalb vorerst deinstallieren.
* Addons können fit gemacht werden für den Website Manager durch Nutzung der neuen REX-Vars aus REDAXO 4.5. Beschreibung folgt...

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
