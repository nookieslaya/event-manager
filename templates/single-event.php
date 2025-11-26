<?php
/**
 * Template for single Event.
 */

global $post;
$event_id     = $post->ID;
$datetime_raw = get_field( 'em_event_datetime', $event_id );
$datetime     = $datetime_raw ? date_i18n( 'd.m.Y H:i', (int) $datetime_raw ) : '';
$limit        = (int) get_field( 'em_event_limit', $event_id );
$registrations = get_post_meta( $event_id, 'event_registrations', true );
$registrations = is_array( $registrations ) ? $registrations : [];
$current_count = count( $registrations );
$cities        = get_the_terms( $event_id, 'city' );
$description   = get_field( 'em_event_description', $event_id );

get_header();
?>

<main class="min-h-screen flex items-center justify-center px-4 py-10 bg-slate-50">
    <article <?php post_class( 'w-full max-w-4xl bg-white shadow rounded-lg !p-6 md:p-8 space-y-6' ); ?>>
        
            <div>
                <h1 class="text-3xl font-bold leading-tight !mb-2"><?php the_title(); ?></h1>
        
                     <div class="flex flex-wrap gap-4 text-sm text-slate-700 !py-4">
                            <?php if ( $datetime ) : ?>
                                <span class="inline-flex items-center gap-2 !p-3 rounded-full bg-slate-100">
                                    <strong class="font-semibold"><?php echo esc_html__( 'Data i godzina:', 'event-manager' ); ?></strong>
                                    <span><?php echo esc_html( $datetime ); ?></span>
                                </span>
                            <?php endif; ?>
            
                            <?php if ( $cities && ! is_wp_error( $cities ) ) : ?>
                                <span class="inline-flex items-center gap-2 !p-3 rounded-full bg-slate-100">
                                    <strong class="font-semibold"><?php echo esc_html__( 'Miasto:', 'event-manager' ); ?></strong>
                                    <span>
                                        <?php
                                        $city_names = wp_list_pluck( $cities, 'name' );
                                        echo esc_html( implode( ', ', $city_names ) );
                                        ?>
                                    </span>
                                </span>
                         <?php endif; ?>
                  </div>
         </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <?php if ( $limit ) : ?>
                <div class="border border-slate-200 rounded-lg px-4 !p-1">
                    <p class="text-sm text-slate-500 !p-3"><?php echo esc_html__( 'Limit miejsc', 'event-manager' ); ?></p>
                    <p class="text-2xl font-semibold em-registrations-limit !p-3"><?php echo esc_html( $limit ); ?></p>
                </div>
            <?php endif; ?>
            <div class="border border-slate-200 rounded-lg px-4  !p-1">
                <p class="text-sm text-slate-500  !p-3"><?php echo esc_html__( 'Zapisanych', 'event-manager' ); ?></p>
                <p class="text-2xl font-semibold em-registrations-count !p-3"><?php echo esc_html( $current_count ); ?></p>
            </div>
        </div>
        <div class="border border-slate-200 rounded-lg !p-3 !mt-2">
        <?php if ( $description ) : ?>
            <div class="prose max-w-none">
                <?php echo wp_kses_post( $description ); ?>
            </div>
        <?php else : ?>
            <div class="prose max-w-none">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>
        </div>

        <?php if ( $limit === 0 || $current_count < $limit ) : ?>

            <form class="em-registration-form space-y-4" method="post">
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="flex flex-col gap-2 font-semibold !mt-3" for="em-name">
                        <span><?php echo esc_html__( 'Imię', 'event-manager' ); ?></span>
                        <input class="w-full rounded-md border border-slate-300 !px-3 !py-2 focus:outline-none focus:ring-2 focus:ring-sky-500" id="em-name" type="text" name="name" required />
                    </label>
                    <label class="flex flex-col gap-2 font-semibold !mt-3" for="em-email">
                        <span><?php echo esc_html__( 'Email', 'event-manager' ); ?></span>
                        <input class="w-full rounded-md border border-slate-300 !px-3 !py-2 focus:outline-none focus:ring-2 focus:ring-sky-500" id="em-email" type="email" name="email" required />
                    </label>
                </div>
                <input type="hidden" name="event_id" value="<?php echo esc_attr( $event_id ); ?>" />
                <div class="em-message text-sm font-semibold min-h-[1.25rem] !my-2" aria-live="polite"></div>
                <button class="inline-flex items-center justify-center rounded-md bg-sky-600 !px-4 !py-2 text-white font-bold hover:bg-sky-700 disabled:bg-slate-400 disabled:cursor-not-allowed transition" type="submit">
                    <?php echo esc_html__( 'Zapisz się', 'event-manager' ); ?>
                </button>
            </form>
        <?php else : ?>
            <p class="text-red-600 font-semibold p-2"><?php echo esc_html__( 'Brak miejsc na wydarzenie.', 'event-manager' ); ?></p>
        <?php endif; ?>
    </article>
</main>

<?php
get_footer();
