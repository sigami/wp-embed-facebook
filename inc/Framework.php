<?php

namespace SIGAMI\WP_Embed_FB;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Framework
 *
 * @version 2.6.3
 */
abstract class Framework {

	#################################################################
	#  Replace this variables on Plugin Class constructor as needed #
	#################################################################

	/**
	 * REQUIRED
	 *
	 * @var string option_name from get_option. Where all plugin options are stored
	 */
	protected static $option = '';

	/**
	 * @var bool Refresh options on each page load, helpful to add new defaults on the fly
	 *           set to WP_DEBUG on Framework constructor.
	 */
	protected static $defaults_change = null;

	/**
	 * String values for toggle options like checkboxes or any other internal process
	 *
	 * @var string
	 */
	protected static $on  = 'true';
	protected static $off = 'false';

	/**
	 * Admin page type to edit the plugin options. Set to false disable this feature.
	 *
	 * @var string
	 *      menu|submenu|management|options|theme|plugins|users|dashboard|posts|media|links|pages|comments
	 */
	protected static $page_type  = 'options';
	protected static $capability = 'manage_options';
	protected static $menu_slug  = 'new_page';
	# Only for menu pages (Top Level)
	protected static $icon     = '';
	protected static $position = null;
	# Only for submenu pages
	protected static $parent_slug = 'options-general.php';
	# Labels for options page. Set i18n on constructor
	protected static $page_title = 'New Page';
	protected static $menu_title = 'New Page';
	/**
	 * @var bool Enable reset button on options page
	 */
	protected static $reset_button = true;
	# Labels for reset options. Set i18n on constructor
	protected static $reset_string = 'Reset to defaults';
	protected static $confirmation = 'Are you sure?';
	/**
	 * @var string internal control to redirect to current tab on save
	 */
	protected static $current_tab = '';

	###########################################
	# Cache of commonly used vars do not edit #
	###########################################

	/**
	 * @var string __FILE__ constant from plugin FILE
	 */
	public static $FILE = '';

	protected static $defaults = null;
	private static   $options  = null;
	private static   $path     = null;
	private static   $url      = null;
	private static   $instance = null;

	static function instance( $file ) {
		if ( self::$instance === null ) {
			self::$instance = new static( $file );
		}

		return self::$instance;
	}

	protected function __construct( $file ) {

		if ( empty( static::$option ) ) {
			wp_die( "You must set an option name on the Plugin class constructor" );
		}

		static::$FILE = $file;

		if ( ( static::$defaults_change === null ) && defined( WP_DEBUG ) ) {
			static::$defaults_change = (bool) WP_DEBUG;
		}

		add_action( 'plugins_loaded', get_called_class() . '::load_translation' );
		register_setting( "Settings_" . static::$option, static::$option, [
			'sanitize_callback' => get_called_class() . '::sanitize_option',
			'default'           => static::defaults(),
		] );

		register_activation_hook( $file, get_called_class() . '::activation' );
		register_uninstall_hook( $file, get_called_class() . '::uninstall' );
		register_deactivation_hook( $file, get_called_class() . '::deactivation' );
		add_action( 'upgrader_process_complete', get_called_class() . '::update', 10, 2 );

		if ( is_admin() && is_string( static::$page_type ) ) {
			/** @see Framework::add_page() */
			add_action( 'admin_menu', get_called_class() . '::add_page' );
			/** @see Framework::add_pager_script() */
			add_action( 'current_screen', get_called_class() . '::add_pager_script' );
			/** @see Framework::redirect_to_tab() */
			add_action('load-options.php', get_called_class() . '::redirect_to_tab' );
		}
	}

	static function activation() {
		static::$defaults_change = true;
		static::get_option();
		do_action( static::$option . '_' . __FUNCTION__, __FUNCTION__ );
	}

