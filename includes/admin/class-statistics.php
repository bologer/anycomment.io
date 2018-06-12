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

        public function get_most_active_users($limit = 6)
        {
            global $wpdb;

            $query = "SELECT users.ID AS user_id, users.`display_name` AS name, COUNT(comments.comment_ID) AS comment_count 
FROM $wpdb->comments AS comments 
RIGHT JOIN $wpdb->users AS users ON users.id = comments.user_id 
GROUP BY user_id
ORDER BY comment_count DESC 
LIMIT $limit";

//            var_dump($query);
            return $wpdb->get_results($query);
        }

        public function getCommentData()
        {
            global $wpdb;

            $query = "SELECT COUNT(comment_ID) as count, DATE_FORMAT(comment_date, '%d') as day 
FROM $wpdb->comments 
WHERE MONTH(comment_date) = MONTH(NOW())
GROUP BY DAY(comment_date) 
ORDER BY day ASC";

            return static::prepare_data($query);
        }

        public function get_commentor_data()
        {
            global $wpdb;

            $query = "SELECT COUNT(ID) as count, DATE_FORMAT(user_registered, '%d') as day 
FROM $wpdb->users 
WHERE MONTH(user_registered) = MONTH(NOW())
GROUP BY DAY(user_registered) 
ORDER BY day ASC";

            return static::prepare_data($query, $type = 'month');
        }

        /**
         * Prepare data to be displayed in a chart.
         * @param string $query Query to be executed.
         * @return array See description below:
         * - label - list of labels
         * - data - list of data for labels
         */
        public static function prepare_data($query)
        {
            global $wpdb;

            $queryResult = $wpdb->get_results($query);

            $labels = [];
            $data = [];

            foreach ($queryResult as $result) {
                $labels[] = $result->day;
                $data[] = $result->count;
            }

            // Default get number of days in a month
            $symbol = 't';

            if ($type = 'month') {
                $symbol = 't';
            } elseif ($type = 'year') {
                $symbol = 'z';
            }

            // Fill 0 for empty days yet
            $expectedCount = date($symbol);
            if (count($data) < $expectedCount) {
                $moreToAdd = $expectedCount - count($data);

                for ($i = 0; $i < $moreToAdd; $i++) {
                    $data[] = 0;
                }
            }

            // Return prepared for graph
            return [
                'label' => json_encode($labels),
                'data' => json_encode($data)
            ];
        }
    }
endif;

