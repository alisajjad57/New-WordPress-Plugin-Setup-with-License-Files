<?php

/**
 * Plugin General Settings
 */

defined( "ABSPATH" ) || exit;

?>

<div class="wrap wn_wrap wn-gen-panel">
    <div class="form-inner">
        <h2 class="wn-ld-ceus-subtitle"><?php _e('Shortcode', 'learndash-ceus'); ?></h2>
        <div class="field border-top border-bottom">
            <div class="woo-ld-ceus-shortcode">
            <code id="wn-ld-ceus-shrtcd">[wn_ld_ceus]</code>
            <button class="btn" type="button" data-copytarget="#wn-ld-ceus-shrtcd"><span class="dashicons dashicons-admin-page"></span></button></div>
            <p><?php _e('This shortcode will display the community tab on the frontend.', 'learndash-ceus'); ?></p>
        </div>
    </div>
</div>