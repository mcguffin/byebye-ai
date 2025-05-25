<?php
/**
 *	@package ByebyeAI\Cron
 *	@version 1.0.0
 *	2018-09-22
 */

namespace ByebyeAI\Cron;

use ByebyeAI\Core;

/**
 *	Usage:
 *	======
 *	Start a job every 10 minutes:
 *
 *	$job = Cron\Cron::getJob( 'some_unique_hook', 'callback', [ 'callback', 'args' ], 600 );
 *	$job->start();
 *
 *	Stop it:
 *
 *	$job = Cron\Cron::getJob( 'same_unique_hook', 'callback', [ 'callback', 'args' ] );
 *	$job->stop();
 */
class Cron extends Core\Singleton {

	private $jobs;

	/**
	 *	@param	string		$hook
	 *	@param	callable	$callback
	 *	@param	array		$args
	 *	@param	absint		$interval
	 *	@return array
	 */
	public static function findJobs( $hook, $callback, $args = null, $interval = null ) {
		$jobs = [];
		$cron = get_option( 'cron' );
		$inst = self::instance();

		unset($cron['version']);

		foreach ( $cron as $time => $wp_jobs ) {

			foreach ( $wp_jobs as $job_hook => $job ) {
				if ( $job_hook != $hook ) {
					continue;
				}
				foreach ( $job as $args_key => $wp_job ) {
					//
					if (
							// callback matches
							in_array( $callback, $inst->jobs[ $hook ] ) &&

							// intarval matches
							( is_null( $interval ) || $interval == $wp_job['interval'] ) &&

							// intarval matches
							( is_null( $args ) || $args == $wp_job['args'] )
					) {
						$jobs[]	= self::createJob( $hook, $callback, $wp_job['args'], $wp_job['interval'] );
					}
				}
			}
		}

		return $jobs;
	}

	/**
	 *	@param	string		$hook
	 *	@param	callable	$callback
	 *	@param	array		$args
	 *	@param	absint		$interval
	 *	@return Cron\Job
	 */
	public static function getJob( $hook, $callback, $args = [], $interval = 3600 ) {

		$found = self::findJobs( $hook, $callback, $args );

		if ( ! empty( $found ) ) {
			return $found[0];
		}

		return self::createJob( $hook, $callback, $args, $interval );
	}

	/**
	 *	@param	string		$hook
	 *	@param	callable	$callback
	 *	@param	array		$args
	 *	@param	absint		$interval
	 *	@return Cron\Job
	 */
	protected static function createJob( $hook, $callback, $args = [], $interval = 3600 ) {

		if ( $schedule = self::instance()->get_schedule( $interval ) ) {
			return new Job( $hook, $callback, $args, $schedule );
		}

	}


	/**
	 *	Constructor
	 */
	protected function __construct() {
//		add_action('init', [ $this, 'init' ] );

		add_option( 'byebye_ai_cronjobs', [] );
		add_option( 'byebye_ai_cronschedules', [] );

		add_filter( 'cron_schedules', [ $this, 'cron_schedules' ] );

		$this->init();
	}

	/**
	 *	@return void
	 */
	private function init() {
		$this->jobs = get_option( 'byebye_ai_cronjobs' );

		foreach ( $this->jobs as $hook => $callbacks ) {
			foreach ( array_unique( (array) $callbacks ) as $callback ) {
				add_action( $hook, $callback );
			}
			$this->jobs[$hook] = (array) $callbacks;
		}
		$this->update();
	}


	/**
	 *	@param int $interval
	 *	@return bool|string
	 */
	public function get_schedule( $interval ) {
		if ( empty( $interval ) ) {
			return false;
		}
		if ( ! $schedule = $this->find_schedule( $interval ) ) {
			$schedule = sprintf( 'every_%d_seconds', $interval );
			$schedules = get_option('byebye_ai_cronschedules');
			$schedules[ $schedule ] = [
				'interval'	=> $interval,
				'display'	=> sprintf( __( 'Every %d seconds', 'calendar-importer' ), $interval ),
			];
			update_option( 'byebye_ai_cronschedules', $schedules );
		}
		return $schedule;
	}

	/**
	 *	@param int $interval
	 *	@return bool|string
	 */
	public function find_schedule( $interval ) {
		foreach ( wp_get_schedules() as $slug => $schedule ) {
			if ( $schedule['interval'] == $interval ) {
				return $slug;
			}
		}
		return false;
	}

	/**
	 *	@filter cron_schedules
	 */
	public function cron_schedules( $schedules ) {
		return $schedules + (array) get_option('byebye_ai_cronschedules');
	}

	/**
	 *	start a job
	 *	@param Cron\Job $job
	 */
	public function register_job( $job ) {

		$hook = $job->get_hook();
		$key = $job->get_key();

		if ( ! isset( $this->jobs[ $hook ] ) ) {
			$this->jobs[ $hook ] = [];
		}
		$this->jobs[ $hook ][ $key ] = $job->get_callback();
		$this->update();
	}

	/**
	 *	Unregister a job
	 *	@param Cron\Job $job
	 */
	public function unregister_job( $job ) {
		$hook = $job->get_hook();
		$key = $job->get_key();
		if ( isset( $this->jobs[ $hook ], $this->jobs[ $hook ][ $key ] ) ) {
			unset( $this->jobs[ $hook ][ $key ] );
		}
		$this->update();
	}

	/**
	 *	Update jobs option
	 */
	private function update() {

		update_option( 'byebye_ai_cronjobs', $this->jobs );

	}


	/**
	 *	@inheritdoc
	 */
	public function activate() {
		return [
			'success'	=> true,
			'messages'	=> [],
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function deactivate() {
		// stop all jobs
		$jobs = get_option( 'byebye_ai_cronjobs', $this->jobs );
		foreach ( $jobs as $hook => $callbacks ) {
			foreach ( array_unique( (array) $callbacks ) as $callback ) {
				foreach ( self::findJobs( $hook, $callback ) as $job ) {
					$job->stop();
				}
			}
		}

		return [
			'success'	=> true,
			'messages'	=> [],
		];
	}

	/**
	 *	@inheritdoc
	 */
	public static function uninstall() {

		delete_option( 'byebye_ai_cronjobs' );
		delete_option( 'byebye_ai_cronschedules' );

		return [
			'success'	=> true,
			'messages'	=> [],
		];
	}

	/**
	 *	@inheritdoc
	 */
	public function upgrade( $new_version, $old_version ) {
	}


}
