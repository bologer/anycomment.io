export interface CommentOwner {
    is_post_author: boolean;
    is_social_login: boolean;
    social_type: null | string;
    profile_url: string;
}

export interface CommentItem {
    id: number;
    post: number;
    parent: number;
    parent_author_name: number;
    author: number;
    author_name: string;
    date: string; // e.g. "2020-02-14T17:38:14+03:00"
    date_gmt: string; // e.g. "2020-02-14T17:38:14+03:00"
    date_native: string; // e.g. "14.02.2020 17:38"
    content: string;
    avatar_url: string;
    children: null | CommentItem[];
    owner: CommentOwner;
    attachments: [];
    permissions: {
        [permission: string]: boolean;
    }
    can_edit_comment: boolean;
    meta: {
        has_like: boolean;
        has_dislike: boolean;
        status: string;
        count_text: string; // e.g. "1 comment"
        is_updated: boolean;
        updated_by: null | string;
        likes: number;
        dislikes: number;
        rating: number;
    }
}
