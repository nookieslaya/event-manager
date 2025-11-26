<?php
/**
 * Plugin Name: Event Manager
 * Description: Zarządzanie wydarzeniami z rejestracją uczestników.
 * Version: 1.0.0
 * Author: Radek
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once EM_PLUGIN_DIR . 'includes/cpt-registration.php';
require_once EM_PLUGIN_DIR . 'includes/acf-fields.php';
require_once EM_PLUGIN_DIR . 'includes/ajax-handlers.php';

function em_enqueue_assets() {
    if ( ! is_singular( 'event' ) ) {
        return;
    }

    wp_enqueue_script( 'em-tailwind-play', 'https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4', [], '4.0.0', false );
    wp_enqueue_style( 'em-style', EM_PLUGIN_URL . 'assets/css/style.css', [], '1.3.4' );
    wp_enqueue_script( 'em-register', EM_PLUGIN_URL . 'assets/js/event-register.js', [], '1.3.4', true );

    wp_localize_script(
        'em-register',
        'EventManagerData',
        [
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'em_register_event' ),
            'messages' => [
                'genericError' => __( 'Coś poszło nie tak. Spróbuj ponownie.', 'event-manager' ),
            ],
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'em_enqueue_assets' );

function em_filter_single_template( $template ) {
    if ( is_singular( 'event' ) ) {
        $plugin_template = EM_PLUGIN_DIR . 'templates/single-event.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter( 'single_template', 'em_filter_single_template' );

function em_activate() {
    em_register_event_cpt();
    em_register_city_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'em_activate' );

function em_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'em_deactivate' );

/**
 * Require city taxonomy selection when saving an event.
 */
function em_require_city_on_save( $data, $postarr ) {
    if ( 'event' !== $data['post_type'] ) {
        return $data;
    }

    // Ignore auto-draft/trash states.
    if ( in_array( $data['post_status'], [ 'auto-draft', 'trash', 'inherit' ], true ) ) {
        return $data;
    }

    $has_city = false;

    if ( isset( $_POST['tax_input']['city'] ) ) {
        $tax_input = $_POST['tax_input']['city'];
        if ( is_array( $tax_input ) ) {
            foreach ( $tax_input as $term ) {
                if ( '' !== $term && '0' !== $term ) {
                    $has_city = true;
                    break;
                }
            }
        } elseif ( '' !== trim( $tax_input ) ) {
            $has_city = true;
        }
    }

    if ( ! $has_city ) {
        $data['post_status'] = 'draft';
        add_filter(
            'redirect_post_location',
            static function ( $location ) {
                return add_query_arg( 'em_city_error', '1', $location );
            }
        );
    }

    return $data;
}
/**
 * Require city taxonomy selection when saving an event (after terms are set).
 */
function em_require_city_on_save( $post_id, $post, $update ) {
    if ( 'event' !== $post->post_type ) {
        return;
    }

    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Skip if user cannot publish/edit this post.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // In REST autosave context (Gutenberg), do not block.
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST && isset( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'] ) {
        return;
    }

    $has_city = has_term( '', 'city', $post_id );

    if ( ! $has_city ) {
        // Force draft and show notice.
        remove_action( 'save_post_event', 'em_require_city_on_save', 20 );
        wp_update_post(
            [
                'ID'          => $post_id,
                'post_status' => 'draft',
            ]
        );
        add_filter(
            'redirect_post_location',
            static function ( $location ) {
                return add_query_arg( 'em_city_error', '1', $location );
            }
        );
    }
}
add_action( 'save_post_event', 'em_require_city_on_save', 20, 3 );

/**
 * Admin notice if city not selected.
 */
function em_city_error_notice() {
    if ( isset( $_GET['em_city_error'] ) ) {
        echo '<div class="notice notice-error"><p>' . esc_html__( 'Wydarzenie wymaga wybranego miasta (taksonomia city). Dodaj co najmniej jedno miasto.', 'event-manager' ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'em_city_error_notice' );
