<?php 
/**
* Plugin Name:       SpeedPlus AntiMat: Remove profanity in comments
* Plugin URI:        https://speedplus.com.ua/en/speedplus-antimat/
* Description:       The plugin checks the text of comments when they are added and, if it detects obscene words, replaces them with the word you specified. You can add your own forbidden words. Latin and Cyrillic are supported. 
* Version:           2.0.4
* Author:            SpeedPlus
* Author URI:        https://speedplus.com.ua
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       speedplus-antimat
* Domain Path:       /languages
*
* Copyright 2021 SpeedPlus
*/

require_once plugin_dir_path(__FILE__) . 'includes/options.php';
function speedplus_antimat_load_plugin_textdomain(){
	load_plugin_textdomain( 'speedplus-antimat', false, basename( __DIR__ ) . '/languages/' );
}
add_action( 'plugins_loaded', 'speedplus_antimat_load_plugin_textdomain' );

/* Add links to Plugins list */
add_filter( 'plugin_action_links', 'speedplus_antimat_add_action_plugin', 10, 5 );
function speedplus_antimat_add_action_plugin( $actions, $plugin_file ) {
	static $plugin;
	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
		$settings = array('settings' => '<a href="options-general.php?page=speedplus-antimat" style="display:inline">' . __('Settings') . '</a>');
		$site_link = array('support' => '<a style="display:inline" href="https://freelancehunt.com/freelancer/Kostyantin.html?r=3YmoD" target="_blank">' . __('Need help? Hire a freelancer here', 'speedplus-antimat') . '</a>');
		$actions = array_merge($site_link, $actions);
		$actions = array_merge($settings, $actions);
	}
	return $actions;
}
$speedplus_antimat = new speedplus_antimat();
add_filter('pre_comment_content','speedplus_antimat_filter_comment', 60);
function speedplus_antimat_filter_comment($comment_text) {
	global $speedplus_antimat;
	$comment_text = $speedplus_antimat->filter($comment_text);
	return $comment_text;
}

