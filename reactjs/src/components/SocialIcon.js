import React, {Component} from 'react'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {
    faVk,
    faTwitter,
    faFacebookF,
    faGoogle,
    faGithub,
    faOdnoklassniki,
    faInstagram,
    faTwitch,
    faDribbble,
    faYahoo,
    faWordpress
} from '@fortawesome/free-brands-svg-icons'

export default class SocialIcon extends Component {
    render() {
        return this.getIcon(this.props.slug, this.props.color);
    };

    /**
     * Generate SVG icon based on provided slug.
     *
     * @param {String} slug
     * @param {String|null} color
     * @returns {*}
     */
    getIcon = (slug, color) => {
        const data = this.getIconBySlug(slug);

        if (data === null) {
            return '';
        }

        const style = {
            backgroundColor: color || data.color,
        };

        return <span className={"anycomment-social anycomment-" + this.slug}
                     style={style}>
            <FontAwesomeIcon icon={data.svg} color="#ffffff"/>
            </span>;
    };

    /**
     * Get SVG icon based on provided slug.
     *
     * @param slug
     * @returns {*|null}
     */
    getIconBySlug = (slug) => {
        let socialMap = [];

        socialMap['vkontakte'] = {color: '#45668E', svg: faVk};
        socialMap['twitter'] = {color: '#1DA1F2', svg: faTwitter};
        socialMap['facebook'] = {color: '#3B5998', svg: faFacebookF};
        socialMap['google'] = {color: '#DD4B39', svg: faGoogle};
        socialMap['github'] = {color: '#333333', svg: faGithub};
        socialMap['odnoklassniki'] = {color: '#ED812B', svg: faOdnoklassniki};
        socialMap['instagram'] = {color: '#C13584', svg: faInstagram};
        socialMap['twitch'] = {color: '#6441A5', svg: faTwitch};
        socialMap['dribbble'] = {color: '#EA4C89', svg: faDribbble};
        socialMap['yahoo'] = {color: '#410093', svg: faYahoo};
        socialMap['wordpress'] = {color: '#00a0d2', svg: faWordpress};

        return socialMap[slug] || null;
    }
}