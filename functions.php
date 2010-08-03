<?php

//
// Upgrade Node Custom Functions for Thematic Framework
//
// This childtheme built on the Thematic Theme Framework
// More ideas can be found on "A Guide To Customizing The Thematic Theme Framework" 
// http://themeshaper.com/thematic-for-wordpress/guide-customizing-thematic-theme-framework/


// Clean up the Dashboard
// Removing unneccesary thematic sidebars
function childtheme_sidebars_init() {
 
// Unregister and sidebars you don't need based on its ID.
// For a full list of Thematic sidebar IDs, look at /thematc/library/extensions/widgets-extensions.php
unregister_sidebar('index-top');
unregister_sidebar('index-insert');
unregister_sidebar('index-bottom');
unregister_sidebar('single-top');
unregister_sidebar('single-insert');
unregister_sidebar('single-bottom');
unregister_sidebar('page-top');
unregister_sidebar('page-bottom');
unregister_sidebar('1st-subsidiary-aside');
unregister_sidebar('2nd-subsidiary-aside');
unregister_sidebar('3rd-subsidiary-aside');
}

// When WP initiates, add the above settings
add_action( 'init', 'childtheme_sidebars_init',20 );


// Add Blueprint CSS and WP Geo into wp_head
function add_wphead() {

 // Include main screen styles css
 $content .= "\t";
 $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"";
 $content .= get_bloginfo('stylesheet_directory');
 $content .= '/styles/grid.css';
 $content .= "\" media=\"grid\" />";
 $content .= "\n";

 // Include print css
 $content .= "\t";
 $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"";
 $content .= get_bloginfo('stylesheet_directory');
 $content .= '/styles/print.css';
 $content .= "\" media=\"print\" />";
 $content .= "\n";
 
 // Include IE-specific CSS fix
 $content .= "\t";
 $content .= "<!--[if lt IE 8]><link rel=\"stylesheet\" type=\"text/css\" href=\"";
 $content .= get_bloginfo('stylesheet_directory');
 $content .= '/styles/ie.css';
 $content .= "\" /><![endif]-->";
 $content .= "\n";
 
 // Include the original style.css again so it overides the blueprint code
 // Ideally we would've also found a way to remove the first reference to styles.css. If there's a better way, please share.
 $content .= "\t";
 $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"";
 $content .= get_bloginfo('stylesheet_url');
 $content .= "'\" media=\"style\" />";
 $content .= "\n";
 
 // Include any other stylesheet you are using
 $content .= "\t";
 $content .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"";
 $content .= get_bloginfo('stylesheet_directory');
 $content .= '/styles/upgradethematic.css';
 $content .= "\" media=\"upgradethematic\" />";
 $content .= "\n";

 // Echo the whole thing
 echo $content;
 
}
 
add_action ('wp_head', 'add_wphead');


// Add gmap divs and calling the Geo Mash Up map.
function gmap_div () {
  ?><!--<body class="<?php //thematic_body_class() ?> <?php //lang_dir() ?>" onload="load()" onunload="GUnload()">-->
  <div id="map">
      <div id="gmap">
      <?php echo GeoMashup::map('height=325&width=100%&add_overview_control=false&add_map_type_control=false');?>
      </div>
      <div id="stripe"></div>
  </div>
      <style>
          #map {
              background-image: url(<?=$header_bg_image?>);
              background-repeat: repeat;
          }
          </style>
    <?php
}
add_action (thematic_aboveheader, gmap_div);


// Determine if the language requires RTL settings (using the qTranslate plugin):
function lang_dir() {
	if ( function_exists('qtrans_getLanguage')) {
		$lang = strtolower(qtrans_getLanguage());
		if ($lang == "ar" || $lang == "fa" || $lang == "iw" || $lang == "ks" || $lang == "ps" || $lang == "sd" || $lang == "ur" || $lang == "yi"){
			echo "dir-rtl lang-".$lang;
		} else {
			echo "lang-".$lang;
		}
	}
}


/*
// Creates the links for the translations of the post. use either 'text', 'image', 'both' or 'dropdown':
function lang_links($id) {
	if ( function_exists('qtrans_getLanguage') ) {
		qtrans_generateLanguageSelectCode('text', $id);
	}
}
*/

