import React, {CSSProperties} from 'react'
import {FontAwesomeIcon} from '@fortawesome/react-fontawesome'
import {IconProp, SizeProp} from "@fortawesome/fontawesome-svg-core";

export interface IconProps {
    icon: IconProp;
    size?: SizeProp;
    color?: string;
    style?: CSSProperties;
    className?: string;
}

export default function Icon({
    icon,
    size = 'sm',
    color,
    style,
    className,
}: IconProps) {

    let classes = 'anycomment anycomment-icon';
    const customClass = className || '';

    if (customClass) {
        classes += ' ' + customClass;
    }

    return <FontAwesomeIcon
        icon={icon}
        className={classes}
        size={size || 'sm'}
        color={color}
        style={style}
    />
}
