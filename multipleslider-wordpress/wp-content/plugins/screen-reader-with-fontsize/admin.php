<?php 
//namespace wp-content\plugins\screenreader;  
/** 
 * Main wp admin render plugin
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2016 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */

// Aggiunge il link settings
function screenreader_plugin_action_links($links, $file) {
	if ($file == plugin_basename ( dirname ( __FILE__ ) . '/screenreader.php' )) {
		$links [] = '<a href="admin.php?page=screenreader-key-config">' . __ ( 'Settings' ) . '</a>';
	}
	
	return $links;
}
add_filter ( 'plugin_action_links', 'screenreader_plugin_action_links', 10, 2 );

// Renderizza il form di configurazione e salva i dati sul DB
function screenreader_conf() {
	global $wpdb, $table_prefix;
	
	// Update DB section
	$query = "SHOW COLUMNS FROM `" . $table_prefix . "screenreader_config`;";
	$columns = $wpdb->get_col( $query );
	
	// Update on 3.28
	if(!in_array('minimized_toolbar_only_mobile', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `minimized_toolbar_only_mobile` tinyint(4) NOT NULL DEFAULT 0 AFTER `status_minimized_toolbar`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `spacing_size_min` int(11) NOT NULL DEFAULT 0 AFTER `spacing_size`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `spacing_size_max` int(11) NOT NULL DEFAULT 10 AFTER `spacing_size_min`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `gtranslateintegration` tinyint(4) NOT NULL DEFAULT 0 AFTER `chunksize`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `highcontrast_root_target` tinyint(4) NOT NULL DEFAULT 0 AFTER `highcontrast_alternate_color_brightness`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `fontsize_minimized_toolbar` tinyint(4) NOT NULL DEFAULT 0 AFTER `minimized_toolbar_only_mobile`;";
		$wpdb->query( $query );
	}
	
	// Update on 3.29
	if(!in_array('read_elements_hovering', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `read_elements_hovering` tinyint(4) NOT NULL DEFAULT 0 AFTER `gtranslateintegration`;";
		$wpdb->query( $query );
	}
	// Update on 3.30
	if(!in_array('hover_minimized_toolbar', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `hover_minimized_toolbar` tinyint(4) NOT NULL DEFAULT 0 AFTER `minimized_toolbar_only_mobile`;";
		$wpdb->query( $query );
	}
	// Update on 3.31
	if(!in_array('reading_guides', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `reading_guides` tinyint(4) NOT NULL DEFAULT 0 AFTER `big_cursor`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `readability` tinyint(4) NOT NULL DEFAULT 0 AFTER `reading_guides`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `readability_selector` varchar(255) NOT NULL DEFAULT '#main,article' AFTER `readability`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `hide_images` tinyint(4) NOT NULL DEFAULT 0 AFTER `readability_selector`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_reading_guides` varchar(255) NOT NULL DEFAULT 'V' AFTER `accesskey_bigcursor`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_readability` varchar(255) NOT NULL DEFAULT 'Q' AFTER `accesskey_reading_guides`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_hideimages` varchar(255) NOT NULL DEFAULT 'F' AFTER `accesskey_readability`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_increase_volume` varchar(255) NOT NULL DEFAULT '+' AFTER `volume_accesskeys`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_decrease_volume` varchar(255) NOT NULL DEFAULT '-' AFTER `accesskey_increase_volume`;";
		$wpdb->query( $query );
	}
	// Update on 3.32
	if(!in_array('customcolors', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `customcolors` tinyint(4) NOT NULL DEFAULT 0 AFTER `readability_selector`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `customcolors_cssselectors` varchar(255) NOT NULL DEFAULT '' AFTER `customcolors`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_change_text_color` varchar(255) NOT NULL DEFAULT 'I' AFTER `accesskey_minimized`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_change_background_color` varchar(255) NOT NULL DEFAULT 'B' AFTER `accesskey_change_text_color`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `proxy_responsive_language_gender` varchar(255) NOT NULL DEFAULT 'auto' AFTER `proxy_responsive_apikey`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `preload_timeout` int(11) NOT NULL DEFAULT 3000 AFTER `preload`;";
		$wpdb->query( $query );
	}
	// Update on 3.34
	if(!in_array('hide_also_videos_iframes', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `hide_also_videos_iframes` tinyint(4) NOT NULL DEFAULT 0 AFTER `hide_images`;";
		$wpdb->query( $query );
	}
	// Update on 3.35
	if(!in_array('page_zoom', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `page_zoom` tinyint(4) NOT NULL DEFAULT 0 AFTER `spacing_size_max`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `enable_dark_mode` tinyint(4) NOT NULL DEFAULT 0 AFTER `target_append_mode`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_pagezoomsize_reset` varchar(255) NOT NULL DEFAULT 'Z' AFTER `accesskey_spacingsize_decrease`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_pagezoomsize_decrease` varchar(255) NOT NULL DEFAULT 'Y' AFTER `accesskey_spacingsize_decrease`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `accesskey_pagezoomsize_increase` varchar(255) NOT NULL DEFAULT 'X' AFTER `accesskey_spacingsize_decrease`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `elements_toexclude_custom` varchar(255) NOT NULL DEFAULT '' AFTER `elements_hovering_selector`;";
		$wpdb->query( $query );
	}
	if(!in_array('enable_accessibility_statement', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `enable_accessibility_statement` tinyint(4) NOT NULL DEFAULT 0 AFTER `enable_dark_mode`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `enable_accessibility_statement_text` varchar(255) NOT NULL DEFAULT 'Accessibility statement' AFTER `enable_accessibility_statement`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `enable_accessibility_statement_link` varchar(255) NOT NULL DEFAULT '' AFTER `enable_accessibility_statement_text`;";
		$wpdb->query( $query );
	}
	// Update on 3.36
	if(!in_array('template_variant', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `template_variant` varchar(255) NOT NULL DEFAULT 'standard' AFTER `template_orientation`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `generate_missing_images_alt` tinyint(4) NOT NULL DEFAULT 0 AFTER `hide_on_mobile`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `generate_missing_images_alt_chatgpt_apikey` varchar(255) NOT NULL DEFAULT '' AFTER `generate_missing_images_alt`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `generate_missing_images_alt_chatgpt_model` varchar(50) NOT NULL DEFAULT 'gpt-3.5-turbo' AFTER `generate_missing_images_alt_chatgpt_apikey`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `fix_headings_structure` tinyint(4) NOT NULL DEFAULT 0 AFTER `generate_missing_images_alt_chatgpt_model`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `fix_low_contrast_text` tinyint(4) NOT NULL DEFAULT 0 AFTER `fix_headings_structure`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `fix_missing_aria_roles` tinyint(4) NOT NULL DEFAULT 0 AFTER `fix_low_contrast_text`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `fix_missing_form_labels` tinyint(4) NOT NULL DEFAULT 0 AFTER `fix_missing_aria_roles`;";
		$wpdb->query( $query );
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `validate_and_fix_focus_order` tinyint(4) NOT NULL DEFAULT 0 AFTER `fix_missing_form_labels`;";
		$wpdb->query( $query );
	}
	// Update on 3.37
	if(!in_array('proxy_responsive_reading_mode', $columns)) {
		$query = "ALTER TABLE `" . $table_prefix . "screenreader_config` ADD `proxy_responsive_reading_mode` varchar(255) NOT NULL DEFAULT 'native' AFTER `proxy_responsive_loading_script`;";
		$wpdb->query( $query );
	}
	
	// Live Site
	$siteUrl = plugins_url('/', __FILE__); 
	// CONFIG LOAD DA DB OPTIONS
	$screenreaderQuery = "SELECT * FROM " . $table_prefix . "screenreader_config";
	$screenreaderConfig = $wpdb->get_row($screenreaderQuery);
 
	// Save config da POST submit
	if ( isset($_POST['submit']) ) {
		$fields = array();
		foreach ($screenreaderConfig as $paramName=>&$paramValue) {
			$paramValue = isset($_POST[$paramName]) ? $_POST[$paramName] : '';
			$fields[$paramName] = $paramValue;
		} 
		if($wpdb->update($table_prefix . 'screenreader_config', $fields, array('id'=>1))) {
			echo '<div id="message" class="updated"><p>Settings saved</p></div>'; 
		} else {
			echo '<div id="message" class="updated"><p>Settings up-to-date</p></div>'; 
		}
	}
	 
	// Config form generation
	if(is_object($screenreaderConfig)) {
		$config = null;
		foreach ($screenreaderConfig as $paramNameForm=>$paramValueForm) {
			switch($paramNameForm) {
				case 'volume_tts':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('main_settings_title')[0] . "</label></div>";
					break;
				case 'reader_engine':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('audio_engine_title')[0] . "</label></div>";
					break;
				case 'showlabel':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('appearance_title')[0] . "</label></div>";
					break;			
				case 'generate_missing_images_alt':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('accessibility_improvements_title')[0] . "</label></div>";
					break;
				case 'auto_background_color':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('advanced_settings_title')[0] . "</label></div>";
					break;
				case 'accesskey_play':
					$config .= "<div class='setting-container'><label class='main-labels'>" . screenreaderTransformFunctionLabel('accesskeys_title')[0] . "</label></div>";
					break;
			}
			
			$labelValues = screenreaderTransformFunctionLabel($paramNameForm);
			$config .= "<div class='setting-container'><label title='" . $labelValues[1] . "' style='float:left;width:240px'>" . $labelValues[0] . "</label>";
			$config .= screenreaderTransformFunctionInput($paramNameForm, $paramValueForm);
			$config .= "</div>";
		} 
	} 
	?>
	
<div id="setting-error-settings_updated" class="notice notice-warning settings-error"> 
	<p><strong>Screen Reader plugin free version. </strong><br>The free version is <strong>limited to read only 300 characters and has only few features.</strong><br> 
	Check the <strong>PRO version</strong> including all features at: <a target="_blank" href="https://test.storejextensions.org/wpscreenreader/">https://test.storejextensions.org/wpscreenreader/</a> <br>
	Get the <strong>PRO version</strong> including all features at <a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html">https://storejextensions.org/extensions/screen_reader.html</a>
	</p>
</div>

<fieldset
	style="padding: 20px 20px 20px 0; margin-top: 30px;">
	<legend style="width: 340px;">
		<img src="<?php echo $siteUrl;?>config_icon.png" style="width:64px;height:64px" alt="config_icon" />
		<label style="font-weight: bold; margin-top: 40px; display: block; float: right; font-size: 24px;">Screen Reader settings</label>
	</legend>
	<form action="" method="post" id="screenreader-conf">
		<input style='float:right;width:150px;background-color:#23282D;color:#FFF;border:none;cursor:pointer;padding:5px 10px;margin-top: -60px;line-height:inherit' type="submit" name="submit" value="<?php _e('Save settings'); ?>" /> 
			<?php echo $config;?>
		<input style='float:right;width:150px;background-color:#23282D;color:#FFF;border:none;cursor:pointer;padding:5px 10px;margin-top: -20px;line-height:inherit' type="submit" name="submit" value="<?php _e('Save settings'); ?>" />
	</form>
</fieldset>
<?php
}
  
function screenreader_load_menu() { 
	add_submenu_page ( 'plugins.php', __ ( 'Screen Reader settings' ), __ ( 'Screen Reader settings' ), 'manage_options', 'screenreader-key-config', 'screenreader_conf' );
	add_menu_page('plugins.php', __ ( 'Screen Reader settings' ), 'manage_options', 'screenreader-key-config', 'screenreader_conf');
}
add_action ( 'admin_menu', 'screenreader_load_menu' ); 

function screenreader_enqueue_color_picker( $hook_suffix ) {
	// Load only in the screen reader settings page
	if ( empty( $_GET['page'] ) || $_GET['page'] !== 'screenreader-key-config' ) {
		return;
	}
	
	// first check that $hook_suffix is appropriate for your admin page
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'my-script-handle', plugins_url('libraries/jquery/colorpicker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	
	$custom_js = <<<JS
			// Add settings label hints
		    document.querySelectorAll('div > label[title]').forEach(function(label) {
		        const title = label.getAttribute('title');
		        if (!title) return;
		
		        // cerca il primo input, select o textarea nello stesso div
		        const container = label.parentElement;
		        const field = container.querySelector('input, select, textarea');
		
		        // evita di aggiungere due volte
		        if (field && !container.querySelector('.setting-hint')) {
		            const hint = document.createElement('small');
		            hint.className = 'setting-hint';
		            hint.textContent = title;
		            container.appendChild(hint);
		        }
		    });
	
			/* iOS switch */
			document.addEventListener('DOMContentLoaded', function() {
				document.querySelectorAll('.wrapper').forEach(function(wrapper) {
					wrapper.addEventListener('click', function(event) {
						if (event.isSynthetic) return;
						if (event.target.tagName === 'INPUT') return;
						
						event.preventDefault();
						
						const radios = wrapper.querySelectorAll('input[type="radio"]');
						const currentChecked = wrapper.querySelector('input[type="radio"]:checked');
						
						radios.forEach(function(radio) {
							if (radio !== currentChecked) {
								radio.checked = true;
								
								const clickEvent = new MouseEvent('click', {
									bubbles: true,
									cancelable: true,
									view: window
								});
								clickEvent.isSynthetic = true;
								radio.dispatchEvent(clickEvent);
								
								const changeEvent = new Event('change', { bubbles: true });
								changeEvent.isSynthetic = true;
								radio.dispatchEvent(changeEvent);
							}
						});
					});
				});
			});
	JS;
	
	wp_add_inline_script( 'my-script-handle', $custom_js );
	
	$custom_css = <<<CSS
			/* Settings hint */
			div.setting-container:not(:has(label.main-labels)):not(:has(input[type="hidden"])) {
				min-height: 70px;
			    margin-bottom: 20px;
			    box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
	        }
			#screenreader-conf div.setting-container:last-of-type {
				box-shadow: none;
			}
			div.setting-container:has(label.main-labels) {
			    border-bottom: 3px solid #2271b1;
			    height: 32px;
			    margin-bottom: 20px;
			}
			#screenreader-conf div.setting-container:has(input[name*="accesskey"]) {
				min-height: 50px;
			}
		    .setting-hint {
				font-size: 13px;
	            margin-left: 240px;
				margin-bottom: 20px;
				margin-top: 6px;
			    display: block;
			    color: rgb(102, 102, 102);
	        }
			.main-labels {
				font-weight: 600;
			    font-size: 14px;
			    margin-bottom: 40px;
			    border: none;
			    background: #2271b1;
			    color: #FFF;
			    text-align: center;
			    padding: 0;
			    vertical-align: middle;
			    border-left: 4px solid #2271b1;
			    padding: 8px 16px;
			    display: inline-block;
			}
			#screenreader-conf {
			    background: #f9f9f9;
			    padding: 20px;
			}
	
			/* iOS switch */
			.wrapper {
			  position: relative;
			  display: inline-block;
			  width: 52px;
			  height: 28px;
			  vertical-align: middle;
			  cursor: pointer;
			}
			
			.wrapper input[type="radio"] {
			  position: absolute;
			  opacity: 0;
			  pointer-events: none;
			}
			
			.wrapper > label {
			  position: absolute;
			  top: 0;
			  left: 0;
			  height: 100%;
			  width: 50%;
			  cursor: pointer;
			}
			.wrapper > label:last-child {
			  left: 50%;
			}
			
			.wrapper > label > span {
			  position: absolute !important;
			  width: 1px;
			  height: 1px;
			  overflow: hidden;
			  clip: rect(1px,1px,1px,1px);
			}
			
			.wrapper::before {
			  content: "";
			  position: absolute;
			  inset: 0;
			  background: #bdbdbd;
			  border-radius: 999px;
			  transition: background-color 0.25s ease, box-shadow 0.25s ease;
			  box-shadow: inset 0 0 3px rgba(0,0,0,0.25);
			  pointer-events: none;
			}
			
			.wrapper::after {
			  position: absolute;
			  top: 3px;
			  left: 3px;
			  width: 22px;
			  height: 22px;
			  border-radius: 50%;
			  background: #fff;
			  box-shadow: 0 1px 4px rgba(0,0,0,0.3);
			  transition: transform 0.28s cubic-bezier(0.45,1.6,0.45,1), color 0.2s ease;
			  pointer-events: none;
			  display: flex;
			  align-items: center;
			  justify-content: center;
			  font-size: 14px;
			  font-weight: bold;
			  content: "";
			}
			
			.wrapper:has(> label:first-child > input:checked)::after {
			  content: "✓";
			  color: #2196f3;
			}
			
			.wrapper:has(> label:first-child > input:checked)::before {
			  background: #2196f3;
			  box-shadow: 0 0 0 3px rgba(33,150,243,0.18);
			}
			.wrapper:has(> label:first-child > input:checked)::after {
			  transform: translateX(24px);
			}
			
			.wrapper:has(> label:last-child > input:checked)::before {
			  background: #bdbdbd;
			}
			.wrapper:has(> label:last-child > input:checked)::after {
			  transform: translateX(0);
			}
			
			.wrapper:hover::before {
			  box-shadow: 0 0 0 3px rgba(33,150,243,0.12);
			}
			.wrapper input[type="radio"]:focus-visible + span {
			  outline: 2px solid rgba(33,150,243,0.45);
			  outline-offset: 2px;
			}
	CSS;
	
	wp_register_style( 'wp-ios-switchers', false );
	wp_enqueue_style( 'wp-ios-switchers' );
	wp_add_inline_style( 'wp-ios-switchers', $custom_css );
}
add_action( 'admin_enqueue_scripts', 'screenreader_enqueue_color_picker' );

/**
 * Funzione di trasformazione HTML controls, restituisce il controllo richiesto
 * @param string $control
 * @param mixed $value
 * @return string
 */
function screenreaderTransformFunctionInput($control, $value) {
	// Text translations
	static $adminLanguageStrings = null;
	if(!$adminLanguageStrings) {
		$adminLanguageFile = dirname(__FILE__) . "/languages/admin/en-GB.ini";
		if(file_exists($adminLanguageFile)) {
			$adminLanguageStrings = parse_ini_file($adminLanguageFile, false, INI_SCANNER_NORMAL);
		}
	}
	
	// Main switch
	switch ($control) {
		case 'volume_tts' :
			$options = array(20=>'20%',40=>'40%',60=>'60%',80=>'80%',100=>'100%');
			$str = '<select name="volume_tts">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'voice_speed' :
			$options = array('veryslow'=>'VOICE_SPEED_VERYSLOW','slow'=>'VOICE_SPEED_SLOW','normal'=>'VOICE_SPEED_NORMAL','fast'=>'VOICE_SPEED_FAST','veryfast'=>'VOICE_SPEED_VERYFAST');
			$str = '<select name="voice_speed">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'read_page' :
			$options = array(0=>'SELECTED_TEXT_ONLY', 1=>'MAINPAGE_PART_AND_SELECTED_TEXT');
			$str = '<select name="read_page">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'read_images_attribute' :
			$options = array('alt'=>'ALT', 'title'=>'TITLE');
			$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			break;

		case 'read_images_ordering' :
			$options = array('before'=>'BEFORE', 'after'=>'AFTER');
			$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			break;
		
		case 'chunksize':
			$options = array (
					'20' => '20',
					'40' => '40',
					'60' => '60',
					'80' => '80',
					'90' => '90',
					'100' => '100',
					'120' => '120',
					'140' => '140',
					'160' => '160',
					'180' => '180',
					'200' => '200',
					'220' => '220',
					'240' => '240',
					'260' => '260',
					'280' => '280',
					'300' => '300',
					'400' => '400',
					'500' => '500',
					'600' => '600',
					'700' => '700',
					'800' => '800',
					'900' => '900',
					'1000'=>'1000');
			$str = '<select name="chunksize">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'highcontrast_alternate_color_hue' :
			$options = array(45=>'FONTSIZE_VERYLOW',
							 90=>'FONTSIZE_LOW',
							 180=>'FONTSIZE_MEDIUM',
							 225=>'FONTSIZE_AVERAGE',
							 270=>'FONTSIZE_HIGH',
							 305=>'FONTSIZE_VERYHIGH');
			$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			break;
			
		case 'highcontrast_alternate_color_brightness' :
			$options = array(2=>'FONTSIZE_VERYLOW',
							 4=>'FONTSIZE_LOW',
							 6=>'FONTSIZE_AVERAGE',
							 8=>'FONTSIZE_HIGH',
							 10=>'FONTSIZE_VERYHIGH');
			$str = '<select name="highcontrast_alternate_color_brightness">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'fontsize_selector_mode' :
			$options = array(0=>'APPEND', 1=>'OVERRIDE');
			$str = '<select name="fontsize_selector_mode">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'corner_position' :
			$options = array('topright'=>'TOP_RIGHT',
							 'bottomright'=>'BOTTOM_RIGHT',
							 'topleft'=>'TOP_LEFT',
							 'bottomleft'=>'BOTTOM_LEFT');
			$str = '<select name="corner_position">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'template' :
			$options = array('accessible.css'=>'ACCESSIBLE_TEMPLATE',
							 'main.css'=>'MAIN_TEMPLATE',
							 'elegant.css'=>'ELEGANT_TEMPLATE',
							 'awesome.css'=>'AWESOME_TEMPLATE',
							 'custom.css'=>'CUSTOM_TEMPLATE');
			$str = '<select name="template">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'template_orientation' :
			$options = array('horizontal'=>'ORIENTATION_HORIZONTAL',
							 'vertical'=>'ORIENTATION_VERTICAL');
			$str = '<select name="template_orientation">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'template_variant' :
			$options = array('standard'=>'TEMPLATE_VARIANT_STANDARD',
							 'compact'=>'TEMPLATE_VARIANT_COMPACT');
			$str = '<select name="template_variant">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'screenreader_icon' :
			$options = array('audio'=>'SCREENREADER_ICON_AUDIO',
							 'wheelchair'=>'SCREENREADER_ICON_WHEELCHAIR',
							 'custom'=>'SCREENREADER_ICON_CUSTOM');
			$str = '<select name="screenreader_icon">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'scrolling' :
			$options = array('fixed'=>'FIXED_IMAGE',
							 'absolute'=>'SCROLLING_IMAGE',
							 'relative'=>'RELATIVE_IMAGE');
			$str = '<select name="scrolling">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'target_append_mode' :
			$options = array('top'=>'TARGET_APPEND_MODE_TOP',
							 'bottom'=>'TARGET_APPEND_MODE_BOTTOM');
			$str = '<select name="target_append_mode">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'script_loading' :
			$options = array('deferred'=>'SCRIPT_LOADING_DEFERRED',
							 'dom'=>'SCRIPT_LOADING_DOM');
			$str = '<select name="script_loading">' .screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
				
		case 'reader_engine' :
			$options = array('proxy_responsive'=>'READER_ENGINE_RESPONSIVEVOICE',
							 'proxy'=>'READER_ENGINE_GOOGLE',
							 'proxy_ispeech'=>'READER_ENGINE_ISPEECH',
							 'proxy_inforobo'=>'READER_ENGINE_INFOROBO',
							 'proxy_virtual_free'=>'READER_ENGINE_VIRTUALSPEAKER_FREE',
							 'proxy_texttomp3'=>'READER_ENGINE_TEXTTOMP3',
							 'proxy_ttsmp3'=>'READER_ENGINE_TTSMP3');
			$str = '<select name="reader_engine">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'mobile_reader_engine' :
			$options = array('proxy_responsive'=>'READER_ENGINE_RESPONSIVEVOICE',
							 'proxy'=>'READER_ENGINE_GOOGLE',
							 'proxy_ispeech'=>'READER_ENGINE_ISPEECH',
							 'proxy_inforobo'=>'READER_ENGINE_INFOROBO',
							 'proxy_virtual_free'=>'READER_ENGINE_VIRTUALSPEAKER_FREE',
							 'proxy_texttomp3'=>'READER_ENGINE_TEXTTOMP3',
							 'proxy_ttsmp3'=>'READER_ENGINE_TTSMP3');
			$str = '<select name="mobile_reader_engine">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
		
		case 'proxy_responsive_reading_mode' :
			$options = array('native'=>'PROXY_RESPONSIVE_READING_MODE_NATIVE',
							 'api'=>'PROXY_RESPONSIVE_READING_MODE_API');
			$str = '<select name="proxy_responsive_reading_mode">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'proxy_responsive_loading_script' :
			$options = array(0=>'PROXY_RESPONSIVE_LOADING_SCRIPT_LOCAL',
							 1=>'PROXY_RESPONSIVE_LOADING_SCRIPT_REMOTE');
			$str = '<select name="proxy_responsive_loading_script">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'proxy_responsive_language_gender' :
			$options = array('auto'=>'PROXY_RESPONSIVE_LANGUAGE_GENDER_AUTO',
							 'male'=>'PROXY_RESPONSIVE_LANGUAGE_GENDER_MALE',
							 'female'=>'PROXY_RESPONSIVE_LANGUAGE_GENDER_FEMALE');
			$str = '<select name="proxy_responsive_language_gender">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'enable_focus_outline_bordersize':
			$options = array('1px'=>'FOCUS_OUTLINE_BORDER_SIZE_1PX',
							 '2px'=>'FOCUS_OUTLINE_BORDER_SIZE_2PX',
							 '3px'=>'FOCUS_OUTLINE_BORDER_SIZE_3PX',
							 '4px'=>'FOCUS_OUTLINE_BORDER_SIZE_4PX');
			$str = '<select name="enable_focus_outline_bordersize">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
		
		case 'toolbar_bgcolor':
			$str = "<input type='text' value='" . $value . "' name='toolbar_bgcolor' class='my-color-field' data-default-color='#EEE'/>";
			break;
			
		case 'enable_focus_outline_color':
			$str = "<input type='text' value='" . $value . "' name='enable_focus_outline_color' class='my-color-field' data-default-color='#F00'/>";
			break;
			
		case 'engine_google_token_mode':
			$options = array(0=>'READER_ENGINE_GOOGLE_STATIC',
							 1=>'READER_ENGINE_GOOGLE_DYNAMIC');
			$str = '<select name="engine_google_token_mode">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'reset_button_behavior':
			$options = array('fontsize'=>'RESET_BUTTON_BEHAVIOR_FONTSIZE',
							 'all'=>'RESET_BUTTON_BEHAVIOR_ALL');
			$str = '<select name="reset_button_behavior">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
		
		case 'status_minimized_toolbar':
			$options = array('closed'=>'STATUS_MINIMIZED_TOOLBAR_CLOSED',
							 'open'=>'STATUS_MINIMIZED_TOOLBAR_OPEN');
			$str = '<select name="status_minimized_toolbar">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'selected_storage':
			$options = array('session'=>'SELECTED_STORAGE_SESSION',
							 'local'=>'SELECTED_STORAGE_LOCAL');
			$str = '<select name="selected_storage">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'preload_timeout' :
			$options = array(1000=>'PRELOAD_TIMEOUT_1SECOND',
							 2000=>'PRELOAD_TIMEOUT_2SECONDS',
							 3000=>'PRELOAD_TIMEOUT_3SECONDS',
							 4000=>'PRELOAD_TIMEOUT_4SECONDS',
							 5000=>'PRELOAD_TIMEOUT_5SECONDS');
			$str = '<select name="preload_timeout">' . screenreaderGenericSelectLists( $options, $value, $adminLanguageStrings) . '</select>';
			break;
			
		case 'generate_missing_images_alt_chatgpt_model' :
			$options = array(
							'gpt-3.5-turbo' => 'gpt-3.5-turbo',
							'gpt-4' => 'gpt-4',
							'gpt-4o' => 'gpt-4o',
							'gpt-4o-mini' => 'gpt-4o-mini',
							'gpt-4.1' => 'gpt-4.1',
							'gpt-4.1-mini' => 'gpt-4.1-mini',
							'gpt-4.1-nano' => 'gpt-4.1-nano',
							'gpt-5-mini' => 'gpt-5-mini',
							'gpt-5-nano' => 'gpt-5-nano',
							'gpt-5.1' => 'gpt-5.1',
							'gpt-5.2' => 'gpt-5.2');
			$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			break;
			
		case 'read_child_nodes':
		case 'exclude_scripts':
		case 'read_images':
		case 'read_images_hovering':
		case 'showlabel':
		case 'screenreader':
		case 'fontsize':
		case 'highcontrast':
		case 'highcontrast_alternate':
		case 'dyslexic_font':
		case 'gray_hues':
		case 'spacing_size':
		case 'big_cursor':
		case 'reading_guides':
		case 'readability':
		case 'customcolors':
		case 'hide_images':
		case 'hide_also_videos_iframes':
		case 'hide_on_mobile':
		case 'use_minimized_toolbar':
		case 'jquery_include':
		case 'preload':
		case 'ie_highcontrast':
		case 'volume_accesskeys':
		case 'ie_highcontrast_advanced':
		case 'select_mainpagearea_text':
		case 'force_jquery_deferred':
		case 'sef_lang_code':
		case 'reader_connection_usesockets':
		case 'use_mobile_reader_engine':
		case 'generate_missing_images_alt':
		case 'fix_headings_structure':
		case 'fix_low_contrast_text':
		case 'fix_missing_aria_roles':
		case 'fix_missing_form_labels':
		case 'validate_and_fix_focus_order':
		case 'show_skip_to_contents':
		case 'remove_links_target':
		case 'enable_focus_outline':
		case 'auto_background_color':
		case 'highcontrast_root_target':
		case 'fontsize_minimized_toolbar':
		case 'hover_minimized_toolbar':
		case 'minimized_toolbar_only_mobile':
		case 'gtranslateintegration':
		case 'read_elements_hovering':
		case 'enable_dark_mode':
		case 'enable_accessibility_statement';
		case 'page_zoom':
			$checked = (bool)$value ? 'checked' : '';
			$nochecked = !(bool)$value ? 'checked' : '';
			if (in_array ( $control, array (
					'read_images',
					'read_images_hovering',
					'read_elements_hovering',
					'dyslexic_font',
					'gray_hues',
					'hide_images',
					'hide_also_videos_iframes',
					'page_zoom',
					'spacing_size',
					'enable_accessibility_statement',
					'big_cursor',
					'reading_guides',
					'readability',
					'customcolors',
					'auto_background_color',
					'enable_dark_mode',
					'generate_missing_images_alt',
					'fix_headings_structure',
					'fix_low_contrast_text',
					'fix_missing_aria_roles',
					'fix_missing_form_labels',
					'validate_and_fix_focus_order',
					'show_skip_to_contents',
					'remove_links_target'
					
			) )) {
				$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			} else {
				$str = '<div class="wrapper">
		               		<label><input type="radio" name="' . $control . '" ' . $checked . ' value="1"> <span>Yes</span></label>
		               		<label><input type="radio" name="' . $control . '" ' . $nochecked . ' value="0"> <span>No</span></label>
	               		</div>';
			}
			
			break;
		case 'id':
			$str = "<input type='hidden' name='id' value='1'/>";
			break;
		default:
			$styles = null;
			if(stripos($control, 'selector') || stripos($control, 'icon_custom') || stripos($control, 'toexclude') || stripos($control, 'statement_link') || stripos($control, 'apikey')) {
				$styles = "style='width: 50%'";
			}
			if (in_array ( $control, array (
					'readability_selector',
					'elements_hovering_selector',
					'spacing_size_min',
					'spacing_size_max',
					'customcolors_cssselectors',
					'enable_accessibility_statement_text',
					'enable_accessibility_statement_link',
					'generate_missing_images_alt_chatgpt_apikey',
					'skiptocontents_selector'
			) )) {
				$str = '<a target="_blank" href="https://storejextensions.org/extensions/screen_reader.html"><i>PRO ONLY</i></a>';
			} else {
				$str = "<input type='text' name='$control' value='$value' $styles/>";
			}
					
	} 
	return $str;
}

/**
 * Funzione di trasformazione HTML controls, restituisce il controllo richiesto
 * @param string $control 
 * @return array
 */
function screenreaderTransformFunctionLabel($controlName) {
	// Text translations
	static $adminLanguageStrings = null;
	$titleString = null;
	$descriptionString = null;
	
	if(!$adminLanguageStrings) {
		$adminLanguageFile = dirname(__FILE__) . "/languages/admin/en-GB.ini";
		if(file_exists($adminLanguageFile)) {
			$adminLanguageStrings = parse_ini_file($adminLanguageFile, false, INI_SCANNER_NORMAL);
		}
	}
	
	// Manage title + desc
	$titleIdentifier = strtoupper($controlName);
	$descriptionIdentifier = $titleIdentifier . '_DESC';

	if(array_key_exists($titleIdentifier, $adminLanguageStrings)) {
		$titleString = $adminLanguageStrings[$titleIdentifier];
	}
	
	if(array_key_exists($descriptionIdentifier, $adminLanguageStrings)) {
		$descriptionString = htmlspecialchars($adminLanguageStrings[$descriptionIdentifier], ENT_QUOTES, 'UTF-8');
	}

	return array($titleString, $descriptionString);
}


/**
 * Renders a generic dropdown list
 * @param string $type
 * @return array
 */
function screenreaderGenericSelectLists($optionsArray, $value, $languageStrings) {
	$optionsString = null;
	foreach ($optionsArray as $optionValue=>$optionText) {
		if(array_key_exists($optionText, $languageStrings)) {
			$optionText = $languageStrings[$optionText];
		}
		$checked = $optionValue == $value ? 'selected="selected"' : '';
		$optionsString .= '<option ' . $checked . ' value="' . $optionValue . '">' . $optionText . '</option>';
	}
	
	return $optionsString;
}

// Load the TinyMCE addon
add_action( 'admin_print_footer_scripts',  'myplugin_register_tinymce_javascript' );

function myplugin_register_tinymce_javascript() {
	$buttonCode = <<<BUTTON
		<script type="text/javascript">
			if(typeof(jQuery) !== 'undefined') {
				jQuery(function($){
					setTimeout(function(){
						if($('#wp-content-editor-tools, div[role=toolbar]').length) {
							window.scrInsertPlayBtn = function() {
								var activeEditor = null;
								if(typeof(tinyMCE) !== 'undefined') {
									activeEditor = tinyMCE.activeEditor;
								} else if(typeof(tinymce) !== 'undefined') {
									activeEditor = tinymce.activeEditor;
								}
								if(activeEditor) {
									activeEditor.execCommand('mceInsertContent', false, codeToAdd);
									setTimeout(function(){
										$('div.mce-edit-focus').blur();
									},100);
								} else {
									if($('p.block-editor-rich-text__editable.is-selected').length) {
										$('p.block-editor-rich-text__editable.is-selected').append(codeToAdd);
									} else {
										// Get it inside the iframe with name="editor-canvas"
				                        var iframe = $('iframe[name="editor-canvas"]');
				                        if(iframe.length) {
				                            try {
				                                var iframeDoc = iframe[0].contentDocument || iframe[0].contentWindow.document;
				                                var iframeBody = $(iframeDoc).find('body');
				                                
				                                // Try to find selected paragraph or block
				                                var selected = iframeBody.find('p.is-selected, p[contenteditable="true"]:focus');
				                                
				                                if(selected.length) {
				                                    selected.append(codeToAdd);
				                                } else {
				                                    // Fallback: append to the first editable paragraph or body
				                                    var editable = iframeBody.find('p[contenteditable="true"]').first();
				                                    if(editable.length) {
				                                        editable.append(codeToAdd);
				                                    } else {
				                                        iframeBody.append(codeToAdd);
				                                    }
				                                }
				                            } catch(e) {
				                                console.error('Cannot access iframe content:', e);
				                                alert('Unable to insert content. The editor may be in a different domain.');
				                            }
				                        }
									}
								}
								return false;
							};
							var codeToAdd = '<span class="screenreader_embed_play_button"> {Play}</span>';
							var codeToAppend = '<div class="wp-media-buttons"><button class="button" onclick="window.scrInsertPlayBtn();return false;"><span style="font: 400 18px/1 dashicons;vertical-align: text-top;color: #82878c;" class="dashicons-video-alt3"></span> ScreenReader Play</button></div>';
							$($('#wp-content-editor-tools, div[role=toolbar]').get(0)).after(codeToAppend);
						};
					}, 1500);
	
					if($('#screenreader-conf').length) {
						$(document).on('change', '#screenreader-conf select[name=template]', function(){
							var currentSelectedTemplate = $(this).val();
							if(currentSelectedTemplate == 'accessible.css') {
								$('#screenreader-conf select[name=template_orientation]').val('vertical');
								$('#screenreader-conf input[name=use_minimized_toolbar]:nth-child(2)').prop('checked', false).removeAttr('checked');
								$('#screenreader-conf input[name=use_minimized_toolbar]:nth-child(3)').prop('checked', true).attr('checked', 'checked');
								$('#screenreader-conf input[name=target_appendto]').val('html');
		
								$('input[name^="customcolors"]').parent('div').show();
							} else {
								$('input[name^="customcolors"]').parent('div').hide();
							}
						});
						if($('#screenreader-conf select[name=template]').val() != 'accessible.css') {
							$('input[name^="customcolors"]').parent('div').hide();
						}
	
						var manageChatGPTAccessibility = function() {
							if($('#screenreader-conf input[name="generate_missing_images_alt"]:checked').val() == 0) {
								$('input[name="generate_missing_images_alt_chatgpt_apikey"]').parent('div').hide();
								$('select[name="generate_missing_images_alt_chatgpt_model"]').parent('div').hide();
							} else {
								$('input[name="generate_missing_images_alt_chatgpt_apikey"]').parent('div').show();
								$('select[name="generate_missing_images_alt_chatgpt_model"]').parent('div').show();
							}
						}
						manageChatGPTAccessibility();
						$(document).on('change', '#screenreader-conf input[name="generate_missing_images_alt"]', function(){
							manageChatGPTAccessibility();
						});
	
						var manageControlsVisibility = function(){
							if($('#screenreader-conf select[name=template]').val() == 'accessible.css' || $('#screenreader-conf select[name=template_orientation]').val() == 'vertical') {
								$('input[name^="page_zoom"]').parent('div').show();
							} else {
								$('input[name^="page_zoom"]').parent('div').hide();
							}
						}
						manageControlsVisibility();
						$(document).on('change', '#screenreader-conf select[name=template], #screenreader-conf select[name=template_orientation]', function(){
							manageControlsVisibility();
						});
					}
				});
			};
		</script>
	BUTTON;

	echo $buttonCode;
}