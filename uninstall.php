<?php
/**
 * Uninstall handler for BlueFairy Performance Kit.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/*
 * Delete through the options API, not with SQL.
 *
 * A LIKE 'bfpk_%' sweep against wp_options — which is what the paid plugin
 * used to do — removes the rows but leaves the values sitting in Redis or
 * Memcached on any site with a persistent object cache, so a reinstall reads
 * back settings that no longer exist in the database.
 *
 * These are named explicitly rather than matched by prefix so this file cannot
 * delete options belonging to the add-on, which cleans up after itself.
 */
$bfpk_options = [
    'bfpk_features',
    'bfpk_schema_version',
    'bfpk_activated_at',
];

foreach ( $bfpk_options as $bfpk_option ) {
    delete_option( $bfpk_option );
}
