<div class="anycomment-tab">
    <table class="form-table">
        <tbody>
        <tr>
            <td>
                <textarea name="" readonly
                          onclick="this.select()">[anycomment]</textarea>
                <p class="description"><?php esc_html_e('Use this shortcode do display comment box in custom place (e.g. landing page).', 'anycomment') ?></p>
            </td>
        </tr>

        <tr>
            <td>
                <textarea name="" readonly
                          onclick="this.select()">[anycomment_socials]</textarea>
                <p class="description"><?php echo __('Use this shortcode do display list of available social networks.', 'anycomment') ?></p>
                <p class="description"><?php echo __('Possible options:', 'anycomment') ?></p>
                <p class="description"><?php echo sprintf(__('- %s: to display only socials icons without starting paragraph, <br>- %s: URL where to redirect user after authorization', 'anycomment'), 'only_socials', 'target_url') ?></p>
            </td>
        </tr>
        </tbody>
    </table>
</div>