// Address categories by name to check if a certain category has a certain slug
function in_category_name($name) {
	foreach (get_the_category() as $cat) {
		if ($cat->category_nicename == $name) {
			return true;
		}
	}
	return false;
}


// Gets the event time (using the Event Calendar plugin):
function ec3_schedule(){
	if ( function_exists('ec3_get_schedule')) {
		echo ec3_get_schedule('%s; ','%1$s %3$s %2$s. ','%s');
	} else {
		$event_time = get_the_time('l, F jS, Y') ." at ". get_the_time('G:i');
		echo $event_time;
	}
}

// Add RSS feed icon link
function add_rss(){
  ?><div class="" id="feed">
      <a title="RSS feed" href="<?php bloginfo('rss2_url'); ?>"><img alt="rss-feed" src="<?php bloginfo('stylesheet_directory'); ?>/styles/images/rss_icon.png"></a>
	</div><?php
}
add_action('thematic_abovecontainer','add_rss');


// Add search bar to header
function add_search() {
include (TEMPLATEPATH . '/searchform.php');
}
add_action('thematic_abovecontainer','add_search');


// Change search box text
function childtheme_search_value() {
  return " ";
}
add_filter('search_field_value', 'childtheme_search_value');


// Filtering the thematic postheader
// If not on a page show all content, else remove category and tags
function upgrade_postheader () {
  if (!is_page()) {
    global $post;
    ?>
    
    <div class="post">
        <div class="post-content span-12">
			<span class="cat-links"><?php printf( __( '%s', 'thematic' ), get_the_category_list(' ') ) ?></span>
			<?php edit_post_link(__('Edit', 'thematic'),'<span class="edit-link">','</span>') ?>
			<div class="heading">
              <h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
            </div>
			<?php if (in_category_name('events')){?>						
				<span class="event-time">
					<!-- Using the Event Calendar plugin's template tag: -->
					<?php ec3_schedule() ?>
				</span><br>
				<span class="event-loc"><?php echo get_post_meta($post->ID, 'event_loc', true);?></span>
			<?php } ?>
			<?php the_tags( __( '<div class="tag-links"><span class="tag-container"><a href=  "#" class="global-tag" title="search tag on the global network"></a>', 'thematic' ), '</span><span class="tag-container"><a href="#" class="global-tag" title="search tag on the global network"></a>', "</span></div>" ) ?>
  <?php
}
  else {
    global $post;
    ?>
    <div class="post">
        <div class="post-content span-12">
			<div class="heading"><h2 class="entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2></div>
			<?php if (in_category_name('events')){?>						
				<span class="event-time">
					<!-- Using the Event Calendar plugin's template tag: -->
					<?php ec3_schedule() ?>
				</span><br>
				<span class="event-loc"><?php echo get_post_meta($post->ID, 'event_loc', true);?></span>
			<?php } ?>
			<?php the_tags( __( '<div class="tag-links"><span class="tag-container"><a href=  "#" class="global-tag" title="search tag on the global network"></a>', 'sandbox' ), '</span><span class="tag-container"><a href="#" class="global-tag" title="search tag on the global network"></a>', "</span></div>" ) ?>
    <?php
  }
}
add_action(thematic_postheader, upgrade_postheader);

// If on a page (ie. About) close post content divs
// This allows contents to float properly next to each other
function close_divs () {
  if (is_page()) {
  ?>
      </div>
    </div>
  <?php
  }
}
add_action(thematic_abovemainasides, close_divs);

// Filtering the thematic postfooter
function upgrade_postfooter(){
  ?>
      	</div>
		<div class="entry-meta meta span-1 last">
			<a href="#" class="btn-up"></a>
			<a class="author-img" href="<?php echo get_author_link( false, $authordata->ID, $authordata->user_nicename )?>" title="View all posts by <?php echo $authordata->display_name ?>">
                <?php echo get_avatar( get_the_author_email(), '20' ); ?>
			</a>
			<!--
			span class="day"><?php the_time('j'); ?></span>
			<span class="month"><?php the_time('M'); ?></span>
			<span class="year"><?php the_time('Y'); ?></span>
			-->
			<span class="permalink"><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'thematic'), the_title_attribute('echo=0') ) ?>" rel="bookmark"></a></span>
			<span class="comments-link"><a href="<?php comments_link?>"><?php comments_number('', '1', '%');?></a></span>
			<a href="#" class="btn-down"></a>
		</div>
	</div><!-- .post -->
  <?php
}
add_filter(thematic_postfooter, upgrade_postfooter);


