<div class="anycomment-tab">
    <table class="form-table">
        <tbody>
        <tr>
            <td>
                <textarea name="" readonly
                          onclick="this.select()">[anycomment include="true"]</textarea>
                <p class="description"><?php echo __( 'Use this shortcode to display a comment box on any type of supported content. Please note that the page or post must have comments enabled (e.g. a landing page).', 'anycomment' ) ?></p>
            </td>
        </tr>

        <tr>
            <td>
                <textarea name="" readonly
                          onclick="this.select()">[anycomment_socials]</textarea>
                <p class="description"><?php echo __( 'Use this shortcode do display list of available social networks.', 'anycomment' ) ?></p>
                <p class="description"><?php echo __( 'Possible options:' ) ?></p>
                <p class="description"><?php echo sprintf( __( '- %s: to display only socials icons without starting paragraph, <br>- %s: URL where to redirect user after authorization' ), 'only_socials', 'target_url' ) ?></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
