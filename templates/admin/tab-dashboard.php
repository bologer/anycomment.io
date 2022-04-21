<?php

use AnyComment\AnyCommentServiceApi;
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Cron\AnyCommentServiceSyncCron;
use AnyComment\Admin\AnyCommentStatistics;

?>

<div class="anycomment-tab anycomment-dashboard__tab" id="anycomment-dashboard">

    <div class="grid-x anycomment-dashboard__splitter">
        <div class="cell large-6 medium-12 small-12 anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-commentators">
            <div class="grid-x align-center">
                <div class="cell shrink">
                    <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/main-character.svg' ?>"
                         alt="<?php echo __( 'Commentators', 'anycomment' ) ?>"><br><br>

                    <div class="anycomment-dashboard__splitter-half-description">
                        <span><?php echo AnyCommentStatistics::get_commentor_count() ?></span>
                        <span><?php echo __( 'Commentators', 'anycomment' ) ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="cell large-6 medium-12 small-12 anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-comments">
            <div class="grid-x align-center">
                <div class="cell shrink">
                    <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/dashboard-comments.svg' ?>"
                         alt="<?php echo __( 'All Comments', 'anycomment' ) ?>">
                    <div class="anycomment-dashboard__splitter-half-description">
                        <span><?php echo AnyCommentStatistics::get_approved_comment_count() ?></span>
                        <span><?php echo __( 'All Comments', 'anycomment' ) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-x grid-margin-x anycomment-dashboard__statistics">
        <div class="cell auto anycomment-dashboard__statistics-graph">
            <h2><?php echo __( 'Overal Statistics', 'anycomment' ) ?></h2>
			<?php
			$comments          = AnyCommentStatistics::get_comment_data();
			$comments['label'] = array_map( 'esc_html', $comments['label'] );
			$comments['data']  = array_map( 'esc_html', $comments['data'] );

			$users          = AnyCommentStatistics::get_commentor_data();
			$users['label'] = array_map( 'esc_html', $users['label'] );
			$users['data']  = array_map( 'esc_html', $users['data'] );
			?>
            <canvas id="anycomment-dashboard-chart"></canvas>

            <script>
                let chart = setInterval(function() {

                    if ('Chart' in window) {
                        new Chart(document.getElementById("anycomment-dashboard-chart").getContext('2d'), {
                            type: 'line',
                            data: {
                                labels: <?php echo json_encode( $comments['label'] ) ?>,
                                datasets: [
                                    {
                                        label: '<?php echo __( 'Comments', 'anycomment' ) ?>',
                                        data: <?php echo json_encode( $comments['data'] ) ?>,
                                        fill: false,
                                        borderColor: '#f1b927',
                                        borderWidth: 5,
                                        lineTension: 0,
                                        borderJoinStyle: 'miter',
                                        pointRadius: 0,
                                        pointHitRadius: 30,
                                    },
                                    {
                                        label: '<?php echo __( 'Users', 'anycomment' ) ?>',
                                        data: <?php echo json_encode( $users['data'] ) ?>,
                                        fill: false,
                                        borderColor: '#ec4568',
                                        borderWidth: 5,
                                        lineTension: 0,
                                        borderJoinStyle: 'miter',
                                        pointRadius: 0,
                                        pointHitRadius: 30,
                                    },
                                ]
                            },
                            options: {
                                layout: {
                                    padding: {
                                        left: 10,
                                        right: 10,
                                        top: 0,
                                        bottom: 0
                                    }
                                },
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });

                        clearInterval(chart);
                    }
                }, 1000);
            </script>
        </div>
        <div class="large-4 medium-4 small-12 anycomment-dashboard__statistics-userlist">
            <h2><?php echo __( 'Most Active Users', 'anycomment' ) ?></h2>
			<?php
			$users = AnyCommentStatistics::get_most_active_users();

			if ( ! empty( $users ) ): ?>
                <ul>
					<?php foreach ( $users as $user ): ?>
                        <li> <span class="anycomment-dashboard__statistics-userlist-avatar"
                                   style="background-image:url('<?php echo \AnyComment\Rest\AnyCommentSocialAuth::get_user_avatar_url( $user->user_id ) ?>')"></span>
							<?php echo esc_html($user->name) ?>
                            <span class="anycomment-dashboard__statistics-userlist-counter"><?php echo esc_html($user->comment_count) ?></span>
                        </li>
					<?php endforeach; ?>
                </ul>
			<?php else: ?>
                <p><?php echo __( 'No users yet', 'anycomment' ) ?></p>
			<?php endif; ?>
        </div>
    </div>
</div>
