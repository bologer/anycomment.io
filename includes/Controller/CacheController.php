<?php

namespace AnyComment\Controller;

use AnyComment\AnyCommentCore;
use AnyComment\Web\BaseController;
use AnyComment\Cache\AnyCommentCacheManager;
use AnyComment\Cache\AnyCommentRestCacheManager;

class CacheController extends BaseController
{
    /**
     * Handles download of debug report.
     */
    public function actionFlush($type)
    {
        switch ($type) {
            case 'all':
                AnyCommentCacheManager::flushAll();
                break;
            case 'rest':
                AnyCommentRestCacheManager::flush();
                break;
            default:
        }

        AnyCommentCore::$app->getNotice()->success(__('Cache was successfully flushed!', 'anycomment'));

        return $this->redirect(['tab' => 'tools']);
    }
}