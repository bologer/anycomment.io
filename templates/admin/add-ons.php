<?php if ( ! class_exists( '\AnyCommentAnalytics' ) ): ?>
    <div class="anycomment-dashboard__sidebar--widget anycomment-dashboard__sidebar--promo">
        <div class="anycomment-dashboard__sidebar--promo-hinter">
			<?php echo __( 'Add-On', 'anycomment' ) ?>
        </div>
        <h2><?php echo __( 'AnyComment Analytics', 'anycomment' ) ?></h2>
		<?php

		$tries = [
			'paragraphs' => [
				esc_html__( 'Get to know your website via in-depth analytics for only $9.99!', 'anycomment' ),
				esc_html__( 'All analytical data you wanted to know in one add-on!', 'anycomment' ),
				esc_html__( 'Nothing supports AnyComment better then buying an add-on!', 'anycomment' ),
				esc_html__( 'In-depth analytics about comments, users and posts. Only for $9.99.', 'anycomment' ),
				esc_html__( 'All analytical data of AnyComment summarized, prepared and visualized as graphs!', 'anycomment' ),
			],
			'buttons'    => [
				esc_html__( 'Buy', 'anycomment' ),
				esc_html__( 'Details', 'anycomment' ),
				esc_html__( 'More', 'anycomment' ),
				esc_html__( 'Purchase', 'anycomment' ),
				esc_html__( 'Learn more', 'anycomment' ),
			],
		];

		$chosen_key = rand( 0, count( $tries['paragraphs'] ) - 1 );

		$query_params = http_build_query( [
			'utm_source'   => 'AnyComment',
			'utm_medium'   => 'plugin',
			'utm_campaign' => 'sidebar_promo',
			'utm_term'     => 'p' . $chosen_key,
			'utm_content'  => 'button',
		] );

		$url = 'https://anycomment.io/analytics/?' . $query_params;
		?>
        <p class="anycomment-addon-p"><?php echo $tries['paragraphs'][ $chosen_key ] ?></p>
        <p class="anycomment-addon-button-p">
            <a class="anycomment-addon-button"
               href="<?php echo $url ?>"><?php echo $tries['buttons'][ $chosen_key ] ?></a>
        </p>
    </div>

    <style>
        #anycomment-wrapper .anycomment-dashboard__sidebar--promo {
            background-color: #ec4568;
            background-size: cover;
            background-repeat: no-repeat;
            padding: 10px 15px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            position: relative;;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo-hinter {
            position: absolute;
            top: -10px;
            right: 12px;
            background-color: #FFEB3B;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            padding: 2px 10px;
            color: #000;
            font-style: italic;
            font-size: 12px;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo h2 {
            font-size: 18pt;
            font-weight: 500;
            margin: 0;
            padding: 10px 0 10px 0;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-p {
            font-size: 11pt;
            font-weight: normal;
            color: #fff;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-button-p {
            text-align: center;
        }

        #anycomment-wrapper .anycomment-dashboard__sidebar--promo .anycomment-addon-button {
            background-color: #2196F3;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            font-family: inherit;
            padding: 0.7em 3em;
            -webkit-appearance: none;
            border-radius: 0;
            -webkit-transition: background-color .25s ease-out, color .25s ease-out;
            transition: background-color .25s ease-out, color .25s ease-out;
            font-size: 16px;
            line-height: 1;
            text-align: center;
            cursor: pointer;
            color: #fff;
            font-weight: bold;
        }
    </style>
<?php endif; ?>



