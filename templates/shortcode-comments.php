<?php

use \AnyComment\Helpers\AnyCommentTemplate;

/**
 * This template is used to display comments.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

echo AnyCommentTemplate::render('comments');