	static function uninstall() {
		do_action( static::$option . '_' . __FUNCTION__, __FUNCTION__ );
		if ( is_multisite() ) {
			$sites = get_sites();
			foreach ( $sites as $site ) {
				switch_to_blog( $site['blog_id'] );
				delete_option( static::$option );
			}
			restore_current_blog();
		} else {
			delete_option( static::$option );
		}
	}

	static function deactivation() {
		do_action( static::$option . '_' . __FUNCTION__, __FUNCTION__ );
	}

	static function update( $object, $options ) {
		if ( $options['action'] == 'update'
		     && isset( $options['plugins'] )
		     && in_array( plugin_basename( static::$FILE ), $options['plugins'] ) ) {

			static::$defaults_change = true;
			static::get_option();
			do_action( static::$option . '_' . __FUNCTION__, __FUNCTION__ );
		}
	}

	static function path() {
		if ( null === self::$path ) {
			self::$path = plugin_dir_path( static::$FILE );
		}

		return self::$path;
	}

	static function url() {
		if ( null === self::$url ) {
			self::$url = plugin_dir_url( static::$FILE );
		}

		return self::$url;
	}

	/**
	 * To save options pass an array with all values as set on defaults. See example below:
	 *
	 * <code>
	 * # Set single option
	 * Plugin::set_options( ['option'=>'new value'] + Plugin::get_option() );
	 * or
	 * Plugin::set_options( array_merge(Plugin::get_option(),['option'=>'new value']) );
	 *
	 * # Reset all options to defaults
	 * Plugin::set_options(Plugin::defaults());
	 *
	 * </code>
	 * @param array|string $options
	 */
	static function set_options( $options ) {
		update_option( static::$option, $options, true );
		self::$options = get_option( static::$option );
	}

	/**
	 * Get a single option or all of them. Can
	 *
	 * @param null|string $option
	 *
	 * @return array|mixed The queried option. False if the option does not exists. Array with all
	 *                     options if $option is null;
	 */
	static function get_option( $option = null ) {
		if ( ! is_array( self::$options ) ) {
			$options = get_option( static::$option );
			if ( is_array( $options ) ) {
				#Sanitize options on install, update and on development also useful when you want to
				# add a new default and want it to show on the next page load.
				# Equals to WP_DEBUG on construct
				if ( static::$defaults_change ) {
					if ( $options === static::defaults() ) {
						self::$options = $options;
					} else {
						$compare = [];
						foreach ( static::defaults() as $default_key => $default_value ) {
							$compare[ $default_key ] = isset( $options[ $default_key ] ) ? $options[ $default_key ]
								: $default_value;
						}
						if ( $compare === $options ) {
							self::$options = $options;
						} else {
							static::set_options( $compare );
						}
					}
				} else {
					self::$options = $options;
				}
			} else {
				static::set_options( static::defaults() );
			}
		}
		if ( $option ) {
			return isset( self::$options[ $option ] ) ? self::$options[ $option ] : false;
		} else {
			return self::$options;
		}
	}

	/**
	 * Sanitizes the option values before saving it to database.
	 *
	 * @param array $options Options previous to be saved on data base
	 *
	 * @return array The validated option.
	 */
	static function sanitize_option( $options ) {

		$defaults = static::defaults();

		if ( is_string( $options ) && ( $options == 'reset_defaults' ) ) {
			do_action( self::$option . '_reset_defaults' );

			return $defaults;
		}

		if ( $options === $defaults ) {
			return $options;
		}

		$clean = [];
		foreach ( $defaults as $name => $default_value ) {
			$clean[ $name ] = $default_value;
			if ( ( ( $default_value === static::$on ) || ( $default_value === static::$off ) ) ) {
				if ( isset( $options[ $name ] ) && ( $options[ $name ] === static::$on ) ) {
					$clean[ $name ] = static::$on;
				} else {
					$clean[ $name ] = static::$off;
				}
			} elseif ( isset( $options[ $name ] ) ) {
				if ( is_int( $default_value ) ) {
					$options[ $name ] = (int) $options[ $name ];
				}
				if ( ( is_string( $default_value ) && is_string( $options[ $name ] ) )
				     || ( is_object( $default_value ) && is_object( $options[ $name ] ) )
				     || ( is_array( $default_value ) && is_array( $options[ $name ] ) ) ) {
					$clean[ $name ] = $options[ $name ];
				}
			}
		}

		return $clean;
	}

