<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register custom post type Event.
 */
function em_register_event_cpt() {
    $labels = [
        'name'               => __( 'Wydarzenia', 'event-manager' ),
        'singular_name'      => __( 'Wydarzenie', 'event-manager' ),
        'add_new'            => __( 'Dodaj nowe', 'event-manager' ),
        'add_new_item'       => __( 'Dodaj wydarzenie', 'event-manager' ),
        'edit_item'          => __( 'Edytuj wydarzenie', 'event-manager' ),
        'new_item'           => __( 'Nowe wydarzenie', 'event-manager' ),
        'view_item'          => __( 'Zobacz wydarzenie', 'event-manager' ),
        'search_items'       => __( 'Szukaj wydarzeń', 'event-manager' ),
        'not_found'          => __( 'Brak wydarzeń', 'event-manager' ),
        'not_found_in_trash' => __( 'Brak wydarzeń w koszu', 'event-manager' ),
        'menu_name'          => __( 'Wydarzenia', 'event-manager' ),
    ];

    register_post_type(
        'event',
        [
            'labels'       => $labels,
            'public'       => true,
            'has_archive'  => true,
            'menu_icon'    => 'dashicons-calendar-alt',
            'supports'     => [ 'title', 'editor', 'excerpt', 'thumbnail' ],
            'rewrite'      => [ 'slug' => 'events' ],
            'show_in_rest' => true,
        ]
    );
}
add_action( 'init', 'em_register_event_cpt' );

/**
 * Register taxonomy City for events.
 */
function em_register_city_taxonomy() {
    $labels = [
        'name'          => __( 'Miasta', 'event-manager' ),
        'singular_name' => __( 'Miasto', 'event-manager' ),
        'search_items'  => __( 'Szukaj miast', 'event-manager' ),
        'all_items'     => __( 'Wszystkie miasta', 'event-manager' ),
        'edit_item'     => __( 'Edytuj miasto', 'event-manager' ),
        'update_item'   => __( 'Aktualizuj miasto', 'event-manager' ),
        'add_new_item'  => __( 'Dodaj miasto', 'event-manager' ),
        'new_item_name' => __( 'Nazwa miasta', 'event-manager' ),
        'menu_name'     => __( 'Miasta', 'event-manager' ),
    ];

    register_taxonomy(
        'city',
        [ 'event' ],
        [
            'hierarchical' => true,
            'labels'       => $labels,
            'show_ui'      => true,
            'show_admin_column' => true,
            'rewrite'      => [ 'slug' => 'city' ],
            'show_in_rest' => true,
        ]
    );
}
add_action( 'init', 'em_register_city_taxonomy' );
