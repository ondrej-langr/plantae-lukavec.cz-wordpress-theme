<?php
/*
Template Name: Single post page
*/
 ?>

<?php get_header(); ?>
  <main scope="page" type="post">
    <?php while (have_posts()): the_post()?>
      <?php the_content();?>
    <?php endwhile;?>
     <?php comments_template('', true)?>
  </main>

<?php get_footer()?>