	/**
	 * @param string $option
	 *
	 * @return bool
	 */
	static function is_on( $option ) {
		return static::get_option( $option ) === static::$on;
	}

	static function get_option_name(){
	    return static::$option;
    }

	/**
	 * Add page to Settings
	 */
	static function add_page() {
		$function = 'add_' . static::$page_type . '_page';
		if ( function_exists( $function ) ) {

			if ( static::$page_type == 'menu' ) {
				$options_page = call_user_func( $function, static::$page_title, static::$menu_title,
					static::$capability, static::$menu_slug, get_called_class() . '::display_page', static::$icon,
					static::$position );
			} elseif ( static::$page_type == 'submenu' ) {
				$options_page = call_user_func( $function, static::$parent_slug, static::$page_title,
					static::$menu_title, static::$capability, static::$menu_slug,
					get_called_class() . '::display_page' );
			} else {
				$options_page = call_user_func( $function, static::$page_title, static::$menu_title,
					static::$capability, static::$menu_slug, get_called_class() . '::display_page' );
			}
		} else {
			wp_die( 'Invalid page page_type: ' . static::$page_type );
		}
	}

	static function add_pager_script() {
		global $current_screen;

		if ( ( strpos( $current_screen->id, static::$menu_slug ) !== false )
		     && ( count( static::tabs() ) > 1 ) ) {
			add_action( 'in_admin_footer', get_called_class() . '::pager_script' );
		}
	}

	static function pager_script() {
		ob_start();
		?>
        <script type="text/javascript">
          const handleTabs = function (hash) {
            const $sections = jQuery('.section');
            const $tabs = jQuery(".nav-tab-wrapper a");
            const $hash_input = jQuery('[name="<?php echo esc_attr(static::$option).'_hash' ?>"]');
            $sections.hide();
            if (hash.length) {
              hash.show();
              jQuery.each($tabs, function (key, value) {
                jQuery(value).removeClass("nav-tab-active");
              });
              $tabs.eq(hash.index()).addClass('nav-tab-active');
              $hash_input.attr('value',window.location.hash);
            } else {
              $sections.first().show();
            }
          };
          handleTabs(jQuery(window.location.hash));
          jQuery(window).bind('hashchange', function () {
            handleTabs(jQuery(window.location.hash));
            // jQuery("html, body").animate({scrollTop: 0}, "slow");
          });
        </script>
		<?php
		echo ob_get_clean();
	}

	static function redirect_to_tab(){
		if(isset($_REQUEST['option_page'])
		   &&($_REQUEST['option_page'] === 'Settings_'.static::$option)){
			if(isset($_POST[static::$option.'_hash'])){
				static::$current_tab = $_POST[static::$option.'_hash'];
				add_filter('wp_redirect',get_called_class().'::redirect_to_tab_filter');
				unset($_POST[static::$option.'_hash']);
				unset($_REQUEST[static::$option.'_hash']);
			}
		}
    }

    static function redirect_to_tab_filter($red){
	    return $red . static::$current_tab;
    }

	/**
	 * Render form sections
	 *
	 * @param string|bool $title
	 * @param string      $description
	 */
	static function field_group( $title = '', $description = '' ) {
		if ( $title ) :
			if ( is_string( $title ) ) {
				echo "<h3>$title</h3>";
			}
			if ( ! empty( $description ) ) {
				echo "<p>$description</p>";

			}
			?>
            <table class="form-table">
            <tbody>
		<?php
		else :
			?>
            </tbody>
            </table>
		<?php
		endif;
	}

