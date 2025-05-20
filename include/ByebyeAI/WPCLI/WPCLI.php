<?php
/**
 *	@package ByebyeAI\WPCLI
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\WPCLI;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

use ByebyeAI\Core;

class WPCLI extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {
		\WP_CLI::add_command( 'byebye-ai-update', [ new Commands\ByebyeAiUpdate(), 'bark' ], [
//			'before_invoke'	=> 'a_callable',
//			'after_invoke'	=> 'another_callable',
			'shortdesc'		=> 'Byebye AI commands',
//			'when'			=> 'before_wp_load',
			'is_deferred'	=> false,
		] );
	}
}
