<?php
/**
 *	@package ByebyeAI\Core
 *	@version 1.0.1
 *	2018-09-22
 */

namespace ByebyeAI\Core;

use ByebyeAI\Asset;
use ByebyeAI\Cron;

class Core extends Plugin {

	/**
	 *	@inheritdoc
	 */
	protected function __construct(...$args) {

		if ( get_option('byebye_ai_enbled') ) {
			add_filter( 'robots_txt', function($robots) {
				if ( $robots_option = get_option('byebye_ai_robotstxt') ) {
					$robots .= "\n# Inserted by the Byebye AI plugin\n";
					$robots .= $robots_option;
				}
				return $robots;
			}, 1, 2 );
		}

		parent::__construct( ...$args );
	}

	public function sync() {
		$this->update_bots();
		$this->apply_settings();
	}

	/**
	 *	Download botlist
	 */
	public function update_bots() {
		if ( ! get_option('byebye_ai_enbled') ) {
			return;
		}
		$synced = [
			'htaccess'  => 'https://raw.githubusercontent.com/ai-robots-txt/ai.robots.txt/refs/heads/main/.htaccess',
			'robotstxt' => 'https://raw.githubusercontent.com/ai-robots-txt/ai.robots.txt/refs/heads/main/robots.txt',
		];
		foreach ( $synced as $key => $url ) {
			$downloaded[$key] = false;
			$response = wp_remote_get($url);
			if ( is_wp_error( $response ) ) {
				update_option("byebye_ai_{$key}", '' );
				continue;
			}
			$content  = wp_remote_retrieve_body($response);
			// TODO: syntax check response
			update_option("byebye_ai_{$key}", $content );
			$downloaded[$key] = true;
		}
		update_option('byebye_ai_updated', time() );
	}

	/**
	 *	Updates htaccess
	 */
	public function apply_settings() {
		if ( ! $this->can_write_htaccess() ) {
			return;
		}

		$cronjob = Cron\Cron::getJob( 'byebye_ai_sync', [$this, 'sync'], [], DAY_IN_SECONDS );

		if ( get_option('byebye_ai_enbled') ) {
			$htaccess_option = get_option('byebye_ai_htaccess');
			if ( ! $htaccess_option ) {
				$htaccess_option = '';
			}
			$cronjob->start();
		} else {
			$htaccess_option = '';
			$cronjob->stop();
		}

		insert_with_markers( ABSPATH . '.htaccess', 'ByebyeAI', $htaccess_option );
	}

	/**
	 *	@return boolean
	 */
	public function can_write_htaccess() {
		if ( ! is_file( ABSPATH . '.htaccess' ) ) {
			return false;
		}
		return is_writable( ABSPATH . '.htaccess' );
	}

	/**
	 *	@return boolean
	 */
	public function can_serve_robotstxt() {
		if ( is_file( ABSPATH . 'robots.txt' ) ) {
			return false;
		}
		return got_url_rewrite();
	}

	/**
	 *	@inheritdoc
	 */
	public function activate() {
		parent::activate();
		$cronjob = Cron\Cron::getJob( 'byebye_ai_sync', [$this, 'sync'], [], DAY_IN_SECONDS );
		$cronjob->start();
	}

	/**
	 *	@inheritdoc
	 */
	public function deactivate() {
		parent::activate();
		$cronjob = Cron\Cron::getJob( 'byebye_ai_sync', [$this, 'sync'], [], DAY_IN_SECONDS );
		$cronjob->stop();
	}
}
