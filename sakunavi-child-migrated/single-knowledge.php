<?php
/**
 * Template: 個別ナレッジ記事
 * CPT: knowledge
 * CSS: style.css（グローバル）, assets/css/support.css, assets/css/knowledge.css
 */
get_header(); ?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main>
                <?php if (have_posts()) : while (have_posts()) : the_post();

                    $post_id         = get_the_ID();
                    $kn_types        = get_the_terms($post_id, 'knowledge_type');
                    $kn_cats         = get_the_terms($post_id, 'knowledge_category');
                    $one_liner       = function_exists('get_field') ? get_field('one_liner',        $post_id) : '';
                    $short_answer    = function_exists('get_field') ? get_field('short_answer',     $post_id) : '';
                    $detail          = function_exists('get_field') ? get_field('detail',           $post_id) : '';
                    $example         = function_exists('get_field') ? get_field('kn_example',       $post_id) : '';
                    $caution         = function_exists('get_field') ? get_field('kn_caution',       $post_id) : '';
                    $misconception   = function_exists('get_field') ? get_field('kn_misconception', $post_id) : '';
                    $related_faq     = function_exists('get_field') ? get_field('kn_related_faq',   $post_id) : [];
                    $related_columns = function_exists('get_field') ? get_field('kn_related_columns', $post_id) : [];
                ?>

                <article <?php post_class('kn-entry'); ?>>
                    <header class="kn-entry-header">
                        <div class="kn-entry-meta-top">
                            <?php if ($kn_types && !is_wp_error($kn_types)): ?>
                            <span class="kn-type-badge kn-type-badge--<?php echo esc_attr($kn_types[0]->slug); ?>">
                                <?php echo esc_html($kn_types[0]->name); ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($kn_cats && !is_wp_error($kn_cats)): ?>
                            <span class="kn-cat-label"><?php echo esc_html($kn_cats[0]->name); ?></span>
                            <?php endif; ?>
                        </div>

                        <h1 class="kn-entry-title"><?php the_title(); ?></h1>

                        <p class="kn-entry-updated">
                            更新：<time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
                                <?php echo esc_html(get_the_modified_date()); ?>
                            </time>
                        </p>
                    </header>

                    <?php if ($one_liner): ?>
                    <div class="kn-one-liner">
                        <p class="kn-one-liner__label">ポイント</p>
                        <p class="kn-one-liner__text"><?php echo esc_html($one_liner); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($detail): ?>
                    <div class="kn-detail entry-content">
                        <?php echo wp_kses_post($detail); ?>
                    </div>
                    <?php elseif ($short_answer): ?>
                    <div class="kn-detail entry-content">
                        <p><?php echo nl2br(esc_html($short_answer)); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($example): ?>
                    <div class="kn-example">
                        <p class="kn-example__heading">実例・補足</p>
                        <p><?php echo nl2br(esc_html($example)); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($caution): ?>
                    <div class="kn-caution">
                        <p class="kn-caution__heading">注意点</p>
                        <p><?php echo nl2br(esc_html($caution)); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($misconception): ?>
                    <div class="kn-misconception">
                        <p class="kn-misconception__heading">よくある誤解</p>
                        <p><?php echo nl2br(esc_html($misconception)); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if ($related_faq && is_array($related_faq)): ?>
                    <div class="kn-related-section">
                        <p class="kn-related-section__heading">関連FAQ・用語</p>
                        <ul class="kn-related-list">
                            <?php foreach ($related_faq as $fid):
                                $fp = get_post($fid);
                                if (!$fp || $fp->post_status !== 'publish') continue;
                            ?>
                            <li><a href="<?php echo esc_url(get_permalink($fid)); ?>"><?php echo esc_html($fp->post_title); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if ($related_columns && is_array($related_columns)): ?>
                    <div class="kn-related-section">
                        <p class="kn-related-section__heading">あわせて読みたいコラム</p>
                        <ul class="kn-related-list kn-related-list--columns">
                            <?php foreach ($related_columns as $cid):
                                $cp = get_post($cid);
                                if (!$cp || $cp->post_status !== 'publish') continue;
                                $thumb = get_the_post_thumbnail_url($cid, 'thumbnail');
                            ?>
                            <li class="kn-related-col-item">
                                <?php if ($thumb): ?>
                                <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy" class="kn-related-col-item__thumb">
                                <?php endif; ?>
                                <a href="<?php echo esc_url(get_permalink($cid)); ?>"><?php echo esc_html($cp->post_title); ?></a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                </article>

                <?php endwhile; else: ?>
                <p>ページが見つかりません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>

<?php get_footer(); ?>
