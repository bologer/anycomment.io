import React, {Component} from 'react'

export default class SocialIcon extends Component {
    render() {
        return this.getIcon();
    };

    /**
     * Generate SVG icon based on provided slug.
     *
     * @returns {*}
     */
    getIcon = () => {
        const {slug, classes} = this.props;

        let className = "anycomment-social anycomment-" + slug;

        if (classes) {
            className += " " + classes;
        }

        const svgSrc = require('../img/socials/' + slug + '.svg');

        return <span className={className}><img src={svgSrc}/></span>;
    };
}