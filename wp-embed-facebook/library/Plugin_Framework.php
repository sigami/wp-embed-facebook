<?php
/**
 * Created for: wp-embed-facebook
 * By: Miguel Sirvent
 * Date: 23/05/18
 * Time: 10:41 PM
 */

namespace SIGAMI\WP_Embed_FB;

abstract class Plugin_Framework {

	const VER = '1.1.2';

	const FILE = null;

	const OPTION = null;

	# Admin page options
	/**
	 * @var string menu|submenu|management|options|theme|plugins|users|dashboard|posts|media|links|pages|comments
	 */
	protected static $type       = 'options';
	protected static $page_title = 'New Page';
	protected static $menu_title = 'New Page';
	protected static $capability = 'manage_options';
	protected static $menu_slug  = 'new_page';
	# Only for menu pages (Top Level)
	protected static $icon     = '';
	protected static $position = null;
	# Only for submenu pages
	protected static $parent_slug = 'options-general.php';
	# Sections of the admin page
	protected static $tabs = array( 'General', 'Advanced' );

	# Cache of commonly used vars.
	protected static $defaults = null;
	private static   $options  = null;
	private static   $path     = null;
	private static   $url      = null;
	private static   $instance = null;

	static function instance() {
		if ( self::$instance === null ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	protected function __construct() {
		# plugin translation
		add_action( 'plugins_loaded', get_called_class() . '::load_translation' );

		if ( ! empty( static::OPTION ) ) {

			register_setting( "Settings_" . static::OPTION, static::OPTION, [
				'sanitize_callback' => get_called_class() . '::sanitize_option',
				'default'           => static::defaults(),
			] );

			register_activation_hook( static::FILE, get_called_class() . '::activation' );
			register_uninstall_hook( static::FILE, get_called_class() . '::uninstall' );
			register_deactivation_hook( static::FILE, get_called_class() . '::deactivation' );

			if ( is_admin() ) {
				/** @see Plugin_Framework::add_page() */
				add_action( 'admin_menu', get_called_class() . '::add_page' );
				/** @see Plugin_Framework::add_pager_script() */
				add_action( 'current_screen', get_called_class() . '::add_pager_script' );
			}

		}
	}

	static function activation() {
		static::get_option();
		do_action( static::OPTION . '_' . __FUNCTION__ );
	}

	static function uninstall() {
		do_action( static::OPTION . '_' . __FUNCTION__ );
		delete_option( static::OPTION );
	}

	static function deactivation() {
		do_action( static::OPTION . '_' . __FUNCTION__ );
	}

	static function path() {
		if ( null === self::$path ) {
			self::$path = plugin_dir_path( static::FILE );
		}

		return self::$path;
	}

	static function url() {
		if ( null === self::$url ) {
			self::$url = plugin_dir_url( static::FILE );
		}

		return self::$url;
	}

	static function load_translation() {
		load_plugin_textdomain( 'text_domain', false, 'lang/' );
	}

	/**
	 * @return array Default variables used on this plugin
	 */
	static function defaults() {
		if ( self::$defaults === null ) {
			self::$defaults = array(
				'off_option'       => 0,
				'on_option'        => 'on',
				'text_option'      => 'Somethin crazy',
				'checklist_option' => array( 'one' => 'One name', 'two' => 'Two title' ),
				'number_option'    => 34,
			);
		}

		return self::$defaults;
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
	 * # reset option to default
	 * Plugin::set_option(array_merge(Plugin::defaults(),['other_option'=>Plugin::defaults()['other_option']]));
	 *
	 * # Reset all options to defaults
	 * Plugin::set_options(Plugin::defaults());
	 *
	 * </code>
	 * @param array $options
	 */
	static function set_options( $options ) {
		update_option( static::OPTION, $options, true );
		self::$options = get_option( static::OPTION );
	}

	/**
	 * Get a single option or all of them.
	 *
	 * @param null|string $option
	 *
	 * @return array|mixed The queried option. False if the option does not exists. Array with all options if $option is null;
	 */
	static function get_option( $option = null ) {
		if ( ! is_array( self::$options ) ) {
			$options = get_option( static::OPTION );
			if ( is_array( $options ) ) {
				if ( $options === static::defaults() ) {
					self::$options = $options;
				} else {
					$compare = array();
					foreach ( static::defaults() as $default_key => $default_value ) {
						$compare[ $default_key ] = isset( $options[ $default_key ] )
							? $options[ $default_key ] : $default_value;
					}
					if ( $compare === $options ) {
						self::$options = $options;
					} else {
						static::set_options( $compare );
					}
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

		if ( is_string( $options ) && $options == 'restore' ) {
			return $defaults;
		}

		if ( $options === $defaults ) {
			return $options;
		}

		$clean = array();
		foreach ( $defaults as $name => $default_value ) {
			$clean[ $name ] = $default_value;
			if ( ( ( $default_value === 'on' ) || ( $default_value === 0 ) ) ) {
				if ( isset( $options[ $name ] ) && ( $options[ $name ] !== 0 ) ) {
					$clean[ $name ] = 'on';
				} else {
					$clean[ $name ] = 0;
				}
			} elseif ( isset( $options[ $name ] ) ) {
				if ( ( is_int( $default_value ) && is_int( $options[ $name ] ) )
				     || ( is_string( $default_value ) && is_string( $options[ $name ] ) )
				     || ( is_object( $default_value ) && is_object( $options[ $name ] ) )
				     || ( is_array( $default_value ) && is_array( $options[ $name ] ) ) ) {
					$clean[ $name ] = $options[ $name ];
				}
			}
		}

		return $clean;
	}

	/**
	 * Add page to Settings
	 */
	static function add_page() {
		$function = 'add_' . static::$type . '_page';
		if ( function_exists( $function ) ) {

			if ( static::$type == 'menu' ) {
				$options_page = call_user_func( $function, static::$page_title, static::$menu_title,
					static::$capability, static::$menu_slug, get_called_class() . '::display_page',
					static::$icon, static::$position );
			} elseif ( static::$type == 'submenu' ) {
				$options_page = call_user_func( $function, static::$parent_slug,
					static::$page_title, static::$menu_title, static::$capability,
					static::$menu_slug, get_called_class() . '::display_page' );
			} else {
				$options_page = call_user_func( $function, static::$page_title, static::$menu_title,
					static::$capability, static::$menu_slug,
					get_called_class() . '::display_page' );
			}

			if ( false === $options_page ) {
				wp_die( 'Invalid page: ' . static::$type . ' ' . $function );
			}

		} else {
			wp_die( 'Invalid page type: ' . static::$type );
		}
	}

	static function add_pager_script() {
		global $current_screen;

		if ( strpos( $current_screen->id, static::$menu_slug ) !== false ) {

			if ( count( static::$tabs ) > 1 ) {
				add_action( 'in_admin_footer', get_called_class() . '::pager_script' );
			}

		}
	}

	static function pager_script() {
		ob_start();
		?>
        <script type="text/javascript">
            jQuery(document).ready(function () {
                var sections = jQuery('section');
                var tabs = jQuery(".nav-tab-wrapper a");
                var hash = jQuery(window.location.hash);
                sections.hide();
                if (hash.length) {
                    var index = hash.index() - 6; //why 6 ? dunno
                    //console.log(index);
                    sections.eq(index).show();
                    jQuery.each(tabs, function (key, value) {
                        jQuery(value).removeClass("nav-tab-active");
                    });
                    tabs.eq(index).addClass('nav-tab-active');
                } else {
                    sections.first().show();
                }
                tabs.on('click', function (event) {
                    var index = jQuery(this).index();
                    var url = window.location.pathname + window.location.search + '#' + sections.eq(index)[0].id;
                    event.preventDefault();
                    sections.hide();
                    jQuery.each(tabs, function (key, value) {
                        jQuery(value).removeClass("nav-tab-active");
                    });
                    sections.eq(index).show();
                    jQuery(this).addClass('nav-tab-active');
                    window.history.pushState(sections.eq(index)[0].id, tabs.eq(index)[0].innerText, url);
                });
            });
        </script>
		<?php
		echo ob_get_clean();
	}

	/**
	 * Render form sections
	 *
	 * @param string|bool $title
	 */
	static function section( $title = '' ) {
		if ( $title ) :
			if ( is_string( $title ) )
				echo "<h3>$title</h3>"
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
	 * @param array|null $atts        HTML attributes like example ['required','max'=>20,'onclick'=>'do_something()']
	 * @param array      $values      Option values for select and checklist fields
	 */
	static function field(
		$type, $name = '', $label = '', $description = '', $atts = null, $values = array()
	) {
		//TODO add aria-describedby on input that points to the id of description
		$options    = apply_filters( static::OPTION . '_field_options', static::get_option() );
		$attsString = '';
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $att => $val ) {
				if ( is_numeric( $att ) ) {
					$attsString .= " $val ";
				} else {
					$attsString .= $att . '="' . $val . '" ';
				}

			}
		}
		switch ( $type ) {
			case 'checklist':

				ob_start();
				?>
                <tr>
                    <th><?php echo $label ?></th>
                    <td>
                        <fieldset>
							<?php
							foreach ( $values as $value => $title ) :
								$checked = ( in_array( $value, $options[ $name ] ) ) ? 'checked'
									: '';
								?>
                                <label for="<?php echo "{$name}_$value" ?>">
                                    <input type="checkbox" id="<?php echo "{$name}_$value" ?>"
                                           name="<?php echo static::OPTION . "[$name][]" ?>"
                                           value="<?php echo $value ?>" <?php echo $checked ?> <?php echo $attsString ?>/>
                                    <span><?php echo $title ?></span>
                                </label>
                                <br>

							<?php endforeach; ?>
                        </fieldset>
						<?php if ( ! empty( $description ) ) : ?>
                            <p class="description"><?php echo $description ?></p>
						<?php endif; ?>
                    </td>
                </tr>
				<?php
				ob_end_flush();
				break;
			case 'checkbox':
				$checked = ( $options[ $name ] === 'on' ) ? 'checked' : '';
				ob_start();
				?>
                <tr valign="middle">
                    <th scope="row"><label
                                for="<?php echo $name ?>"><?php echo $label ?></label></th>
                    <td>
                        <input type="checkbox" id="<?php echo $name ?>"
                               name="<?php echo static::OPTION
						                        . "[$name]" ?>" <?php echo $checked ?> <?php echo $attsString ?>/>
						<?php if ( ! empty( $description ) ) : ?>
                            <span><?php echo $description ?></span>
						<?php endif; ?>
                    </td>
                </tr>
				<?php
				ob_end_flush();
				break;
			case 'select' :
				$option = $options[ $name ];
				ob_start();
				?>
                <tr valign="middle">
                    <th scope="row"><label for="<?php echo static::OPTION
					                                       . "[$name]" ?>"><?php echo $label ?></label>
                    </th>
                    <td>
                        <select name="<?php echo static::OPTION
						                         . "[$name]" ?>" <?php echo $attsString ?>>
							<?php
							foreach ( $values as $value => $name ) :
								if ( is_numeric( $value ) ) {
									$value = $name;
								}
								?>
                                <option value="<?php echo $name ?>" <?php echo $option == $value
									? 'selected' : '' ?>><?php echo $name ?></option>
							<?php endforeach; ?>
                        </select>
						<?php if ( ! empty( $description ) ) : ?>
                            <p class="description"><?php echo $description ?></p>
						<?php endif; ?>
                    </td>
                </tr>
				<?php
				ob_end_flush();
				break;

			case 'string' :
				ob_start();
				?>
                <tr valign="middle">
                    <th><?php echo $label ?></th>
                    <td>
						<?php echo $name ?>
                    </td>
                </tr>
				<?php
				ob_end_flush();
				break;
			default:
				ob_start();
				?>
                <tr>
                    <th scope="row"><label for="<?php echo static::OPTION
					                                       . "[$name]" ?>"><?php echo $label ?></label>
                    </th>
                    <td>
                        <input id="<?php echo $name ?>"
                               type="<?php echo $type ?>"
                               name="<?php echo static::OPTION . "[$name]" ?>"
                               value="<?php echo esc_attr( $options[ $name ] ) ?>" <?php echo $attsString ?>
							<?php echo ! isset( $atts['class'] ) ? ' class="regular-text" '
								: ''; ?>/>
						<?php if ( ! empty( $description ) ) : ?>
                            <p class="description"><?php echo $description ?></p>
						<?php endif; ?>
                    </td>
                </tr>
				<?php
				ob_end_flush();
				break;
		}
	}

	/**
	 * Renders the wp-admin settings page
	 */
	static function display_page() {
		if ( isset( $_POST['restore-data'] )
		     && wp_verify_nonce( $_POST['restore-data'], 'W7ziLKoLojka' ) ) {
			update_option( static::OPTION, static::defaults(), true );
		}
		?>
        <div class="wrap">
            <h2><?php echo static::$page_title ?></h2>
			<?php //settings_errors(); ?>
            <!--suppress HtmlUnknownTarget -->
            <form action="options.php" method="post">
				<?php settings_fields( "Settings_" . static::OPTION ); ?>
				<?php
				$tabs = apply_filters( static::OPTION . '_tabs', static::$tabs );
				?>
				<?php if ( count( static::$tabs ) > 1 ) : ?>
                    <h2 class="nav-tab-wrapper">
						<?php
						foreach ( $tabs as $tab ) {
							$class = $tabs[0] == $tab ? "nav-tab-active" : "";
							echo "<a class='nav-tab $class' href='#'>$tab</a>";
						}
						?>
                    </h2><br>
				<?php endif; ?>
				<?php echo static::form_content() ?>
				<?php submit_button(); ?>
            </form>
            <br>
            <!--suppress HtmlUnknownTarget -->
            <form action="options.php" method="post"
                  onsubmit="return confirm('<?php _e( 'Restore default values?',
				      'text_domain' ) ?>');">
				<?php settings_fields( "Settings_" . static::OPTION ); ?>
                <input type="hidden" name="<?php echo static::OPTION ?>" value="restore"/>
                <input type="submit" name="restore" class="button"
                       value="<?php _e( 'Restore defaults', 'text_domain' ) ?>"/>
            </form>
        </div>
		<?php
	}

	static function form_content() {
		ob_start();
		?>
        <section id="general" class="section">
			<?php
			self::section( __( 'General options', 'text_domain' ) );
			self::field( 'text', 'text_option', __( 'Text Option', 'text_domain' ), 'Help text' );
			self::field( 'checkbox', 'off_option', __( 'Off Option', 'text_domain' ) );
			self::field( 'checkbox', 'on_option', __( 'On Option', 'text_domain' ), 'extra info' );
			self::section();
			?>
        </section>
        <section id="advanced" class="section">
			<?php
			self::section( __( 'Advanced options', 'text_domain' ) );
			self::field( 'number', 'number_option', __( 'Number Option', 'text_domain' ),
				'Help text', array( 'max' => '30' ) );
			#using the default values on checklist is optional they can also be created by other means
			self::field( 'checklist', 'checklist_option', __( 'Checklist Option', 'text_domain' ),
				'more info', '', self::get_option( 'checklist_option' ) );
			self::section();
			?>
        </section>
		<?php
		return ob_get_clean();
	}

}