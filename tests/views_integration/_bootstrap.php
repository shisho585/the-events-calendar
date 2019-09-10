<?php

use Tribe\Events\Views\V2\Service_Provider;

// Let's  make sure Views v2 are activated if not.
putenv( 'TRIBE_EVENTS_V2_VIEWS=1' );
tribe_register_provider( Service_Provider::class );

// Let's make sure to set rewrite rules.
global $wp_rewrite;
$wp_rewrite->permalink_structure = '/%postname%/';
$wp_rewrite->rewrite_rules();

add_filter( 'option_uploads_use_yearmonth_folders', '__return_empty' );