// Dashboard Node Settings
// Additional Node admin page for the Upgrade template
add_action('admin_menu', 'upgrades_add_theme_page');

function upgrades_add_theme_page() {
	if ( $_GET['page'] == basename(__FILE__) ) {
    
		if ( 'save' == $_REQUEST['action'] ) {
			if ( isset($_REQUEST['njform']) ) {
			
					if (empty($_REQUEST['upgrades_use_gmap']))
						delete_option('upgrades_use_gmap');
					else {
						update_option('upgrades_use_gmap', "true");
					}
                    
					if ( '' == $_REQUEST['upgrades_node_name'] )
						delete_option('upgrades_node_name');
					else {
						$upgrades_node_name = trim ( $_REQUEST['upgrades_node_name'] );
						update_option('upgrades_node_name', $upgrades_node_name);
					}
					
					if ( '' == $_REQUEST['node_google_key'] )
						delete_option('node_google_key');
					else {
						$node_google_key = trim ( $_REQUEST['node_google_key'] );
						update_option('node_google_key', $node_google_key);
					}
					
					if ( '' == $_REQUEST['node_color_light'] )
						delete_option('node_color_light');
					else {
						$node_color_light = trim ( $_REQUEST['node_color_light'] );
						update_option('node_color_light', $node_color_light);
					}
                    
				 	if ( '' == $_REQUEST['node_color_dark'] )
						delete_option('node_color_dark');
					else {
						$node_color_dark = trim ( $_REQUEST['node_color_dark'] );
						update_option('node_color_dark', $node_color_dark);
					}
                    
				 	if ( '' == $_REQUEST['node_color_text'] )
						delete_option('node_color_text');
					else {
						$node_color_text = trim ( $_REQUEST['node_color_text'] );
						update_option('node_color_text', $node_color_text);
					}
                    
					if ( '' == $_REQUEST['upgrades_node_address'] )
						delete_option('upgrades_node_address');
					else {
						$upgrades_node_address = trim ( $_REQUEST['upgrades_node_address'] );
						update_option('upgrades_node_address', $upgrades_node_address);
					}
					
			  		if ( '' == $_REQUEST['upgrades_node_lat'] )
						delete_option('upgrades_node_lat');
					else {
						$upgrades_node_lat = trim ( $_REQUEST['upgrades_node_lat'] );
						update_option('upgrades_node_lat', $upgrades_node_lat);
					}
                    
					if ( '' == $_REQUEST['upgrades_node_lon'] )
						delete_option('upgrades_node_lon');
					else {
						$upgrades_node_lon = trim ( $_REQUEST['upgrades_node_lon'] );
						update_option('upgrades_node_lon', $upgrades_node_lon);
					}
                    
					if ( '' == $_REQUEST['upgrades_node_zoom'] )
						delete_option('upgrades_node_zoom');
					else {
						$upgrades_node_zoom = trim ( $_REQUEST['upgrades_node_zoom'] );
						update_option('upgrades_node_zoom', $upgrades_node_zoom);
					}
			}
            
			//print_r($_REQUEST);
			wp_redirect("themes.php?page=functions.php&saved=true");
			die;
		} 
		 
		add_action('admin_head', 'upgrades_theme_node_meta');
	}
	add_theme_page(__('Node Settings'), __('Node Settings'), 'edit_themes', basename(__FILE__), 'upgrades_theme_page');
}


// Head of the node's settings page
function upgrades_theme_node_meta() {
?>
<script type='text/javascript'>
// <![CDATA[
	function lalal() {
	}
// ]]>
</script>
<style type='text/css'>
	#headwrap {
		text-align: center;
	}
</style>
<?php
}


