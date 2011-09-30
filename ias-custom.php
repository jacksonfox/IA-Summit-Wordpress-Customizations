<?php
/*
Plugin Name: IA Summit Customizations
Plugin URI: http://iasummit.org
Description: Customized code for the IA Summit site. Provides custom post types for Sessions and Speakers. 
Author: Jackson Fox
Version: 1.0.0
Author URI: http://jacksonfox.org/
*/

/**
 * Define the ias_session custom post type, make sure it's created in init
 */
function create_ias_session() {
	register_post_type( 'ias_session',
		array(
			'labels' => array(
				'name' => __('Sessions'),
				'singular_name' => __('Session'),
				'add_new_item' => __('Add New Session'),
				'edit_item' => __('Edit Session')
			),
		'public' => true,                   
		'rewrite' => array('slug' => 'sessions', 'with_front' => false)
		)
	);
}                                         

add_action( 'init', 'create_ias_session' );

/**
 * Add custom metadata for ias_sessions during init
 */
function add_ias_session_custom_fields() {
  register_taxonomy("Tracks", array("ias_session"), array("hierarchical" => false, "label" => "Tracks", "singular_label" => "Track", "rewrite" => true));
  register_taxonomy("Topics", array("ias_session"), array("hierarchical" => false, "label" => "Topics", "singular_label" => "Topic", "rewrite" => true));                    
  register_taxonomy("Date_Time", array("ias_session"), array("hierarchical" => true, "label" => "Date & Time", "singular_label" => "Day & Time", "rewrite" => true));                    
  register_taxonomy("Rooms", array("ias_session"), array("hierarchical" => false, "label" => "Rooms", "singular_label" => "Room", "rewrite" => true));                      
}

add_action( 'init', 'add_ias_session_custom_fields' );

/**
 * Add the speaker selection custom field for ias_sessions
 */
function sessions_meta_init() {
  add_meta_box("speakers_meta", "Speakers", "render_speakers_meta", "ias_session", "normal", "low");
}                                                                    

/**
 * Render the speaker selection custom field for ias_sessions
 */
function render_speakers_meta() {                
  $current_speakers = split(',',get_custom_field_value("session_speakers"));
  $speakers = get_posts(
    array(
      'numberposts' => -1,
      'orderby' => 'title',
      'post_type' => 'ias_speaker'
    )
  );
  wp_nonce_field( plugin_basename(__FILE__), 'ias_session_speakers' );                       
  echo "<div style=\"overflow:hidden;\">";
  foreach ($speakers as $speaker) {               
    echo "<div style=\"float:left;width:150px;margin:5px 0px;\">";
    if (in_array($speaker->ID, $current_speakers)) {
      echo "<input style=\"margin-right:5px;\" type=\"checkbox\" id=\"session_speakers[$speaker->ID]\" name=\"session_speakers[$speaker->ID]\" value=\"$speaker->ID\" checked=\"checked\" />";      
      echo "<label style=\"margin-right:0px;\" for=\"session_speakers[$speaker->ID]\">$speaker->post_title</label>";
    } else {
      echo "<input style=\"margin-right:5px;\" type=\"checkbox\" id=\"session_speakers[$speaker->ID]\" name=\"session_speakers[$speaker->ID]\" value=\"$speaker->ID\" />";      
      echo "<label style=\"margin-right:0px;\" for=\"session_speakers[$speaker->ID]\">$speaker->post_title</label>";
    }                                              
    echo "</div>";
  }
  echo "</div>";
}         

/**
 * Save the selected speakers when saving changes to ias_session posts
 */                                 
