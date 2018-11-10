<div class="anycomment-tab">
    <h2><?php echo __( 'Need Help', 'anycomment' ) ?></h2>
    <p><?php echo __( 'Easiest way to get help is to use one of the links below', 'anycomment' ) ?></p>
    <ul>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getVkontakte() ?>"
               target="_blank"><?php echo __( 'Vkontakte Group', 'anycomment' ) ?></a></li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getTelegram() ?>"
               target="_blank"><?php echo __( 'Telegram Channel', 'anycomment' ) ?></a></li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getGitHub() ?>"
               target="_blank"><?php echo __( 'GitHub Repository', 'anycomment' ) ?></a></li>
    </ul>

    <h2><?php echo __( 'Resources', 'anycomment' ) ?></h2>
    <ul>
        <li>
            <a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getOfficialWebsite() ?>"><?php echo __( 'Official Website', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getSocialGuidesLink() ?>"><?php echo __( 'Set-up Socials', 'anycomment' ) ?></a>
        </li>
        <li><a href="<?php echo \AnyComment\Helpers\AnyCommentLinkHelper::getGuidesLink() ?>"><?php echo __( 'All Guides', 'anycomment' ) ?></a></li>
    </ul>
</div>
