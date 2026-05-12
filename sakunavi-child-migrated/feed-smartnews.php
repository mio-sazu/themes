<?php
/**
 * RSS2.0 Template for SmartNews SmartFormat
 */
header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:snf="http://www.smartnews.be/snf">

<channel>
    <title><?php bloginfo_rss('name'); ?></title>
    <link><?php self_link(); ?></link>
    <description><?php bloginfo_rss('description'); ?></description>
    <language><?php bloginfo_rss('language'); ?></language>
    <copyright>Copyright © <?php echo date('Y'); ?> <?php bloginfo_rss('name'); ?></copyright>
    <snf:logo><url>https://saku-okane-navi.com/path-to-your-logo.png</url></snf:logo>

    <?php
    $args = array('posts_per_page' => 20); // 最新20件を取得
    $query = new WP_Query($args);
    while ($query->have_posts()) : $query->the_post();
    ?>
    <item>
        <title><?php the_title_rss(); ?></title>
        <link><?php the_permalink_rss(); ?></link>
        <pubDate><?php echo get_post_time('r', true); ?></pubDate>
        <content:encoded>
            <![CDATA[
                <?php the_content(); // 記事全文を出力 ?>
            ]]>
        </content:encoded>
        <?php if (has_post_thumbnail()): ?>
            <?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large'); ?>
            <media:thumbnail url="<?php echo $image[0]; ?>" />
        <?php endif; ?>
    </item>
    <?php endwhile; wp_reset_postdata(); ?>
</channel>
</rss>