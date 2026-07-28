<?php if (! is_active_sidebar('sidebar-main')) : ?>
  <!-- ウィジェット未設定時は静的HTMLを表示 -->
  <aside class="column-sidebar">

    <?php
    // ============================
    // 共通設定
    // ============================

    // 「条件検索」親カテゴリを取得
    // スラッグが違う場合は 'conditional' を実際のスラッグに変更
    $conditional_parent = get_term_by('slug', 'conditional', 'column_category');
    $conditional_parent_id = ($conditional_parent && ! is_wp_error($conditional_parent))
      ? (int) $conditional_parent->term_id
      : 0;
    ?>

    <!-- ============================
         最新の記事
    ============================ -->
    <?php
    $sb_latest = get_posts([
      'post_type'      => 'column',
      'posts_per_page' => 5,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
    ]);
    if (! empty($sb_latest)) :
      $column_archive = get_post_type_archive_link('column') ?: home_url('/column/');
    ?>
    <div class="sidebar-box">
      <h3>最新の記事</h3>
      <ul class="sb-article-list">
        <?php foreach ($sb_latest as $col) :
          $thumb = get_the_post_thumbnail_url($col->ID, 'thumbnail');
          $date  = get_the_modified_date('Y.m.d', $col->ID);
          $terms = get_the_terms($col->ID, 'column_category');
          $cats  = ($terms && ! is_wp_error($terms)) ? $terms : [];
        ?>
          <li class="sb-article-list__item">
            <a href="<?php echo esc_url(get_permalink($col->ID)); ?>" class="sb-article-list__link">
              <span class="sb-article-list__thumb<?php echo $thumb ? '' : ' sb-article-list__thumb--noimg'; ?>">
                <?php if ($thumb) : ?>
                  <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy">
                <?php endif; ?>
              </span>
              <span class="sb-article-list__body">
                <span class="sb-article-list__date"><?php echo esc_html($date); ?></span>
                <span class="sb-article-list__title"><?php echo esc_html($col->post_title); ?></span>
              </span>
            </a>
            <?php if (! empty($cats)) : ?>
              <div class="sb-article-list__cats">
                <?php foreach ($cats as $cat) : ?>
                  <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="sb-article-list__cat">#<?php echo esc_html($cat->name); ?></a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <a href="<?php echo esc_url($column_archive); ?>" class="sidebar-box__more">一覧を見る &rsaquo;</a>
    </div>
    <?php endif; ?>

    <!-- ============================
         編集者おすすめの記事
    ============================ -->
    <?php
    $sb_picks = get_posts([
      'post_type'      => 'column',
      'posts_per_page' => 5,
      'post_status'    => 'publish',
      'orderby'        => 'date',
      'order'          => 'DESC',
      'meta_query'     => [[
        'key'   => 'is_editor_pick',
        'value' => '1',
      ]],
    ]);
    if (! empty($sb_picks)) :
    ?>
    <div class="sidebar-box">
      <h3>編集者おすすめ</h3>
      <ul class="sb-article-list">
        <?php foreach ($sb_picks as $col) :
          $thumb = get_the_post_thumbnail_url($col->ID, 'thumbnail');
          $date  = get_the_modified_date('Y.m.d', $col->ID);
          $terms = get_the_terms($col->ID, 'column_category');
          $cats  = ($terms && ! is_wp_error($terms)) ? $terms : [];
        ?>
          <li class="sb-article-list__item">
            <a href="<?php echo esc_url(get_permalink($col->ID)); ?>" class="sb-article-list__link">
              <span class="sb-article-list__thumb<?php echo $thumb ? '' : ' sb-article-list__thumb--noimg'; ?>">
                <?php if ($thumb) : ?>
                  <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy">
                <?php endif; ?>
              </span>
              <span class="sb-article-list__body">
                <span class="sb-article-list__date"><?php echo esc_html($date); ?></span>
                <span class="sb-article-list__title"><?php echo esc_html($col->post_title); ?></span>
              </span>
            </a>
            <?php if (! empty($cats)) : ?>
              <div class="sb-article-list__cats">
                <?php foreach ($cats as $cat) : ?>
                  <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="sb-article-list__cat">#<?php echo esc_html($cat->name); ?></a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <!-- ============================
         人気のキーワード
    ============================ -->
    <div class="sidebar-box">
      <?php get_template_part('template-parts/popular-keywords'); ?>
    </div>

    <!-- ============================
         カテゴリー一覧
         ※ お金コラム用カテゴリを表示
         ※ 条件検索の親カテゴリ conditional は除外
    ============================ -->
    <div class="sidebar-box">
      <h3>カテゴリー一覧</h3>
      <ul>
        <?php
        $category_terms = get_terms([
          'taxonomy'   => 'column_category',
          'hide_empty' => false,
          'parent'     => 0, // 親カテゴリだけ表示したい場合
          'orderby'    => 'name',
          'order'      => 'ASC',
          'exclude'    => $conditional_parent_id ? [$conditional_parent_id] : [],
        ]);

        if (! empty($category_terms) && ! is_wp_error($category_terms)) :
          foreach ($category_terms as $term) :
        ?>
            <li>
              <a href="<?php echo esc_url(get_term_link($term)); ?>">
                <?php echo esc_html($term->name); ?>
              </a>
            </li>
        <?php
          endforeach;
        else :
          echo '<li>まだカテゴリーがありません。</li>';
        endif;
        ?>
      </ul>
    </div>

    <!-- ============================
         条件一覧
         ※ column_category の親「conditional」の子カテゴリだけ表示
         ※ グローバルメニューの条件検索と揃える
    ============================ -->
    <div class="sidebar-box">
      <h3>条件一覧</h3>
      <ul>
        <?php
        if ($conditional_parent_id) {

          $condition_terms = get_terms([
            'taxonomy'   => 'column_category',
            'hide_empty' => false,
            'parent'     => $conditional_parent_id,
            'orderby'    => 'name',
            'order'      => 'ASC',
          ]);

          if (! empty($condition_terms) && ! is_wp_error($condition_terms)) :
            foreach ($condition_terms as $term) :
        ?>
              <li>
                <a href="<?php echo esc_url(get_term_link($term)); ?>">
                  <?php echo esc_html($term->name); ?>
                </a>
              </li>
        <?php
            endforeach;
          else :
            echo '<li>条件カテゴリがまだありません。</li>';
          endif;
        } else {
          echo '<li>条件検索の親カテゴリが見つかりません。</li>';
        }
        ?>
      </ul>
    </div>

    <!-- ============================
         よくある質問（ナレッジ上位5件）
    ============================ -->
    <?php
    $sb_faq = new WP_Query([
      'post_type'      => 'knowledge',
      'posts_per_page' => 5,
      'orderby'        => 'meta_value_num',
      'order'          => 'ASC',
      'meta_key'       => 'kn_display_order',
      'meta_query'     => [[
        'key'     => 'kn_display_order',
        'value'   => 0,
        'compare' => '>=',
        'type'    => 'NUMERIC',
      ]],
    ]);

    if ($sb_faq->have_posts()) :
      $faq_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'page-knowledge-faq.php', 'number' => 1]);
      $faq_url   = $faq_pages ? get_permalink($faq_pages[0]->ID) : null;
    ?>
    <div class="sidebar-box">
      <h3>よくある質問</h3>
      <ul>
        <?php while ($sb_faq->have_posts()) : $sb_faq->the_post(); ?>
          <li>
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </li>
        <?php endwhile; wp_reset_postdata(); ?>
      </ul>
      <?php if ($faq_url) : ?>
      <a href="<?php echo esc_url($faq_url); ?>" class="sidebar-box__more">一覧を見る &rsaquo;</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ============================
         カードローン会社一覧
    ============================ -->
    <div class="sidebar-box">
      <h3>カードローン会社一覧</h3>
      <ul>
        <?php
        $companies = get_posts([
          'post_type'      => 'card_loan_company',
          'posts_per_page' => -1,
          'orderby'        => 'menu_order',
          'order'          => 'ASC',
        ]);

        if (! empty($companies)) :
          foreach ($companies as $post) :
            setup_postdata($post);
        ?>
            <li>
              <a href="<?php echo esc_url(get_permalink()); ?>">
                <?php echo esc_html(get_the_title()); ?>
              </a>
            </li>
        <?php
          endforeach;
          wp_reset_postdata();
        else :
          echo '<li>カードローン会社がまだありません。</li>';
        endif;
        ?>
      </ul>
    </div>

  <!-- ============================
      おすすめコンテンツ
  ============================ -->
    <div class="sidebar-box banner-column">
      <h3>おすすめコンテンツ</h3>

      <?php
      // ----------------------------
      // コラムカテゴリ取得
      // ----------------------------
      $term_beginner = get_term_by('name', '初めてのカードローン', 'column_category');
      $term_choice   = get_term_by('name', 'カード会社の選び方', 'column_category');

      // ----------------------------
      // 固定ページ「返済シミュレーター」を取得
      // ※ スラッグが日本語の場合
      // ----------------------------
      $simulator_page = get_page_by_path('返済シミュレーター');
      ?>

      <!-- 初めてのカードローン -->
      <?php if ($term_beginner && ! is_wp_error($term_beginner)) : ?>
        <a href="<?php echo esc_url(get_term_link($term_beginner)); ?>">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/index/bana01.png'); ?>" alt="初めてのカードローン">
        </a>
      <?php endif; ?>

      <!-- 返済シミュレーター -->
      <?php if ($simulator_page) : ?>
        <a href="<?php echo esc_url(get_permalink($simulator_page->ID)); ?>">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/index/bana02.png'); ?>" alt="返済シミュレーター">
        </a>
      <?php endif; ?>

      <!-- カードローン会社の選び方 -->
      <?php if ($term_choice && ! is_wp_error($term_choice)) : ?>
        <a href="<?php echo esc_url(get_term_link($term_choice)); ?>">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/index/bana03.png'); ?>" alt="カードローン会社の選び方">
        </a>
      <?php endif; ?>
    </div>

  </aside>
<?php else: ?>
  <!-- ウィジェットが設定されていればそちらを優先 -->
  <aside class="column-sidebar">
    <?php dynamic_sidebar('sidebar-main'); ?>
  </aside>
<?php endif; ?>
