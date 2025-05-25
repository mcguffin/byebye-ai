<?php
/**
 *	@package ByebyeAI\WPCLI
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\WPCLI;

use ByebyeAI\Core;

class WPCLI extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		\WP_CLI::add_command( 'byebye-ai sync', [ new Commands\ByebyeAISync(), 'sync' ], [
			'shortdesc'   => 'Update AI blocklist from https://github.com/ai-robots-txt/ai.robots.txt',
			'is_deferred' => false,
		] );
		\WP_CLI::add_command( 'byebye-ai reset', [ new Commands\ByebyeAISync(), 'reset' ], [
			'shortdesc'   => 'Reset plugin to factory default',
			'is_deferred' => false,
		] );
	}
}