// Body of the node's setting page:
function upgrades_theme_page() {
	if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.').'</strong></p></div>';
?>
<div class='wrap'>
	<h2><?php _e('Customize settings for your node'); ?></h2>
	
	<div id="nonJsForm">
			<form method="post" action="">				
				<?php wp_nonce_field('node_settings'); ?>
				
				<h3>General details</h3>
                
				<table class="form-table">
				
					<tr valign="top">
					<th scope="row"><label for="upgrades_node_name"><?php _e('Upgrade node codename:');?></label></th>
					<td><input name="upgrades_node_name" type="text" id="upgrades_node_name" 
						value="<?php echo attribute_escape(upgrades_node_name()); ?>" size="64" />
					<br /><span>This is used for identifying the different feeds. 
					For example: &#214;stersund-Stockholm could be simply 'stockholm' four these purposes </span>
					</td>
					</tr>
					
					
					<tr valign="top">
					<th scope="row"><label for="upgrades_use_gmap"><?php _e('Use Google Map:');?></label></th>
					<td>
					
					<input type="checkbox" name="upgrades_use_gmap" id="upgrades_use_gmap" value="1"
					<?php print (get_option('upgrades_use_gmap')) ? "checked=\"checked\"" : null; ?> />
					<br /><span>If checked, the Upgrade! theme will use a Google Map in the header.  
					If unchecked, the theme will use an image from the most recent post.</span>
					
					</td>
					</tr>
					
					<!-- 
					This will be taken directly from the WP-GEO plugin
					<tr valign="top">
					<th scope="row"><label for="node_google_key"><?php _e('Google Map API Key:');?></label></th>
					<td><input name="node_google_key" type="text" id="node_google_key" 
						value="<?php //echo attribute_escape(node_google_key()); ?>" size="64" />
					</td>
					</tr>
					 -->
				</table>
				
				<h3>Node Ribbon Colors</h3>
                
				<table class="form-table">
				
					<tr valign="top">
					<th scope="row"><label for="node_color_light"><?php _e('Node color light:');?></label></th>
					<td>#<input name="node_color_light" type="text" id="node_color_light" 
						value="<?php echo attribute_escape(node_color_light()); ?>" size="6" />
					</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><label for="node_color_dark"><?php _e('Node color dark:');?></label></th>
					<td>#<input name="node_color_dark" type="text" id="node_color_dark" 
						value="<?php echo attribute_escape(node_color_dark()); ?>" size="6" />
						<br />No need to specify the gray color which will be added to each ribbon
					</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><label for="node_color_text"><?php _e('Node color link:');?></label></th>
					<td>#<input name="node_color_text" type="text" id="node_color_text" 
						value="<?php echo attribute_escape(node_color_text()); ?>" size="6" />
						<br />This will be used for text links and should be contrasted enough from your other two tones (a 30% darker tone based on your dark ribbon colors usually works)
					</td>
					</tr>
                
				</table>
				
				
				<h3>Default Location</h3>
                
				<table class="form-table">
				
					<tr valign="top">
					<th scope="row"><label for="upgrades_node_address"><?php _e('Address:');?></label></th>
					<td><input name="upgrades_node_address" type="text" id="upgrades_node_address" 
						value="<?php echo attribute_escape(upgrades_node_address()); ?>" size="64" />
						<p>Enter your address and your latitude and longitude will be calculated automatically.</p>
					</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><label for="Geo_location"><?php _e('Geo location:');?></label></th>
					<td>Latitude:<input name="upgrades_node_lat" type="text" id="upgrades_node_lat" 
						value="<?php echo attribute_escape(upgrades_node_lat()); ?>" size="32" />
					
					Longitude:<input name="upgrades_node_lon" type="text" id="upgrades_node_lon" 
						value="<?php echo attribute_escape(upgrades_node_lon()); ?>" size="32" />
                    
					Zoom:<input name="upgrades_node_zoom" type="text" id="upgrades_node_zoom" 
						value="<?php echo attribute_escape(upgrades_node_zoom()); ?>" size="32" />
                    
					<br />
					   Use Google Maps to find the lat/lon of your location. 
					   <a href="http://www.getlatlon.com/" target="_blank">More tips here</a>
					</td>
					</tr>
                
				</table>
                
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="njform" value="true" />
                
				<br />
				<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Settings'); ?>" /></p>
				
			</form>
		</div>
</div>
<?php }