	/**
	 * Render form fields
	 *
	 * @param string     $type        Type of input field
	 * @param string     $name        Input name
	 * @param string     $label       Input Label
	 * @param string     $description Field description
	 * @param array|null $atts        HTML attributes like example
	 *                                ['required','max'=>20,'onclick'=>'do_something()']
	 * @param array      $values      Option values for select and checklist fields
	 * @param string     $option      Option to store the values
	 */
	static function field( $type, $name = '', $label = '', $description = '', $atts = null, $values = [], $option = ''
	) {
		if ( empty( $option ) || $option == static::$option ) {
			$option  = static::$option;
			$options = apply_filters( $option . '_field_options', static::get_option() );
		} else {
			$options = (array) get_option( $option );
		}

		//TODO add aria-described-by on input that points to the id of description but first find out how to test them

		$help_text  = ! empty( $description ) ? "<p class=\"description\">$description</p>" : "";
		$attsString = '';
		ob_start();
		switch ( $type ) {
			case 'checklist':
				?>
                <tr>
                    <th><?php echo $label ?></th>
                    <td>
                        <fieldset>
							<?php
							foreach ( $values as $value => $title ) :
								$checked = ( in_array( $value, $options[ $name ] ) ) ? 'checked' : '';
								?>
                                <label for="<?php echo esc_attr("{$option}_$name") ?>">
                                    <input type="checkbox" id="<?php echo esc_attr("{$option}_$name") ?>"
                                           name="<?php echo $option . "[$name][]" ?>"
                                           value="<?php echo $value ?>" <?php echo $checked . ' ' . $attsString ?>/>
                                    <span><?php echo $title ?></span>
                                </label>
                                <br>
							<?php endforeach; ?>
                        </fieldset>
						<?php echo $help_text ?>
                    </td>
                </tr>
				<?php
				break;
			case 'checkbox':
				$checked = ( $options[ $name ] === static::$on ) ? 'checked' : '';
				//if(empty($description))
				//    wp_die('checkbox field needs description');
				?>
                <tr valign="middle">
                    <th scope="row">
						<?php echo $label ?>
                    </th>
                    <td>
                        <label for="<?php echo esc_attr("{$option}_$name") ?>">
                            <input type="checkbox" id="<?php echo esc_attr("{$option}_$name") ?>"
                                   name="<?php echo $option . "[$name]" ?>" <?php echo $checked ?>
                                   value="<?php echo static::$on ?>" <?php echo self::atts2string( $atts ) ?>/>
							<?php echo $description ?></label>
                    </td>
                </tr>
				<?php
				break;
			case 'select' :
				?>
                <tr valign="middle">
                    <th scope="row"><label
                                for="<?php echo esc_attr("{$option}_$name") ?>"><?php echo $label ?></label>
                    </th>
                    <td>
                        <select id="<?php echo esc_attr("{$option}_$name") ?>" name="<?php echo $option . "[$name]" ?>" <?php echo $attsString ?>>
							<?php
							foreach ( $values as $value => $option_label ) :
								if ( is_numeric( $value ) && strpos($option_label,' ') === false ) {
									$value = $option_label;
								}
								?>
                                <option value="<?php echo $value ?>" <?php echo $options[ $name ] == $value ? 'selected'
									: '' ?>><?php echo $option_label ?></option>
							<?php endforeach; ?>
                        </select>
						<?php echo $help_text ?>
                    </td>
                </tr>
				<?php
				break;
			case 'string' :
				?>
                <tr valign="middle">
                    <th><?php echo $label ?></th>
                    <td <?php echo $attsString ?>>
						<?php echo $name ?>
						<?php echo $help_text ?>
                    </td>
                </tr>
				<?php
				break;
			case 'hidden':
				$value_hidden = esc_attr(isset( $atts['value'] ) ? $atts['value'] : $options[ $name ]);
				?>
                <input id="<?php echo esc_attr("{$option}_$name") ?>"
                       type="hidden"
                       name="<?php echo $option . "[$name]" ?>"
                       value="<?php echo esc_attr($value_hidden) ?>" <?php echo self::atts2string( $atts ) ?>>
                <?php
				break;
			default:
				if ( ! isset( $atts['class'] ) ) {
					$atts['class'] = 'regular-text';
				}
				?>
                <tr>
                    <th scope="row"><label
                                for="<?php echo esc_attr("{$option}_$name") ?>"><?php echo $label ?></label>
                    </th>
                    <td>
                        <input id="<?php echo esc_attr("{$option}_$name") ?>"
                               type="<?php echo $type ?>"
                               name="<?php echo $option . "[$name]" ?>"
                               value="<?php echo esc_attr( $options[ $name ] ) ?>" <?php echo self::atts2string( $atts ) ?>>
						<?php echo $help_text ?>
                    </td>
                </tr>
				<?php
				break;
		}
		ob_end_flush();
	}

