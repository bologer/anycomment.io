import TimeAgo from 'react-timeago';
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter';
import i18En from 'react-timeago/lib/language-strings/en';
import i18Ru from 'react-timeago/lib/language-strings/ru';
import {useSettings} from '~/hooks/setting';
import React from 'react';

export interface TimeagoProps {
    date: string;
}

/**
 * Renders timeago relative date.
 *
 * @param date
 * @constructor
 */
export default function Timeago({date, ...rest}: TimeagoProps) {
    const settings = useSettings();

    let languageStrings = i18En;

    const locale = settings.locale.substr(0, 2);
    if (locale === 'ru') {
        languageStrings = i18Ru;
    }

    const formatter = buildFormatter(languageStrings);

    return <TimeAgo date={date} formatter={formatter} {...rest} />;
}

Timeago.displayName = 'Timeago';
