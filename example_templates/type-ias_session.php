<?php
/**
 * Example template for ias_session archive
 */
?>

<?php get_header(); ?>
  
<?php /* START THE LOOP */ ?>

<h1>All Sessions</h1>
<?php $sessions_by_day = get_sessions_by_day(); ?>
<table class="schedule-grid">         
  <?php foreach($sessions_by_day as $day => $sessions): ?>            
    <tr class="schedule-grid-date">
      <th colspan="3"><h2><?php echo $day ?></h2></th>
    </tr>
    <?php foreach($sessions as $session): ?>
  	  <tr>
      <?php 
  	    $time = get_session_time($session->ID, false); 
        if (!$last_time) $last_time = '';
        if ($time != $last_time):
  	  ?>
  	  <td class="schedule-grid-time"><?php get_session_time($session->ID, true); ?></td>
  	  <?php   
	      $last_time = get_session_time($session->ID, false);
  	    else:
  	  ?>
    	  <td class="schedule-grid-time"></td>
  	  <?php   
  	    endif;
  	  ?>
	    <td class="schedule-grid-session" id="post-<?php echo $session->ID; ?>">
	      <?php get_session_link($session->ID, true); ?>
	      <div class="schedule-grid-speakers"><?php get_session_speakers($session->ID, true); ?></div>
	    </td>          	    
  	</tr><!-- #post-## -->              
  <?php endforeach; ?>
<?php endforeach; ?>

<?php /* END THE LOOP */ ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
