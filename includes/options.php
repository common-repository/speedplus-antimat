<?php
/* 
SpeedPlus AntiMat Options Page
*/

/* Load styles and scripts */
add_action('admin_enqueue_scripts', 'speedplus_antimat_load_styles_scripts');
function speedplus_antimat_load_styles_scripts() {
	wp_enqueue_style('speedplus_antimat_css', plugin_dir_url( __FILE__ ) . '/css/style.css' );
	if ( is_admin() ){
		if ( isset($_GET['page']) && $_GET['page'] == 'speedplus-antimat' ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery-form' );
		}
	}
}

/* Admin Menu */
add_action('admin_menu', 'speedplus_antimat_admin_menu');
function speedplus_antimat_admin_menu() {
	add_options_page( 'SpeedPlus AntiMat', 'SpeedPlus AntiMat', 'manage_options', 'speedplus-antimat', 'speedplus_antimat_options' );
}
add_action('admin_init', 'speedplus_antimat_init');

/* Sections */
function speedplus_antimat_init(){
	// Register settings
	register_setting( 'speedplus_antimat_plugin_options', 'speedplus_antimat_plugin_options', false );
	// Add Sections (id, title, callback, page)
	add_settings_section(
		'speedplus_antimat_options_1',
		'' /*esc_html__('Settings', 'speedplus-antimat') */,
		'speedplus_antimat_section_1',
		'speedplus_antimat_section'
	);
	
	/* Admin Menu Fields */
	add_settings_field('speedplus_antimat_input_replace_with',
		esc_html__('Replacement word', 'speedplus-antimat'),
		'speedplus_antimat_input_replace_with',
		'speedplus_antimat_section',
		'speedplus_antimat_options_1'
	);
	add_settings_field('speedplus_antimat_select_color',
		esc_html__('Color', 'speedplus-antimat'),
		'speedplus_antimat_select_color',
		'speedplus_antimat_section',
		'speedplus_antimat_options_1'
	);
	add_settings_field('speedplus_antimat_textarea_bad_words',
		esc_html__('Additional list of bad words', 'speedplus-antimat'),
		'speedplus_antimat_textarea_bad_words',
		'speedplus_antimat_section',
		'speedplus_antimat_options_1'
	);
	add_settings_field('speedplus_antimat_checkbox_defaults',
		esc_html__('Defaults', 'speedplus-antimat'),
		'speedplus_antimat_checkbox_defaults',
		'speedplus_antimat_section',
		'speedplus_antimat_options_1'
	);
}

/* Options Description*/
function speedplus_antimat_section_1() {
	// echo '<p>' . esc_html__('Basic plugin settings', 'speedplus-antimat') . '</p>';
}

/* Options */
// Default values
$antimat_replacement = '[censored]';
$antimat_color = '12';
/* First run */
if ( !isset($speedplus_antimat_options['replace_with']) ) {
	$speedplus_antimat_options['replace_with'] = "[censored]";
}
if ( !isset($speedplus_antimat_options['checkbox_color']) ) {
	$speedplus_antimat_options['checkbox_color'] = 12;
}
/* if ( !isset($speedplus_antimat_options['bad_words']) ) {
	$speedplus_antimat_options['bad_words'] = '';
	} */

// Replacement word
function speedplus_antimat_input_replace_with() {
	$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
	if ( !isset($speedplus_antimat_options['replace_with']) ) {
		$speedplus_antimat_options['replace_with'] = "[censored]";
	}
	?>
	<textarea id='speedplus_antimat_input_replace_with' name='speedplus_antimat_plugin_options[replace_with]' rows='2' cols='50' type='textarea'><?php 
		if ( isset($speedplus_antimat_options['checkbox_defaults']) ) {
			global $antimat_replacement;
			esc_html_e( $antimat_replacement );
		}
		else {
			esc_html_e( $speedplus_antimat_options['replace_with'] );
		} 
	?> </textarea>
	<label for='speedplus_antimat_input_replace_with'><?php echo esc_html__( 'Write a word that will replace all bad words.', 'speedplus-antimat' ) . '<br><span class="speedplus_antimat_info">' . esc_html__( 'You can use multiple words, brackets, asterisks, and line breaks.', 'speedplus-antimat' ) . '</span>' ?></label>
	<?php
}

// Additional list of bad words
function speedplus_antimat_textarea_bad_words() {
	$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
	?>
	<textarea id='speedplus_antimat_textarea_bad_words' name='speedplus_antimat_plugin_options[bad_words]' rows='7' cols='50' type='textarea' placeholder="<?php echo esc_html__( 'badword', 'speedplus-antimat' ) . ', *' . esc_html__( 'partofbadword', 'speedplus-antimat' ) . '*' ?>"><?php 
		if ( isset($speedplus_antimat_options['bad_words']) ) {
			if ( isset($speedplus_antimat_options['checkbox_defaults']) ) {
				unset($speedplus_antimat_options['bad_words']);
			}
			else {
				esc_html_e( $speedplus_antimat_options['bad_words'] );
			}
		}
	?></textarea>
	<label for='speedplus_antimat_textarea_bad_words'><?php echo esc_html__( 'Write additional bad words in lowercase, separated by commas. Use asterisks to search for part of a word.', 'speedplus-antimat' ) . '<br><span class="speedplus_antimat_info">' . esc_html__( 'Do not use a comma after the last word of the list. Use only letters. Do not use special characters, spaces, line breaks.', 'speedplus-antimat' ) . '</span>' ?></label>
	<?php
}

