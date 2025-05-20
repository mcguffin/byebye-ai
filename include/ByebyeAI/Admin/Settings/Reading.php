<?php
/**
 *	@package ByebyeAI\Admin\Settings
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\Admin\Settings;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use ByebyeAI\Asset;
use ByebyeAI\Core;

class Reading extends Settings {

	private $optionset = 'reading';


	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		$this->core = Core\Core::instance();
		add_action( "load-options-{$this->optionset}.php" , [ $this, 'enqueue_assets' ] );



		parent::__construct();

	}



	/**
	 * Enqueue settings Assets
	 *
	 *	@action load-options-{$this->optionset}.php
	 */
	public function enqueue_assets() {

	}


	/**
	 *	Setup options.
	 *
	 *	@action admin_init
	 */
	public function register_settings() {

		$settings_section	= 'byebye_ai_reading_settings';

		add_settings_section( $settings_section, __( 'Section #1',  'byebye-ai' ), [ $this, 'section_1_description' ], $this->optionset );



		// TODO: Implement Reading settings
		$option_name		= 'byebye_ai_reading_setting_1';
		register_setting( $this->optionset , $option_name, [ $this , 'sanitize_setting_1' ] );
		add_settings_field(
			$option_name,
			__( 'Setting #1',  'byebye-ai' ),
			[ $this, 'setting_1_ui' ],
			$this->optionset,
			$settings_section,
			[
				'option_name'			=> $option_name,
				'option_label'			=> __( 'Setting #1',  'byebye-ai' ),
				'option_description'	=> __( 'Setting #1 description',  'byebye-ai' ),
			]
		);
	}

	/**
	 * Print some documentation for the optionset
	 */
	public function section_1_description( $args ) {
		// TODO: Reading settings section description

		?>
		<div class="inside">
			<p><?php _e( 'Section 1 Description.' , 'byebye-ai' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Output Theme selectbox
	 */
	public function setting_1_ui( $args ) {

		@list( $option_name, $label, $description ) = array_values( $args );

		$option_value = get_option( $option_name );

		?>
			<label for="<?php echo $option_name ?>">
				<input type="text" id="<?php echo $option_name ?>" name="<?php echo $option_name ?>" value="<?php esc_attr_e( $option_value ) ?>" />
				<?php echo $label ?>
			</label>
			<?php
			if ( ! empty( $description ) ) {
				printf( '<p class="description">%s</p>', $description );
			}
			?>
		<?php
	}

	/**
	 * Sanitize value of setting_1
	 *
	 * @return string sanitized value
	 */
	public function sanitize_setting_1( $value ) {
		// do sanitation here!
		return $value;
	}

	/**
	 *	@inheritdoc
	 */
	public function activate() {
		// TODO: Reading settings activation
		add_option( 'byebye_ai_reading_setting_1', 'Default Value', '', false );
	}

	/**
	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {

	}

	/**
	 *	@inheritdoc
	 */
	public function deactivate() {

	}

	/**
	 *	@inheritdoc
	 */
	public static function uninstall() {
		// TODO: Reading settings uninstall
		delete_option( 'byebye_ai_reading_setting_1' );
	}

}
