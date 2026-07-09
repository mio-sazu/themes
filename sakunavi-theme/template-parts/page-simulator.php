<?php
/* Template Name: 返済シミュレーター */
get_header();
?>

<div class="wrapper simulator-page" style="padding-top:32px; padding-bottom:32px;">
    <?php echo do_shortcode('[repayment_simulator]'); ?>
</div>

<?php get_footer(); ?>