// Embed styles in header.
function nodeStyles(){
	echo "
	
	a,
	a:hover,
	.meta a:hover,
	.edit-link a:hover  {
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#container div.entry-content a.more-link:hover{
		background-color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#stripe{
		border-top-color: #" . attribute_escape(node_color_light()) . ";
		background-color: #" . attribute_escape(node_color_dark()) . " ;
	}
	
	div#right-col h2.page-title{
		background:#" . attribute_escape(node_color_light()) . ";
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#right-col h2.page-title a{
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#container div.post span.event-loc{
		background: #" . attribute_escape(node_color_light()) . ";
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#container div.post span.event-time{
		background: #" . attribute_escape(node_color_dark()) . " ;
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div#container div.entry-content a.more-link{
		background: #" . attribute_escape(node_color_dark()) . " ;
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div.entry-meta{
		background: #" . attribute_escape(node_color_dark()) . " ;
		color: #" . attribute_escape(node_color_text()) . ";
	}
	
	div.entry-meta span.comments-link a{
		color: #" . attribute_escape(node_color_dark()) . " ;
	}
	
	div.entry-meta a.author-img:hover img {
		border: 2px solid #" . attribute_escape(node_color_dark()) . " ;
	}
	
	div.entry-meta a.author-img img{
		border-color: 2px solid #" . attribute_escape(node_color_text()) . ";
	}
	
	.comment-author{
		background: #" . attribute_escape(node_color_light()) . ";
	}
	
	div#wp-calendar table td.ec3_eventday{
		border-color: #" . attribute_escape(node_color_light()) . ";
	}
	
	div#wp-calendar table td.ec3_eventday a{
		color: #" . attribute_escape(node_color_text()) . "!important;
		border-color: #" . attribute_escape(node_color_dark()) . "; 
	}
	
	";
}


// Keys
/*
function node_google_key() {
	return apply_filters('node_google_key', get_option('node_google_key'));
}
*/

function upgrades_node_name() {
	if(!get_option('upgrades_node_name')) {
		add_option('upgrades_node_name', 'Default Node Name');
	}
	return apply_filters('upgrades_node_name', get_option('upgrades_node_name'));
}

function node_color_light() {
	if(!get_option('node_color_light')) {
		add_option('node_color_light', 'CCCCCC');
	}
	return apply_filters('node_color_light', get_option('node_color_light'));
}

function node_color_dark() {
	if(!get_option('node_color_dark')) {
		add_option('node_color_dark', '999999');
	}
	return apply_filters('node_color_dark', get_option('node_color_dark'));
}

function node_color_text() {
	if(!get_option('node_color_text')) {
		add_option('node_color_text', '555555');
	}
	return apply_filters('node_color_text', get_option('node_color_text'));
}

function upgrades_node_address() {
	if(!get_option('upgrades_node_address')) {
		add_option('upgrades_node_address', 'Put your address here');
	}
	return apply_filters('upgrades_node_address', get_option('upgrades_node_address'));
}

function upgrades_node_lon() {
	if(!get_option('upgrades_node_lon')) {
		geolocate();
	}
	return apply_filters('upgrades_node_lon', get_option('upgrades_node_lon'));
}

function upgrades_node_lat() {
	if(!get_option('upgrades_node_lat')) {
		geolocate();
	}
	return apply_filters('upgrades_node_lat', get_option('upgrades_node_lat'));
}

function upgrades_node_zoom() {
	if(!get_option('upgrades_node_zoom')) {
		geolocate();
	}
	return apply_filters('upgrades_node_zoom', get_option('upgrades_node_zoom'));
}

function geolocate()
{
	if(!($a = urlencode(upgrades_node_address())))
	{
	  return false;
	}
	$wpgeo_options = get_option("wp_geo_options");
	$key = $wpgeo_options['google_api_key'];
	$url = "http://maps.google.com/maps/geo?q=$a&output=csv&key=$key";
	if(function_exists("curl_init"))
	{
	    $cinit = curl_init();  
		curl_setopt($cinit, CURLOPT_URL, $url);  
		curl_setopt($cinit, CURLOPT_HEADER,0);  
		curl_setopt($cinit, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);  
		curl_setopt($cinit, CURLOPT_FOLLOWLOCATION, 1);  
		curl_setopt($cinit, CURLOPT_RETURNTRANSFER, 1);  
		$response = curl_exec($cinit);  
		curl_close($cinit);
	}
	else if(function_exists("file_get_contents"))
	{
	  $response = file_get_contents($url);
	}
	if(strstr($response,"200"))
	{  
		$data = explode(",",$response);  
		if(!get_option('upgrades_node_lat')) {
			add_option('upgrades_node_lat', $data[2]);
		}
		if(!get_option('upgrades_node_lon')) {
			add_option('upgrades_node_lon', $data[3]);
		}
		if(!get_option('upgrades_node_zoom')) {
			add_option('upgrades_node_zoom', $data[1]);
		}
	}
}

