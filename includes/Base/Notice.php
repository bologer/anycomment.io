<?php

namespace AnyComment\Base;

/**
 * Class AnyCommentNotices helps to manage notices.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Base
 */
class Notice
{
    const TRANSIENT_KEY = 'anycomment_notice';

    /**
     * Get transient value.
     *
     * @return mixed
     */
    public function get()
    {
        return get_transient(self::TRANSIENT_KEY);
    }

    /**
     * Delete transient.
     *
     * @return bool
     */
    public function clear()
    {
        return delete_transient(self::TRANSIENT_KEY);
    }

    /**
     * Adds new notice to the list.
     *
     * @param string $type Notice type.
     * @param string $message
     */
    public function add($type, $message)
    {
        $current_value = $this->get();

        if ( ! is_array($current_value)) {
            $current_value = [['type' => $type, 'message' => $message]];
        } else {
            $current_value[] = ['type' => $type, 'message' => $message];
        }

        set_transient(self::TRANSIENT_KEY, $current_value);
    }

    /**
     * Adds new success to the notice list.
     *
     * @param string $message Success message.
     */
    public function success($message)
    {
        $this->add('success', $message);
    }

    /**
     * Adds new error to the notice list.
     *
     * @param string $message Error message.
     */
    public function error($message)
    {
        $this->add('error', $message);
    }
}
