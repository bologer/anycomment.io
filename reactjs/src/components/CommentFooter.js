import React from 'react';
import AnyCommentComponent from "./AnyCommentComponent";
import Icon from './Icon'
import {faReply, faHeart, faEdit} from '@fortawesome/free-solid-svg-icons'

/**
 * CommentFooter renders single comment actions such as
 * edit, reply, like, etc.
 */
class CommentFooter extends AnyCommentComponent {

    constructor(props) {
        super();

        this.state = {
            likeClass: '',
            processLikeStates: false
        };
    }

    /**
     * Handle like action.
     */
    handleLike = (e) => {

        if (!this.state.processLikeStates) {
            this.setState({
                processLikeStates: true
            });
        }

        this.props.onLike(e);
    };

    render() {
        const settings = this.props.settings;
        const comment = this.props.comment;
        const isGuest = this.isGuest();

        const replyIcon = <Icon icon={faReply}/>;
        const likeIcon = <Icon icon={faHeart} style={this.props.hasLike ? {color: '#EC4568'} : ''}/>;
        const editIcon = <Icon icon={faEdit}/>;

        let likeActionClass = "anycomment comment-single-body__actions-like";

        if (this.state.processLikeStates) {
            likeActionClass += " comment-single-body__actions-like-" + (this.props.hasLike ? 'active' : 'static')
        }

        return (
            <footer className="anycomment comment-single-body__actions">
                <ul className="anycomment">
                    <li className="anycomment"><a className="anycomment" href="javascript:void(0)"
                                                  onClick={(e) => this.props.onReply(e)}>{replyIcon}{settings.i18.reply}</a>
                    </li>

                    {settings.options.commentRating === 'likes' ?
                        <li className="anycomment">
                            <a href="javascript:void(0)"
                               className={likeActionClass}
                               onClick={(e) => this.handleLike(e)}>{likeIcon}
                                <span itemProp="upvoteCount">{this.props.likesCount}</span>
                            </a>
                        </li> : ''}

                    {!isGuest && comment.permissions.can_edit_comment ?
                        <li className="anycomment"><a className="anycomment" href="javascript:void(0)"
                                                      onClick={(e) => this.props.onEdit(e)}>{editIcon}{settings.i18.edit}</a>
                        </li> : ''}
                </ul>
            </footer>
        );
    }
}


export default CommentFooter;