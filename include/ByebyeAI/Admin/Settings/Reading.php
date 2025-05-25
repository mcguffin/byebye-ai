<?php
/**
 *	@package ByebyeAI\Admin\Settings
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\Admin\Settings;

use ByebyeAI\Asset;
use ByebyeAI\Core;

class Reading extends Core\Singleton {

	private $core;
	private $optionset = 'reading';

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		$this->core = Core\Core::instance();

		add_action( 'admin_init', [ $this, 'register_settings' ] );

		add_action( 'add_option_byebye_ai_enbled', [ $this->core, 'sync' ] );
		add_action( 'update_option_byebye_ai_enbled', [ $this->core, 'sync' ] );

		add_filter( 'allowed_options', function( $allowed ) {
			$allowed['reading'][] = 'byebye_ai_enbled';
			return $allowed;
		});

		parent::__construct();
	}

	/**
	 *	Setup options.
	 *
	 *	@action admin_init
	 */
	public function register_settings() {
		if ( ! $this->core->can_write_htaccess() && ! $this->core->can_serve_robotstxt() ) {
			add_action('admin_notices', function() {
				$plugin_file = $this->core->get_wp_plugin();
				$deactivate_url = 'plugins.php?action=deactivate' .
					'&amp;plugin=' . urlencode( $plugin_file );

				?>
				<div class="notice notice-warning">
					<p>
						<strong><?php esc_html_e('Byebye AI says:','byebye-ai'); ?></strong>
						<?php
							esc_html_e('WordPress can not write to the .htaccess file and you have a static robots.txt. There is nothing we can do about it.', 'byebye-ai' );
						?>
						<?php
						printf(
							'<a class="button" href="%s">%s</a>',
							wp_nonce_url( $deactivate_url, 'deactivate-plugin_' . $plugin_file ),
							_x( 'Deactivate Plugin', 'plugin' )
						);
						?>
					</p>
				</div>
				<?php
			});
			return;
		}

		$option_name = 'byebye_ai_enbled';

		register_setting( $this->optionset , $option_name, 'intval' );

		add_settings_field(
			$option_name,
			__( 'AI visibility',  'byebye-ai' ),
			function() use ( $option_name ){
				$option_value = get_option( $option_name );
				?>
				<fieldset>
					<legend class="screen-reader-text"><span><?php esc_html_e( 'AI visibility',  'byebye-ai' ); ?></span></legend>
					<label for="<?php echo $option_name ?>">
						<input type="checkbox" id="<?php echo $option_name ?>" name="<?php echo $option_name ?>" value="1" <?php checked( $option_value, 1, true ); ?> />
						<?php
						if ( $this->core->can_write_htaccess() ) {
							esc_html_e( 'Prevent AI bots from crawling this site', 'byebye-ai' );
						} else {
							esc_html_e( 'Discourage AI bots from crawling this site', 'byebye-ai' );
						}
						?>
					</label>
					<p class="description">
						<?php
						if ( $this->core->can_write_htaccess() ) {
							echo wp_kses_post( __('Known AI crawlers will be effectively blocked using the <code>.htaccess</code>.', 'byebye-ai') );
						} else {
							esc_html_e('It is up the AI crawlers to honor this request.', 'byebye-ai');
						}
						?>
					</p>
				</fieldset>
				<?php
			},
			$this->optionset,
			'default'
		);
	}

	/**
	 *	@inheritdoc
	 */
	public function activate() {
		add_option( 'byebye_ai_enbled', 1, '', true );
		add_option( 'byebye_ai_htaccess', '', '', false );
		add_option( 'byebye_ai_enbled', '', '', false );
	}

	/**
	 *	@inheritdoc
	 */
	public function deactivate() {}

	/**
	 *	@inheritdoc
	 */
	public function uninstall() {
		delete_option( 'byebye_ai_enbled' );
		delete_option( 'byebye_ai_htaccess' );
		delete_option( 'byebye_ai_enbled' );
		delete_option( 'byebye_ai_updated' );
		// reset htaccess
		$this->core->apply_settings();
		return [
			'success'	=> true,
			'messages'	=> [],
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {}

}
