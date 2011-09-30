<?php
/**
 * Example template for ias_speaker pages
 */
?>

<?php get_header(); ?>

<?php /* START THE LOOP */ ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h1 class="speaker-name"><?php the_title(); ?></h1>
	<div class="speaker-bio">
    <?php the_content(); ?>
  </div>
	<div class="spaker-info">
    <?php if (has_post_thumbnail()): ?>
      <?php the_post_thumbnail(array(270,600),array('class' => 'speaker-photo')); ?>
    <?php endif ?>

    <?php if ($sessions = find_speaker_sessions($post->ID)): ?>  
      <h2>Talks</h2>
      <ul>
        <?php foreach ($sessions as $session): ?>                    
          <li><a href="<?php echo get_permalink($session->ID) ?>"><?php echo __($session->post_title) ?></a></li>
        <?php endforeach; ?>                            
      </ul>                              
    <?php endif; ?>

    <?php if (has_speaker_links()): ?>  
      <h2>Elsewhere</h2>
      <ul>
        <?php the_speaker_links(); ?>
      </ul>                              
    <?php endif; ?>
  </div>
	<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
	<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
  <hr />
  <div id="nav-below" class="navigation">
  	<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'twentyten' ) . '</span> %title' ); ?></div>
  	<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'twentyten' ) . '</span>' ); ?></div>
  </div><!-- #nav-below -->
</div><!-- /#post-## -->
<?php endwhile; ?>

<?php /* END THE LOOP */ ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
