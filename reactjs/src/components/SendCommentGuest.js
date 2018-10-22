import React from 'react'
import AnyCommentComponent from "./AnyCommentComponent"
import LoginSocialList from './LoginSocialList'

class SendCommentGuest extends AnyCommentComponent {

    constructor(props) {
        super(props);

        this.state = {
            showGuestFields: false
        };
    }

    /**
     * Handle guest fields (toggle them).
     */
    handleGuestFields = () => {
        this.setState({showGuestFields: !this.state.showGuestFields});
    };

    componentDidMount() {
        const settings = this.getSettings();

        if (settings.options.isFormTypeGuests) {
            this.setState({showGuestFields: true});
        }
    }

    render() {
        const settings = this.getSettings();
        const translations = settings.i18;
        const inputs = settings.options.guestInputs;

        let elementInputs = [];

        inputs.forEach(el => {
            if (el === 'name') {
                elementInputs.push(
                    <div className="anycomment anycomment-form__inputs-item anycomment-form__inputs-name">
                        <label form="anycomment-author-name">{translations.name} <span
                            className="anycomment-label-import">*</span></label>
                        <input type="text" name="author_name" id="anycomment-author-name"
                               value={this.props.authorName}
                               required={true}
                               onChange={this.props.handleAuthorNameChange}
                        />
                    </div>);
            } else if (el === 'email') {
                elementInputs.push(
                    <div className="anycomment anycomment-form__inputs-item anycomment-form__inputs-email">
                        <label form="anycomment-author-email">{translations.email} <span
                            className="anycomment-label-import">*</span></label>
                        <input type="email" name="author_email" id="anycomment-author-email"
                               value={this.props.authorEmail}
                               required={true}
                               onChange={this.props.handleAuthorEmailChange}
                        />
                    </div>);
            } else if (el === 'website') {
                elementInputs.push(
                    <div className="anycomment-form__inputs-item anycomment-form__inputs-website">
                        <label form="anycomment-author-website">{translations.website}</label>
                        <input type="text" name="author_url" id="anycomment-author-website"
                               value={this.props.authorWebsite}
                               onChange={this.props.handleAuthorWebsiteChange}
                        />
                    </div>);
            }
        });

        const guestInputList = this.state.showGuestFields && elementInputs.length ?
            <div
                className={"anycomment anycomment-form__inputs anycomment-form__inputs-" + elementInputs.length}>
                {elementInputs}
            </div> : '';

        return <React.Fragment>
            <div className="anycomment anycomment-form__guest-socials">
                {(settings.options.isFormTypeSocials || settings.options.isFormTypeAll) ?
                    <LoginSocialList handleGuestFields={this.handleGuestFields}/> : ''}
            </div>

            {settings.options.isFormTypeGuests || settings.options.isFormTypeAll ? guestInputList : ''}
        </React.Fragment>;
    }
}

export default SendCommentGuest;