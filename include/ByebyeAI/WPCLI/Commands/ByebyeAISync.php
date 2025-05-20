<?php
/**
 *	@package ByebyeAI\WPCLI
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\WPCLI\Commands;

use ByebyeAI\Core;

class ByebyeAISync extends \WP_CLI_Command {

	/**
	 * Sync Byebye AI
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp byebye-ai sync
	 *
	 *	@alias comment-check
	 */
	public function sync( $args, $assoc_args ) {

		$core = Core\Core::instance();
		$core->sync();
		\WP_CLI::success( __( 'Sync successful.', 'byebye-ai' ) );
	}

	/**
	 * Reset Byebye AI
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 *     wp byebye-ai reset
	 *
	 *	@alias comment-check
	 */
	public function reset( $args, $assoc_args ) {
		Core\Plugin::uninstall();
		\WP_CLI::success( 'Reset complete' );
	}
}