// Add custom tags into the feeds.
function feed_insert_node_info() {
  print "<upgrade:nodeName>".upgrades_node_name()."</upgrade:nodeName>\n";
  print "<upgrade:nodeUrl>".bloginfo('home')."</upgrade:nodeUrl>\n";
  print "<upgrade:nodeAddress>".upgrades_node_address()."</upgrade:nodeAddress>\n";
  print "<upgrade:nodeColorLight>#".node_color_light()."</upgrade:nodeColorLight>\n";
  print "<upgrade:nodeColorDark>#".node_color_dark()."</upgrade:nodeColorDark>\n";
  print "<upgrade:nodeColorText>#".node_color_text()."</upgrade:nodeColorText>\n";
  print "<upgrade:nodeThemeVersion>".$THEME_VERSION."</upgrade:nodeThemeVersion>\n";
  //print "<upgrade:nodeMarker>".bloginfo('stylesheet_directory')."/styles/images/icon.png</upgrade:nodeMarker>\n";
}

// Add Upgrade namespace.
function feed_insert_namespace() {
  print "\n\txmlns:upgrade=\"http://upgrade.eyebeam.org/upgrade\"";
}

function upgrade_event_form() {
  global $post;
  $loc = get_post_meta($post->ID, "event_loc", true);
  if(empty($loc))
  {
	$loc = upgrades_node_address();
  }
	$edit_html = '
	  <div class="postbox if-js-open">
		<h3>' . __('Upgrade Event Location') . '</h3>
		  <div class="inside">
			<input style="width: 100%;" type="text" name="event_loc" value="'.$loc.'" />
		  </div>
	  </div>';
  print $edit_html;
}

function upgrade_save_post($post_id) {
  if (isset($_POST['event_loc']))
  {
    // Only delete post meta if isset (to avoid deletion in bulk/quick edit mode)
	delete_post_meta($post_id, 'event_loc');
	add_post_meta($post_id, 'event_loc', $_POST['event_loc']);	
  }
}

add_action('rss_head', 'feed_insert_node_info');
add_action('rss2_head', 'feed_insert_node_info');
add_action('atom_head', 'feed_insert_node_info');
add_action('rdf_header', 'feed_insert_node_info');
add_action('atom_ns', 'feed_insert_namespace');
add_action('rdf_ns', 'feed_insert_namespace');
add_action('rss2_ns', 'feed_insert_namespace');
add_action('edit_form_advanced', 'upgrade_event_form');
add_action('save_post', 'upgrade_save_post');


