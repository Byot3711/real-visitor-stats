# Real Visitor Stats

A lightweight WordPress plugin for real traffic stats: unique visitors, pageviews, browsers, devices and referrers, right in your admin dashboard, with no external services and no tracking cookies.

`#wordpress` `#wordpress-plugin` `#analytics` `#visitor-stats` `#php` `#privacy-friendly` `#web-analytics` `#dashboard` `#seo` `#traffic-analytics`

## Why

Most analytics plugins send visitor data to external servers or add heavy scripts that slow the site down. Real Visitor Stats keeps everything local, in your own WordPress database, with minimal impact on performance.

## Features

- Unique visitors and pageviews, daily and total
- 14-day traffic chart (unique visitors vs. pageviews)
- Browser and device breakdown (desktop / mobile / tablet)
- Top 10 visited pages (last 7 days)
- Top 10 referrers (last 30 days)
- Quick stats widget right on the WordPress dashboard
- `[real_visitor_stats]` shortcode for front-end display
- Automatic cleanup of old data (configurable retention, 90 days by default)
- IPs and user agents are stored as hashes only, never in plain text

## Installation

1. Download the `real-visitor-stats.zip` archive or clone this repository.
2. In your WordPress admin, go to **Plugins → Add New → Upload Plugin** and upload the archive.
3. Activate the plugin.
4. Visit **Visitor Stats** in the admin menu to see your statistics.

## Requirements

- WordPress 5.0+
- PHP 7.2+

## Structure

```
real-visitor-stats/
├── real-visitor-stats.php       Main plugin file
├── includes/
│   ├── class-rvs-plugin.php     Bootstrap, cron, shortcode
│   ├── class-rvs-tracker.php    Visit tracking + user agent parsing
│   ├── class-rvs-admin.php      Admin menu, queries, dashboard widget
│   └── views/admin-page.php     Admin page template
└── assets/
    ├── css/admin.css
    └── js/admin.js
```

## License

GPL v2 or later.
