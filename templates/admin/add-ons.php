<?php

use AnyComment\Helpers\AnyCommentLinkHelper;

if (class_exists('\AnyCommentAnalytics')): ?>
    <div class="anycomment-dashboard__sidebar--widget anycomment-dashboard__sidebar--promo">
        <div class="anycomment-dashboard__sidebar--promo-hinter">
            <?php echo __('Add-On', 'anycomment') ?>
        </div>
        <h2><?php echo __('AnyComment Analytics', 'anycomment') ?></h2>
        <?php


        $language = AnyCommentLinkHelper::get_language();

        switch ($language) {
            // List of translated languages
            case 'ru':
                break;
            default:
                $language = '';
        }

        $url = sprintf('https://%swordpress.org/plugins/anycomment-analytics/', $language);
        ?>
        <p class="anycomment-addon-p">
            <?php esc_html_e('It is an advanced analytics for AnyComment & it is free!',
                'anycomment'); ?>
        </p>
        <p class="anycomment-addon-button-p">
            <a class="anycomment-addon-button"
               href="<?php echo $url ?>"><?php esc_html_e('Download', 'anycomment'); ?></a>
        </p>
    </div>

    <style>
        #anycomment-wrapper .anycomment-dashboard__sidebar--promo {
            background-color: #ec4568;
            background-size: cover;
            background-repeat: no-repeat;
            padding: 10px 15px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            position: relative;;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo-hinter {
            position: absolute;
            top: -10px;
            right: 12px;
            background-color: #FFEB3B;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            padding: 2px 10px;
            color: #000;
            font-style: italic;
            font-size: 12px;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo h2 {
            font-size: 18pt;
            font-weight: 500;
            margin: 0;
            padding: 10px 0 10px 0;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-p {
            font-size: 11pt;
            font-weight: normal;
            color: #fff;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-button-p {
            text-align: center;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-button {
            background-color: #2196F3;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            font-family: inherit;
            padding: 0.7em 3em;
            -webkit-appearance: none;
            border-radius: 0;
            -webkit-transition: background-color .25s ease-out, color .25s ease-out;
            transition: background-color .25s ease-out, color .25s ease-out;
            font-size: 16px;
            line-height: 1;
            text-align: center;
            cursor: pointer;
            color: #fff;
            font-weight: bold;
        }
    </style>
<?php endif; ?>