	/**
	 * @param array associative array of html attributes i.e.
	 *              ['class'=>'control error'] turns into <... class="control error" >
	 *              ['required'=>true] turns into <... required >
	 *
	 * @return string attributes on html format
	 */
	static function atts2string( $array ) {

		if ( ! is_array( $array ) || empty( $array ) ) {
			return '';
		}

		$return = '';
		foreach ( $array as $att => $val ) {
			if ( is_numeric( $att ) ) {//this is not ok but there for some reason...
				$return .= " " . esc_attr( $val ) . " ";
			} elseif ( $val === true ) {
				$return .= " " . esc_attr( $att ) . " ";
			} else {
				$return .= $att . '="' . esc_attr( $val ) . '" ';
			}
		}

		return $return;
	}

	static function parse_tabs( $tabs ) {
		foreach ( $tabs as $tab ) {
			$data = wp_parse_args( $tab, [
				'label'       => '',
				'id'          => '',
				'sections'    => [],
				'attributes'  => [],
				'description' => '',
			] );
			if ( ! isset( $data['attributes']['class'] ) ) {
				$data['attributes']['class'] = 'section';
			}
			if ( isset( $data['attributes']['id'] ) ) {
				$data['id'] = $data['attributes']['id'];
			}
			if ( empty( $data['id'] ) || empty( $data['label'] ) ) {
				wp_die( 'You must set id and label at least. ' . print_r( $tabs, true ) );
			}
			$attributes = static::atts2string( $data['attributes'] );
			echo "<section id='{$data['id']}' $attributes>";
			echo $data['description'];
			foreach ( $data['sections'] as $section ) {
				$data = wp_parse_args( $section, [
					'title'       => '',
					'fields'      => [],
					'description' => '',
				] );
				static::field_group( $data['title'], $data['description'] );
				foreach ( $data['fields'] as $field ) {
					$field_data = wp_parse_args( $field, [
						'type'        => '',//text, checkbox, select, checklist, string or number
						'name'        => '',//option name must match plugin defaults
						'label'       => '',
						'description' => '',//help text
						'values'      => [],//values for checklist
						'attributes'  => [],//html attributes
						'option'      => static::$option,
					] );
					static::field( $field_data['type'], $field_data['name'], $field_data['label'],
						$field_data['description'], $field_data['attributes'], $field_data['values'],
						$field_data['option'] );
				}
				static::field_group();
			}
			echo "</section>";
		}
	}

