<?php

use AnyComment\Helpers\AnyCommentTemplate;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!is_singular() || !comments_open() || post_password_required()) {
    return;
}

echo AnyCommentTemplate::render('comments');