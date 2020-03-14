<div class="anycomment-tab">
    <h2><?php echo __( 'Need Help', 'anycomment' ) ?></h2>

    <p><?php echo __( 'Easiest way to get help is to use one of the links below', 'anycomment' ) ?></p>
    <ul>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_telegram() ?>"
               target="_blank"><?php echo __( 'Telegram Channel', 'anycomment' ) ?></a> <?php echo sprintf( __( 'or use this name "%s" to search', 'anycomment' ), \AnyComment\Helpers\AnyCommentLinkHelper::get_telegram_slug( true ) ) ?>
        </li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_wp_forum() ?>"
               target="_blank"><?php echo __( 'WordPress Forum', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_github() ?>"
               target="_blank"><?php echo __( 'GitHub Repository', 'anycomment' ) ?></a></li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_vkontakte() ?>"
               target="_blank"><?php echo __( 'Vkontakte Group', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?php echo __( 'Resources', 'anycomment' ) ?></h2>
    <ul>
        <li>
            <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_official_website() ?>"><?php echo __( 'Official Website', 'anycomment' ) ?></a>
        </li>
        <li>
            <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_social_guides() ?>"><?php echo __( 'Set-up Socials', 'anycomment' ) ?></a>
        </li>
        <li>
            <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_guides() ?>"><?php echo __( 'All Guides', 'anycomment' ) ?></a>
        </li>
        <li>
            <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::get_demo() ?>"><?php echo __( 'Demo', 'anycomment' ) ?></a>
        </li>
    </ul>
</div>
