<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Web\BaseController;
use AnyComment\Helpers\AnyCommentManipulatorHelper;

/**
 * Class ToolController handles tools related logic.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Controller
 */
class ToolController extends BaseController
{
    /**
     * Opens comments per specified type.
     *
     * @param $type
     *
     * @return bool
     */
    public function actionOpenComments($type)
    {
		$this->verifyNonce();
        $notice = AnyCommentCore::instance()->getNotice();

        switch ($type) {
            case 'all':
                AnyCommentManipulatorHelper::open_all_comments();
                break;
            case 'posts':
                AnyCommentManipulatorHelper::open_all_post_comments();
                break;
            case 'pages':
                AnyCommentManipulatorHelper::open_all_page_comments();
                break;
            case 'wc-products':
                AnyCommentManipulatorHelper::open_all_product_comments();
                break;
            default:
                $notice->error(__('Failed to open comments - unknown type.', 'anycomment'));

                return $this->redirect(['tab' => 'tools']);
        }

        $notice->success(__('Comments opened successfully.', 'anycomment'));

        return $this->redirect(['tab' => 'tools']);
    }
}