// SimplePie RSS U! Network Feed Widget
error_reporting(E_ALL);
add_action("widgets_init", array('network_feed_widget', 'register'));
register_activation_hook( __FILE__, array('network_feed_widget', 'activate'));
register_deactivation_hook( __FILE__, array('network_feed_widget', 'deactivate'));
  class network_feed_widget {
      function activate(){
          $data = array( 'option1' => 'Default value' ,'option2' => 55);
          if ( ! get_option('network_feed_widget')){
            add_option('network_feed_widget' , $data);
          } else {
            update_option('network_feed_widget' , $data);
          }
      }
      
      function deactivate(){
          delete_option('network_feed_widget');
      }
      
      function control(){
          echo 'This is the U! Network Feed for Global Events. Place this widget in the primary or secondary asides.';
      }
      
      
      function widget($args){
              // Set up an hourly wp-cron job to cache the feed
              // Currently this creates a job but does not help the feed load any faster.
              if ( !wp_next_scheduled('cache_networkfeed') ) {
              wp_schedule_event( time(), 'hourly', 'cache_networkfeed' ); // hourly, daily and or twicedaily
              }
          echo $args['before_widget'];
          echo $args['before_title'] . 'Latest Global Events' . $args['after_title'];
          
              // Use the SimplePie Core through an array, pull multiple rss feeds together.
              // For more information: http://simplepie.org/
              $feed = new SimplePie (array(
                'http://wowm.org/uz/',
                'http://turbulence.org/upgrade_boston/',
                'http://www.upgrade-berlin.net/',
                'http://www.upgradesaopaulo.com.br/arte-novas-midias/',
                'http://digiwaukee.net/upgrade/',
                'http://www.upgrade.artapsu.com/',
                'http://upgrade.eyebeam.org/',
                'http://upgrade.okno.be/',
                'http://www.incident.net/upgradedakar/',
                'http://www.likenow.org/upgrade/',
                'http://upgradechicago.org/'));
              
              // Set to pull only 1 item per feed
              $feed->set_item_limit(1);
              
              // Initialize the feed so that we can use it.
              $feed->init();
              $feed->handle_content_type();
              //$feed->set_cache_duration(999999999); 
              //$feed->set_timeout(-1);
              
                echo "<ul>";
                foreach ($feed->get_items(0,6) as $item):
              
                // Call the custom Upgrade tags within the feeds (we must also call the original feed from which the data is being pulled)
                $nodename = $item->get_feed()->get_channel_tags('http://upgrade.eyebeam.org/upgrade', 'nodeName');
                $name = $nodename[0]['data'];
                
                $nodecolordark = $item->get_feed()->get_channel_tags('http://upgrade.eyebeam.org/upgrade', 'nodeColorDark');
                $dark = $nodecolordark[0]['data'];
                
                $nodecolorlight = $item->get_feed()->get_channel_tags('http://upgrade.eyebeam.org/upgrade', 'nodeColorLight');
                $light = $nodecolorlight[0]['data'];
                
                $nodecolortext = $item->get_feed()->get_channel_tags('http://upgrade.eyebeam.org/upgrade', 'nodeColorText');
                $text = $nodecolortext[0]['data'];
                
                //$nodemarker = $item->get_feed()->get_channel_tags('http://upgrade.eyebeam.org/upgrade', 'nodeMarker');
                //$marker = $nodemarker[0]['data'];
                
                // Activate the cron job
                add_action('cache_networkfeed', 'widget');
              
                // Finally, echo the custom data and place it in the widget.
                ?>
                  <div class="feedcontent">
                    <div class="wrap" style="border-color:<?php echo $dark?>">
                      <li style="border-color:<?php echo $text?>; padding: 0 6px">
                        <span class="nodename" style="color:<?php echo $text ?>">U! <?php echo $name; ?>:</span>
                <?php
                    // List the global feed post titles and link to the post permalink.
                    ?>
                        <span class="feed">
                        <a href="<?php print $item->get_permalink(); ?>" style="color:<?php echo $text ?>">
                        <?php print $item->get_title(); ?></a></span>
                      </li>
                    </div>
                  </div>
                  
              <?php
              
              endforeach;
              echo "</ul>";
              
              $feed->__destruct(); // Do what PHP should be doing on it's own so that there are no memory leaks.
              unset($feed);
              unset($item);
            
          echo $args['after_widget'];
          
          //echo "Memory usage: " . number_format(memory_get_usage()); 
        
      }
      
    function register() {
            register_sidebar_widget('U! Network Feed', array('network_feed_widget', 'widget'));
            register_widget_control('U! Network Feed', array('network_feed_widget', 'control'));
    }
  }


function control(){
    $data = get_option('network_feed_widget');
    ?>
        <p><label>Option 1<input name="network_feed_widget_option1"
        type="text" value="<?php echo $data['option1']; ?>" /></label></p>
        <p><label>Option 2<input name="network_feed_widget_option2"
        type="text" value="<?php echo $data['option2']; ?>" /></label></p>
    <?php
      if (isset($_POST['network_feed_widget_option1'])){
        $data['option1'] = attribute_escape($_POST['network_feed_widget_option1']);
        $data['option2'] = attribute_escape($_POST['network_feed_widget_option2']);
        update_option('network_feed_widget', $data);
      }
}


?>