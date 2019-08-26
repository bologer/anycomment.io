<?php
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Cron\AnyCommentServiceSyncCron;
use AnyComment\Admin\AnyCommentStatistics;
?>

<div class="anycomment-tab anycomment-dashboard__tab" id="anycomment-dashboard">

    <?php if (AnyCommentLinkHelper::get_language() === 'ru'): ?>
        <div class="anycomment-dashboard__splitter">
            <div class="grid-x">
                <div class="small-12">
                    <br>
                    <div class="callout success">
                        <h3>Новинка! У AnyComment <a href="https://anycomment.io" target="_blank">появилась облачная
                                версия</a>!</h3>
                        <p>Теперь AnyComment будет иметь две версии (1) как сейчас и (2) как сервис.</p>
                        <p>Если говорить простым языком, AnyComment начнет работать как Disqus или любое другое облачное
                            решение, только в разы быстрее, с отличной поддержкой, понимаением, прозрачностью и
                            обратной связью со своей аудиторией.</p>

                        <p><a class="button" href="https://anycomment.io" target="_blank">Подробнее</a></p>

                        <h4>Статус комментариев:</h4>

                        <?php

                        if (AnyCommentServiceSyncCron::isSyncReady()):
                            $syncInfo = AnyCommentServiceSyncCron::getSyncInfo();

                            if ($syncInfo['complete_percent'] === 100): ?>
                                <p>Все комментарии синхронизированы!</p>
                            <?php else: ?>
                                <p><?php echo sprintf('Обработано %s из %s комментариев, завершено на %s%%', $syncInfo['current'], $syncInfo['total'], $syncInfo['complete_percent']) ?></p>
                            <?php endif; ?>

                        <?php else: ?>
                            <p>У вас еще не настроены синхронизация с сервисом. Для этого вам нужно:</p>
                            <ul>
                                <li>- Добавить свой сайт на <a
                                            href="<?php echo AnyCommentLinkHelper::get_service_website() ?>"
                                            target="_blank">сервисе</a>
                                </li>
                                <li>- Зайти на страницу сайта, под заголовком "Настройка синхронизации" нажмите на
                                    кнопку "Настроить"
                                </li>
                                <li>- Далее следуйте инструкции на странице "Синхронизация"</li>
                                <li>- После завершения вместо этого сообщения должна появиться
                                    информация по синхронизации комментариев
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <div class="grid-x anycomment-dashboard__splitter">
        <div class="cell large-6 medium-12 small-12 anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-commentators">
            <div class="grid-x align-center">
                <div class="cell shrink">
                    <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/dashboard-users.svg' ?>"
                         alt="<?php echo __('Commentators', 'anycomment') ?>">

                    <div class="anycomment-dashboard__splitter-half-description">
                        <span><?php echo AnyCommentStatistics::get_commentor_count() ?></span>
                        <span><?php echo __('Commentators', 'anycomment') ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="cell large-6 medium-12 small-12 anycomment-dashboard__splitter-half anycomment-dashboard__splitter-half-comments">
            <div class="grid-x align-center">
                <div class="cell shrink">
                    <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/dashboard-comments.svg' ?>"
                         alt="<?php echo __('All Comments', 'anycomment') ?>">
                    <div class="anycomment-dashboard__splitter-half-description">
                        <span><?php echo AnyCommentStatistics::get_approved_comment_count() ?></span>
                        <span><?php echo __('All Comments', 'anycomment') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-x grid-margin-x anycomment-dashboard__statistics">
        <div class="cell auto anycomment-dashboard__statistics-graph">
            <h2><?php echo __('Overal Statistics', 'anycomment') ?></h2>
            <?php

            $comments = AnyCommentStatistics::get_comment_data();
            $users = AnyCommentStatistics::get_commentor_data();
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
                                labels: <?php echo $comments['label'] ?>,
                                datasets: [
                                    {
                                        label: '<?php echo __('Comments', 'anycomment') ?>',
                                        data: <?php echo $comments['data'] ?>,
                                        fill: false,
                                        borderColor: '#f1b927',
                                        borderWidth: 5,
                                        lineTension: 0,
                                        borderJoinStyle: 'miter',
                                        pointRadius: 0,
                                        pointHitRadius: 30,
                                    },
                                    {
                                        label: '<?php echo __('Users', 'anycomment') ?>',
                                        data: <?php echo $users['data'] ?>,
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
        <div class="large-4 medium-4 small-12 anycomment-dashboard__statistics-userlist">
            <h2><?php echo __('Most Active Users', 'anycomment') ?></h2>
            <?php
            $users = AnyCommentStatistics::get_most_active_users();

            if (!empty($users)): ?>
                <ul>
                    <?php foreach ($users as $user): ?>
                        <li> <span class="anycomment-dashboard__statistics-userlist-avatar"
                                   style="background-image:url('<?php echo \AnyComment\Rest\AnyCommentSocialAuth::get_user_avatar_url($user->user_id) ?>')"></span>
                            <?php echo $user->name ?>
                            <span class="anycomment-dashboard__statistics-userlist-counter"><?php echo $user->comment_count ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p><?php echo __('No users yet', 'anycomment') ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>