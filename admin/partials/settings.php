<?php

/**
 * Plugin General Settings
 */

defined( "ABSPATH" ) || exit;

$wn_ld_ceus_settings    = get_option( 'wn_ld_ceus_settings', array() );

// Do not publish new comments.
$first_setting_checkbox = isset( $wn_ld_ceus_settings['first_setting_checkbox'] ) ? $wn_ld_ceus_settings['first_setting_checkbox'] : '';


?>
<div class="wrap wn-ld-ceu-setting wn_wrap wn-sttg-panel">
    <form method="post">
        <h2 class="wn-ld-ceu-subtitle"><?php _e('General Settings', 'learndash-ceus'); ?></h2>
        <table class="form-table">
        <!-- ldct nonce field -->
        <?php wp_nonce_field( "learndash_ceus_nonce", "learndash_ceus_nonce" ); ?>

            <!-- Do not publish new comments -->
            <tr>
                <th scope="row" class="wn-qst-mrk">
                    <label><?php echo __( 'first setting checkbox', 'learndash-ceus' ); ?>
                   <i class="fas fa-question"></i>
                    </label>
 
                    <p class="description">
                        <?php echo __('first setting checkbox description.','learndash-ceus'); ?>
                    </p>
                </th>
                <td class="ldscie-flex-row">
                    <label>
                        <input name="first_setting_checkbox" type="checkbox" value="1" <?php checked(true, $first_setting_checkbox ); ?> >
                    </label>
                    
                </td>
            </tr>

        </table>
        <div class="submit">
            <input type="submit" name="ld_ceus_save_settings_options" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'learndash-ceus' ); ?>">
        </div>
    </form>
</div>
