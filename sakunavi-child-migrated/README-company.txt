Sakunavi Child - Company Single Template
=======================================

Files:
- single-card_company.php
- assets/css/company.css

How to install:
1) Upload these two files into your child theme:
   wp-content/themes/sakunavi-child-migrated/

2) Enqueue the CSS **only** on company pages. Add this to your child functions.php:

add_action('wp_enqueue_scripts', function () {
  if (is_singular('card_company')) {
    $path = get_stylesheet_directory() . '/assets/css/company.css';
    wp_enqueue_style('sakunavi-company', get_stylesheet_directory_uri() . '/assets/css/company.css', ['sakunavi-child-style'], file_exists($path)?filemtime($path):null);
  }
}, 30);

3) In the Company post edit screen:
   - Set the **Featured Image** (used as hero background + inline image)
   - Fill ACF fields: rate_min, rate_max, limit_amount, exam_fast, no_interest_days, web_only, cta_label, cta_url, rank_score
   - Use the post content area to write points/description

Optional:
- Replace --c-primary color in CSS to match brand.
- If ACF fields differ, adjust field names in single-card_company.php accordingly.

Tracking:
- The CTA anchor has class "btn btn--primary apply-btn" which will be captured by the GA4 CTA script we added earlier.
