<?php
/**
 * Example template for ias_speaker pages
 */
?>

<?php get_header(); ?>

<?php /* START THE LOOP */ ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <h1 class="session-title"><?php the_title(); ?></h1>
	<div class="session-description">
    <?php the_content(); ?>
  </div>
  <div class="session-info">
    <h2>Date &amp; Time</h2> 
    <p><?php get_session_date_time(get_the_ID(), true); ?></p>
    <?php if ( has_session_location() ) : ?>
      <h2>Location</h2> 
      <p><?php get_session_location(get_the_ID(), true); ?></p>
    <?php endif; ?>
    <h2>Speakers</h2>
    <ul>  
      <?php foreach(split(',', get_post_meta(get_the_ID(),'session_speakers', true)) as $speaker_ID): ?>
        <li><?php get_speaker_link($speaker_ID, true); ?></li>
      <?php endforeach; ?>
    </ul>                                           
    <?php if ( has_session_track() ) : ?>
      <h2>Track</h2> 
      <p><?php get_session_track(get_the_ID(), true); ?></p>
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
