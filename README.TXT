=== ReviewGuard Pro ===
Contributors: grkmefe34
Tags: testimonials, reviews, customer feedback, social proof, schema
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.2
Stable tag: 4.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

ReviewGuard Pro offers testimonial forms, negative review filtering, automatic setup, social proof pop-ups, and Google SEO Schema integration.

== Description ==

ReviewGuard Pro is designed to boost your conversion rates while protecting your business reputation.

= English Features =
* Smart Negative Filter: Reviews with 3 stars or lower are sent directly to the admin's email as a private support ticket instead of being published.
* Automated Setup: Automatically creates a "Service Evaluation Form" page upon activation.
* Social Proof Live Pop-ups: Displays beautiful live notifications showing recent 5-star reviews.
* Google SEO Rich Snippets: Automatically injects schema codes for golden stars (★★★★★) in Google results.

= Türkçe Özellikler =
* Akıllı Negatif Filtresi: 3 yıldız veya daha düşük puanlı yorumlar sitede yayınlanmaz, doğrudan admin e-postasına gizli destek talebi olarak gider.
* Otomatik Kurulum: Eklenti aktif edildiğinde otomatik olarak şık bir "Hizmet Değerlendirme Formu" sayfası oluşturur.
* Canlı Sosyal Kanıt Bildirimleri: Sitenizin sol alt köşesinde en son yapılan 5 yıldızlı yorumları gösteren şık pop-up'lar görüntüler.
* Google SEO Zengin Sonuçlar: Web sitenizin Google arama sonuçlarında altın yıldızlarla görünmesini sağlar.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/reviewguard-pro` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the shortcode `[yorumları_listele]` anywhere you want.

== Shortcodes ==

= [memnuniyet_formu] =
Used to display the review submission form.

= [yorumları_listele] =
Displays all approved reviews.
* Example: `[yorumları_listele limit="3"]` (Shows latest 3 reviews)
* Example: `[yorumları_listele min_puan="5"]` (Shows only 5-star reviews)