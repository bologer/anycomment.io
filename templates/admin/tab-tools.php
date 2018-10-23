<?php
$url = menu_page_url( $_GET['page'], false ) . '&tab=tools';

if ( isset( $_GET['action'] ) ) {
	$action = $_GET['action'];
	switch ( $action ) {
		case 'cache-flush-all':
			\anycomment\cache\AnyCommentCacheManager::flushAll();
			break;
		case 'cache-flush-rest':
			\anycomment\cache\rest\AnyCommentRestCacheManager::flush();
			break;
		case 'open-all-comments':
			\anycomment\helpers\AnyCommentManipulatorHelper::open_all_comments();
			break;
		case 'open-posts-comments':
			\anycomment\helpers\AnyCommentManipulatorHelper::open_all_post_comments();
			break;
		case 'open-pages-comments':
			\anycomment\helpers\AnyCommentManipulatorHelper::open_all_page_comments();
			break;
		case 'open-products-comments':
			\anycomment\helpers\AnyCommentManipulatorHelper::open_all_product_comments();
			break;
		default:
	}
}
?>

<div class="anycomment-tab">
    <h2><?php echo __( 'Tools', 'anycomment' ) ?></h2>
    <p><?php echo __( 'This page will have helpers and debug information related to the plugin. For example, version of plugin, WordPress or PHP. Also you may drop comments or global plugins cache, open comments for all posts, pages or WooCommerce products (when plugin activated).', 'anycomment') ?></p>


    <h3><?php echo __( 'Debug Information', 'anycomment' ) ?></h3>
	<?php

	global $wp_version;


	$debugData = [
		[ 'th' => __( 'AnyComment Version', 'anycomment' ), 'td' => AnyComment()->version ],
		[ 'th' => __( 'WordPress Version', 'anycomment' ), 'td' => $wp_version ],
		[ 'th' => __( 'PHP Version', 'anycomment' ), 'td' => PHP_VERSION ],
		[ 'th' => __( 'Locale', 'anycomment' ), 'td' => get_locale() ],
		[
			'th' => __( 'Using Custom Design', 'anycomment' ),
			'td' => AnyCommentGenericSettings::is_design_custom() ? __( 'Yes', 'anycomment' ) : __( 'No', 'anycomment' )
		],
		[ 'th' => __( 'Generated at', 'anycomment' ), 'td' => date( 'c' ) ],
	];

	function anycomment_get_debug_summary( $debugData ) {
		$text = '';
		foreach ( $debugData as $key => $debug ) {
			$text .= sprintf( "%s: %s%s", $debug['th'], $debug['td'], ( isset( $debugData[ $key + 1 ] ) ? "\n" : '' ) );
		}

		return $text;
	}

	?>
    <table class="form-table">
        <tbody>
		<?php foreach ( $debugData as $row ): ?>
            <tr>
                <th><?php echo $row['th'] ?></th>
                <td><?php echo $row['td'] ?></td>
            </tr>
		<?php endforeach; ?>
        <tr>
            <th><?php echo __( 'Summary', 'anycomment' ) ?></th>
            <td>
                <textarea name="" id="" cols="100"
                          readonly="readonly"
                          onclick="this.select()"
                          rows="10"><?php echo anycomment_get_debug_summary( $debugData ) ?></textarea>
                <p class="description"><?php echo __( 'Copy and paste this information to developer as it may be helpful for problem investigation.', 'anycomment' ) ?></p>
            </td>
        </tr>
        </tbody>
    </table>

    <hr>
    <h3><?php echo __( 'Cache Manager', 'anycomment' ) ?></h3>
    <a href="<?php echo $url . "&action=cache-flush-all" ?>"
       class="button button-primary"><?php echo __( "Flush All", 'anycomment' ) ?></a>
    <a href="<?php echo $url . "&action=cache-flush-rest" ?>"
       class="button button-primary"><?php echo __( "Flush Comments", 'anycomment' ) ?></a>

    <hr>
    <h3><?php echo __( 'Other Helpers', 'anycomment' ) ?></h3>
    <a href="<?php echo $url . "&action=open-all-comments" ?>"
       class="button button-primary"><?php echo __( "Open All Comments", 'anycomment' ) ?></a>
    <a href="<?php echo $url . "&action=open-posts-comments" ?>"
       class="button button-primary"><?php echo __( "Open Posts Comments", 'anycomment' ) ?></a>
    <a href="<?php echo $url . "&action=open-pages-comments" ?>"
       class="button button-primary"><?php echo __( "Open Pages Comments", 'anycomment' ) ?></a>
    <a href="<?php echo $url . "&action=open-products-comments" ?>"
       class="button button-primary"><?php echo __( "Open WooCommerce Product Comments", 'anycomment' ) ?></a>
</div>
