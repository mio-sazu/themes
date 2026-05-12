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