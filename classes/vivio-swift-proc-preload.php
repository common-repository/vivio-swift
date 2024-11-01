<?php

// thanks to:
// https://github.com/A5hleyRich/wp-background-processing

class Vivio_Swift_Process_Preload extends Vivio_Swift_Background_Process {

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task($item) {
        global $vivio_swift_global;
        // for this task, the item we're passing is the URL to be cached
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Process_Preload::task() - begin process for: ".$item, 1);
        $vivio_swift_global->cache_obj->cache_url_with_agent($item);
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Process_Preload::task() - complete process for: ".$item, 1);
		return false;
	}
	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
		// Show notice to user or perform some other arbitrary task...
	}
}