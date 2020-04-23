<?php

namespace AnyComment;

use AnyComment\Admin\AnyCommentGenericSettings;
use AnyComment\Cache\AnyCommentCacheManager;
use AnyComment\Models\AnyCommentLikes;
use AnyComment\Models\AnyCommentRating;

/**
 * Class AnyCommentSeoFriendly helps to process comments and turn them into plain HTML with valid schema.org mark-up.
 *
 * This HTML comments are then rendered on the website to achieve SEO friendliness.
 *
 * @author Alexander Teshabaev <sasha.tesh@gmail.com>
 * @package AnyComment
 */
class AnyCommentSeoFriendly
{
    /**
     * @var int|null Post id.
     */
    private $_post_id;
    /**
     * @var array List of arguments passed down to WP_Comment_Query.
     * @see \WP_Comment_Query
     */
    private $_args = ['status' => 'approve'];

    /**
     * AnyCommentSeoFriendly constructor.
     * @param int $post_id Post ID to generate SEO structures.
     */
    public function __construct($post_id)
    {
        if (is_numeric($post_id)) {
            $this->_post_id = $post_id;
            $this->_args = array_merge($this->_args, ['post_id' => $post_id]);
        }
    }

    /**
     * Renders list of comments.
     *
     * @return mixed|string
     */
    public function render()
    {
        if (!is_numeric($this->_post_id)) {
            return '';
        }

        $this->_args = array_merge($this->_args, [
            'orderby' => 'comment_date_gmt',
            'order' => AnyCommentGenericSettings::get_seo_sorting() === AnyCommentGenericSettings::SEO_SORTING_NEW2OLD ?
                'DESC' :
                'ASC',
            'number' => AnyCommentGenericSettings::get_seo_limit()
        ]);

        $cacheKey = AnyCommentCacheManager::getRootNamespace() . '/seo/' . md5(serialize($this->_args));

        $cacheItem = AnyCommentCore::cache()->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $html = '';
//        $html .= $this->prepare_rating();
        $html .= $this->prepare_comments();

        $cacheItem
            ->set($html)
            ->expiresAfter(strtotime('+1 day') - time())
            ->save();

        return $html;
    }

    /**
     * Prepares rating.
     * todo: make configuration to toggle it. Currently this is off, because Google puts sanctions on websites with it
     * @return string
     */
    public function prepare_rating()
    {
        if (!AnyCommentGenericSettings::is_rating_on()) {
            return '';
        }

        $rating_value = AnyCommentRating::get_average_by_post($this->_post_id);
        $review_count = AnyCommentRating::get_count_by_post($this->_post_id);

        $post = get_post($this->_post_id);

        $post_name = $post->post_title;
        $post_url = get_permalink($post);

        return <<<HTML
<div itemscope itemtype="http://schema.org/Product">
    <meta itemprop="name" content="$post_name">
    <meta itemprop="url" content="$post_url">
    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
        <span itemprop="ratingValue">$rating_value</span>
        <span itemprop="reviewCount">$review_count</span>
    </div>
</div>
HTML;
    }

    /**
     * Prepares comments.
     *
     * @return string
     */
    public function prepare_comments()
    {
        $comments_query = new \WP_Comment_Query;
        $comments = $comments_query->query($this->_args);

        $html = '<ul>';

        foreach ($comments as $key => $comment) {
            $html .= $this->prepare_comment($comment);
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Prepares single comment HTML.
     *
     * @param \WP_Comment $comment
     * @return string Formatted HTML
     */
    public function prepare_comment($comment)
    {

        $rating_summary = AnyCommentLikes::get_summary($comment->comment_ID);
        $date_created = date('c', strtotime($comment->comment_date));

        return <<<HTML
<li itemtype="http://schema.org/Comment" itemscope="">
    <div>
        <div itemprop="upvoteCount">$rating_summary->likes</div>
        <div itemprop="downvoteCount">$rating_summary->dislikes</div>
        
        <time itemprop="dateCreated" datetime="$date_created" itemprop="dateCreated">$date_created</time>
    </div>
    <div>
        <p itemprop="creator" itemscope itemtype="http://schema.org/Person">
            <span itemprop="name"><a href="$comment->comment_author_url" rel="external nofollow" itemprop="url">$comment->comment_author</a></span>
        </p>
    </div>
    <div itemprop="text">$comment->comment_content</div>
</li>
HTML;

    }
}
