<?php

/**
 * Class ActionScheduler_Action
 */
class ActionScheduler_Action {
	protected $hook = '';
	protected $args = array();
	/** @var ActionScheduler_Schedule */
	protected $schedule = null;
	protected $group    = '';

	/**
	 * Priorities are conceptually similar to those used for regular WordPress actions.
	 * Like those, a lower priority takes precedence over a higher priority and the default
	 * is 10.
	 *
	 * Unlike regular WordPress actions, the priority of a scheduled action is strictly an
	 * integer and should be kept within the bounds 0-255 (anything outside the bounds will
	 * be brought back into the acceptable range).
	 *
	 * @var int
	 */
	protected $priority = 10;

	public function __construct( $hook, array $args = array(), ActionScheduler_Schedule $schedule = null, $group = '' ) {
		$schedule = empty( $schedule ) ? new ActionScheduler_NullSchedule() : $schedule;
		$this->set_hook( $hook );
		$this->set_schedule( $schedule );
		$this->set_args( $args );
		$this->set_group( $group );
	}

	/**
	 * Executes the action.
	 *
	 * If no callbacks are registered, an exception will be thrown and the action will not be
	 * fired. This is useful to help detect cases where the code responsible for setting up
	 * a scheduled action no longer exists.
	 *
	 * @throws Exception If no callbacks are registered for this action.
	 */
	public function execute() {
		$hook = $this->get_hook();

		if ( ! has_action( $hook ) ) {
			throw new Exception(
				sprintf(
					/* translators: 1: action hook. */
					__( 'Scheduled action for %1$s will not be executed as no callbacks are registered.', 'action-scheduler' ),
					$hook
				)
			);
		}

		do_action_ref_array( $hook, array_values( $this->get_args() ) );
	}

	/**
	 * @param string $hook
	 */
	protected function set_hook( $hook ) {
		$this->hook = $hook;
	}

	public function get_hook() {
		return $this->hook;
	}

	protected function set_schedule( ActionScheduler_Schedule $schedule ) {
		$this->schedule = $schedule;
	}

	/**
	 * @return ActionScheduler_Schedule
	 */
	public function get_schedule() {
		return $this->schedule;
	}

	protected function set_args( array $args ) {
		$this->args = $args;
	}

	public function get_args() {
		return $this->args;
	}

	/**
	 * @param string $group
	 */
	protected function set_group( $group ) {
		$this->group = $group;
	}

	/**
	 * @return string
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * @return bool If the action has been finished
	 */
	public function is_finished() {
		return false;
	}

	/**
	 * Sets the priority of the action.
	 *
	 * @param int $priority Priority level (lower is higher priority). Should be in the range 0-255.
	 *
	 * @return void
	 */
	public function set_priority( $priority ) {
		if ( $priority < 0 ) {
			$priority = 0;
		} elseif ( $priority > 255 ) {
			$priority = 255;
		}

		$this->priority = (int) $priority;
	}

	/**
	 * Gets the action priority.
	 *
	 * @return int
	 */
	public function get_priority() {
		return $this->priority;
	}
}
