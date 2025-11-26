<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register ACF fields for Event.
 */
function em_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    acf_add_local_field_group(
        [
            'key'   => 'group_em_event',
            'title' => __( 'Dane wydarzenia', 'event-manager' ),
            'fields' => [
                [
                    'key'           => 'field_em_datetime',
                    'label'         => __( 'Data i godzina rozpoczecia', 'event-manager' ),
                    'name'          => 'em_event_datetime',
                    'type'          => 'date_time_picker',
                    'display_format'=> 'd/m/Y H:i',
                    'return_format' => 'U',
                    'required'      => 1,
                ],
                [
                    'key'      => 'field_em_limit',
                    'label'    => __( 'Limit uczestnikow', 'event-manager' ),
                    'name'     => 'em_event_limit',
                    'type'     => 'number',
                    'required' => 1,
                    'min'      => 1,
                    'step'     => 1,
                ],
                [
                    'key'          => 'field_em_description',
                    'label'        => __( 'Opis / szczegoly', 'event-manager' ),
                    'name'         => 'em_event_description',
                    'type'         => 'wysiwyg',
                    'required'     => 0,
                    'tabs'         => 'all',
                    'toolbar'      => 'basic',
                    'media_upload' => 0,
                ],
            ],
            'location' => [
                [
                    [
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'event',
                    ],
                ],
            ],
        ]
    );
}
add_action( 'acf/init', 'em_register_acf_fields' );
