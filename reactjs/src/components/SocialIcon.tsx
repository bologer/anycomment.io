import React from 'react'

export interface SocialIconProps {
    slug: string;
    classes?: string;
    alt: string;
}

export default function SocialIcon({slug, classes, alt}: SocialIconProps) {

    let className = "anycomment-social anycomment-" + slug;

    if (classes) {
        className += " " + classes;
    }

    const svgSrc = require('../img/socials/' + slug + '.svg');

    return <span className={className}><img src={svgSrc} alt={alt} /></span>;
}
