<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'anycomment-dashboard';
?>

<div class="wrap">
    <h2><?= __('Dashboard', 'anycomment') ?></h2>

    <div class="anycomment-dashboard">
        <div class="anycomment-dashboard__container">
            <header class="anycomment-dashboard__header">
                <div class="anycomment-dashboard__header-logo">
                    <img src="<?= AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                         alt="<?= __('AnyComment', 'anycomment') ?>">
                    <h2><?= __('AnyComment', 'anycomment') ?></h2>
                </div>

                <div class="anycomment-dashboard__header-official">
                    <a href="https://anycomment.io" target="_blank"><?= __('Official Website', 'anycomment') ?></a>
                </div>

                <div class="clearfix"></div>
            </header>

            <div class="anycomment-dashboard__tabs" style="display: none">
                <ul>
                    <li><a href="#"><?= __('Dashboard', 'anycomment') ?></a></li>
                    <li><a href="#"><?= __('Social', 'anycomment') ?></a></li>
                    <li><a href="#"><?= __('Settings', 'anycomment') ?></a></li>
                </ul>
                <div class="clearfix"></div>
            </div>


            <div class="anycomment-dashboard__splitter">
                <div class="anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-commentators">
                    <div class="anycomment-dashboard__splitter-half-center">
                        <img src="<?= AnyComment()->plugin_url() . '/assets/img/dashboard-users.svg' ?>"
                             alt="<?= __('Commentators', 'anycomment') ?>">

                        <div class="anycomment-dashboard__splitter-half-description">
                            <span><?= AnyComment()->statistics->get_commentor_count() ?></span>
                            <span><?= __('Commentators', 'anycomment') ?></span>
                        </div>
                    </div>
                </div>
                <div class="anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-comments">
                    <div class="anycomment-dashboard__splitter-half-center">
                        <img src="<?= AnyComment()->plugin_url() . '/assets/img/dashboard-comments.svg' ?>"
                             alt="<?= __('All Comments', 'anycomment') ?>">
                        <div class="anycomment-dashboard__splitter-half-description">
                            <span><?= AnyComment()->statistics->get_approved_comment_count() ?></span>
                            <span><?= __('All Comments', 'anycomment') ?></span>
                        </div>
                    </div>
                </div>

                <div class="clearfix"></div>
            </div>

            <div class="anycomment-dashboard__statistics">
                <div class="anycomment-dashboard__statistics-graph">
                    <h2><?= __('Overal Statistics', 'anycomment') ?></h2>
                    <?php

                    $comments = AnyComment()->statistics->get_comment_data();
                    $users = AnyComment()->statistics->get_commentor_data();
                    ?>
                    <canvas id="anycomment-dashboard-chart"></canvas>

                    <script>
                        let chart = setInterval(function () {

                            if ('Chart' in window) {
                                Chart.defaults.global.defaultFontColor = '#c8c8c8';
                                Chart.defaults.global.defaultFontFamily = 'Roboto, Verdana, sans-serif';
                                Chart.defaults.global.defaultFontSize = 18;

                                // legend
                                Chart.defaults.global.legend.position = 'bottom'
                                let c = new Chart(document.getElementById("anycomment-dashboard-chart").getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: <?= $comments['label'] ?>,
                                        datasets: [
                                            {
                                                label: '<?= __('Comments', 'anycomment') ?>',
                                                data: <?= $comments['data'] ?>,
                                                fill: false,
                                                borderColor: '#f1b927',
                                                borderWidth: 5,
                                                lineTension: 0,
                                                borderJoinStyle: 'miter',
                                                pointRadius: 0,
                                                pointHitRadius: 30,
                                            },
                                            {
                                                label: '<?= __('Users', 'anycomment') ?>',
                                                data: <?= $users['data'] ?>,
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
                                            xAxes: [{
                                                display: false,
                                                gridLines: '#f1f1f1'
                                            }],
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
                <div class="anycomment-dashboard__statistics-userlist">
                    <h2><?= __('Most Active Users', 'anycomment') ?></h2>
                    <?php if (!empty($users = AnyComment()->statistics->get_most_active_users())): ?>
                        <ul>
                            <?php foreach ($users as $user): ?>
                                <li>
                                    <span class="anycomment-dashboard__statistics-userlist-avatar"
                                          style="background-image:url('<?= AnyComment()->auth->get_user_avatar_url($user->user_id) ?>')"></span>
                                    <?= $user->name ?>
                                    <span class="anycomment-dashboard__statistics-userlist-counter"><?= $user->comment_count ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p><?= __('No users yet', 'anycomment') ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <aside class="anycomment-dashboard__sidebar">
            <h2><?= __('News', 'anycomment') ?></h2>
            <ul class="anycomment-dashboard__sidebar-news">
                <?php
                $posts = AnyComment()->admin_pages->get_news();

                if ($posts !== null):
                    foreach ($posts as $key => $post): ?>
                        <li>
                            <a href="<?= $post['link'] ?>"
                               target="_blank"
                               class="anycomment-dashboard__sidebar-news-title"><?= esc_html($post['title']['rendered']) ?></a>
                            <div class="anycomment-dashboard__sidebar-news-summary"><?= $post['content']['rendered'] ?></div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><?= __('No news yet', 'anycomment') ?></li>
                <?php endif; ?>
            </ul>
        </aside>

        <div class="clearfix"></div>
    </div>
</div>