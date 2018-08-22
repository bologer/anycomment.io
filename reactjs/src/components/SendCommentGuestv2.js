import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";

class SendCommentGuestv2 extends AnyCommentComponent {

    render() {
        const settings = this.props.settings;
        const translations = settings.i18;

        return <div className="anycomment anycomment-input-list">
            <div className="anycomment anycomment-input-list-single anycomment-input-list-single-name">
                <label form="anycomment-author-name">{translations.name}</label>
                <input type="text" name="author_name" id="anycomment-author-name"
                       value={this.props.authorName}
                       required={true}
                       onChange={this.props.onAuthorNameChange}
                />
            </div>
            <div className="anycomment anycomment-input-list-single anycomment-input-list-single-email">
                <label form="anycomment-author-email">{translations.email}</label>
                <input type="email" name="author_email" id="anycomment-author-email"
                       value={this.props.authorEmail}
                       required={true}
                       onChange={this.props.onAuthorEmailChange}
                />
            </div>
            <div className="anycomment anycomment-input-list-single anycomment-input-list-single-website">
                <label form="anycomment-author-website">{translations.website}</label>
                <input type="text" name="author_website" id="anycomment-author-website"
                       value={this.props.authorWebsite}
                       onChange={this.props.onAuthorWebsiteChange}
                />
            </div>

            <div className="anycomment anycomment-input-list-single anycomment-input-list-single-submit">
                <input type="submit" className="anycomment-btn send-comment-body__btn"
                       value={this.props.buttonText}/>
            </div>
        </div>
    }
}

export default SendCommentGuestv2;