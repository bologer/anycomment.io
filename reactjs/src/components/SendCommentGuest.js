import React from 'react';
import LoginSocialList from './LoginSocialList';
import AnyCommentComponent from "./AnyCommentComponent";
import DataProcessing from './DataProcessing';

class SendCommentGuest extends AnyCommentComponent {
    constructor(props) {
        super(props);

        this.state = {
            isAgreementAccepted: true
        };

        this.handleAgreement = this.handleAgreement.bind(this);
    }

    /**
     * Handle agreement check.
     *
     * @param e
     */
    handleAgreement(e) {
        this.setState({isAgreementAccepted: e.target.checked});
    }

    render() {
        if (this.props.user) {
            return null;
        }


        const classes = "send-comment-body-outliner__auth" +
            " " + (this.state.isAgreementAccepted ? 'accepted' : 'not-accepted') +
            " " + (this.props.settings.options.userAgreementLink ? 'has-link' : 'no-link');

        return (
            <React.Fragment>
                <div className="send-comment-body-outliner__logo"></div>

                <div className={classes}
                     style={{display: this.props.isShouldLogin ? 'block' : 'none'}}>
                    <LoginSocialList isAccepted={this.state.isAgreementAccepted}/>
                    <DataProcessing isAccepted={this.state.isAgreementAccepted} onAccept={this.handleAgreement}/>
                </div>
            </React.Fragment>
        );
    }
}

export default SendCommentGuest;