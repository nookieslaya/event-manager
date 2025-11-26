<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function em_get_registrations( $event_id ) {
    $data = get_post_meta( $event_id, 'event_registrations', true );
    if ( ! is_array( $data ) ) {
        return [];
    }
    return $data;
}

function em_handle_register_event() {
    check_ajax_referer( 'em_register_event', 'nonce' );

    $event_id = isset( $_POST['event_id'] ) ? absint( $_POST['event_id'] ) : 0;
    if ( ! $event_id || 'event' !== get_post_type( $event_id ) ) {
        wp_send_json_error( [ 'message' => __( 'Nie znaleziono wydarzenia.', 'event-manager' ) ], 404 );
    }

    $name  = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
    $email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

    if ( empty( $name ) || empty( $email ) || ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => __( 'Podaj poprawne imie i email.', 'event-manager' ) ], 400 );
    }

    $registrations = em_get_registrations( $event_id );

    foreach ( $registrations as $registration ) {
        if ( isset( $registration['email'] ) && strtolower( $registration['email'] ) === strtolower( $email ) ) {
            wp_send_json_error( [ 'message' => __( 'Ten email jest juz zapisany na wydarzenie.', 'event-manager' ) ], 409 );
        }
    }

    $limit = (int) get_field( 'em_event_limit', $event_id );
    $limit = $limit > 0 ? $limit : 0;

    if ( $limit && count( $registrations ) >= $limit ) {
        wp_send_json_error( [ 'message' => __( 'Brak miejsc na wydarzenie.', 'event-manager' ) ], 409 );
    }

    $registrations[] = [
        'name'       => $name,
        'email'      => $email,
        'created_at' => current_time( 'mysql' ),
    ];

    update_post_meta( $event_id, 'event_registrations', $registrations );

    wp_send_json_success(
        [
            'message'       => __( 'Zapisano na wydarzenie.', 'event-manager' ),
            'registrations' => count( $registrations ),
            'limit'         => $limit,
        ]
    );
}
add_action( 'wp_ajax_register_event', 'em_handle_register_event' );
add_action( 'wp_ajax_nopriv_register_event', 'em_handle_register_event' );
