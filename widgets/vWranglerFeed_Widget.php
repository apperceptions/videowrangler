<?php
/*
Plugin Name: video Wrangler Feeds widget
Description: Adds a sidebar for video Wrangler unique Feeds widget.
Author: Enric Teller
Version: 0.03
Author URI: http://showinabox.tv

   Copyright 2008  Enric Teller  (email: enric@vpip.org)
   License (GPL License)
   ===================================================================
     This file is part of "video Wrangler".

    video Wrangler is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    video Wrangler is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
   ===================================================================

*/
$vWranglerFeedsLocation = get_settings("siteurl") . "/wp-content/plugins/widgets";
// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_vWranglerFeeds_init() {

	define("TBL_VIDEOFORMATS", $GLOBALS['wpdb']->prefix . "vPIP_VideoFormats");

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// The main video Wrangler Feed function.
	function widget_vWranglerFeeds($args) {
		global $vWranglerFeedsLocation;
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);
		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_vWranglerFeeds');
		$title = $options['title'];
		//$buttontext = $options['buttontext'];

		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $options[0]['title'] . $after_title;
		$url_parts = parse_url(get_bloginfo('home'));
				echo "
					<div id=\"vWranglerFeeds\">";
		for ($i=0; $i<count($options); $i++)
		{
			$option = $options[$i];
			if ($i == 0)
			{
				echo "
					<ul>";
				
			}
			else 
			{
				if (strlen(trim($option['media'])) > 0 && 
					substr(trim($option['media']),0,14) != "vs-title: None")
				{
					if (strlen(trim($option['mimetype'])) > 0 && strtolower(trim($option['mimetype'])) != "external")
						$mimetype = "&mimetype=" . trim($option['mimetype']);
					else
						$mimetype = "";
						
					if (strtolower(trim($option['mimetype'])) == "external")
						$href = $option['media'];
					else 
					{
						$blogURL = get_bloginfo('url');
						$href = $vWranglerFeedsLocation . "/vWranglerFeed.php?blogURL=" . $blogURL . "&media=" . urlencode($option['media']) . $mimetype;
					}
					
					if ($i == 1)
						echo "
						<li><a href=\"" . $href . "\" >" . $option['title'] . "</a></li>";
					else
						echo "
						<li><a href=\"" . $href . "\" >" . $option['title'] . "</a></li>";
				}
				
			}
		}
		echo "
					</ul>
				</div>
