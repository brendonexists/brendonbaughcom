<?php
/**
 * Template Name: Live Now
 *
 * @package brendon-core
 */

get_header();

global $post;

$channel        = bb_live_twitch_channel();
$parent_domain  = bb_live_twitch_parent_domain();
$stream_status  = bb_live_twitch_stream_status( $channel );
$default_about  = get_post_field( 'post_content', $post->ID );
$about_content  = '';
$commands       = array();
$links          = array();
$schedule_events = array();

if ( function_exists( 'get_field' ) ) {
	$about_content = get_field( 'live_about' ) ?: '';
	$acf_commands  = get_field( 'live_commands' );
	if ( is_array( $acf_commands ) ) {
		$commands = $acf_commands;
	}

	$acf_links = get_field( 'live_links' );
	if ( is_array( $acf_links ) ) {
		$links = $acf_links;
	}
}

$about_output = $about_content ? $about_content : $default_about;
$about_output = apply_filters( 'the_content', $about_output );

if ( ! $commands ) {
	$commands = array(
		array(
			'command'     => '!uptime',
			'description' => 'Check how long the stream has been live.',
		),
		array(
			'command'     => '!discord',
			'description' => 'Get a link to the Discord community.',
		),
		array(
			'command'     => '!schedule',
			'description' => 'See what is next on the calendar.',
		),
	);
}

if ( ! $links ) {
	$links = array(
		array(
			'label' => 'Discord',
			'url'   => 'https://discord.gg/brendonbaugh',
		),
		array(
			'label' => 'YouTube',
			'url'   => 'https://www.youtube.com/brendonbaugh',
		),
		array(
			'label' => 'Merch',
			'url'   => 'https://brendonbaugh.com/shop',
		),
	);
}

$schedule_json = bb_live_schedule_json();
if ( $schedule_json ) {
	$decoded = json_decode( $schedule_json, true );
	if ( is_array( $decoded ) ) {
		$schedule_events = $decoded;
	}
}

if ( ! $schedule_events ) {
	$schedule_events = array(
		array(
			'day'   => 'Wednesday',
			'time'  => '8PM CT',
			'title' => 'Cozy chat & games',
		),
		array(
			'day'   => 'Friday',
			'time'  => '7PM CT',
			'title' => 'Creative coding & chill',
		),
	);
}

$player_src = add_query_arg(
	array(
		'channel' => $channel,
		'parent'  => $parent_domain,
		'muted'   => 'true',
	),
	'https://player.twitch.tv/'
);
$chat_src = sprintf(
	'https://www.twitch.tv/embed/%1$s/chat?parent=%2$s',
	rawurlencode( $channel ),
	rawurlencode( $parent_domain )
);

$status_label       = $stream_status['is_live'] ? esc_html__( 'Live Now', 'brendon-core' ) : esc_html__( 'Offline', 'brendon-core' );
$status_description = $stream_status['is_live'] ? esc_html__( 'Streaming live on Twitch.', 'brendon-core' ) : esc_html__( 'Channel is currently offline.', 'brendon-core' );
$viewer_count       = absint( $stream_status['raw']['data'][0]['viewer_count'] ?? 0 );
$status_updated     = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), current_time( 'timestamp' ) );

?>