// TEST bad words array. Caution! This will display the resulting array at the top of each page
/* $bad_words = array (
	".*fuck.*"
);
$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
if ( isset($speedplus_antimat_options['bad_words']) ) {
		$bad_words_2 = $speedplus_antimat_options['bad_words'];
		$bad_words_2 = str_replace('*', '.*', $bad_words_2);
		$bad_words_2 = str_replace(' ', '', $bad_words_2);		
		$bad_words_2 = explode(",", $bad_words_2);
		$bad_words = array_merge($bad_words, $bad_words_2);
}
print_r($bad_words);
echo '2<pre>'; print_r($bad_words); echo '</pre>';   */

// Color
function speedplus_antimat_select_color() {
	$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
	if ( isset($speedplus_antimat_options['checkbox_defaults']) ) {
		global $antimat_color;
		$speedplus_antimat_options['checkbox_color'] = $antimat_color;
	}
	if ( !isset($speedplus_antimat_options['checkbox_color']) ) {
		$speedplus_antimat_options['checkbox_color'] = 12;
	}
	?>
	<div class="antimat-color-box" style="background-color:<?php 
	// Color for left demo box
	if ( isset($speedplus_antimat_options['checkbox_defaults']) ) {echo '#FF0000';}	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 1 ) {echo '#00FFFF';} 
		elseif ( $speedplus_antimat_options['checkbox_color'] == 2 ) {echo '#000000';}  
		elseif ( $speedplus_antimat_options['checkbox_color'] == 3 ) {echo '#0000FF';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 4 ) {echo '#FF00FF';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 5 ) {echo '#808080';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 6 ) {echo '#008000';}  
		elseif ( $speedplus_antimat_options['checkbox_color'] == 7 ) {echo '#00FF00';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 8 ) {echo '#800000';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 9 ) {echo '#000080';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 10 ) {echo '#808000';}  
		elseif ( $speedplus_antimat_options['checkbox_color'] == 11 ) {echo '#800080';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 12 ) {echo '#FF0000';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 13 ) {echo '#C0C0C0';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 14 ) {echo '#008080';}  
		elseif ( $speedplus_antimat_options['checkbox_color'] == 15 ) {echo '#FFFFFF';} 	
		elseif ( $speedplus_antimat_options['checkbox_color'] == 16 ) {echo '#FFFF00';}
	?>"></div>
	<select id='speedplus_antimat_select_color' name='speedplus_antimat_plugin_options[checkbox_color]'>
	    <option value='1' <?php selected( $speedplus_antimat_options['checkbox_color'], 1 ); ?> style="background-color:#00FFFF"><?php echo esc_html__( 'Aqua', 'speedplus-antimat' ); ?></option>
        <option value='2' <?php selected( $speedplus_antimat_options['checkbox_color'], 2 ); ?> style="background-color:#000000;color:#FFFFFF"><?php echo esc_html__( 'Black', 'speedplus-antimat' ); ?></option>
        <option value='3' <?php selected( $speedplus_antimat_options['checkbox_color'], 3 ); ?> style="background-color:#0000FF;color:#FFFFFF"><?php echo esc_html__( 'Blue', 'speedplus-antimat' ); ?></option>
        <option value='4' <?php selected( $speedplus_antimat_options['checkbox_color'], 4 ); ?> style="background-color:#FF00FF;color:#FFFFFF"><?php echo esc_html__( 'Fuchsia', 'speedplus-antimat' ); ?></option>
		<option value='5' <?php selected( $speedplus_antimat_options['checkbox_color'], 5 ); ?> style="background-color:#808080;color:#FFFFFF"><?php echo esc_html__( 'Gray', 'speedplus-antimat' ); ?></option>
        <option value='6' <?php selected( $speedplus_antimat_options['checkbox_color'], 6 ); ?> style="background-color:#008000;color:#FFFFFF"><?php echo esc_html__( 'Green', 'speedplus-antimat' ); ?></option>
        <option value='7' <?php selected( $speedplus_antimat_options['checkbox_color'], 7 ); ?> style="background-color:#00FF00"><?php echo esc_html__( 'Lime', 'speedplus-antimat' ); ?></option>
        <option value='8' <?php selected( $speedplus_antimat_options['checkbox_color'], 8 ); ?> style="background-color:#800000;color:#FFFFFF"><?php echo esc_html__( 'Maroon', 'speedplus-antimat' ); ?></option>
		<option value='9' <?php selected( $speedplus_antimat_options['checkbox_color'], 9 ); ?> style="background-color:#000080;color:#FFFFFF"><?php echo esc_html__( 'Navy', 'speedplus-antimat' ); ?></option>
        <option value='10' <?php selected( $speedplus_antimat_options['checkbox_color'], 10 ); ?> style="background-color:#808000;color:#FFFFFF"><?php echo esc_html__( 'Olive', 'speedplus-antimat' ); ?></option>
        <option value='11' <?php selected( $speedplus_antimat_options['checkbox_color'], 11 ); ?> style="background-color:#800080;color:#FFFFFF"><?php echo esc_html__( 'Purple', 'speedplus-antimat' ); ?></option>
        <option value='12' <?php selected( $speedplus_antimat_options['checkbox_color'], 12 ); ?> style="background-color:#FF0000;color:#FFFFFF"><?php echo esc_html__( 'Red', 'speedplus-antimat' ); ?></option>
		<option value='13' <?php selected( $speedplus_antimat_options['checkbox_color'], 13 ); ?> style="background-color:#C0C0C0"><?php echo esc_html__( 'Silver', 'speedplus-antimat' ); ?></option>
        <option value='14' <?php selected( $speedplus_antimat_options['checkbox_color'], 14 ); ?> style="background-color:#008080;color:#FFFFFF"><?php echo esc_html__( 'Teal', 'speedplus-antimat' ); ?></option>
        <option value='15' <?php selected( $speedplus_antimat_options['checkbox_color'], 15 ); ?> style="background-color:#FFFFFF"><?php echo esc_html__( 'White', 'speedplus-antimat' ); ?></option>
        <option value='16' <?php selected( $speedplus_antimat_options['checkbox_color'], 16 ); ?> style="background-color:#FFFF00"><?php echo esc_html__( 'Yellow', 'speedplus-antimat' ); ?></option>
    </select>
	<label for='speedplus_antimat_select_color'><?php echo esc_html__( 'Select the color of the replacement word.', 'speedplus-antimat' ); ?></label>
	<?php
}

