import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"

class DataProcessing extends AnyCommentComponent {
    render() {
        const i18 = this.props.settings.i18;

        return (
            <div className="user-agreement">
                <label htmlFor="accept-user-agreement">
                    <input type="checkbox" checked={this.props.isAccepted} id="accept-user-agreement" onClick={(e) => this.props.onAccept(e)} />
                    {i18.accept_user_agreement}
                    <span className="checkmark"></span>
                </label>
            </div>
        )
    }
}

export default DataProcessing;