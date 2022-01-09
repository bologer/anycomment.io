<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Web\BaseController;
use AnyComment\Import\HyperComments;

/**
 * Class ImportController helps to manage imports from different sources, e.g. Hypercomments.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment\Controller
 */
class ImportController extends BaseController
{
    /**
     * Handles logic for import from HyperComments.
     *
     * @param string $url URL to the XML file to be used for import.
     * @param bool $revert Flag whether to revert imported comments.
     *
     * @return bool
     */
    public function actionHypercomments($url, $revert = false)
    {
		$this->verifyNonce();

        $notice = AnyCommentCore::instance()->getNotice();

        if ($revert !== null && boolval($revert)) {
            $notice->success(__('Comments from HyperComments were removed.', 'anycomment'));
        } else {
            $hc = new HyperComments(sanitize_text_field($url));

            $hc->process();

            $notice->success(__('Comments were imported successfully.', 'anycomment'));
        }

        return $this->redirect(['tab' => 'tools']);
    }
}
