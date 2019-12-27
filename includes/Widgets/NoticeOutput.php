<?php

namespace AnyComment\Widgets;

use AnyComment\AnyCommentCore;
use AnyComment\Base\Notice;

/**
 * Class AnyCommentNoticeOutput is a widget which renders available notices.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Widgets
 */
class NoticeOutput
{
    /**
     * Renders available notices.
     */
    public static function render()
    {
        $noticeInstance = AnyCommentCore::instance()->getNotice();
        $notices        = $noticeInstance->get();

        if ( ! empty($notices)) {
            $noticeInstance->clear();

            $notice_html = '';

            foreach ($notices as $notice) {
                $notice_html .= static::render_single_notice($notice);
            }

            return $notice_html;
        }

        return '';
    }

    /**
     * Renders single notice.
     *
     * @param array $notice Single notice information. Expected to have 'type' and 'message' keys.
     *
     * @return string
     */
    protected static function render_single_notice($notice)
    {
        $type    = isset($notice['type']) ? $notice['type'] : null;
        $message = isset($notice['message']) ? esc_html($notice['message']) : null;

        if ( ! empty($type) && ! empty($message)) {
            return <<<HTML
<div class="notice notice-$type is-dismissible">
    <p>$message</p>
</div>
HTML;
        }

        return '';
    }
}