";

		
		
		echo $after_widget;
	}

	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_vWranglerFeeds_control() {
		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_vWranglerFeeds');
		//Get array of Videoformats:
		$sql = "SELECT * FROM " . TBL_VIDEOFORMATS . " ORDER BY displayOrder";
		$aVideoFormats = $GLOBALS['wpdb']->get_results($sql);

		//Remove prior options settings (Beta 1 of verison 0.01)
		if (is_array($options))
		{
			if (substr(trim($options[1]['media']),0,9) != "vs-title:")
			{
				$options = false;
			}
		}
		
		if ( !is_array($options) )
		{
			$options = array(0=>array('title'=>'Feeds', 'media'=>'', 'mimetype'=>'heading'));
		
			for($i=1; $i<=count($aVideoFormats); $i++)
			{
				$media = "vs-title: " . $aVideoFormats[$i-1]->mediaName;
				$options[$i] = array('title'=>$aVideoFormats[$i-1]->mediaName, 'media'=>$media, 'mimetype'=>$aVideoFormats[$i-1]->mimeType);
			}
			
			// Any remaining line up to 9 for custom settings:
			for(; $i<=9; $i++)
			{
				$options[$i] = array('title'=>('more feeds #' . $i), 'media'=>'', 'mimetype'=>'');
			}
			
			/* TBR:
			// mimetype = "heading" for Feeds heading, 
			// mimetype = "[video/audio mimetype]" for regular video specs.
			// mimetype = "external" for external feeds (i.e., feedburner, wordpress, etc.)
			$options = array(0=>array('title'=>'Feeds', 'media'=>'', 'mimetype'=>'heading'),
							 1=>array('title'=>'flash', 'media'=>'flv', 'mimetype'=>'video/x-flv'),
							 2=>array('title'=>'quicktime', 'media'=>'mov,mp4', 'mimetype'=>''),
							 3=>array('title'=>'windows media', 'media'=>'wmv', 'mimetype'=>'video/x-ms-wmv'),
							 4=>array('title'=>'more feeds #4', 'media'=>'', 'mimetype'=>''),
							 5=>array('title'=>'more feeds #5', 'media'=>'', 'mimetype'=>''),
							 6=>array('title'=>'more feeds #6', 'media'=>'', 'mimetype'=>''),
							 7=>array('title'=>'more feeds #7', 'media'=>'', 'mimetype'=>''),
							 8=>array('title'=>'more feeds #8', 'media'=>'', 'mimetype'=>''),
							 9=>array('title'=>'more feeds #9', 'media'=>'', 'mimetype'=>'')
							 );
			:TBR */
		}
		else if (count($options) < 9)
		{
			for ($i=0; $i<9; $i++)
			{
				$options[$i] = array('title'=>('more feeds #' . $i), 'media'=>'', 'mimetype'=>'');
			}
		}
		
		//If updating, set $newoptions to entries for update 
		if ( $_POST['vWranglerFeeds-submit'] ) {

			// Loop through POSTed feed info
			$i = 0;
			$newoptions = array();
			while (TRUE)
			{
				$curr_title = 'vWranglerFeeds_title' . $i;
				if (isset($_POST[$curr_title]))
				{
					$curr_media = 'vWranglerFeeds_media' . $i;
					$curr_mimetype = 'vWranglerFeeds_mimetype' . $i;
					if ($i > 0 && $i <= count($aVideoFormats))
					{
						if ($_POST[$curr_media] == NULL)
							$media = "vs-title: None";
						else
							$media = "vs-title: " . $_POST[$curr_media];
					}
					else 
					{
						$media = $_POST[$curr_media];
					}
					
					$newoptions[$i] = array("title"=>$_POST[$curr_title], "media"=>$media, 
											"mimetype"=>$_POST[$curr_mimetype]);
					$i++;
				}
				else
					break;
			}
			// Remember to sanitize and format use input appropriately.
			//$options['title'] = strip_tags(stripslashes($_POST['gsearch-title']));
			//$options['buttontext'] = strip_tags(stripslashes($_POST['gsearch-buttontext']));
			//Updating the options in the database if there was a change
			if ( $options != $newoptions ) {
				$options = $newoptions;
				update_option('widget_vWranglerFeeds', $options);
			}
		}

		// Be sure you format your options to be valid HTML attributes.
		//$title = htmlspecialchars($options['title'], ENT_QUOTES);
		//$buttontext = htmlspecialchars($options['buttontext'], ENT_QUOTES);
				
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo "		
				<p><table border=\"2\" cellpadding=\"2\" style=\" margin: 0; background-color: #FEFEFE; \">
";
		for ($i=0; $i<count($options); $i++)
		{
			$option = $options[$i];
			//Heading:
			if ($i == 0)
			{
				echo "
					<tr align=\"center\">
						<td colspan=\"3\" >Heading: <input type=\"text\" name=\"vWranglerFeeds_title" . $i . "\" size=\"50\" maxlength=\"100\" value=\"" . $option['title'] . "\" /></td>
					</tr>
";
				
			}
			else 
			{
				echo "
					<tr align=\"left\">
						<td>Title: <input type=\"text\" name=\"vWranglerFeeds_title" . $i . "\" size=\"20\" maxlength=\"100\" value=\"" . $option['title'] . "\" /></td>";
				if (substr(trim($option['media']),0,9) == "vs-title:")		
				{
					echo "
							<td colspan=\"2\" >Select: 
								<select name=\"vWranglerFeeds_media" . $i . "\" />
									<option value=\"None\" >None</option>
";
					for($j=0; $j<count($aVideoFormats); $j++)
					{
						$media = "vs-title: " . $aVideoFormats[$j]->mediaName;

						if ($media == $option['media'])
						{

							echo "
									<option value=\"" . $aVideoFormats[$j]->mediaName . "\" selected=\"selected\" >" . $aVideoFormats[$j]->mediaName . "</option>
";
						}
						else
						{
							
							echo "
									<option value=\"" . $aVideoFormats[$j]->mediaName . "\">" . $aVideoFormats[$j]->mediaName . " </option>
";
						}
					}
					echo "
								</select>
							</td>
						</tr>
";
				}	
				else 
				{
					
					echo "
							<td>Setting: <input type=\"text\" name=\"vWranglerFeeds_media" . $i . "\" size=\"30\" maxlength=\"255\" value=\"" . $option['media'] . "\" /></td>
							<td>Mimetype: <input type=\"text\" name=\"vWranglerFeeds_mimetype" . $i . "\" size=\"15\" maxlength=\"30\" value=\"" . $option['mimetype'] . "\" /></td>
						</tr>
";
				}			
			}
		}
		echo "
				<input type=\"hidden\" id=\"feed-submit\" name=\"vWranglerFeeds-submit\" value=\"1\" />
				</table></p>
";
	}
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget(array('video Wrangler Feeds', 'widgets'), 'widget_vWranglerFeeds');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control(array('video Wrangler Feeds', 'widgets'), 'widget_vWranglerFeeds_control', 550, 515);
}

// Run our code later in case this loads prior to any required plugins.
add_action('widgets_init', 'widget_vWranglerFeeds_init');
?>
