# IA Summit Customizations for Wordpress

This plugin creates two new custom post types:

* ias_sessions
* ias_speakers

These allow us to manage sessions and speakers via Wordpress, and makes it easy to associate sessions and speakers. It was originally created for the IA Summit 2011 website.

## Sessions (ias_sessions)

Sessions have the following metadata:

* Tracks (tags, can have multiple values)
* Topics (tags, can have multiple values)
* Date & Time (categories, can have multiple values)
* Rooms (tags, can have multiple values)
* Speakers (pulled from ias_speakers, can select multiple speakers)

The plugin will automatically create the custom taxonomy types, but the site admin will need to create values by hand either before adding sessions or via the post editing form.

_Note on Date & Time:_ We found it easiest to create top-level categories for each day, then sub-categories within those days for each time slot. Other methods could work, but the plugin currently assumes that the Date & Time will be distinct categories. That is, while dates and times are in the same category tree, each category term can be either a date or a time, but not both.

## Speakers (ias_speakers)

Speakers have the following metadata:

* External Links (up to three, each can have a label and URL)
* Featured Image (headshot)

## Using speakers and sessions in themes

We've created a variety of functions that can be used to display information about sessions and speakers in Wordpress themes. Check out the `example_templates` folder for sample templates that use these functions.

### Session Functions

* get_sessions_by_day()                                   // Get all of the sessions grouped by day in an array             
* the_speakers()                                          // Print the list of speakers for the current session in the loop 
* has_session_track()                                     // Does the current session in the loop have a track specified?   
* has_session_location()                                  // Does the current session in the loop have a location specified?
* get_session_speakers($ID, $print = false)               // Get a list of the speakers for session with ID                 
* get_session_date($ID, $print = false)                   // Get the date for session with ID                               
* get_session_time($ID, $print = false)                   // Get the time slot for session with ID                          
* get_session_date_time($ID, $print = false)              // Get the date and time for session with ID                      
* get_session_location($ID, $print = false)               // Get the location for session with ID                           
* get_session_track($ID, $print = false)                  // Get the track for session with ID                              
* get_session_link($session_ID, $print = false)           // Get a link to the page for session with ID                     
                                                        
### Speaker Links                                       
                                                        
* get_speaker_link($speaker_ID, $print = false)           // Get a link to the page for the speaker with ID
* get_speaker_name($speaker_ID, $print = false)           // Get the name of the speaker with ID
* has_speaker_links()                                     // Does the current speaker in the loop have external links?
* the_speaker_links($before = "<li>", $after = "</li>")   // Output external links for the current speaker in the loop
* find_speaker_sessions($speaker_ID)                      // Return an array of sessions for the speaker with ID
                                                        
### Utility Functions                                   
                                                        
* pvd($var)                                               // Prettier version of print_r
* get_custom_field_value($custom_field, $print = false)   // Check if the current post in the loop has a value for the specified custom field