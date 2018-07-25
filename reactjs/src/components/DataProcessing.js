import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"

class DataProcessing extends AnyCommentComponent {
    render() {
        const settings = this.props.settings;
        const i18 = settings.i18;

        if (!('accept_user_agreement' in i18) || !settings.options.user_agreement_link) {
            return (null);
        }

        return (
            <div className="user-agreement">
                <label htmlFor="accept-user-agreement">
                    <input type="checkbox" checked={this.props.isAccepted} id="accept-user-agreement"
                           onClick={(e) => this.props.onAccept(e)}/>
                    <span dangerouslySetInnerHTML={{__html: i18.accept_user_agreement}}/>
                    <span className="checkmark"></span>
                </label>
            </div>
        )
    }
}

export default DataProcessing;