/* Main part of plugin */
class speedplus_antimat {
	// Bad words array. Regexp's symbols are readable.
	/* USE THIS TEMPLATE FOR ADD or EDIT NEW BAD WORDS # ИСПОЛЬЗУЙТЕ ЭТОТ ШАБЛОН ПРИ РЕДАКТИРОВАНИИ или ДОБАВЛЕНИИ НОВЫХ ПЛОХИХ СЛОВ
		".*бля(де|т|ц).*", - template # шаблон
		"word", - quotes and comma after are required # кавычки и запятая обязательны (кроме последней строки массива)
		.* - any characters and any length # любые символы в любом количестве
		(де|т|ц) - any of this characters # любой из этих символов
	*/
	var $bad_words = array (
		"arse",
		"ass",
		"(ass|arse|pee)hole.*",
		"bastard.*",
		"bdsm",
		"bint",
		"bisnotch",
		"bitch",
		"bloke",
		"bloodclaat",
		"blowjob",
		"bollocks",
		"(p|b)rat.*",
		"breast.*",
		"bugger",
		"bullshit",
		"candyass",
		"capon",
		"choad",
		"clunge",
		"cock.*",
		"crap",
		"cum",
		".*cumshot.*",
		"cunt",
		"dafaq",
		"damn.*",
		"dick",
		"dickhead",
		"dork",
		"dumd",
		"faggot",
		"fart.*",
		".*f(u|e)ck.*",
		"finook",
		"gash",
		"gay",
		"git",
		"goddamn",
		"goof",
		"hooker",
		"incest",
		"jade",
		"jerk.*",
		"lesbian",
		"minge",
		"moron",
		"munter",
		"naked",
		".*(n|w)i(gg|g)er.*",
		"nitwit",
		"nude",
		"numbnuts",
		"penis",
		".*piss.*",
		"poop",
		".*porn.*",
		"prick",
		".*pussy.*",
		"queer",
		"retard",
		"(douche|scum)bag.*",
		"shemale",
		"shit",
		"sissy",
		"sex",
		"slut.*",
		"stupid",
		"suck.*",
		"tit(s|ties).*",
		"turd.*",
		"tw(a|un)t.*",
		"wank(er|ing).*",
		"whore.*",
		".*ху(й|и|я|е|ли|ле|і|є|лі|лє|ї).*", // хуй, хуевый, хули
		".*п(и|і|ьо|е|йо|ё|є)(з|с)(д|ж).*", // пизда, пиздец, припизженный
		"^(?!ру).*бля.*", // блядство, проблядовка. Исключаем "рубля". Идеальная и неработающая формула: (?=(^(?!рубля$).*$))(?=.*бля.*)
		"(?!ко)манд(а|о|е|є|ю|я|у|и)(?!рин|т).*", // манда. Исключаем мандарин, команда, мандат.
		".*др(и|е|і|є)ст.*", // дристать
		".*трах.*", // затраханный, трахать
		".*муд(а|о|и|і|н).*", // мудило, муди, мудозвон, мудак, мудня
		".*бл(е|є)в.*", // блевотина
		".*др(а|о|ю)ч.*", // дрючить, дрочить, задрочный
		".*бзд.*", // бздеть, бздун
		".*жоп.*", // жопа, поджопник, голожопый
		".*гом(ик|осек).*", // гомик, гомосек
		"п(и|е|і|є)д(ик|дор|дар|рил).*", // педик, пидар, педрила
		".*п(и|і)с(ят|ян|ьк|юн).*", // писька, записянный, писюн
		".*сра(н|т|л|ч|к).*", // срать, засранный, срака
		".*(вы|об|пере|до|за|об|под|у)(сир|сер|сик).*", // высер, высикать, обсирать
		".*сик(ан|ат).*", // сикать, высиканный
		".*с(с|ц)а(н|т|л|к).*", // ссать, засцаный, ссака
		".*с(с|ц)(ы|и)(к).*", // ссыкуха
		"(с|сц|сс)у(ч|к)(е|є|а|о|и|і).*", // сцука, суки, ссученный
		".*ублюд.*", // ублюдок
		"(я|е|є|йо)д(рен|рит).*", // едрить, едреный
		"(е|є)т(и|ит).*", // етить, ети
		".*курв.*", // курва, скурвился
		".*лярв.*", // лярва
		"поц", // поц
		".*м(е|и|і|є)нет.*", // минет
		"дава(лк|х).*", // давалка, даваха
		"(е|є|ю|я)лд.*", // елда, юлда, ялда
		"(е|є|йо)б.*", // ебать, еб, йобаный
		".*сноша.*", // сношать
		".*пенд(юр|юл|ел).*", // пендель, пендюрить
		".*с(а|о)с(ат|и|і|ал|ыв|ан).*", // сосать, соси, насасывать
		".*отсос.*", // отсос
		".*(при|про|недо|до|за|пере|в|по|вы|у|ь|ъ|на|о)(я|е|ё|є|ї|йо|я)б.*", // уебан, въебать, объебанный, выебон, поебать, мозгоебка
		".*(е|є|ї|йо)б(а|и|і|ы)(н|с|щ|ц|ш|т).*", // проебать, поебывать
		".*(е|є|ї|йо)бу(ч|щ).*", // ебучий
		".*п(и|і|є|е)д(о|е|а)р.*", // пидарко, пидор
		".*х(е|є)р.*", // херовый, хер
		".*хр(е|є)н.*", // хрен, охреневший
		"г(а|о|и|і)ндон.*", // гондон
		".*з(а|о)луп.*", // залупа
		".*д(е|и|і|є)(рьм|рм).*", // дерьмо, дерьмовый
		".*г(а|о)(в|ве)н.*" // гавняный, говно, говенный
	);
	function rand_replace (){
		// Add replacing word AND color
		$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options');
		if ( !isset($speedplus_antimat_options['checkbox_color']) ) {
			$speedplus_antimat_checkbox_color_selected = "#FF0000";
		}
		else {
			$speedplus_antimat_checkbox_color_selected = 
			($speedplus_antimat_options['checkbox_color'] == 1) ? "#00FFFF"
			: (($speedplus_antimat_options['checkbox_color'] == 2) ? "#000000" 
			: (($speedplus_antimat_options['checkbox_color'] == 3) ? "#0000FF"
			: (($speedplus_antimat_options['checkbox_color'] == 4) ? "#FF00FF"
			: (($speedplus_antimat_options['checkbox_color'] == 5) ? "#808080"
			: (($speedplus_antimat_options['checkbox_color'] == 6) ? "#008000"
			: (($speedplus_antimat_options['checkbox_color'] == 7) ? "#00FF00"
			: (($speedplus_antimat_options['checkbox_color'] == 8) ? "#800000"
			: (($speedplus_antimat_options['checkbox_color'] == 9) ? "#000080"
			: (($speedplus_antimat_options['checkbox_color'] == 10) ? "#808000"
			: (($speedplus_antimat_options['checkbox_color'] == 11) ? "#800080"
			: (($speedplus_antimat_options['checkbox_color'] == 12) ? "#FF0000"
			: (($speedplus_antimat_options['checkbox_color'] == 13) ? "#C0C0C0"
			: (($speedplus_antimat_options['checkbox_color'] == 14) ? "#008080"
			: (($speedplus_antimat_options['checkbox_color'] == 15) ? "#FFFFFF"
			: "#FFFF00"))))))))))))));
		}
		if ( !isset($speedplus_antimat_options['replace_with']) ) {
			$speedplus_antimat_word_selected = "[censored]";
		}
		else {
			$speedplus_antimat_word_selected = $speedplus_antimat_options['replace_with'];
		}
		$output = " <font color='" . $speedplus_antimat_checkbox_color_selected . "'>" . $speedplus_antimat_word_selected . "</font> ";
		return $output;
	}
	function filter ($string){
		$counter = 0;
		$string = str_replace("\n", " {nl} ", $string); // Replace line feed character with codes
		$elems = explode (" ", $string); // Here we explode string to words
		$count_elems = count($elems);
		for ($i=0; $i<$count_elems; $i++) {
			$blocked = 0;
			$str_rep = preg_replace ("/[^a-zйцукенгшщзхъфывапролджэячсмитьбюёіїє]/i", "", mb_convert_case($elems[$i], MB_CASE_LOWER, "UTF-8"));
			// START Connecting own bad words
			$speedplus_antimat_options = get_option('speedplus_antimat_plugin_options'); // Connecting options
			if ( isset($speedplus_antimat_options['bad_words']) && (strlen($speedplus_antimat_options['bad_words'])>0)) { // Check for the presence of own words
				$bad_words_2 = $speedplus_antimat_options['bad_words']; // Get own words
				$bad_words_2 = str_replace('*', '.*', $bad_words_2); // Replace any-symbol-character
				$bad_words_2 = str_replace(' ', '', $bad_words_2); // Removing spaces
				$bad_words_2 = explode(",", $bad_words_2); // Convert to array with delimiter ","
				$this->bad_words = array_merge($this->bad_words, $bad_words_2); // Combining both arrays of bad words
			}
			// END Connecting own bad words
			// Here we are trying to find bad word. Match in the special array.
			for ($k=0; $k<count($this->bad_words); $k++) {
				if (preg_match("#\*$#", $this->bad_words[$k])) {
					if (preg_match("#^" . $this->bad_words[$k] . "#", $str_rep)) {
						$elems[$i] = $this->rand_replace();
						$blocked = 1;
						$counter++;
						break;
					}
				}
				if ($str_rep == $this->bad_words[$k]){
					$elems[$i] = $this->rand_replace();
					$blocked = 1;
					$counter++;
					break;
				}
			}
		}
		if ($counter != 0)
			$string = implode (" ", $elems); //here we implode words in the whole string
			$string = str_replace(" {nl} ", "\n", $string);
			return $string; 
	}
}
?>