<?php
/**
 * Stops the heartbeat on the front end.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Features;

final class HeartbeatControl {

    public function register(): void {
        // wp_enqueue_scripts does not fire in wp-admin or the block editor, so
        // scoping to it is what keeps autosave and post locking working there.
        add_action( 'wp_enqueue_scripts', [ $this, 'deregister' ], 1 );
    }

    public function deregister(): void {
        wp_deregister_script( 'heartbeat' );
    }
}
