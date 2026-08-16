# Real Visitor Stats

Plugin WordPress simplu pentru statistici reale de trafic: vizitatori unici, pageviews, browsere, dispozitive și referreri, direct din dashboard-ul de admin, fără servicii externe și fără cookie-uri de tracking.

`#wordpress` `#wordpress-plugin` `#analytics` `#visitor-stats` `#php` `#privacy-friendly` `#web-analytics` `#dashboard` `#seo` `#traffic-analytics`

## De ce

Majoritatea plugin-urilor de analytics trimit datele către servere externe sau adaugă scripturi grele care încetinesc site-ul. Real Visitor Stats ține totul local, în baza de date proprie a WordPress-ului, cu un impact minim asupra performanței.

## Funcționalități

- Vizitatori unici și pageviews, pe zi și în total
- Grafic cu ultimele 14 zile (vizitatori unici vs. pageviews)
- Distribuție pe browsere și tipuri de dispozitive (desktop / mobil / tabletă)
- Top 10 pagini vizitate (ultimele 7 zile)
- Top 10 referreri (ultimele 30 de zile)
- Widget cu statistici de azi direct pe dashboard-ul WordPress
- Shortcode `[real_visitor_stats]` pentru afișare pe front-end
- Curățare automată a datelor vechi (retenție configurabilă, implicit 90 zile)
- IP-uri și user agent-uri stocate doar ca hash — nu se păstrează date personale în clar

## Instalare

1. Descarcă arhiva `real-visitor-stats.zip` sau clonează acest repository.
2. În WordPress admin, mergi la **Plugins → Add New → Upload Plugin** și încarcă arhiva.
3. Activează pluginul.
4. Vizitează **Visitor Stats** din meniul admin pentru a vedea statisticile.

## Cerințe

- WordPress 5.0+
- PHP 7.2+

## Structură

```
real-visitor-stats/
├── real-visitor-stats.php       Fișierul principal al pluginului
├── includes/
│   ├── class-rvs-plugin.php     Bootstrap, cron, shortcode
│   ├── class-rvs-tracker.php    Tracking vizite + parsare user agent
│   ├── class-rvs-admin.php      Meniu admin, interogări, dashboard widget
│   └── views/admin-page.php     Template-ul paginii de admin
└── assets/
    ├── css/admin.css
    └── js/admin.js
```

## Licență

GPL v2 sau ulterior.
