<?php

use \AnyComment\Helpers\AnyCommentTemplate;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

echo AnyCommentTemplate::render('comments');