import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"

class DataProcessing extends AnyCommentComponent {
    render() {
        const settings = this.getSettings();
        const i18 = settings.i18;

        if (!('accept_user_agreement' in i18) || !settings.options.userAgreementLink) {
            return (null);
        }

        const agreementId = "accept-user-agreement-" + (new Date()).getTime();

        return (
            <div className="anycomment anycomment-form__terms-agreement">
                <label htmlFor={agreementId}>
                    <input type="checkbox" required={true} checked={this.props.isAgreementAccepted}
                           id={agreementId}
                           onClick={(e) => this.props.onAccept(e)}/>
                    <span dangerouslySetInnerHTML={{__html: i18.accept_user_agreement}}/>
                </label>
            </div>
        )
    }
}

export default DataProcessing;