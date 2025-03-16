<?php
/*
Template Name: Front page template
*/
?>

<?php get_header(); ?>
    <div class="main-content-wrap">
      <?php while (have_posts()): the_post()?>
        <?php the_content();?>
      <?php endwhile;?>
    </div>
<?php get_footer();?>