<main id="primary" class="site-main min-h-screen bg-canvas text-slate-900">
	<div class="w-full px-6 py-8 space-y-8">
		<div class="lg:hidden">
			<?php get_template_part( 'template-parts/mobile-sidebar-panel' ); ?>
		</div>

		<div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">

			<aside class="hidden lg:block">
				<?php get_template_part( 'template-parts/sidebar-panel' ); ?>
			</aside>

			<section class="space-y-6">
				<div class="live-now-page">
					<div class="live-now-layout">
						<section class="live-now-player">
							<div class="live-player__status">
								<p class="live-player__title">Twitch Stream</p>
								<p class="live-player__subtitle"><?php echo esc_html( $channel ); ?></p>
							</div>
							<div class="live-player__embed">
								<iframe
									title="Twitch stream for <?php echo esc_attr( $channel ); ?>"
									src="<?php echo esc_url( $player_src ); ?>"
									frameborder="0"
									allowfullscreen="true"
									scrolling="no"
									loading="lazy"
									referrerpolicy="no-referrer"
								></iframe>
							</div>
							<?php if ( ! $stream_status['is_live'] ) : ?>
								<div class="live-player__offline">
									<p><?php esc_html_e( 'The stream is currently offline, but the player will resume when mr__brights1de goes live.', 'brendon-core' ); ?></p>
								</div>
							<?php endif; ?>
						</section>

						<div class="live-now-chat">
							<div class="live-now-chat__inner">
								<div class="live-panel__header">
									<span>Chat</span>
								</div>
								<div class="live-now-chat__embed">
									<iframe
										title="Twitch chat for <?php echo esc_attr( $channel ); ?>"
										src="<?php echo esc_url( $chat_src ); ?>"
										frameborder="0"
										allowfullscreen="true"
										scrolling="no"
										loading="lazy"
										referrerpolicy="no-referrer"
									></iframe>
								</div>
							</div>
						</div>

						<div class="live-now-panels-grid">
							<article class="live-panel live-panel--about">
								<div class="live-panel__header">
									<span>About</span>
								</div>
								<div class="live-panel__body">
									<?php echo wp_kses_post( $about_output ); ?>
								</div>
							</article>

							<article class="live-panel live-panel--status">
								<div class="live-panel__header">
									<span>Live Status</span>
								</div>
								<div class="live-panel__body">
									<p class="live-status__label"><?php echo esc_html( $status_label ); ?></p>
									<p class="live-status__description"><?php echo esc_html( $status_description ); ?></p>
									<?php if ( $stream_status['is_live'] ) : ?>
										<p class="live-status__meta">
											<?php echo esc_html( sprintf( '%s viewers', number_format_i18n( $viewer_count ) ) ); ?>
										</p>
									<?php else : ?>
										<p class="live-status__meta">
											<?php echo esc_html__( 'Check back soon for the next live stream.', 'brendon-core' ); ?>
										</p>
									<?php endif; ?>
									<p class="live-status__updated">
										<?php echo esc_html( sprintf( __( 'Updated %s', 'brendon-core' ), $status_updated ) ); ?>
									</p>
									<?php if ( ! $stream_status['is_live'] ) : ?>
										<p class="live-status__offline">
											<a href="<?php echo esc_url( "https://www.twitch.tv/{$channel}/videos?filter=archives" ); ?>" target="_blank" rel="noreferrer noopener">
												<?php esc_html_e( 'Watch the latest VODs', 'brendon-core' ); ?>
											</a>
										</p>
									<?php endif; ?>
								</div>
							</article>

							<article class="live-panel live-panel--schedule">
								<div class="live-panel__header">
									<span>Schedule</span>
								</div>
								<div class="live-panel__body">
									<ul class="live-schedule">
										<?php foreach ( $schedule_events as $event ) : ?>
											<li>
												<strong><?php echo esc_html( $event['day'] ?? '' ); ?></strong>
												<span><?php echo esc_html( $event['time'] ?? '' ); ?></span>
												<p><?php echo esc_html( $event['title'] ?? '' ); ?></p>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</article>

							<article class="live-panel live-panel--commands">
								<div class="live-panel__header">
									<span>Commands</span>
								</div>
								<div class="live-panel__body">
									<ul class="live-commands">
										<?php foreach ( $commands as $command ) : ?>
											<li>
												<strong><?php echo esc_html( $command['command'] ?? '' ); ?></strong>
												<p><?php echo esc_html( $command['description'] ?? '' ); ?></p>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</article>

							<article class="live-panel live-panel--links">
								<div class="live-panel__header">
									<span>Links</span>
								</div>
								<div class="live-panel__body">
									<ul class="live-links">
										<?php foreach ( $links as $link ) : ?>
											<li>
												<a href="<?php echo esc_url( $link['url'] ?? '' ); ?>" target="_blank" rel="noreferrer noopener">
													<?php echo esc_html( $link['label'] ?? '' ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							</article>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</main>

<?php
get_footer();
