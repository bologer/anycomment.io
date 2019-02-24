<?php

$table_styles = 'border-spacing: 0;border-collapse: collapse;';

$td_styles = 'border-collapse: collapse;';

$p_styles = 'font-family: \'Open Sans\', Arial, Helvetica, sans-serif;font-size: 14px;margin-top:0px;margin-bottom:0px;line-height:24px;text-align:left;';

$spacer = <<<HTML
<td class="anycomment-email--table-td anycomment-email--table-td-spacer" style="height: 15px;"></td>
HTML;

?>

<table class="anycomment-email--table" dir="ltr" width="100%" cellspacing="0" cellpadding="0" border="0"
       bgcolor="#f6f6f6">
    <tbody>
    <tr>
		<?php echo $spacer ?>
		<?php echo $spacer ?>
    </tr>
    <tr>
        <td>
            <table class="anycomment-email--table" width="640" cellspacing="0" cellpadding="0" border="0"
                   align="center" style="<?php echo $table_styles; ?>">
                <tbody>
                <tr>
                    <td class="anycomment-email--table-td">
                        <table class="anycomment-email--table" style="<?php echo $table_styles; ?>">
                            <tbody>
                            <tr>
                                <td class="anycomment-email--table-td anycomment-email--table-td-text">
                                    <p style="<?php echo $p_styles; ?>">{firstParagraph}</p>
                                </td>
                            </tr>
                            <tr>
								<?php echo $spacer ?>
                            </tr>
                            </tbody>
                        </table>

                        <table class="anycomment-email--table" style="<?php echo $table_styles; ?>">
                            <tbody>
                            <tr>
                                <td style="width: 55px; vertical-align: top;">
                                    <img src="{commentAuthorImgSrc}"
                                         style="width: 40px;">
                                </td>
                                <td class="anycomment-email--table-td">
                                    <table class="anycomment-email--table" style="<?php echo $table_styles; ?>">
                                        <tbody>
                                        <tr>
                                            <td class="anycomment-email--table-td"
                                                style="font-family: 'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;line-height: 15px;color: #000;">
                                                <strong>{commentAuthorName}</strong></td>
                                        </tr>
                                        <tr>
                                            <td class="anycomment-email--table-td anycomment-email--table-td-text">
                                                <p style="<?php echo $p_styles; ?>">{commentAuthorText}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="anycomment-email--table-td anycomment-email--table-td-text">
                                                <p style="<?php echo $p_styles; ?>">{commentAuthorDate}</p>
                                            </td>
                                        </tr>
                                        <tr>
											<?php echo $spacer ?>
                                        </tr>
                                        <tr>
                                            <td>
                                                <a href="{commentAuthorReplyUrl}"
                                                   style="font-weight: 400;font-family: 'Open Sans',Arial,Helvetica,sans-serif;border-radius: 3px;color: #ffffff;display: inline-block; font-size: 14px;background-color: #2d8cff;line-height: 20px;padding: 10px 0 10px 0;text-align: center;text-decoration: none;width: 157px;">
                                                    {commentAuthorReplyText}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
												<?php echo $spacer; ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="anycomment-email--table-td"
                                                style="font-family: 'Open Sans', Arial, Helvetica, sans-serif;font-size: 14px;line-height: 15px;color: #000;">
                                                <p style="<?php echo $p_styles; ?>">
                                                    <strong>{commentReplyAuthorParagraph}</strong>
                                                </p>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="anycomment-email--table-td">
                                                <table class="anycomment-email--table"
                                                       style="<?php echo $table_styles; ?>">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width: 48px; vertical-align: top;">
                                                            <img style="width: 28px;"
                                                                 src="{commentReplyAuthorImgSrc}"
                                                                 alt="">
                                                        </td>
                                                        <td class="anycomment-email--table-td anycomment-email--table-td-text">
                                                            <p style="<?php echo $p_styles; ?>">
                                                                {commentReplyAuthorText}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
		<?php echo $spacer ?>
		<?php echo $spacer ?>
    </tr>
    </tbody>
</table>