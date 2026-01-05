<?php
//namespace plugins\screenreader;
/** 
 * Main wp install and render plugin
 * @package SCREENREADER::plugins
 * @author JExtensions Store 
 * @copyright (C) 2023 - JExtensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */

/*
 Plugin Name: Screen Reader Free
 Plugin URI: https://storejextensions.org/extensions/screen_reader.html
 Description: ⚠️Screen Reader plugin free version. The free version is limited to read only 300 characters and has only few features. Visit our website at <a href="https://storejextensions.org/extensions/screen_reader.html">https://storejextensions.org/extensions/screen_reader.html</a> to get the full and unlimited version of the plugin. 
 Author: JExtensions Store
 Version: 3.37
 Author URI: https://storejextensions.org
*/

// SITE SECTION OUTPUT SCRIPTS
function screenreader() { 
	global $wpdb, $table_prefix;
	// CONFIG LOAD DA DB OPTIONS
	$screenreaderQuery = "SELECT * FROM " . $table_prefix . "screenreader_config";
	$pparams = $wpdb->get_row($screenreaderQuery);
	
	$siteUrl = plugins_url('/', __FILE__);
	$langTag = get_bloginfo ( 'language' );
	
	if($pparams->sef_lang_code) {
		$explodedLangTag = explode ( '-', $langTag );
		$langCode = array_shift ( $explodedLangTag );
	} else {
		$langCode = $langTag;
	}
		
	$token = md5 ( $_SERVER ["HTTP_HOST"] );
	
	// Security safe
	if($langTag == 'el') {
		$langTag = 'el-GR';
	}
	if(strlen($langTag) == 2) {
		$langTag = $langTag . '-' . strtoupper($langTag);
	}
	
	// Security safe
	if (! file_exists( dirname(__FILE__) . '/languages/' . $langTag . '.js' )) {
		$langTag = 'en-GB';
	}
	
	// Ensure that the chunk length is correct for Responsive Voice Greek
	if($pparams->reader_engine == 'proxy_responsive' && $langTag == 'el-GR' && $pparams->chunksize > 200) {
		$pparams->chunksize = 200;
	}
	
	// Ensure that the chunk length is correct for Google
	if($pparams->reader_engine == 'proxy' && $pparams->chunksize > 100) {
		$pparams->chunksize = 100;
	}
	
	// Ensure that the chunk length is correct for ISpeech
	if($pparams->reader_engine == 'proxy_ispeech' && $pparams->chunksize > 90) {
		$pparams->chunksize = 90;
	}
		
	// Ensure that the chunk length is correct for Virtual Readers
	if($pparams->reader_engine == 'proxy_virtual_free') {
		if($pparams->chunksize >= 300) {
			$pparams->chunksize = 280;
		}
		$pparams->preload = 1;
	}
	
	// Override params for texttomp3 optimization
	if(($pparams->reader_engine == 'proxy_texttomp3')) {
		$pparams->chunksize = 300;
		$pparams->preload = 1;
	}
	
	// Override params for neospeech optimization
	if(($pparams->reader_engine == 'proxy_neospeech')) {
		if($pparams->chunksize < 400) {
			$pparams->chunksize = 400;
		}
		$pparams->preload = 1;
	}
	
	// Exceptions force settings for the accessible template
	if($pparams->template == 'accessible.css') {
		$pparams->template_orientation = 'vertical';
		$pparams->use_minimized_toolbar = 1;
		$pparams->minimized_toolbar_only_mobile = 0;
		$pparams->target_appendto = 'html';
		$pparams->scrolling = 'fixed';
	}
	
	// Compat vars
	if(!isset($pparams->hide_also_videos_iframes)) {
		$pparams->hide_also_videos_iframes = 0;
	}
	
	echo '<link type="text/css" href="' . $siteUrl . 'libraries/controller/css/' . $pparams->template . '" rel="stylesheet" charset="utf-8"/>'; 
	
	// Ensure update to 3.36 without settings refresh
	if(!isset($pparams->template_variant)) {
		$pparams->template_variant = 'standard';
		$pparams->generate_missing_images_alt = 0;
		$pparams->generate_missing_images_alt_chatgpt_apikey = '';
		$pparams->generate_missing_images_alt_chatgpt_model = 'gpt-3.5-turbo';
		$pparams->fix_headings_structure = 0;
		$pparams->fix_low_contrast_text = 0;
		$pparams->fix_missing_aria_roles = 0;
		$pparams->fix_missing_form_labels = 0;
		$pparams->validate_and_fix_focus_order = 0;
	}
	
	$bas64FunctionNameEncode = 'base'. 64 . '_encode';
	$key = $pparams->generate_missing_images_alt_chatgpt_apikey;
	$key = strrev($key);
	$secret = 'screenreader';
	$out = '';
	for ($i = 0; $i < strlen($key); $i++) {
		$out .= chr(ord($key[$i]) ^ ord($secret[$i % strlen($secret)]));
	}
	$pparams->generate_missing_images_alt_chatgpt_apikey = $bas64FunctionNameEncode($out);
	
	// Ensure a default API is defined
	if(!$pparams->proxy_responsive_reading_mode) {
		$pparams->proxy_responsive_reading_mode = 'native';
	}
	
	// Override for the accessible compact theme
	if($pparams->template == 'accessible.css' && $pparams->template_variant == 'compact') {
		echo '<link type="text/css" href="' . $siteUrl . 'libraries/controller/css/accessible_compact.css" rel="stylesheet" charset="utf-8"/>'; 
	}
	
	// Load jQuery in safe way
	if($pparams->jquery_include) {
		wp_enqueue_script('jquery');
		if($pparams->force_jquery_deferred) {
			echo '<script src="' . $siteUrl . 'libraries/jquery/jquery.js" charset="utf-8" defer></script>';
		}
	}
	
	$scriptDeferLoading = $pparams->script_loading == 'deferred' ? 'defer="defer"' : null;
	
	echo '<script src="' . $siteUrl . 'languages/' . $langTag . '.js" charset="utf-8"></script>';
	echo '<script src="' . $siteUrl . 'libraries/tts/soundmanager/soundmanager2.js" charset="utf-8"></script>';
	echo '<script src="' . $siteUrl . 'libraries/tts/tts.js" charset="utf-8" ' . $scriptDeferLoading . '></script>';
	echo '<script src="' . $siteUrl . 'libraries/controller/controller.js" charset="utf-8" ' . $scriptDeferLoading . '></script>';

	$pparams->proxy_responsive_apikey = trim($pparams->proxy_responsive_apikey) ? trim($pparams->proxy_responsive_apikey) : 'kWyXm5dC';
	if($pparams->reader_engine == 'proxy_responsive' || ($pparams->use_mobile_reader_engine && $pparams->mobile_reader_engine == 'proxy_responsive')) {
		if($pparams->proxy_responsive_loading_script == 0) {
			echo '<script src="' . $siteUrl . 'libraries/tts/responsivevoice.js" charset="utf-8" ' . $scriptDeferLoading . '></script>';
		}else {
			echo '<script src="https://code.responsivevoice.org/responsivevoice.js?key=' . $pparams->proxy_responsive_apikey . '" charset="utf-8" ' . $scriptDeferLoading . '></script>';
		}
	}
	
	echo ("<style type='text/css'>#accessibility-links #text_plugin{width:" . (int)$pparams->labelwidth . "px;}</style>");
	
	// Add focusable outline if enabled
	if($pparams->enable_focus_outline) {
		echo ("<style type='text/css'>:focus{outline: $pparams->enable_focus_outline_bordersize solid $pparams->enable_focus_outline_color !important;}</style>");
	}
	
	if($toolbarBorderRadius = (int)$pparams->toolbar_border_radius) {
		echo ("<style type='text/css'>div#accessibility-links,div#accessibility-links.bottomleft,div#accessibility-links.topleft,div#accessibility-links.bottomright,div#accessibility-links.topright,div#tts_message,div#accessibility-links div#msgtext,div#accessibility-links div#playicon{border-radius:" . $toolbarBorderRadius . "px;}</style>");
	}
	
	// Add custom image icon
	if($pparams->screenreader_icon == 'custom') {
		if($pparams->template == 'accessible.css') {
			echo("<style type='text/css'>div.scbasebin.screenreader.text.scr_customicon{background: url(" . $base . $pparams->screenreader_icon_custom . ") no-repeat center center;background-size: 32px 32px}" .
				 "div#accessibility-links #toolbar_plugin{background: url(" . $base . $pparams->screenreader_icon_custom . ") no-repeat center left 8px;background-size: 32px 32px;padding-left: 5px}</style>");
		} else {
			echo("<style type='text/css'>div.scbasebin.screenreader.text.scr_customicon{background: url(" . $pparams->screenreader_icon_custom . ") no-repeat 4px 2px;background-size: 32px 32px}</style>");
		}
	}
	
	$jsInject = <<<JS
	<script type="text/javascript">
		window.soundManager.url = '{$siteUrl}libraries/tts/soundmanager/swf/';
		window.soundManager.debugMode = false;
		window.soundManager.defaultOptions.volume = $pparams->volume_tts;
	
		var screenReaderConfigOptions = {	baseURI: '$siteUrl',
											token: '$token',
											langCode: '$langCode',
											chunkLength: $pparams->chunksize,
											gtranslateIntegration: $pparams->gtranslateintegration,
											readElementsHovering: $pparams->read_elements_hovering,
											elementsHoveringSelector: '$pparams->elements_hovering_selector',
											elementsToexcludeCustom: '$pparams->elements_toexclude_custom',
											screenReaderVolume: '$pparams->volume_tts',
											screenReaderVoiceSpeed: '$pparams->voice_speed',
											position: '$pparams->corner_position',
											scrolling: '$pparams->scrolling',
											targetAppendto: '$pparams->target_appendto',
											targetAppendMode: '$pparams->target_append_mode',
											enableDarkMode: $pparams->enable_dark_mode,
											enableAccessibilityStatement: $pparams->enable_accessibility_statement,
											enableAccessibilityStatementText: '$pparams->enable_accessibility_statement_text',
											enableAccessibilityStatementLink: '$pparams->enable_accessibility_statement_link',
											preload: $pparams->preload,
											preloadTimeout: $pparams->preload_timeout,
											autoBackgroundColor: $pparams->auto_background_color,
											readPage: $pparams->read_page,
											readChildNodes: $pparams->read_child_nodes,
											ieHighContrast: $pparams->ie_highcontrast,
											ieHighContrastAdvanced: $pparams->ie_highcontrast_advanced,
											selectedStorage: '$pparams->selected_storage',
											selectMainpageareaText: $pparams->select_mainpagearea_text,
											excludeScripts: $pparams->exclude_scripts,
											readImages: $pparams->read_images,
											readImagesAttribute: '$pparams->read_images_attribute',
											readImagesOrdering: '$pparams->read_images_ordering',
											readImagesHovering: $pparams->read_images_hovering,
											mainpageSelector: '$pparams->mainpage_selector',
											showlabel: $pparams->showlabel,
											labeltext: '$pparams->labeltext',
											screenreaderIcon: '$pparams->screenreader_icon',
											screenreader: $pparams->screenreader,
											highcontrast: $pparams->highcontrast,
											highcontrastAlternate: $pparams->highcontrast_alternate,
											colorHue: $pparams->highcontrast_alternate_color_hue,
											colorBrightness: $pparams->highcontrast_alternate_color_brightness,
											rootTarget: $pparams->highcontrast_root_target,
											dyslexicFont: $pparams->dyslexic_font,
											grayHues: $pparams->gray_hues,
											spacingSize: $pparams->spacing_size,
											spacingSizeMin: $pparams->spacing_size_min,
											spacingSizeMax: $pparams->spacing_size_max,
											pageZoom: $pparams->page_zoom,
											bigCursor: $pparams->big_cursor,
											readingGuides: $pparams->reading_guides,
											readability: $pparams->readability,
											readabilitySelector: '$pparams->readability_selector',
											hideImages: $pparams->hide_images,
											hideAlsoVideosIframes: $pparams->hide_also_videos_iframes,
											customColors: $pparams->customcolors,
											customColorsCssSelectors: '$pparams->customcolors_cssselectors',
											fontsizeMinimizedToolbar: $pparams->fontsize_minimized_toolbar,
											hoverMinimizedToolbar: $pparams->hover_minimized_toolbar,
											fontsize: $pparams->fontsize,
											fontsizeDefault: $pparams->font_size_default,
											fontsizeMin: $pparams->font_size_min,
											fontsizeMax: $pparams->font_size_max,
											fontsizeSelector: '$pparams->fontsize_selector',
											fontSizeHeadersIncrement: $pparams->fontsize_headers_increment,
											toolbarBgcolor: '$pparams->toolbar_bgcolor',
											template: '$pparams->template',
											templateOrientation: '$pparams->template_orientation',
											accesskey_play: '$pparams->accesskey_play',
											accesskey_pause: '$pparams->accesskey_pause',
											accesskey_stop: '$pparams->accesskey_stop',
											accesskey_increase: '$pparams->accesskey_increase',
											accesskey_decrease: '$pparams->accesskey_decrease',
											accesskey_reset: '$pparams->accesskey_reset',
											accesskey_highcontrast: '$pparams->accesskey_highcontrast',
											accesskey_highcontrast2: '$pparams->accesskey_highcontrast2',
											accesskey_highcontrast3: '$pparams->accesskey_highcontrast3',
											accesskey_dyslexic: '$pparams->accesskey_dyslexicfont',
											accesskey_grayhues: '$pparams->accesskey_grayhues',
											accesskey_spacingsize_increase: '$pparams->accesskey_spacingsize_increase',
											accesskey_spacingsize_decrease: '$pparams->accesskey_spacingsize_decrease',
											accesskey_bigcursor: '$pparams->accesskey_bigcursor',
											accesskey_reading_guides: '$pparams->accesskey_reading_guides',
											accesskey_readability: '$pparams->accesskey_readability',
											accesskey_hideimages: '$pparams->accesskey_hideimages',
											accesskey_skiptocontents: '$pparams->accesskey_skiptocontents',
											accesskey_minimized: '$pparams->accesskey_minimized',
											volume_accesskeys: $pparams->volume_accesskeys,
											accesskey_increase_volume: '$pparams->accesskey_increase_volume',
											accesskey_decrease_volume: '$pparams->accesskey_decrease_volume',
											accesskey_change_text_color: '$pparams->accesskey_change_text_color',
											accesskey_change_background_color: '$pparams->accesskey_change_background_color',
											readerEngine: '$pparams->reader_engine',
											useMobileReaderEngine: $pparams->use_mobile_reader_engine,
											mobileReaderEngine: '$pparams->mobile_reader_engine',
											proxyResponsiveApikey: '$pparams->proxy_responsive_apikey',
											proxyResponsiveLanguageGender: '$pparams->proxy_responsive_language_gender',
											proxyResponsiveReadingMode: '$pparams->proxy_responsive_reading_mode',
											hideOnMobile: $pparams->hide_on_mobile,
											useMinimizedToolbar: $pparams->use_minimized_toolbar,
											statusMinimizedToolbar: '$pparams->status_minimized_toolbar',
											minimizedToolbarOnlyMobile: $pparams->minimized_toolbar_only_mobile,
											generateMissingImagesAlt: $pparams->generate_missing_images_alt,
											generateMissingImagesAltChatgptApikey: '$pparams->generate_missing_images_alt_chatgpt_apikey',
											generateMissingImagesAltChatgptModel: '$pparams->generate_missing_images_alt_chatgpt_model',
											fixHeadingsStructure: $pparams->fix_headings_structure,
											fixLowContrastText: $pparams->fix_low_contrast_text,
											fixMissingAriaRoles: $pparams->fix_missing_aria_roles,
											fixMissingFormLabels: $pparams->fix_missing_form_labels,
											validateAndFixFocusOrder: $pparams->validate_and_fix_focus_order,
											showSkipToContents: $pparams->show_skip_to_contents,
											skipToContentsSelector: '$pparams->skiptocontents_selector',
											removeLinksTarget: $pparams->remove_links_target,
											resetButtonBehavior: '$pparams->reset_button_behavior'
										};
	</script>
JS;
	echo $jsInject;
}

