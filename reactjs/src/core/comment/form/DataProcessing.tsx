import React from 'react';
import {useSettings} from '~/hooks/setting';

export interface DataProcessingProps {
    isAgreementAccepted: boolean;
    onAccept: (e: React.SyntheticEvent<HTMLInputElement>) => void;
}

export default function DataProcessing({isAgreementAccepted, onAccept}: DataProcessingProps) {
    const settings = useSettings();
    const i18 = settings.i18;

    if (!('accept_user_agreement' in i18) || !settings.options.userAgreementLink) {
        return null;
    }

    const agreementId = 'accept-user-agreement-' + new Date().getTime();

    return (
        <div className='anycomment anycomment-form__terms-agreement'>
            <label htmlFor={agreementId}>
                <input type='checkbox' required checked={isAgreementAccepted} id={agreementId} onClick={onAccept} />
                <span dangerouslySetInnerHTML={{__html: i18.accept_user_agreement}} />
            </label>
        </div>
    );
}
