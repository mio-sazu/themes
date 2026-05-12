<?php
/**
 * Archive: card_loan_company 一覧
 */

$css_path = get_stylesheet_directory() . '/assets/css/company-archive.css';
wp_enqueue_style('sakunavi-company-archive', get_stylesheet_directory_uri().'/assets/css/company-archive.css', [], file_exists($css_path)?filemtime($css_path):null);

get_header(); ?>

<div class="wrapper company-archive">
  <?php get_template_part('template-parts/breadcrumbs'); ?>
  <h1 class="archive-title">カードローン会社 一覧</h1>

  <?php if ( have_posts() ) : ?>
    <div class="company-cards">
      <?php while ( have_posts() ) : the_post();
        $rate_min = function_exists('get_field') ? get_field('rate_min') : get_post_meta(get_the_ID(),'rate_min',true);
        $rate_max = function_exists('get_field') ? get_field('rate_max') : get_post_meta(get_the_ID(),'rate_max',true);
        $limit_amt = function_exists('get_field') ? get_field('limit_amount') : get_post_meta(get_the_ID(),'limit_amount',true);
        $exam_fast = function_exists('get_field') ? get_field('exam_fast') : get_post_meta(get_the_ID(),'exam_fast',true);
        $cta_label = function_exists('get_field') ? get_field('cta_label') : get_post_meta(get_the_ID(),'cta_label',true);
        $cta_url   = function_exists('get_field') ? get_field('cta_url')   : get_post_meta(get_the_ID(),'cta_url',true);
        if (!$cta_label) $cta_label = '詳細を見る';
        if (!$cta_url)   $cta_url = get_permalink();
      ?>
      <article class="company-card">
        <a class="thumb" href="<?php the_permalink(); ?>">
          <?php if ( has_post_thumbnail() ) { the_post_thumbnail('medium'); } ?>
        </a>
        <div class="meta">
          <h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <ul class="spec">
            <?php if ($rate_min !== '' || $rate_max !== ''): ?>
              <li><span>金利</span>
                <?php
                  $min = $rate_min !== '' ? floatval($rate_min) : null;
                  $max = $rate_max !== '' ? floatval($rate_max) : null;
                  if (!is_null($min) && !is_null($max))      echo esc_html($min) . '% ～ ' . esc_html($max) . '%';
                  elseif (!is_null($min) && is_null($max))   echo esc_html($min) . '%';
                  elseif (is_null($min) && !is_null($max))   echo '～ ' . esc_html($max) . '%';
                ?>
              </li>
            <?php endif; ?>
            <?php if ($limit_amt !== ''): ?><li><span>限度額</span><?php echo esc_html(number_format($limit_amt)); ?>万円</li><?php endif; ?>
            <?php if ($exam_fast): ?><li><span>審査</span><?php echo esc_html($exam_fast); ?></li><?php endif; ?>
          </ul>
          <div class="actions">
            <a class="btn" href="<?php the_permalink(); ?>">詳細</a>
            <a class="btn btn--ghost apply-btn" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
          </div>
        </div>
      </article>
      <?php endwhile; ?>
    </div>

    <div class="pagination">
      <?php the_posts_pagination([
        'mid_size'  => 1,
        'prev_text' => '«',
        'next_text' => '»',
      ]); ?>
    </div>

  <?php else : ?>
    <p>現在、登録がありません。</p>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
