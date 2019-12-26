<?php

use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Helpers\AnyCommentTemplate;
use AnyComment\Libraries\AnyCommentUserTour;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';
?>

    <div class="wrap">
        <h2><?php echo __('Dashboard', 'anycomment') ?></h2>

        <div id="anycomment-wrapper" data-intro-show="<?= AnyCommentUserTour::was_disabled($current_tab) ? '0' : '1' ?>"
             data-current-tab="<?php echo(isset($_GET['page']) ? sanitize_text_field($_GET['tab']) : '') ?>">
            <div class="anycomment-notice anycomment-success">
                <?php echo sprintf(__('Please take a few seconds and <a href="%s" target="_blank">rate us on WordPress.org</a>. You are the one who can help to grow and make this plugin better!',
                    'anycomment'), 'https://wordpress.org/support/plugin/anycomment/reviews/?rate=5#new-post') ?>
            </div>

            <div class="anycomment-dashboard grid-x">
                <div class="cell auto anycomment-dashboard__container">
                    <header class="grid-x anycomment-dashboard__header">
                        <div class="cell auto anycomment-dashboard__header-logo">
                            <img src="<?php echo AnyComment()->plugin_url() . '/assets/img/mini-logo.svg' ?>"
                                 alt="<?php echo __('AnyComment', 'anycomment') ?>">
                            <h2><?php echo __('AnyComment', 'anycomment') ?>
                                &nbsp;<sup><?php echo AnyComment()->version ?></sup></h2>
                        </div>
                        <div class="cell auto anycomment-dashboard__header-official">
                            <?php

                            $links = [
                                'telegram'  => [
                                    'text' => __('Telegram', 'anycomment'),
                                    'url'  => AnyCommentLinkHelper::get_telegram(),
                                ],
                                'vkontakte' => [
                                    'text' => __('Vkontakte', 'anycomment'),
                                    'url'  => AnyCommentLinkHelper::get_vkontakte(),
                                ],
                                'website'   => [
                                    'text' => __('Website', 'anycomment'),
                                    'url'  => AnyCommentLinkHelper::get_official_website(),
                                ],
                                'github'    => [
                                    'text' => __('GitHub', 'anycomment'),
                                    'url'  => AnyCommentLinkHelper::get_github(),
                                ],
                            ];

                            $i = 0;
                            foreach ($links as $link) {
                                $separator = $i !== 0 ? ' · ' : '';
                                echo $separator . '<a href="' . $link["url"] . '" target="_blank">' . $link['text'] . '</a>';
                                $i++;
                            }
                            ?>
                        </div>
                    </header>

                    <?php echo AnyCommentTemplate::render('admin/tabs') ?>

                    <div class="anycomment-dashboard__splitter anycomment-dashboard__splitter-right-space anycomment-dashboard__splitter-possible-problems">
                        <h2><?php echo __('Possible Problems', 'anycomment') ?></h2>
                        <?php echo AnyCommentTemplate::render('admin/notifications') ?>
                    </div>
                </div>
                <aside class="large-3 medium-12 anycomment-dashboard__sidebar">
                    <?php echo AnyCommentTemplate::render('admin/add-ons') ?>
                    <?php echo AnyCommentTemplate::render('admin/help-sidebar') ?>
                    <?php echo AnyCommentTemplate::render('admin/news-sidebar') ?>
                </aside>
            </div>
        </div>
    </div>

<?php echo AnyCommentTemplate::render('admin/tour-steps') ?>