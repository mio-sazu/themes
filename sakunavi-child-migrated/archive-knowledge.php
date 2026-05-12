<?php get_header(); ?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main class="kn-archive">
                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        if (is_tax('knowledge_type')) {
                            $term = get_queried_object();
                            echo esc_html($term->name) . '一覧';
                        } elseif (is_tax('knowledge_category')) {
                            $term = get_queried_object();
                            echo esc_html($term->name) . '｜ナレッジ';
                        } else {
                            echo 'FAQ・用語一覧';
                        }
                        ?>
                    </h1>
                    <?php
                    if (is_tax()) {
                        $term = get_queried_object();
                        if ($term && !is_wp_error($term) && !empty($term->description)):
                    ?>
                    <p class="page-lead"><?php echo esc_html($term->description); ?></p>
                    <?php
                        endif;
                    }
                    ?>
                </header>

                <?php
                // 種別タブ
                $kn_type_terms = get_terms(['taxonomy' => 'knowledge_type', 'hide_empty' => true]);
                if ($kn_type_terms && !is_wp_error($kn_type_terms)):
                    $current_type = is_tax('knowledge_type') ? get_queried_object()->slug : '';
                ?>
                <nav class="kn-type-tabs" aria-label="種別で絞り込む">
                    <ul class="kn-type-tabs__list">
                        <li class="kn-type-tabs__item <?php echo !$current_type ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(get_post_type_archive_link('knowledge')); ?>">すべて</a>
                        </li>
                        <?php foreach ($kn_type_terms as $t): ?>
                        <li class="kn-type-tabs__item <?php echo $current_type === $t->slug ? 'is-active' : ''; ?>">
                            <a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                <?php endif; ?>

                <?php if (have_posts()) : ?>
                <div class="kn-list">
                    <?php while (have_posts()) : the_post();
                        $kn_types = get_the_terms(get_the_ID(), 'knowledge_type');
                        $one_liner = function_exists('get_field') ? get_field('one_liner') : '';
                    ?>
                    <article class="kn-card">
                        <div class="kn-card__head">
                            <?php if ($kn_types && !is_wp_error($kn_types)): ?>
                            <span class="kn-type-badge kn-type-badge--<?php echo esc_attr($kn_types[0]->slug); ?>">
                                <?php echo esc_html($kn_types[0]->name); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <h2 class="kn-card__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <?php if ($one_liner): ?>
                        <p class="kn-card__answer"><?php echo esc_html(mb_strimwidth($one_liner, 0, 80, '…')); ?></p>
                        <?php endif; ?>
                    </article>
                    <?php endwhile; ?>
                </div>

                <div class="pagination">
                    <?php echo paginate_links([
                        'total'     => $wp_query->max_num_pages,
                        'current'   => max(1, get_query_var('paged')),
                        'mid_size'  => 1,
                        'prev_text' => '« 前へ',
                        'next_text' => '次へ »',
                    ]); ?>
                </div>
                <?php else: ?>
                <p>ナレッジがまだ登録されていません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>

<?php get_footer(); ?>