	/**
	 * Renders the wp-admin settings page with tabs rendered by js or no tab for single static:tabs
	 */
	static function display_page() {
		?>
        <div class="wrap">
            <h2><?php echo static::$page_title ?></h2>
			<?php static::before_form() ?>
            <form action="options.php" method="post">
				<?php
				settings_fields( "Settings_" . static::$option );
				do_action( static::$option . '_inside_form' );
				?>
				<?php if ( count( static::tabs() ) > 1 ) : ?>
                    <h2 class="nav-tab-wrapper">
						<?php
						foreach ( static::tabs() as $key => $tab ) {
							$class = 0 == $key ? "nav-tab-active" : "";
							echo "<a class='nav-tab $class' href='#{$tab['id']}'>{$tab['label']}</a>";
						}
						?>
                    </h2><br>
				<?php endif; ?>
                <input type="hidden" name="<?php echo esc_attr(static::$option).'_hash' ?>" value="">
                <div class="sections">
					<?php static::parse_tabs( static::tabs() ) ?>
					<?php submit_button(); ?>
                </div>
            </form>
			<?php if ( static::$reset_button ): ?>
                <br>
                <form action="options.php" method="post"
                      onsubmit="return confirm('<?php echo static::$confirmation ?>');">
					<?php settings_fields( "Settings_" . static::$option ); ?>
                    <input type="hidden" name="<?php echo static::$option ?>"
                           value="reset_defaults"/>
                    <input type="submit" name="restore" class="button"
                           value="<?php echo static::$reset_string ?>"/>
                </form>
			<?php endif ?>
			<?php static::after_form() ?>
        </div>
		<?php
	}

	##############################################
	# Replace this functions on the Plugin class #
	##############################################

	/**
	 * Set the correct text domain
	 */
	static function load_translation() {
		load_plugin_textdomain( 'text_domain', false, basename( dirname( static::$FILE ) ) . '/languages' );
	}

	/**
	 * This function is an example of how to set default values of the option
	 *
	 * @return array Default variables used on this plugin
	 */
	static function defaults() {
		if ( self::$defaults === null ) {
			self::$defaults = [
				'off_option'       => 'false',
				'on_option'        => 'true',
				'text_option'      => 'Something crazy',
				'checklist_option' => [ 'one' ],//only checkbox one is selected two is not
				'number_option'    => 34,
			];
		}

		return self::$defaults;
	}

	static function before_form() {
		echo '';
	}

	static function after_form() {
		echo '';
	}

	/**
	 * This function is an example of how to produce the form to hold all settings.
	 *
	 * @return array options page sections
	 *
	 */
	static function tabs() {
		$sections = [
			[
				'label'    => __( 'General', 'text_domain' ),
				'id'       => 'general',
				'sections' => [
					[
						//Set title to true for no title
						'title'       => __( 'Some Stuff', 'text_domain' ),
						'description' => __( 'This stuff is interesting', 'text_domain' ),
						'fields'      => [
							[
								'type'        => 'text',
								'name'        => 'text_option',
								'label'       => __( 'Text Option', 'text_domain' ),
								'description' => 'Help text'
							],
							[
								'type'        => 'checkbox',
								'name'        => 'off_option',
								'label'       => __( 'Off Option', 'text_domain' ),
								'description' => 'Help text'
							],
							[
								'type'  => 'checkbox',
								'name'  => 'on_option',
								'label' => __( 'On Option', 'text_domain' ),
							],
						]
					]
				]

			],
			[
				'label'    => __( 'Advanced', 'text_domain' ),
				'id'       => 'advanced',
				'sections' => [
					[
						'title'  => __( 'More Important Stuff', 'text_domain' ),
						'fields' => [
							[
								'type'        => 'number',
								'name'        => 'number_option',
								'label'       => __( 'Number Option', 'text_domain' ),
								'description' => 'Help text',
								//'attributes'  => [ 'max' => '30', 'required' => true ]
							],
							[
								'type'        => 'checklist',
								'name'        => 'checklist_option',
								'label'       => __( 'Checklist Option', 'text_domain' ),
								'description' => 'more info',
								'values'      => [
									'one' => __( 'One title', 'text_domain' ),
									'two' => __( 'Two title', 'text_domain' )
								]
							],
						]
					]
				]

			],
		];

		/**
		 * Allow 3rd party add-on's or extensions to add other sections
		 */
		return apply_filters( self::$option . '_admin_sections', $sections );
	}
}