// Defaults
function speedplus_antimat_checkbox_defaults() {
	$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
	?>
	<input id='speedplus_antimat_checkbox_defaults' name='speedplus_antimat_plugin_options[checkbox_defaults]' value="1" type='checkbox' <?php checked( 1, isset($speedplus_antimat_options['checkbox_defaults']) ); ?> />
	<label for='speedplus_antimat_checkbox_defaults'><?php echo esc_html__( 'Set the "Color" and "Replacement word" fields to their default values and ', 'speedplus-antimat' ) .'<span class="speedplus_antimat_bold">' . esc_html__( 'clear the "Additional list of bad words".', 'speedplus-antimat' ) . '</span>'; ?></label>
	<?php
}

/* Options */
function speedplus_antimat_options() {
	?>
	<div id="speedplus_antimat_admin_panel" class="wrap">
		<h1><span class="speedplus_antimat-header"></span><?php echo esc_html__( 'SpeedPlus AntiMat Settings', 'speedplus-antimat' ); ?></h1>
		<form method="post" action="options.php" id="speedplus_antimat_admin_panel_form">
			<?php settings_fields( 'speedplus_antimat_plugin_options' ); ?>
			<?php do_settings_sections( 'speedplus_antimat_section' ); ?>
			<?php submit_button(); ?>
		</form>
		<div id="speedplus_antimat_save_result"></div>
	</div>

	<div id="speedplus_antimat_admin_panel_footer">
		<?php echo '<p>' . esc_html__( 'Did you like this plugin? You can ', 'speedplus-antimat' ) . '<a href="https://buymeacoffee.com/speedplus">' . esc_html__( 'buy me a coffee.', 'speedplus-antimat' ) . '</a><span class="dashicons dashicons-heart"></span></p>';
		echo '<p>' . esc_html__( 'Also try our plugins', 'speedplus-antimat' ) . ' <strong><a href="https://wordpress.org/plugins/speedplus-optimini/">SpeedPlus OptiMini</a></strong> ' . esc_html__( '(removes obscene words in comments) and', 'speedplus-antimat' ) . ' <strong><a href="https://codecanyon.net/item/speedplus-check-woo-phone/37016075">SpeedPlus Check Woo Phone</a></strong> ' . esc_html__( '(checks the customer\'s phone number on the WooCommerce Checkout page against your rules).', 'speedplus-antimat' ) . '</p>'; ?>
	</div>
	<!-- Ajax Submit and Message -->
	<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery('#speedplus_antimat_admin_panel_form').submit(function() {
				jQuery(this).ajaxSubmit({
					success: function(){
						jQuery('#speedplus_antimat_save_result').html("<div id='speedplus_antimat_save_message' class='successModal'></div>");
						jQuery('#speedplus_antimat_save_message').append("<p><?php echo htmlentities(esc_html__('Settings saved. Nice work!','speedplus-antimat'),ENT_QUOTES); ?></p>").show();
					},
					timeout: 5000
				});
				setTimeout("jQuery('#speedplus_antimat_save_message').hide('slow');", 10000);
				return false;
			});
		});
	</script>
	<?php
}