function save_speakers_meta($post_id) {
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( !wp_verify_nonce( $_POST['ias_session_speakers'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return $post_id;    
  }

  $speakers = implode(',',$_POST["session_speakers"]);         
  update_post_meta($post_id, "session_speakers", $speakers);
}

/* Make sure ias_session custom fields get created and saved */
add_action('admin_init', 'sessions_meta_init');                                                        
add_action('save_post', 'save_speakers_meta');

/**
 * Define the ias_speaker custom post type, make sure it's created in init
 */
function create_ias_speaker() {
	register_post_type( 'ias_speaker',
		array(
			'labels' => array(
				'name' => __('Speakers'),
				'singular_name' => __('Speaker'),
				'add_new_item' => __('Add New Speaker'),
				'edit_item' => __('Edit Speaker')
			),
		'public' => true,                   
		'rewrite' => array('slug' => 'speakers', 'with_front' => false),
		'supports' => array('title', 'editor', 'thumbnail')
		)
	);
}

add_action( 'init', 'create_ias_speaker' );

/**
 * Add the external links custom fields for ias_speakers
 */
function speakers_meta_init() {
  add_meta_box("speaker_links_meta", "External Links", "render_speaker_links_meta", "ias_speaker", "normal", "low");
}                                                                                      

/**
 * Render the external links custom fields for ias_speakers
 */
function render_speaker_links_meta($post) {                                    
  $custom = get_post_custom($post->ID);
  $link_text_1 = $custom['link_1_text'][0]; 
  $link_url_1 = $custom['link_1_url'][0]; 
  $link_text_2 = $custom['link_2_text'][0]; 
  $link_url_2 = $custom['link_2_url'][0]; 
  $link_text_3 = $custom['link_3_text'][0]; 
  $link_url_3 = $custom['link_3_url'][0]; 
  wp_nonce_field( plugin_basename(__FILE__), 'ias_speaker_links' );
  echo "<p style=\"color:#aaa;\">You can provide up to 3 external links for this speaker:</p>";
  echo "<label style=\"font-weight:bold;\" for=\"\">Link #1 &rarr; Text</label>";
  echo "<input name=\"link_1_text\" id=\"link_1_text\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_text_1\" />";
  echo "<label style=\"font-weight:bold;margin-left:10px;\" for=\"\">+ URL</label>";
  echo "<input name=\"link_1_url\" id=\"link_1_url\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_url_1\" />";
  echo "<br />";
  echo "<label style=\"font-weight:bold;\" for=\"\">Link #2 &rarr; Text</label>";
  echo "<input name=\"link_2_text\" id=\"link_2_text\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_text_2\" />";
  echo "<label style=\"font-weight:bold;margin-left:10px;\" for=\"\">+ URL</label>";
  echo "<input name=\"link_2_url\" id=\"link_2_url\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_url_2\" />";
  echo "<br />";
  echo "<label style=\"font-weight:bold;\" for=\"\">Link #3 &rarr; Text</label>";
  echo "<input name=\"link_3_text\" id=\"link_3_text\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_text_3\" />";
  echo "<label style=\"font-weight:bold;margin-left:10px;\" for=\"\">+ URL</label>";
  echo "<input name=\"link_3_url\" id=\"link_3_url\" style=\"margin-left:10px;\" type=\"text\" value=\"$link_url_3\" />";
  echo "<br />";
}

/**
 * Save custom links when saving changes to ias_speaker posts
 */
function save_links_meta($post_id) {
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( !wp_verify_nonce( $_POST['ias_speaker_links'], plugin_basename(__FILE__) )) {
    return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return $post_id;    
  }

  update_post_meta($post_id, "link_1_text", $_POST["link_1_text"]); 
  update_post_meta($post_id, "link_1_url", $_POST["link_1_url"]);
  update_post_meta($post_id, "link_2_text", $_POST["link_2_text"]); 
  update_post_meta($post_id, "link_2_url", $_POST["link_2_url"]);
  update_post_meta($post_id, "link_3_text", $_POST["link_3_text"]); 
  update_post_meta($post_id, "link_3_url", $_POST["link_3_url"]);
}

/* Make sure ias_speaker custom fields get created and saved */
add_action('admin_init', 'speakers_meta_init');  
add_action('save_post', 'save_links_meta');                                                      

/* Add theme support for thumbnails, we use these for speaker images... */
add_theme_support('post-thumbnails');
add_action('init', 'remove_thumbnails');

/* ...but remove them from normal pages and posts */
function remove_thumbnails() {
  remove_post_type_support('page', 'thumbnail');
  remove_post_type_support('post', 'thumbnail');
}

/*---- The following are a crap ton of utility functions for working with sessions and speakers... ----*/

/**
 * Given a session, check if the current speaker in the loop is associated with that session
 * Returns true if speaker is associated with the session, false if not
 */
function is_speaker_session($session) {
  global $post;
  $speakers = split(',',get_post_meta($session->ID, "session_speakers", true));
  return(in_array($post->ID, $speakers));
}

/**
 * Given the ID of a speaker, get a link to that speaker's page
 * Returns the link as a string if $print is false, echoes the link if $print is true
 */
function get_speaker_link($speaker_ID, $print = false) {
  $speaker = get_post($speaker_ID);
  $speaker_permalink = get_permalink($speaker_ID);
  $speaker_link = "<a href=\"$speaker_permalink\">$speaker->post_title</a>";
	if ($print == false) return $speaker_link; else echo $speaker_link;
}                          
 
/**
 * Given the ID of a speaker, return the speaker's name
 * Returns name as a string if $print is false, echoes name if $print is true
 */
function get_speaker_name($speaker_ID, $print = false) {
  $speaker = get_post($speaker_ID);
	if ($print == false) return $speaker->post_title; else echo $speaker->post_title;
}                          

/**
 * Given the ID of a speaker, return an array with all of their sessions
 * Returns sessions as an array
 */
function find_speaker_sessions($speaker_ID) {
  $sessions = get_posts(
    array(
      'numberposts' => -1,
      'orderby' => 'title',
      'post_type' => 'ias_session',
    )                            
  );                
  return(array_filter($sessions,'is_speaker_session'));
}

/**
 * Get a list of the speakers for the current session in the loop
 * Returns all of the speakers for a session in a comma delimited string
 */
function the_speakers() {
  global $post;
  $speakers = split(',',get_post_meta($post->ID, "session_speakers", true));
  $speakers = array_map('get_speaker_name', $speakers);
  echo join(', ', $speakers);
}

/**
 * Given the ID of a session, return a list of the speakers associated with it
 * Returns comma delimited list as a string if $print is false, echoes the list if $print is true
 */
function get_session_speakers($ID, $print = false) {
  $speakers = get_post_meta($ID, "session_speakers", true);
  if ($speakers) {
    $speakers = split(',', $speakers);    
    $speakers = array_map('get_speaker_name', $speakers);
    $speakers = join(', ', $speakers);    
  }
  if ($print == false) return $speakers; else echo $speakers;  
}  

/**
 * Check if a given term is a date
 * Returns true if the term contains the name of a day of the week, false if not
 */
function term_is_date($term) {  
  return preg_match('/(Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)/', $term->name);
}

/**
 * Given the ID of a session, return the date associated with it
 * Returns date as a string if $print is false, echoes the date if $print is true
 */
function get_session_date($ID, $print = false) {
  $session_date_time = wp_get_object_terms($ID, 'Date_Time');
  $session_date = array_filter($session_date_time, 'term_is_date');                                                                  
  $session_date = current($session_date);
  if ($print == false) return $session_date; else echo $session_date->name;  
}                          
                                                                
/**
 * Check if a given term is a time
 * Returns true if the term does not contain the name of a day of the week, false if it does
 */
function term_is_time($term) {
  return !preg_match('/(Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)/', $term->name);  
}

/**
 * Given the ID of a session, return the time slot associated with it
 * Returns time as a string if $print is false, echoes the time if $print is true
 */
function get_session_time($ID, $print = false) {
  $session_date_time = wp_get_object_terms($ID, 'Date_Time');
  $session_time = array_filter($session_date_time, 'term_is_time');
  $session_time = current($session_time);      
  if ($print == false) return $session_time; else echo $session_time->name;  
}                          

/**
 * Given the ID of a session, return the date & time associated with it
 * Returns date & time as a string if $print is false, echoes date & time if $print is true
 */
function get_session_date_time($ID, $print = false) {
  $session_time = get_session_time($ID);            
  $session_date = get_session_date($ID);
  $session_date_time = "$session_date->name, $session_time->name";           
	if ($print == false) return $session_date_time; else echo $session_date_time;
}  

/**
 * Check if the current speaker in the loop as external links associated with it
 * Returns true if the speaker has links, false if not
 */
function has_speaker_links() {
  global $post;         
  $custom = get_post_custom($post->ID);
  $link_text_1 = $custom['link_1_text'][0]; 
  $link_text_2 = $custom['link_2_text'][0]; 
  $link_text_3 = $custom['link_3_text'][0]; 
  if ($link_text_1 == '' && $link_text_2 == '' && $link_text_3 == '') return false; else return true;
}   

/**
 * Return all of the external links for the current speaker in the loop
 * Echoes each link as an <li> by default, or wrapped in $before and $after if specified
 */
function the_speaker_links($before = "<li>", $after = "</li>") { 
  global $post;
  $custom = get_post_custom($post->ID);
  $link_text_1 = $custom['link_1_text'][0]; 
  $link_url_1 = $custom['link_1_url'][0]; 
  $link_text_2 = $custom['link_2_text'][0]; 
  $link_url_2 = $custom['link_2_url'][0]; 
  $link_text_3 = $custom['link_3_text'][0]; 
  $link_url_3 = $custom['link_3_url'][0];
  
  $speaker_links = '';
  if ($link_text_1 != '') $speaker_links .= "$before<a href=\"$link_url_1\">$link_text_1</a>$after";
  if ($link_text_2 != '') $speaker_links .= "$before<a href=\"$link_url_2\">$link_text_2</a>$after";
  if ($link_text_3 != '') $speaker_links .= "$before<a href=\"$link_url_3\">$link_text_3</a>$after";
  echo $speaker_links;
}  

/**
 * Given the ID of a session, return a link to that session's page
 * Returns link as a string if $print is false, echoes link if $print is true
 */
function get_session_link($session_ID, $print = false) {
  $session = get_post($session_ID);
  $session_permalink = get_permalink($session_ID);
  $session_link = "";
  if (empty($session->post_content)) {
    $session_link = $session->post_title;
  } else {
    $session_link = "<a href=\"$session_permalink\">$session->post_title</a>";    
  }
	if ($print == false) return $session_link; else echo $session_link;
}    

/**
 * Check if the current session in the loop has a location associated with it
 * Returns true if the session has a location, false if not
 */
function has_session_location() {
  global $post;
  $session_locations = wp_get_object_terms($post->ID, 'Rooms');
  if (count($session_locations) == 0) return false; else return true;  
}                           

/**
 * Given the ID of a session, return its location
 * Returns location as a string if $print is false, echoes location if $print is true
 */
function get_session_location($ID, $print = false) {
  $session_locations = wp_get_object_terms($ID, 'Rooms');
  $session_location = current($session_locations);      
  if ($print == false) return $session_location; else echo $session_location->name;  
}                           

/**
 * Check if the current session in the loop has at least one track associated with it
 * Returns true if the session has at least one track, false if not
 */
 function has_session_track() {
  global $post;
  $session_tracks = wp_get_object_terms($post->ID, 'Tracks');
  if (count($session_tracks) == 0) return false; else return true;  
}                           

/**
 * Given the ID of a session, return its track
 * Returns track as a string if $print is false, echoes track if $print is true
 */
function get_session_track($ID, $print = false) {
  $session_tracks = wp_get_object_terms($ID, 'Tracks');
  $session_track = current($session_tracks);      
  if ($print == false) return $session_track; else echo $session_track->name;  
}                           

/**
 * Returns the list of days from the Date_Time taxonomy
 */
function get_days() {
  $all_terms = get_terms('Date_Time', array('hide_empty' => false));
  $days = array_filter($all_terms, 'term_is_date');
  return $days;
}

/**
 * Returns an array of all sessions, grouped by day
 */
function get_sessions_by_day() {
  $days = get_days();
  $sessions_by_day = '';     
  foreach ($days as $day) {
    $sessions_by_day[$day->name] = get_posts(
      array(
        'numberposts' => -1, 
        'post_type' => 'ias_session',
        'taxonomy' => 'Date_Time',
        'term' => $day->slug
      )
    );  
    usort($sessions_by_day[$day->name],'compare_sessions');
  }   
  return $sessions_by_day;
} 

/**
 * Sort function to sort sessions by time
 */
function compare_sessions($a, $b) {
  $a_time = convert_session_time($a->ID);
  $b_time = convert_session_time($b->ID);
  return $a_time - $b_time;
}

/**
 * Given the ID of a session, convert the time to a 24h representation for comparison
 * Returns the time as an integer representation in 24h format
 */
function convert_session_time($session_ID) {
  $session_time = get_session_time($session_ID);
  $session_time = preg_split("/ - /", $session_time->name);
  if (preg_match("/PM/",$session_time[0])) {
    $session_time = preg_split("/ /", $session_time[0]);
    $session_time = preg_replace("/:/", "", $session_time[0]);
    if ($session_time < 1200) $session_time += 1200;
  } else {
    $session_time = preg_split("/ /", $session_time[0]);    
    $session_time = preg_replace("/:/", "", $session_time[0]);
  }  
  return $session_time;
}

/**
 * Prettier version of print_r
 */
function pvd($var) {
  $info = print_r($var, true);
  echo "<pre>$info</pre>";
}

/**
 * Given a custom field name, return its value for the current post in the loop
 * Returns field value if $print is false, echoes field value if $print is true
 *
 * To use:
 * if ( function_exists('get_custom_field_value') ){
 *   get_custom_field_value('featured_image', true);
 * }
 */
function get_custom_field_value($custom_field, $print = false) {
	global $post;
	$custom_field_value = get_post_meta($post->ID, $custom_field, true);
	if ( $print == false ) return $custom_field_value; else echo $custom_field_value;
}
?>