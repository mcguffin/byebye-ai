<?php
/**
 *	@package ByebyeAI\WPCLI
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\WPCLI\Commands;

if ( ! defined('ABSPATH') ) {
	die('FU!');
}

class ByebyeAiUpdate extends \WP_CLI_Command {

	/**
	 * Bark.
	 *
	 * ## OPTIONS
	 * <animal>...
	 * : Type of animal
	 * ---
	 * default: dog
	 * options:
	 *  - dog
	 *  - cat
	 *  - wolve
	 *  - squirrel
	 *  - mole
	 * ---
	 *
	 * --volume=<volume>
	 * : sound volume of barking.
	 * ---
	 * default: normal
	 * options:
	 *   - quiet
	 *   - normal
	 *   - loud
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp access-areas bark dog mole wolve --volume=quiet
	 *
	 *	@alias comment-check
	 */
	public function bark( $args, $assoc_args ) {
		$total = 0;
		foreach ( $args as $animal ) {
			if ( in_array( $animal, [ 'dog', 'wolve' ] ) ) {
				$total++;
				if ( $animal === 'dog' ) {
					$bark = __( "Rouff", 'byebye-ai' );
				} else {
					$bark = __( "Ahooouuu", 'byebye-ai' );
				}
				switch ( $assoc_args['volume'] ) {
					case 'loud':
						$bark = strtoupper($bark) . '!!!';
						break;
					case 'quiet':
						$bark = '(' . strtolower($bark) . '...)';
						break;
				}
				\WP_CLI::line( $bark );
			} else if ( $animal === 'cat' ) {
				\WP_CLI::error( __( "Bad Idea, chuck!", 'byebye-ai' ) );
			} else {
				\WP_CLI::warning( __( "$animal did not bark.", 'byebye-ai' ) );
			}
		}
		\WP_CLI::success( sprintf( __( "%d animal(s) barked.", 'byebye-ai' ), $total ) );
	}

}
