<?php

namespace AnyComment\Integrations;

use AnyComment\Base\BaseObject;

/**
 * Class AnyCommentBuddyPress helps to handle mentions with "@" login based on provided text or list of recipients
 * directly.
 *
 * Usage example:
 *
 * ```php
 * $buddyPressMention = AnyComment\Integrations\AnyCommentBuddyPress();
 *
 * // This will send buddyPress mention to user with "username" as login
 * $buddyPressMention->sendMentionsByText('Some text @username');
 *
 * // Alternatively you can send in the following way:
 * $buddyPressMention->sendMentionsByRecipients(['@username']);
 * ```
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Integrations
 */
class AnyCommentBuddyPress extends BaseObject
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        add_action('bp_init', function () {
            if ( ! function_exists('bp_activity_add')) {
                require_once BP_PLUGIN_DIR . '/bp-activity/bp-activity-functions.php';
            }
        });
    }

    /**
     * Parses text for "@" mentions and send mentions when detected.
     *
     * Internally, it would use sendMentionsByRecipients() on parsed mentions.
     *
     * @param string $text Text to be used to detect "@" mentions. No notification would be sent when no mentions would
     * be detected.
     *
     * @return bool
     */
    public function sendMentionsByText($text)
    {
        if ( ! $this->isMentionModuleEnabled()) {
            return false;
        }

        $text = static::normalizeNotificationText($text);

        if (empty($text)) {
            return false;
        }

        $buddyPressUsernameRegex = '/@([a-zA-Z0-9-_.]{1,})/i';

        preg_match_all($buddyPressUsernameRegex, $text, $matches, PREG_SET_ORDER, 0);


        if (empty($matches)) {
            return false;
        }

        $recipients = [];

        foreach ($matches as $singleMatch) {
            if (isset($singleMatch[1]) && ! empty($singleMatch[1])) {
                $recipients[] = $singleMatch[1];
            }
        }

        return $this->sendMentionsByRecipients($recipients, $text);
    }

    /**
     * Sends mentions to provided list of recipients.
     *
     * @param array $recipients Non-associative list of username. Usernames can be with or without "@" sign.
     * @param string $text Text to send to recipients.
     *
     * @return bool
     */
    public function sendMentionsByRecipients($recipients, $text)
    {
        if ( ! $this->isMentionModuleEnabled() || ! is_array($recipients) || ! defined('BP_PLUGIN_DIR')) {
            return false;
        }

        $sentMentionCount = 0;
        foreach ($recipients as $username) {
            if ($this->sendMention($username, $text)) {
                $sentMentionCount++;
            }
        }

        return $sentMentionCount > 0;
    }

    /**
     * Check whether mentions enabled or not.
     *
     * @return bool
     */
    public function isMentionModuleEnabled()
    {
        return bp_activity_do_mentions();
    }

    /***
     * Sends single mention for provided username.
     *
     * Notification would not be sent, if user with provided username not found.
     *
     * @param string $username User login.
     * @param string $text Text send as mention.
     *
     * @return bool
     */
    protected function sendMention($username, $text)
    {
        $username = static::normalizeRecipientUsername($username);

        if ( ! empty($username)) {
            $user = get_user_by('login', $username);

            if ($user !== false) {
                $mention_id = bp_activity_add([
                    'user_id'   => $user->ID,
                    'component' => 'activity',
                    'type'      => 'activity_update',
                    'content'   => $text
                ]);

                if (is_int($mention_id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Strips all HTML tags from text, so it can plain text.
     *
     * @param string $text Text to be normalized.
     *
     * @return string
     */
    public static function normalizeNotificationText($text)
    {
        if (empty($text)) {
            return $text;
        }

        return wp_strip_all_tags($text);
    }

    /**
     * Normalizes username by removing spaces, newliens, "@" sign.
     *
     * @param string $username Username to be normalized.
     *
     * @return string
     */
    public static function normalizeRecipientUsername($username)
    {
        return trim($username, " \t\n\r\0\x0B@");
    }
}
