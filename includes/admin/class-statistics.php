<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AC_Statistics')) :
    /**
     * AC_Statistics helps to process statistics.
     */
    class AC_Statistics
    {
        /**
         * AC_Statistics constructor.
         */
        public function __construct()
        {
        }


        /**
         * Get number of users in the system.
         * @return int
         */
        public function getCommentorCount()
        {
            global $wpdb;

            return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users");
        }

        /**
         * Get approved comment count.
         * @return int
         */
        public function getApprovedCommentCount()
        {
            global $wpdb;

            return $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments WHERE `comment_approved`=1");
        }

        public function getCommentData()
        {
            global $wpdb;

            $query = "SELECT COUNT(comment_ID) as count, DATE_FORMAT(comment_date, '%d') as day 
FROM $wpdb->comments 
WHERE MONTH(comment_date) = MONTH(NOW())
GROUP BY DAY(comment_date) 
ORDER BY day DESC";

            $res = $wpdb->get_results($query);

            $labels = [];
            $data = [];

            foreach ($res as $r) {
                $labels[] = $r->day;
                $data[] = $r->count;
            }

            return [
                'label' => json_encode($labels),
                'data' => json_encode($data)
            ];
        }
    }
endif;