// Now we set that function up to execute when the admin_notices action is called 
add_action( 'wp_head', 'screenreader' );  

function screenreader_install() {
	global $wpdb, $table_prefix;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	$sqlCreateConfigTable = "DROP TABLE IF EXISTS `" . $table_prefix . "screenreader_config`;
	CREATE TABLE IF NOT EXISTS `" . $table_prefix . "screenreader_config` (
		`id` int(11) NOT NULL DEFAULT 1 PRIMARY KEY,
		`volume_tts` int(11) NOT NULL DEFAULT 80,
		`voice_speed` varchar(255) NOT NULL DEFAULT 'normal',
		`read_page` tinyint(4) NOT NULL DEFAULT 1,
		`read_child_nodes` tinyint(4) NOT NULL DEFAULT 1,
		`exclude_scripts` tinyint(4) NOT NULL DEFAULT 1,
		`read_images` tinyint(4) NOT NULL DEFAULT 0,
		`read_images_ordering` varchar(255) NOT NULL DEFAULT 'before',
		`read_images_hovering` tinyint(4) NOT NULL DEFAULT 0,
		`read_images_attribute` varchar(255) NOT NULL DEFAULT 'alt',
		`mainpage_selector` varchar(255) NOT NULL DEFAULT '*[name*=main], *[class*=main], *[id*=main]',
		`select_mainpagearea_text` tinyint(4) NOT NULL DEFAULT 0,
		`chunksize` int(11) NOT NULL DEFAULT 300,
		`gtranslateintegration` tinyint(4) NOT NULL DEFAULT 0,
		`read_elements_hovering` tinyint(4) NOT NULL DEFAULT 0,
		`elements_hovering_selector` varchar(255) NOT NULL DEFAULT 'p',
		`elements_toexclude_custom` varchar(255) NOT NULL DEFAULT '',
		`showlabel` tinyint(4) NOT NULL DEFAULT 1,
		`labeltext` varchar(255) NOT NULL DEFAULT 'Screen Reader',
		`labelwidth` int(11) NOT NULL DEFAULT 80,
		`screenreader` tinyint(4) NOT NULL DEFAULT 1,
		`screenreader_icon` varchar(255) NOT NULL DEFAULT 'audio',
		`screenreader_icon_custom` varchar(255) NOT NULL DEFAULT '',
		`fontsize` tinyint(4) NOT NULL DEFAULT 1,
		`font_size_default` int(11) NOT NULL DEFAULT 100,
		`font_size_min` int(11) NOT NULL DEFAULT 50,
		`font_size_max` int(11) NOT NULL DEFAULT 200,
		`fontsize_selector` varchar(255) NOT NULL DEFAULT '',
		`fontsize_headers_increment` int(11) NOT NULL DEFAULT 20,
		`highcontrast` tinyint(4) NOT NULL DEFAULT 1,
		`highcontrast_alternate` tinyint(4) NOT NULL DEFAULT 1,
		`highcontrast_alternate_color_hue` int(11) NOT NULL DEFAULT 180,
		`highcontrast_alternate_color_brightness` int(11) NOT NULL DEFAULT 6,
		`highcontrast_root_target` tinyint(4) NOT NULL DEFAULT 0,
	 	`dyslexic_font` tinyint(4) NOT NULL DEFAULT 0,
		`gray_hues` tinyint(4) NOT NULL DEFAULT 0,
		`spacing_size` tinyint(4) NOT NULL DEFAULT 0,
		`spacing_size_min` int(11) NOT NULL DEFAULT 0,
		`spacing_size_max` int(11) NOT NULL DEFAULT 10,
		`page_zoom` tinyint(4) NOT NULL DEFAULT 0,
		`big_cursor` tinyint(4) NOT NULL DEFAULT 0,
		`reading_guides` tinyint(4) NOT NULL DEFAULT 0,
		`readability` tinyint(4) NOT NULL DEFAULT 0,
		`readability_selector` varchar(255) NOT NULL DEFAULT '#main,article',
		`hide_images` tinyint(4) NOT NULL DEFAULT 0,
		`hide_also_videos_iframes` tinyint(4) NOT NULL DEFAULT 0,
		`customcolors` tinyint(4) NOT NULL DEFAULT 0,
		`customcolors_cssselectors` varchar(255) NOT NULL DEFAULT '',
		`corner_position` varchar(255) NOT NULL DEFAULT 'bottomright',
		`template` varchar(255) NOT NULL DEFAULT 'accessible.css',
		`template_orientation` varchar(255) NOT NULL DEFAULT 'vertical',
		`template_variant` varchar(255) NOT NULL DEFAULT 'standard',
		`toolbar_bgcolor` varchar(255) NOT NULL DEFAULT '#EEE',
		`toolbar_border_radius` int(11) NOT NULL DEFAULT 0,
		`scrolling` varchar(255) NOT NULL DEFAULT 'fixed',
		`target_appendto` varchar(255) NOT NULL DEFAULT 'html',
		`target_append_mode` varchar(255) NOT NULL DEFAULT 'bottom',
		`enable_dark_mode` tinyint(4) NOT NULL DEFAULT 0,
		`enable_accessibility_statement` tinyint(4) NOT NULL DEFAULT 0,
		`enable_accessibility_statement_text` varchar(255) NOT NULL DEFAULT 'Accessibility statement',
		`enable_accessibility_statement_link` varchar(255) NOT NULL DEFAULT '',
		`use_minimized_toolbar` tinyint(4) NOT NULL DEFAULT 1,
		`status_minimized_toolbar` varchar(255) NOT NULL DEFAULT 'closed',
		`minimized_toolbar_only_mobile` tinyint(4) NOT NULL DEFAULT 0,
		`fontsize_minimized_toolbar` tinyint(4) NOT NULL DEFAULT 0,
		`hover_minimized_toolbar` tinyint(4) NOT NULL DEFAULT 0,
		`hide_on_mobile` tinyint(4) NOT NULL DEFAULT 0,
		`generate_missing_images_alt` tinyint(4) NOT NULL DEFAULT 0,
		`generate_missing_images_alt_chatgpt_apikey` varchar(255) NOT NULL DEFAULT '',
		`generate_missing_images_alt_chatgpt_model` varchar(50) NOT NULL DEFAULT 'gpt-3.5-turbo',
		`fix_headings_structure` tinyint(4) NOT NULL DEFAULT 0,
		`fix_low_contrast_text` tinyint(4) NOT NULL DEFAULT 0,
		`fix_missing_aria_roles` tinyint(4) NOT NULL DEFAULT 0,
		`fix_missing_form_labels` tinyint(4) NOT NULL DEFAULT 0,
		`validate_and_fix_focus_order` tinyint(4) NOT NULL DEFAULT 0,
		`show_skip_to_contents` tinyint(4) NOT NULL DEFAULT 0,
		`skiptocontents_selector` varchar(255) NOT NULL DEFAULT '',
		`enable_focus_outline` tinyint(4) NOT NULL DEFAULT 0,
		`enable_focus_outline_color` varchar(255) NOT NULL DEFAULT '#F00',
		`enable_focus_outline_bordersize` varchar(255) NOT NULL DEFAULT '2px',
		`remove_links_target` tinyint(4) NOT NULL DEFAULT 0,
		`auto_background_color` tinyint(4) NOT NULL DEFAULT 1,
		`jquery_include` tinyint(4) NOT NULL DEFAULT 1,
		`preload` tinyint(4) NOT NULL DEFAULT 1,
		`preload_timeout` int(11) NOT NULL DEFAULT 3000,
		`ie_highcontrast` tinyint(4) NOT NULL DEFAULT 1,
		`ie_highcontrast_advanced` tinyint(4) NOT NULL DEFAULT 1,
		`selected_storage` varchar(255) NOT NULL DEFAULT 'session',
		`force_jquery_deferred` tinyint(4) NOT NULL DEFAULT 0,
		`script_loading` varchar(255) NOT NULL DEFAULT 'deferred',
		`reset_button_behavior` varchar(255) NOT NULL DEFAULT 'fontsize',
		`sef_lang_code` tinyint(4) NOT NULL DEFAULT 1,
		`reader_engine` varchar(255) NOT NULL DEFAULT 'proxy_responsive',
		`engine_google_token_mode` tinyint(4) NOT NULL DEFAULT 0,
		`reader_connection_usesockets` tinyint(4) NOT NULL DEFAULT 1,
		`use_mobile_reader_engine` tinyint(4) NOT NULL DEFAULT 0,
		`mobile_reader_engine` varchar(255) NOT NULL DEFAULT 'proxy_responsive',
		`proxy_responsive_apikey` varchar(255) NOT NULL DEFAULT 'kWyXm5dC',
		`proxy_responsive_language_gender` varchar(255) NOT NULL DEFAULT 'auto',
		`proxy_responsive_loading_script` tinyint(4) NOT NULL DEFAULT 0,
		`proxy_responsive_reading_mode` varchar(255) NOT NULL DEFAULT 'native',
		`accesskey_play` varchar(255) NOT NULL DEFAULT 'P',
		`accesskey_pause` varchar(255) NOT NULL DEFAULT 'E',
		`accesskey_stop` varchar(255) NOT NULL DEFAULT 'S',
		`accesskey_increase` varchar(255) NOT NULL DEFAULT 'O',
		`accesskey_decrease` varchar(255) NOT NULL DEFAULT 'U',
		`accesskey_reset` varchar(255) NOT NULL DEFAULT 'R',
		`accesskey_highcontrast` varchar(255) NOT NULL DEFAULT 'H',
		`accesskey_highcontrast2` varchar(255) NOT NULL DEFAULT 'J',
		`accesskey_highcontrast3` varchar(255) NOT NULL DEFAULT 'K',
	 	`accesskey_dyslexicfont` varchar(255) NOT NULL DEFAULT 'D',
		`accesskey_grayhues` varchar(255) NOT NULL DEFAULT 'G',
		`accesskey_spacingsize_increase` varchar(255) NOT NULL DEFAULT 'M',
		`accesskey_spacingsize_decrease` varchar(255) NOT NULL DEFAULT 'N',
		`accesskey_pagezoomsize_increase` varchar(255) NOT NULL DEFAULT 'X',
		`accesskey_pagezoomsize_decrease` varchar(255) NOT NULL DEFAULT 'Y',
		`accesskey_pagezoomsize_reset` varchar(255) NOT NULL DEFAULT 'Z',
		`accesskey_bigcursor` varchar(255) NOT NULL DEFAULT 'W',
		`accesskey_reading_guides` varchar(255) NOT NULL DEFAULT 'V',
		`accesskey_readability` varchar(255) NOT NULL DEFAULT 'Q',
		`accesskey_hideimages` varchar(255) NOT NULL DEFAULT 'F',
		`accesskey_skiptocontents` varchar(255) NOT NULL DEFAULT 'C',
		`accesskey_minimized` varchar(255) NOT NULL DEFAULT 'L',
		`accesskey_change_text_color` varchar(255) NOT NULL DEFAULT 'I',
		`accesskey_change_background_color` varchar(255) NOT NULL DEFAULT 'B',
		`volume_accesskeys` tinyint(4) NOT NULL DEFAULT 1,
		`accesskey_increase_volume` varchar(255) NOT NULL DEFAULT '+',
		`accesskey_decrease_volume` varchar(255) NOT NULL DEFAULT '-'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
	dbDelta($sqlCreateConfigTable);
} 
register_activation_hook(__FILE__,'screenreader_install');
 
function screenreader_install_data() {
	global $wpdb, $table_prefix;  
	$table_name = 'screenreader_config';
	$wpdb->insert( $table_prefix . $table_name, array( 'id' => 1 ));
}
register_activation_hook(__FILE__,'screenreader_install_data'); 

function screenreader_uninstall() {
	global $wpdb, $table_prefix;
	$table_name = 'screenreader_config';
	$wpdb->query( "DROP TABLE " . $table_prefix . $table_name);
}
register_uninstall_hook( __FILE__, 'screenreader_uninstall' );

// ADMIN SECTION
if ( is_admin() )
	require_once dirname( __FILE__ ) . '/admin.php';