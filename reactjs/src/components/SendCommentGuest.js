import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import DataProcessing from './DataProcessing'
import LoginSocialList from './LoginSocialList'

class SendCommentGuest extends AnyCommentComponent {

    render() {
        const settings = this.getSettings();
        const translations = settings.i18;
        const inputs = settings.options.guestInputs;

        let elementInputs = [];

        inputs.map(el => {
            if (el === 'name') {
                elementInputs.push(
                    <div className="anycomment anycomment-input-list-single anycomment-input-list-single-name">
                        <label form="anycomment-author-name">{translations.name}</label>
                        <input type="text" name="author_name" id="anycomment-author-name"
                               value={this.props.authorName}
                               required={true}
                               onChange={this.props.onAuthorNameChange}
                        />
                    </div>);
            } else if (el === 'email') {
                elementInputs.push(
                    <div className="anycomment anycomment-input-list-single anycomment-input-list-single-email">
                        <label form="anycomment-author-email">{translations.email}</label>
                        <input type="email" name="author_email" id="anycomment-author-email"
                               value={this.props.authorEmail}
                               required={true}
                               onChange={this.props.onAuthorEmailChange}
                        />
                    </div>);
            } else if (el === 'website') {
                elementInputs.push(
                    <div className="anycomment anycomment-input-list-single anycomment-input-list-single-website">
                        <label form="anycomment-author-website">{translations.website}</label>
                        <input type="text" name="author_url" id="anycomment-author-website"
                               value={this.props.authorWebsite}
                               onChange={this.props.onAuthorWebsiteChange}
                        />
                    </div>);
            }
        });

        const guestInputList = elementInputs.length ?
            <div className="anycomment anycomment-form-guest__container">
                <div className={"anycomment anycomment-input-list anycomment-input-list-" + elementInputs.length}>
                    {elementInputs}
                </div>
            </div> : '';

        return <div className="anycomment anycomment-form-guest">

            {settings.options.isFormTypeGuests || settings.options.isFormTypeAll ? guestInputList : ''}

            <div className="anycomment anycomment-form-guest__container">
                <div className="anycomment anycomment-form-guest-socials">
                    {(settings.options.isFormTypeSocials || settings.options.isFormTypeAll) && this.props.isAgreementAccepted ?
                        <LoginSocialList/> : ''}
                </div>
                <div className="anycomemnt anycomment-form-submit">
                    <DataProcessing isAgreementAccepted={this.props.isAgreementAccepted}
                                    onAccept={this.props.handleAgreement}/>
                    <input type="submit" className="anycomment-btn anycomment-send-comment-body__btn"
                           value={this.props.buttonText}/>
                </div>
            </div>
        </div>;
    }
}

export default SendCommentGuest;