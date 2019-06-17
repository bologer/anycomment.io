<?php
$url = menu_page_url( $_GET['page'], false ) . '&tab=tools';

if ( isset( $_GET['action'] ) ) {
	$action = $_GET['action'];
	switch ( $action ) {
		case 'cache-flush-all':
			\AnyComment\Cache\AnyCommentCacheManager::flushAll();
			break;
		case 'cache-flush-rest':
			\AnyComment\Cache\AnyCommentRestCacheManager::flush();
			break;
		case 'open-all-comments':
			\AnyComment\Helpers\AnyCommentManipulatorHelper::open_all_comments();
			break;
		case 'open-posts-comments':
			\AnyComment\Helpers\AnyCommentManipulatorHelper::open_all_post_comments();
			break;
		case 'open-pages-comments':
			\AnyComment\Helpers\AnyCommentManipulatorHelper::open_all_page_comments();
			break;
		case 'open-products-comments':
			\AnyComment\Helpers\AnyCommentManipulatorHelper::open_all_product_comments();
			break;
		default:
	}
}

if ( isset( $_GET['hypercomment_url'] ) && ! empty( $_GET['hypercomment_url'] ) ) {
	$hc = new \AnyComment\Import\HyperComments( sanitize_text_field( $_GET['hypercomment_url'] ) );

	$hc->process();

	wp_redirect( $url );

	exit;
}

if ( isset( $_GET['hypercomment_revert'] ) ) {
	\AnyComment\Import\HyperComments::revert();

	wp_redirect( $url );

	exit;
}

?>

<div class="anycomment-tab">
    <h2><?php echo __( 'Tools', 'anycomment' ) ?></h2>
    <p><?php echo __( 'Tools and debug information to help with AnyComment.', 'anycomment' ) ?></p>

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

    <hr>
    <h3><?php echo __( 'Import HyperComments', 'anycomment' ) ?></h3>

    <p><?php echo __( 'Enter URL to XML file to start importing comments from HyperComments.', 'anycomment' ) ?></p>

    <form action="" method="GET">
        <input type="hidden" name="page" value="anycomment-dashboard">
        <input type="hidden" name="tab" value="tools">
        <div class="grid-x">
            <div class="cell auto">
                <input type="text" name="hypercomment_url"
                       placeholder="http://static.hypercomments.com/data/export/hcexport_XXX.xml">
            </div>
        </div>
        <input type="submit" value="<?php echo __( 'Import', 'anycomment' ) ?>" class="button button-default">
        <p><?php echo __( 'Clicking on "Import"  would copy all comments from provided XML document via URL to your website. It would automatically match posts, pages and create comments for them. It means after page reloaded, you may go and check for imported comments.', 'anycomment' ) ?></p>
    </form>

    <form action="" method="GET">
        <input type="hidden" name="page" value="anycomment-dashboard">
        <input type="hidden" name="tab" value="tools">
        <input type="hidden" name="hypercomment_revert" value="">
        <input type="submit" value="<?php echo __( 'Revert', 'anycomment' ) ?>" class="button button-danger">
        <p><?php echo __( 'This action would delete all imported comments from HyperComment that were improted via this tool.', 'anycomment' ) ?></p>
    </form>

    <hr>

    <h3><?php echo __( 'Debug Information', 'anycomment' ) ?></h3>
	<?php

	$preparedData = \AnyComment\AnyCommentDebugReport::prepare();

	?>
    <table class="form-table">
        <tbody>
		<?php foreach ( $preparedData as $row ): ?>
            <tr>
                <th><?php echo $row['name'] ?></th>
                <td style="word-break: break-all;"><?php echo $row['value'] ?></td>
            </tr>
		<?php endforeach; ?>
        <tr>
            <th><?php echo __( 'Summary', 'anycomment' ) ?></th>
            <td>
                <textarea name="" id="" cols="100"
                          readonly="readonly"
                          onclick="this.select()"
                          rows="10"><?php echo \AnyComment\AnyCommentDebugReport::generate( $preparedData ) ?></textarea>
                <p class="description"><?php echo __( 'Copy and paste this information to developer as it may be helpful for problem investigation.', 'anycomment' ) ?></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
