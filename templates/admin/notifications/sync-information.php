<?php

/**
 * Renders SaaS synchronization information, such as:
 * - when service is not configured and required synchronization
 * - when service configure and synchronizing comments
 * - when service configure and finished synchronization
 */
use AnyComment\Helpers\AnyCommentLinkHelper;
use AnyComment\Cron\AnyCommentServiceSyncCron;
use AnyComment\AnyCommentServiceApi;

?>

<div class="anycomment-notification anycomment-notification--center">
    <?php

    if (AnyCommentServiceApi::is_ready()):
        $syncInfo = AnyCommentServiceSyncCron::getSyncInfo();

        if ($syncInfo['complete_percent'] === 100): ?>
            <div class="anycomment-notification__icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="96"
                     height="96" viewBox="0 0 96 96">
                    <defs>
                        <rect id="b" width="512" height="232" rx="4"/>
                        <filter id="a" width="102.3%" height="105.2%" x="-1.2%" y="-2.2%"
                                filterUnits="objectBoundingBox">
                            <feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter1"/>
                            <feGaussianBlur in="shadowOffsetOuter1" result="shadowBlurOuter1" stdDeviation="1.5"/>
                            <feColorMatrix in="shadowBlurOuter1" result="shadowMatrixOuter1"
                                           values="0 0 0 0 0.2471 0 0 0 0 0.2471 0 0 0 0 0.2667 0 0 0 0.15 0"/>
                            <feMorphology in="SourceAlpha" operator="dilate" radius=".5" result="shadowSpreadOuter2"/>
                            <feOffset in="shadowSpreadOuter2" result="shadowOffsetOuter2"/>
                            <feColorMatrix in="shadowOffsetOuter2" result="shadowMatrixOuter2"
                                           values="0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 1 0"/>
                            <feMerge>
                                <feMergeNode in="shadowMatrixOuter1"/>
                                <feMergeNode in="shadowMatrixOuter2"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <g fill="none" fill-rule="evenodd">
                        <path fill="#FFF" d="M-1394-161H396v432h-1790z"/>
                        <g transform="translate(-208 -20)">
                            <use fill="#000" filter="url(#a)" xlink:href="#b"/>
                            <use fill="#FFF" xlink:href="#b"/>
                        </g>
                        <g fill-rule="nonzero" transform="translate(24)">
                            <circle cx="24" cy="24" r="24" fill="#FBDDE3"/>
                            <rect width="9" height="36.75" x="19.502" y="5.625" fill="#EC4568" rx=".375"
                                  transform="rotate(-45 24.002 24)"/>
                            <rect width="9" height="36.75" x="19.5" y="5.623" fill="#EC4568" rx=".375"
                                  transform="rotate(-135 24 23.998)"/>
                        </g>
                        <g>
                            <circle cx="47.7" cy="47.7" r="47.7" fill="#815493" fill-rule="nonzero"/>
                            <path fill="#F4C09E" fill-rule="nonzero"
                                  d="M35.775 93.89c3.816.954 7.791 1.51 11.925 1.51 1.351 0 2.623-.08 3.975-.159V59.148L35.775 47.7v46.19z"/>
                            <path fill="#EB9380" fill-rule="nonzero"
                                  d="M43.725 20.67v25.678c0 1.909-1.272 3.657-3.1 3.976-2.624.556-4.85-1.352-4.85-3.816V20.829c0-1.908 1.272-3.657 3.1-3.975 2.624-.636 4.85 1.352 4.85 3.816z"/>
                            <path fill="#53146C" fill-rule="nonzero"
                                  d="M42.93 18.285l-6.28 7.87c-.557.716-.875 1.59-.875 2.465v11.845c0 1.908 1.272 3.657 3.1 3.975 2.544.557 4.85-1.43 4.85-3.895V20.67c0-.875-.318-1.749-.795-2.385z"
                                  opacity=".2"/>
                            <path fill="#F2B497" fill-rule="nonzero"
                                  d="M51.675 24.645c0 .875-.318 1.749-.874 2.465l-7.076 8.824v10.415c0 1.908-1.272 3.657-3.1 3.975-2.624.556-4.85-1.352-4.85-3.816V34.582c0-.875.318-1.75.875-2.465l7.95-9.938c1.351-1.748 3.895-1.987 5.564-.636 1.034.716 1.511 1.909 1.511 3.101z"/>
                            <path fill="#53146C" fill-rule="nonzero"
                                  d="M50.323 27.745c-8.109.636-14.469 7.473-14.469 15.741 0 1.988.398 3.896 1.034 5.645.954 1.034 2.305 1.51 3.895 1.193 1.75-.398 2.942-2.147 2.942-3.975V36.014l6.598-8.269z"
                                  opacity=".1"/>
                            <path stroke="#F4C09E" stroke-linecap="round" stroke-linejoin="round" stroke-width="7.95"
                                  d="M62.646 52.311c-2.464 6.837-10.017 6.916-11.05 6.916-6.52 0-11.846-5.326-11.846-11.845 0-6.519 5.327-11.845 11.845-11.845 5.884 0 8.825 3.418 8.825 3.418"/>
                            <path fill="#EC4568" fill-rule="nonzero"
                                  d="M35.775 78.705h16.059c.795 0 1.431-.636 1.431-1.431v-6.678c0-.795-.636-1.431-1.431-1.431H35.775v9.54z"/>
                            <path fill="#FFF" fill-rule="nonzero"
                                  d="M34.185 66.78h-.795c-.477 0-.795.318-.795.795v.795h2.385v-.795c0-.477-.318-.795-.795-.795z"/>
                            <circle cx="49.688" cy="73.935" r="1.192" fill="#C23956" fill-rule="nonzero"/>
                            <circle cx="44.917" cy="73.935" r="1.192" fill="#C23956" fill-rule="nonzero"/>
                            <path fill="#382673" fill-rule="nonzero"
                                  d="M34.185 66.78h-.795c-.477 0-.795.318-.795.795v.795h2.385v-.795c0-.477-.318-.795-.795-.795z"
                                  opacity=".2"/>
                            <path fill="#FFF" fill-rule="nonzero"
                                  d="M33.39 81.09h.795c.874 0 1.59-.716 1.59-1.59V69.165c0-.875-.715-1.59-1.59-1.59h-.795c-.874 0-1.59.715-1.59 1.59V79.5c0 .874.716 1.59 1.59 1.59z"/>
                            <path fill="#382673" fill-rule="nonzero"
                                  d="M34.185 67.575h-.795c-.159 0-.239 0-.398.08a1.532 1.532 0 0 1 1.193 1.51V79.5c0 .716-.477 1.352-1.193 1.51.16 0 .239.08.398.08h.795c.874 0 1.59-.716 1.59-1.59V69.165c0-.875-.715-1.59-1.59-1.59z"
                                  opacity=".2"/>
                            <path fill="#44CDAC" fill-rule="nonzero"
                                  d="M69.403 22.737l.716-1.828c.239-.477.954-.477 1.113 0l.716 1.828a5.414 5.414 0 0 0 3.1 3.1l1.828.716c.477.238.477.954 0 1.113l-1.828.715a5.414 5.414 0 0 0-3.1 3.101l-.716 1.828c-.239.477-.954.477-1.113 0l-.716-1.828a5.414 5.414 0 0 0-3.1-3.1l-1.828-.716c-.477-.239-.477-.954 0-1.113l1.828-.716a5.414 5.414 0 0 0 3.1-3.1zM17.808 44.123l.477-1.193c.159-.318.636-.318.715 0l.477 1.193a3.767 3.767 0 0 0 2.067 2.067l1.193.477c.318.158.318.636 0 .715l-1.272.398a3.767 3.767 0 0 0-2.067 2.066l-.477 1.193c-.159.318-.636.318-.715 0l-.477-1.193a3.767 3.767 0 0 0-2.067-2.066l-1.193-.477c-.318-.16-.318-.636 0-.716l1.193-.477c1.033-.318 1.748-1.113 2.146-1.987z"/>
                            <circle cx="75.525" cy="48.495" r="1.59" fill="#FFF" fill-rule="nonzero"/>
                            <circle cx="25.44" cy="29.415" r="1.59" fill="#FFF" fill-rule="nonzero"/>
                        </g>
                    </g>
                </svg>
            </div>

            <h3 class="anycomment-notification__header"><?php esc_html_e('All Comments Synced!', 'anycomment') ?></h3>
        <?php else: ?>
            <div class="anycomment-notification__icon">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48"
                     height="48" viewBox="0 0 48 48">
                    <defs>
                        <rect id="b" width="512" height="232" rx="4"/>
                        <filter id="a" width="102.3%" height="105.2%" x="-1.2%" y="-2.2%"
                                filterUnits="objectBoundingBox">
                            <feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter1"/>
                            <feGaussianBlur in="shadowOffsetOuter1" result="shadowBlurOuter1" stdDeviation="1.5"/>
                            <feColorMatrix in="shadowBlurOuter1" result="shadowMatrixOuter1"
                                           values="0 0 0 0 0.2471 0 0 0 0 0.2471 0 0 0 0 0.2667 0 0 0 0.15 0"/>
                            <feMorphology in="SourceAlpha" operator="dilate" radius=".5" result="shadowSpreadOuter2"/>
                            <feOffset in="shadowSpreadOuter2" result="shadowOffsetOuter2"/>
                            <feColorMatrix in="shadowOffsetOuter2" result="shadowMatrixOuter2"
                                           values="0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 1 0"/>
                            <feMerge>
                                <feMergeNode in="shadowMatrixOuter1"/>
                                <feMergeNode in="shadowMatrixOuter2"/>
                            </feMerge>
                        </filter>
                    </defs>
                    <g fill="none" fill-rule="evenodd">
                        <path fill="#FFF" d="M-866-161H924v432H-866z"/>
                        <g transform="translate(-232 -20)">
                            <use fill="#000" filter="url(#a)" xlink:href="#b"/>
                            <use fill="#FFF" xlink:href="#b"/>
                        </g>
                        <g fill-rule="nonzero">
                            <circle cx="24" cy="24" r="24" fill="#FBDDE3"/>
                            <rect width="9" height="36.75" x="19.502" y="5.625" fill="#EC4568" rx=".375"
                                  transform="rotate(-45 24.002 24)"/>
                            <rect width="9" height="36.75" x="19.5" y="5.623" fill="#EC4568" rx=".375"
                                  transform="rotate(-135 24 23.998)"/>
                        </g>
                        <g fill-rule="nonzero">
                            <circle cx="37.06" cy="20.589" r="5.989" fill="#FBDDE3"/>
                            <rect width="2.246" height="4.492" x="33.691" y="19.84" fill="#EC4568" rx=".094"
                                  transform="rotate(-45 34.814 22.086)"/>
                            <circle cx="23.958" cy="23.958" r="23.958" fill="#DDF5EF"/>
                            <path fill="#7ADBC3"
                                  d="M19.724 25.133l-7.412-7.412a.374.374 0 0 0-.528 0L5.96 23.546a.374.374 0 0 0 0 .528l13.764 13.765a.374.374 0 0 0 .528 0l6.087-6.087 15.617-15.618a.374.374 0 0 0 0-.528l-5.82-5.824a.374.374 0 0 0-.528 0L20.259 25.13a.374.374 0 0 1-.535.003z"/>
                        </g>
                    </g>
                </svg>

            </div>

            <h3 class="anycomment-notification__header">Настроено</h3>

            <div class="anycomment-notification__body">
                <?php
                $finishedPercentageWithSign = $syncInfo['complete_percent'] . '%';
                ?>
                <p>
                    <?php echo sprintf(
                        _n(
                            'Synced %s comment out of %s, finished %s in total.',
                            'Synced %s comments out of %s, finished %s in total.',
                            $syncInfo['current'],
                            'anycomment'
                        ),
                        $syncInfo['current'],
                        $syncInfo['total'],
                        $finishedPercentageWithSign
                    ) ?>
                </p>

                <div class="success progress">
                    <div class="progress-meter" style="width: <?php echo $finishedPercentageWithSign ?>">
                        <span class="progress-meter-text"><?php echo $finishedPercentageWithSign ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="anycomment-notification__icon">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                 width="48" height="48" viewBox="0 0 48 48">
                <defs>
                    <rect id="b" width="512" height="232" x="82" y="141" rx="4"/>
                    <filter id="a" width="102.3%" height="105.2%" x="-1.2%" y="-2.2%"
                            filterUnits="objectBoundingBox">
                        <feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter1"/>
                        <feGaussianBlur in="shadowOffsetOuter1" result="shadowBlurOuter1"
                                        stdDeviation="1.5"/>
                        <feColorMatrix in="shadowBlurOuter1" result="shadowMatrixOuter1"
                                       values="0 0 0 0 0.2471 0 0 0 0 0.2471 0 0 0 0 0.2667 0 0 0 0.15 0"/>
                        <feMorphology in="SourceAlpha" operator="dilate" radius=".5"
                                      result="shadowSpreadOuter2"/>
                        <feOffset in="shadowSpreadOuter2" result="shadowOffsetOuter2"/>
                        <feColorMatrix in="shadowOffsetOuter2" result="shadowMatrixOuter2"
                                       values="0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 0 0.847058824 0 0 0 1 0"/>
                        <feMerge>
                            <feMergeNode in="shadowMatrixOuter1"/>
                            <feMergeNode in="shadowMatrixOuter2"/>
                        </feMerge>
                    </filter>
                </defs>
                <g fill="none" fill-rule="evenodd">
                    <path fill="#FFF" d="M-314-161h1790v432H-314z"/>
                    <g transform="translate(-314 -161)">
                        <use fill="#000" filter="url(#a)" xlink:href="#b"/>
                        <use fill="#FFF" xlink:href="#b"/>
                    </g>
                    <g fill-rule="nonzero">
                        <circle cx="24" cy="24" r="24" fill="#FBDDE3"/>
                        <rect width="9" height="36.75" x="19.502" y="5.625" fill="#EC4568" rx=".375"
                              transform="rotate(-45 24.002 24)"/>
                        <rect width="9" height="36.75" x="19.5" y="5.623" fill="#EC4568" rx=".375"
                              transform="rotate(-135 24 23.998)"/>
                    </g>
                </g>
            </svg>
        </div>

        <h3 class="anycomment-notification__header"><?php esc_html_e('Synchronization required') ?></h3>

        <div class="anycomment-notification__body">
            <p><?php esc_html_e('Synchronization with cloud is not configured yet. Click button below to learn more about it.',
                    'anycomment') ?></p>
        </div>

        <div class="anycomment-notification__footer">
            <a class="button button-primary"
               href="<?php echo AnyCommentLinkHelper::get_service_documentation() ?>/connect-to-saas"
               target="_blank"><?php esc_html_e('Learn more', 'anycomment') ?></a>
        </div>

    <?php endif; ?>
</div>
