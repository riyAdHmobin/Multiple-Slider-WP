<?php
/**
 * Class for admin methods.
 *
 * @package Blog_Reader
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'Blog_Reader_Settings' ) ) {
	/**
	 * Setting Class.
	 */
	class Blog_Reader_Settings {
		/**
		 * Settings Option.
		 *
		 * @var array
		 */
		private $options;

		/**
		 * Input Fields.
		 *
		 * @var array
		 */
		private $field_definitions;

		/**
		 * Constructor for the DynamicSettingsFields class.
		 */
		public function __construct() {
			$this->options = get_option( 'blp_blog_options' );

			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'page_init' ) );
		}

		/**
		 * Add the plugin settings page to the WordPress admin menu.
		 */
		public function add_plugin_page() {
			add_menu_page(
				esc_html__( 'Blog Reader', 'blp-blog-reader' ),
				esc_html__( 'Blog Reader', 'blp-blog-reader' ),
				'manage_options',
				'blp-blog-reader',
				array( $this, 'create_admin_page' ),
				'dashicons-megaphone'
			);
		}

		/**
		 * Create the plugin settings admin page.
		 */
		public function create_admin_page() {
			?>
			<div class="wrap">
				<h2><?php esc_html_e( 'Settings', 'blp-blog-reader' ); ?></h2>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'blp-blog-reader' );
					do_settings_sections( 'blp-blog-reader' );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Initialize the settings and register sections and fields.
		 */
		public function page_init() {

			// Get post types.
			$post_types = get_post_types(
				array(
					'public' => true,
				),
				'objects'
			);

			/**
			 * Exclude some default post types if needed.
			 *
			 * @var array
			 */
			$exclude_post_types = apply_filters(
				'blp_br_excluded_post_types',
				array( 'attachment', 'nav_menu_item' )
			);

			$blp_br_post_types = array();

			if ( ! empty( $post_types ) && is_array( $post_types ) ) {
				$selected_post_types = get_option( 'blp_cp_allowed_post_types', array() );

				if ( empty( $selected_post_types ) || ! is_array( $selected_post_types ) ) {
					$selected_post_types = array();
				}

				foreach ( $post_types as $post_type_obj ) {
					$post_name = ! empty( $post_type_obj->name ) ? $post_type_obj->name : '';

					if ( is_array( $exclude_post_types ) && in_array( $post_name, $exclude_post_types, true ) ) {
						continue;
					}

					$post_label = ! empty( $post_type_obj->label ) ? $post_type_obj->label : '';

					$blp_br_post_types[ $post_name ] = $post_label;
				}
			}

			// Define Settings fields.
			$this->field_definitions = apply_filters(
				'blp_br_setting_fields',
				array(
					array(
						'id'    => 'blp_blog_reader_enabled',
						'label' => esc_html__( 'Enable Blog Reader', 'blp-blog-reader' ),
						'type'  => 'checkbox',
					),
					array(
						'id'          => 'blp_blog_reader_post_types',
						'label'       => esc_html__( 'Enable for Post types', 'blp-blog-reader' ),
						'description' => blog_reader_fs()->can_use_premium_code() ? esc_html__( 'Enable blog reader for selected post types.', 'blp-blog-reader' ) : sprintf( '<span style="color:#b32d2e">%s</span>', esc_html__( 'This feature is only available in Pro plugin.', 'blp-blog-reader' ) ),
						'type'        => 'checkbox_group',
						'options'     => $blp_br_post_types,
						'disabled'    => blog_reader_fs()->can_use_premium_code() ? false : true,
					),
					array(
						'id'          => 'blp_blog_reader_is_floating',
						'label'       => esc_html__( 'Enable floating controls', 'blp-blog-reader' ),
						'description' => blog_reader_fs()->can_use_premium_code() ? '' : sprintf( '<span style="color:#b32d2e">%s</span>', esc_html__( 'This feature is only available in Pro plugin.', 'blp-blog-reader' ) ),
						'type'        => 'checkbox',
						'disabled'    => blog_reader_fs()->can_use_premium_code() ? false : true,
					),
					array(
						'id'          => 'blp_blog_reader_float_location',
						'label'       => esc_html__( 'Float location', 'blp-blog-reader' ),
						'description' => blog_reader_fs()->can_use_premium_code() ? '' : sprintf( '<span style="color:#b32d2e">%s</span>', esc_html__( 'This feature is only available in Pro plugin.', 'blp-blog-reader' ) ),
						'type'        => 'select',
						'options'     => array(
							'left'   => esc_html__( 'Left', 'blp-blog-reader' ),
							'right'  => esc_html__( 'Right', 'blp-blog-reader' ),
							'bottom' => esc_html__( 'Bottom', 'blp-blog-reader' ),
						),
						'disabled'    => blog_reader_fs()->can_use_premium_code() ? false : true,
					),
				)
			);

			if ( is_plugin_active( 'memberpress/memberpress.php' ) ) {
				$this->field_definitions[] = array(
					'id'          => 'blp_br_allowed_memberships',
					'label'       => esc_html__( 'Allowed Memberships', 'blp-blog-reader' ),
					'description' => blog_reader_fs()->can_use_premium_code() ? '' : sprintf( '<span style="color:#b32d2e">%s</span>', esc_html__( 'This feature is only available in Pro plugin.', 'blp-blog-reader' ) ),
					'type'        => 'select2-pages-dropdown-multiple',
					'section'     => 'blp-br-membership-section',
					'disabled'    => blog_reader_fs()->can_use_premium_code() ? false : true,
				);
			}

			register_setting(
				'blp-blog-reader',
				'blp_blog_options',
				array( $this, 'sanitize' )
			);

			add_settings_section(
				'blp-blog-reader-section',
				'',
				array( $this, 'print_section_info' ),
				'blp-blog-reader'
			);

			// Register MemberPress settings section.
			if ( is_plugin_active( 'memberpress/memberpress.php' ) ) {
				add_settings_section(
					'blp-br-membership-section',
					esc_html__( 'MemberPress', 'blp-blog-reader' ),
					array(),
					'blp-blog-reader'
				);
			}

			foreach ( $this->field_definitions as $field ) {

				add_settings_field(
					$field['id'],
					$field['label'],
					array( $this, 'field_callback' ),
					'blp-blog-reader',
					! empty( $field['section'] ) ? esc_attr( $field['section'] ) : 'blp-blog-reader-section',
					array(
						'id'          => $field['id'],
						'label_for'   => $field['id'],
						'type'        => $field['type'],
						'options'     => isset( $field['options'] ) ? $field['options'] : array(),
						'description' => isset( $field['description'] ) ? $field['description'] : '',
						'disabled'    => isset( $field['disabled'] ) ? $field['disabled'] : false,
					)
				);
			}
		}

		/**
		 * Sanitize and validate user input before saving.
		 *
		 * @param array $input User input data.
		 * @return array Sanitized input data.
		 */
		public function sanitize( $input ) {
			$sanitized_input = array();

			foreach ( $this->field_definitions as $field ) {
				$field_id = $field['id'];

				if ( isset( $input[ $field_id ] ) ) {
					switch ( $field['type'] ) {
						case 'number':
							$sanitized_input[ $field_id ] = intval( $input[ $field_id ] );
							break;
						case 'textarea':
							$sanitized_input[ $field_id ] = wp_kses_post( $input[ $field_id ] );
							break;
						case 'select':
							$sanitized_input[ $field_id ] = sanitize_text_field( $input[ $field_id ] );
							break;
						case 'select_multiple':
						case 'checkbox_group':
						case 'select2-pages-dropdown-multiple':
							if ( ! empty( $input[ $field_id ] ) && is_array( $input[ $field_id ] ) ) {
								$sanitized_input[ $field_id ] = array_map( 'sanitize_text_field', $input[ $field_id ] );
							}
							break;
						case 'checkbox':
							$sanitized_input[ $field_id ] = empty( $input[ $field_id ] ) ? '0' : '1';
							break;
						case 'text':
							$sanitized_input[ $field_id ] = sanitize_text_field( $input[ $field_id ] );
					}
				}
			}

			return $sanitized_input;
		}

		/**
		 * Display section information.
		 *
		 * @param array $args Section arguments.
		 */
		public function print_section_info( $args ) {
			?>
			<p id="<?php echo esc_attr( $args['id'] ); ?>"></p>
			<?php
		}

		/**
		 * Callback function to render individual fields.
		 *
		 * @param array $args Field arguments.
		 */
		public function field_callback( $args ) {
			$field_id          = $args['id'];
			$field_type        = $args['type'];
			$field_description = isset( $args['description'] ) ? $args['description'] : '';
			$field_options     = isset( $args['options'] ) ? $args['options'] : array();
			$field_disabled    = isset( $args['disabled'] ) ? $args['disabled'] : false;
			$values            = isset( $this->options[ $field_id ] ) ? $this->options[ $field_id ] : '';

			switch ( $field_type ) {
				case 'number':
					printf(
						'<input type="number" name="blp_blog_options[%1$s]" id="%2$s" value="%3$s" %4$s>',
						esc_attr( $field_id ),
						esc_attr( $field_id ),
						esc_textarea( $values ),
						disabled( $field_disabled, true, false )
					);
					break;
				case 'textarea':
					printf(
						'<textarea name="blp_blog_options[%1$s]" id="%2$s" cols="30" rows="10" %3$s>%4$s</textarea>',
						esc_attr( $field_id ),
						esc_attr( $field_id ),
						disabled( $field_disabled, true, false ),
						esc_textarea( $values )
					);
					break;
				case 'checkbox':
					printf(
						'<input type="checkbox" name="blp_blog_options[%1$s]" id="%2$s" value="1" %3$s %4$s>',
						esc_attr( $field_id ),
						esc_attr( $field_id ),
						checked( '1', $values, false ),
						disabled( $field_disabled, true, false )
					);
					break;
				case 'checkbox_group':
					foreach ( $field_options as $option_value => $option_label ) {
						printf(
							'<label><input type="checkbox" name="blp_blog_options[%1$s][]" id="%2$s" value="%3$s" %4$s %5$s>%6$s</label></br>',
							esc_attr( $field_id ),
							esc_attr( $field_id ),
							esc_attr( $option_value ),
							checked( ( is_array( $values ) && in_array( $option_value, $values, true ) ), 1, false ),
							disabled( $field_disabled, true, false ),
							esc_html( $option_label )
						);
					}
					break;
				case 'text':
					printf(
						'<input type="text" name="blp_blog_options[%1$s]" id="%2$s" value="%3$s" %4$s>',
						esc_attr( $field_id ),
						esc_attr( $field_id ),
						esc_textarea( $values ),
						disabled( $field_disabled, true, false )
					);
					break;
				case 'select':
					echo "<select id='" . esc_attr( $field_id ) . "' name='blp_blog_options[" . esc_attr( $field_id ) . "]' " . disabled( $field_disabled, true, false ) . '>';
					foreach ( $field_options as $option_value => $option_label ) {
						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							esc_attr( $option_value ),
							selected( $values, $option_value, false ),
							esc_html( $option_label )
						);
					}
					echo '</select>';
					break;
				case 'select2-pages-dropdown-multiple':
					$selected_pages = ! empty( $values ) ? $values : array();
					?>
					<select
						id="<?php echo esc_attr( $field_id ); ?>"
						name="blp_blog_options[<?php echo esc_attr( $field_id ); ?>][]"
						class="regular-text"
						multiple="true"
						<?php disabled( $field_disabled, true ); ?>
					>
					<?php
					if ( is_array( $selected_pages ) && ! empty( $selected_pages ) ) {
						foreach ( $selected_pages as $page_id ) {
							if ( ! empty( $page_id ) ) {

								// Page Title.
								$page_title = ! empty( get_the_title( $page_id ) ) ? esc_html( get_the_title( $page_id ) ) : esc_html__( 'Page Not Found', 'blp-blog-reader' );

								printf(
									'<option value="%1$s" selected>%2$s</option>',
									esc_attr( $page_id ),
									esc_html( $page_title )
								);
							}
						}
					}
					?>
					</select>
					<?php
					break;
			}

			if ( ! empty( $field_description ) ) {
				printf( '<p class="description">%s</p>', wp_kses_post( $field_description ) );
			}
		}
	}

	if ( is_admin() ) {
		new Blog_Reader_Settings();
	}
}
