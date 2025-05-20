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
//			'before_invoke'	=> 'a_callable',
//			'after_invoke'	=> 'another_callable',
			'shortdesc'		=> 'Update AI blocklist from https://github.com/ai-robots-txt/ai.robots.txt',
//			'when'			=> 'before_wp_load',
			'is_deferred'	=> false,
		] );
		\WP_CLI::add_command( 'byebye-ai reset', [ new Commands\ByebyeAISync(), 'reset' ], [
	//			'before_invoke'	=> 'a_callable',
	//			'after_invoke'	=> 'another_callable',
			'shortdesc'		=> 'Reset plugin to factory default',
	//			'when'			=> 'before_wp_load',
			'is_deferred'	=> false,
		] );
	}
}
