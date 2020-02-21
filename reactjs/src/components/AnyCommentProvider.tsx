import React, {Context} from "react";
import AnyCommentContext from "./AnyCommentContext";

export interface AuthorOption {
    data: {
        ID: number;
        user_login: string;
        user_nicename: string;
        user_email: string;
        user_url: string | null;
        display_name: string | null;
        user_avatar: string | null;
    },
    ID: number;
    caps: {
        [cap: string]: boolean;
    };
    cap_key: string;
    roles: string[];
    allcaps: {
        [cap: string]: boolean;
    }
}

export interface UrlOption {
    logout: string;
    postUrl: string;
}

export interface PostOption {
    id: number;
    permalink: string;
    comments_open: boolean;
}

export interface RatingOption {
    value: number;
    count: number;
    hasRated: boolean;
}

export type SocialType = 'vkontakte' |
    'twitter' |
    'facebook' |
    'google' |
    'github' |
    'odnoklassniki' |
    'instagram' |
    'twitch' |
    'dribbble' |
    'yandex' |
    'mailru' |
    'steam' |
    'yahoo' |
    'wordpress';

export interface SocialItemOption {
    slug: SocialType;
    url: string;
    label: string;
    color: string;
    visible: boolean;
}

export interface ConfigurationOption {
    limit: 10
    isCopyright: true
    socials: {
        [social: SocialType]: SocialItemOption
    };
    sort_order: 'desc' | 'ask';
    guestInputs: 'name' | 'email' | 'website'[];
    isShowUpdatedInfo: boolean;
    isNotifySubscribers: boolean;
    isShowProfileUrl: boolean;
    isShowImageAttachments: boolean;
    isShowVideoAttachments: boolean;
    isShowTwitterEmbeds: boolean;
    isModerateFirst: boolean;
    userAgreementLink: string;
    notifyOnNewComment: boolean;
    intervalCommentsCheck: number;
    isLoadOnScroll: boolean;
    isFormTypeAll: boolean;
    isFormTypeGuests: boolean;
    isFormTypeSocials: boolean;
    isFormTypeWordpress: boolean;
    isFileUploadAllowed: boolean;
    isGuestCanUpload: 'on';
    fileMimeTypes: string;
    fileLimit: number;;
    fileMaxSize: number;
    fileUploadLimit: number;
    isRatingOn: boolean;
    isReadMoreOn: boolean;
    commentRating: 'likes';
    dateFormat: 'relative';
    isEditorOn: boolean;
    editorToolbarOptions: 'bold' | 'italic' | 'underline' | 'blockquote' | 'ordered' | 'bullet' | 'link' | 'clean'[];
    reCaptchaOn: boolean;
    reCaptchaUserAll: boolean;
    reCaptchaUserGuest: boolean;
    reCaptchaUserAuth: boolean;
    reCaptchaSiteKey: null
    reCaptchaTheme: 'light';
    reCaptchaPosition: 'bottomright';
}

export interface i18nOption {
    error_generic: string;
    loading: string;
    load_more: string;
    waiting_moderation: string;
    edited: string;
    button_send: string;
    button_save: string;
    button_reply: string;
    sorting: string;
    sort_by: string;
    sort_oldest: string;
    sort_newest: string;
    reply_to: string;
    editing: string;
    add_comment: string;
    no_comments: string;
    footer_copyright: string;
    reply: string;
    edit: string;
    delete: string;
    comments_closed: string;
    subscribed: string;
    subscribe: string;
    subscribe_pre_paragraph: string;
    cancel: string;
    quick_login: string;
    guest: string;
    login: string;
    logout: string;
    comment_waiting_moderation: string;
    new_comment_was_added: string;
    author: string;
    name: string;
    email: string;
    website: string;
    already_rated: string;
    accept_user_agreement: string;
    upload_file: string;
    file_upload_in_progress: string;
    file_uploaded: string;
    file_too_big: string;
    file_limit: string;
    file_not_selected_or_extension: string;
    read_more: string;
    show_less: string;
    hide_this_message: string;
    login_with: string;
    or_as_guest: string;
    lighbox_close: string;
    lighbox_left_arrow: string;
    lighbox_right_arrow: string;
    lighbox_image_count_separator: string;
}

export interface SettingContextProps {
    postId: number;
    nonce: string;
    locale: string;
    restUrl: string;
    commentCount: number;
    errors: null | []
    user: AuthorOption | null;
    urls: UrlOption;
    post: PostOption;
    rating: RatingOption
    options: ConfigurationOption;
    i18: i18nOption;
}

export interface ConfigProps {
    root: string;
}

export interface ContextValueProps {
    settings: SettingContextProps;
    config: ConfigProps;
}

export interface AnyCommentProviderProps {
    children: React.ReactElement;
}

export default function AnyCommentProvider({settings, config, children}: AnyCommentProviderProps & ContextValueProps) {

    const Context: Context<ContextValueProps | {}> = AnyCommentContext;

    const contextValue = {
        settings,
        config
    };

    return <Context.Provider value={contextValue}>{children}</Context.Provider>;
}
