import React from 'react';
import styled from 'styled-components';

interface SocialSvgMap {
    [socialKey: string]: {
        key: string;
        name: string;
        fill?: string;
        path?: string;
        svg?: React.ReactElement;
    };
}

const SOCIALS: SocialSvgMap = {
    vkontakte: {
        key: 'vkontakte',
        name: 'ВКонтакте',
        path:
            'M16.0234 7.26562C15.9766 7.5 15.7891 7.89844 15.4609 8.4375C15.2266 8.83594 14.9453 9.28125 14.6172 9.79688C14.3359 10.2188 14.1719 10.4297 14.1719 10.4297C14.0781 10.5703 14.0312 10.6875 14.0312 10.7578C14.0312 10.8516 14.0781 10.9453 14.1719 11.0391L14.3828 11.2734C15.5078 12.4453 16.1406 13.2422 16.2812 13.6641C16.3281 13.8516 16.3047 14.0156 16.2109 14.1094C16.1172 14.2031 16 14.25 15.8125 14.25H14.4297C14.2422 14.25 14.1016 14.2031 13.9609 14.0859C13.8672 14.0391 13.6797 13.875 13.4453 13.5938C13.2109 13.3125 13 13.0781 12.8125 12.8906C12.1797 12.3047 11.7109 12 11.4297 12C11.2891 12 11.1953 12.0234 11.1484 12.0703C11.1016 12.1172 11.0781 12.2344 11.0781 12.375C11.0547 12.4922 11.0547 12.7266 11.0547 13.1016V13.7109C11.0547 13.8984 11.0078 14.0391 10.9141 14.1094C10.7734 14.2031 10.4922 14.25 10.0703 14.25C9.32031 14.25 8.59375 14.0391 7.84375 13.5938C7.09375 13.1719 6.4375 12.5391 5.875 11.7188C5.33594 11.0156 4.89062 10.2891 4.53906 9.53906C4.25781 8.97656 4.02344 8.46094 3.85938 7.96875C3.74219 7.59375 3.69531 7.3125 3.69531 7.14844C3.69531 6.89062 3.83594 6.75 4.16406 6.75H5.54688C5.71094 6.75 5.82812 6.79688 5.92188 6.86719C6.01562 6.96094 6.08594 7.10156 6.15625 7.28906C6.36719 7.92188 6.625 8.53125 6.95312 9.11719C7.23438 9.67969 7.51562 10.125 7.79688 10.4531C8.07812 10.8047 8.28906 10.9688 8.45312 10.9688C8.54688 10.9688 8.59375 10.9453 8.64062 10.875C8.6875 10.8047 8.71094 10.6641 8.71094 10.4531V8.41406C8.6875 8.17969 8.64062 7.94531 8.54688 7.75781C8.5 7.66406 8.40625 7.54688 8.3125 7.40625C8.19531 7.26562 8.14844 7.17188 8.14844 7.07812C8.14844 6.98438 8.17188 6.91406 8.24219 6.84375C8.3125 6.79688 8.40625 6.75 8.5 6.75H10.6797C10.8203 6.75 10.9141 6.79688 10.9609 6.86719C11.0078 6.96094 11.0547 7.07812 11.0547 7.26562V9.98438C11.0547 10.125 11.0781 10.2422 11.125 10.2891C11.1719 10.3594 11.2188 10.3828 11.2891 10.3828C11.3594 10.3828 11.4531 10.3594 11.5469 10.3125C11.6406 10.2656 11.7578 10.1484 11.9219 9.96094C12.2031 9.63281 12.5078 9.23438 12.8125 8.74219C13 8.41406 13.2109 8.03906 13.3984 7.61719L13.6328 7.14844C13.7266 6.89062 13.9375 6.75 14.2188 6.75H15.6016C15.9766 6.75 16.1172 6.9375 16.0234 7.26562Z',
        fill: '#45668E',
    },
    dribbble: {
        key: 'dribbble',
        name: 'Dribbble',
        path:
            'M10 4.6875C11.0312 4.6875 12.0156 4.96875 12.9062 5.48438C13.7969 6 14.5 6.70312 15.0156 7.59375C15.5312 8.48438 15.8125 9.46875 15.8125 10.5C15.8125 11.5547 15.5312 12.5156 15.0156 13.4062C14.5 14.2969 13.7969 15.0234 12.9062 15.5391C12.0156 16.0547 11.0312 16.3125 10 16.3125C8.94531 16.3125 7.98438 16.0547 7.09375 15.5391C6.20312 15.0234 5.47656 14.2969 4.96094 13.4062C4.44531 12.5156 4.1875 11.5547 4.1875 10.5C4.1875 9.46875 4.44531 8.48438 4.96094 7.59375C5.47656 6.70312 6.20312 6 7.09375 5.48438C7.98438 4.96875 8.94531 4.6875 10 4.6875ZM13.8438 7.35938C13.6562 7.59375 13.4219 7.85156 13.1172 8.10938C12.5078 8.625 11.8281 9.02344 11.0781 9.32812C11.2188 9.65625 11.3594 9.98438 11.5 10.3125C12.2031 10.2188 12.9531 10.2188 13.7969 10.2891C14.2188 10.3359 14.6172 10.4062 14.9688 10.4531C14.9453 9.89062 14.8516 9.32812 14.6641 8.8125C14.4766 8.29688 14.1953 7.80469 13.8438 7.35938ZM13.2812 6.79688C12.6719 6.25781 11.9688 5.88281 11.1953 5.69531C10.3984 5.50781 9.625 5.48438 8.82812 5.67188C9.48438 6.5625 10.1172 7.54688 10.6797 8.57812C11.4297 8.32031 12.0625 7.96875 12.625 7.5C12.9062 7.26562 13.1406 7.03125 13.2812 6.79688ZM7.89062 6.02344C7.1875 6.35156 6.57812 6.82031 6.10938 7.42969C5.61719 8.03906 5.28906 8.74219 5.14844 9.49219C5.57031 9.49219 6.0625 9.46875 6.64844 9.42188C7.72656 9.32812 8.73438 9.16406 9.71875 8.88281C9.15625 7.875 8.54688 6.91406 7.89062 6.02344ZM5.03125 10.5C5.03125 11.1328 5.125 11.7422 5.35938 12.3047C5.59375 12.8672 5.89844 13.3828 6.32031 13.8281C6.74219 13.1016 7.30469 12.4453 8.05469 11.8359C8.85156 11.1797 9.67188 10.7344 10.5391 10.5C10.4219 10.2188 10.2812 9.9375 10.1406 9.65625C9.08594 9.98438 7.96094 10.1953 6.74219 10.2891C6.08594 10.3359 5.5 10.3594 5.03125 10.3594V10.5ZM6.95312 14.4141C7.65625 15 8.47656 15.3516 9.36719 15.4453C10.2578 15.5391 11.125 15.4219 11.9453 15.0703C11.7344 13.875 11.3828 12.6328 10.8906 11.3203C9.92969 11.6484 9.08594 12.1172 8.35938 12.7266C7.70312 13.2891 7.23438 13.8516 6.95312 14.4141ZM12.7656 14.625C13.3281 14.25 13.7969 13.7812 14.1719 13.1953C14.5469 12.6328 14.7812 12 14.8984 11.2969C14.6172 11.2031 14.2656 11.1328 13.8672 11.0859C13.1406 10.9922 12.4609 10.9922 11.8047 11.0859C12.25 12.3516 12.5781 13.5234 12.7656 14.625Z',
        fill: '#EA4C89',
    },
    facebook: {
        key: 'facebook',
        name: 'Facebook',
        path:
            'M8.71094 16.5H10.9141V11.1328H12.625L12.9062 9H10.9141V7.52344C10.9141 7.19531 10.9609 6.9375 11.1016 6.77344C11.2422 6.58594 11.5469 6.49219 11.9688 6.49219H13.0938V4.59375C12.6719 4.54688 12.1094 4.5 11.4531 4.5C10.6094 4.5 9.95312 4.75781 9.46094 5.25C8.94531 5.74219 8.71094 6.42188 8.71094 7.3125V9H6.90625V11.1328H8.71094V16.5Z',
        fill: '#3B5998',
    },
    github: {
        key: 'github',
        name: 'GitHub',
        path:
            'M8.07812 13.8047C8.07812 13.8047 8.05469 13.7812 8.03125 13.7578C8.00781 13.7578 7.98438 13.7344 7.9375 13.7344C7.84375 13.7344 7.82031 13.7812 7.82031 13.8281C7.82031 13.8984 7.84375 13.9219 7.9375 13.9219C8.03125 13.9219 8.07812 13.875 8.07812 13.8047ZM7.35156 13.7109C7.35156 13.6641 7.39844 13.6406 7.49219 13.6641C7.5625 13.6875 7.58594 13.7109 7.58594 13.7578C7.58594 13.8047 7.5625 13.8281 7.53906 13.8281C7.49219 13.8516 7.46875 13.8516 7.44531 13.8281C7.39844 13.8281 7.375 13.8047 7.35156 13.7812C7.32812 13.7578 7.32812 13.7344 7.35156 13.7109ZM8.38281 13.6641C8.45312 13.6641 8.5 13.6875 8.5 13.7344C8.5 13.7812 8.47656 13.8281 8.40625 13.8516C8.35938 13.8516 8.33594 13.8516 8.3125 13.8516C8.28906 13.8516 8.26562 13.8047 8.26562 13.7578C8.26562 13.7109 8.28906 13.6875 8.38281 13.6641ZM9.92969 4.6875C10.9844 4.6875 11.9688 4.94531 12.8828 5.4375C13.7734 5.95312 14.5 6.63281 15.0156 7.47656C15.5312 8.36719 15.8125 9.35156 15.8125 10.4062C15.8125 11.7188 15.4375 12.8672 14.7109 13.8984C13.9844 14.9297 13.0469 15.6328 11.875 16.0078C11.7344 16.0547 11.6406 16.0312 11.5703 15.9609C11.5 15.9141 11.4766 15.8203 11.4766 15.7266V13.7578C11.4766 13.2656 11.3359 12.9141 11.0781 12.6797C11.6406 12.6328 12.0859 12.5391 12.3672 12.4453C12.7891 12.3047 13.1406 12.0703 13.375 11.7422C13.6328 11.3438 13.7734 10.7812 13.7734 10.0781C13.7734 9.79688 13.7031 9.53906 13.6094 9.30469C13.5156 9.16406 13.375 8.95312 13.1641 8.71875C13.2109 8.57812 13.2578 8.39062 13.2812 8.15625C13.2812 7.82812 13.2344 7.47656 13.1172 7.125C12.9531 7.07812 12.7422 7.125 12.4609 7.21875C12.2734 7.3125 12.0391 7.40625 11.8047 7.54688L11.5 7.75781C11.0078 7.61719 10.5156 7.54688 10 7.54688C9.48438 7.54688 9.01562 7.61719 8.54688 7.75781L8.24219 7.54688C7.98438 7.40625 7.75 7.3125 7.5625 7.21875C7.28125 7.125 7.07031 7.07812 6.92969 7.125C6.78906 7.47656 6.71875 7.82812 6.76562 8.15625C6.76562 8.39062 6.78906 8.55469 6.85938 8.69531C6.67188 8.92969 6.53125 9.16406 6.4375 9.35156C6.34375 9.53906 6.32031 9.79688 6.32031 10.0781C6.32031 10.7812 6.4375 11.3438 6.69531 11.7188C6.90625 12.0469 7.23438 12.3047 7.67969 12.4453C7.96094 12.5391 8.38281 12.6328 8.94531 12.6797C8.73438 12.8672 8.61719 13.1484 8.57031 13.4766C8.28906 13.6172 8.03125 13.6641 7.79688 13.6172C7.375 13.5938 7.07031 13.3828 6.85938 12.9844C6.76562 12.8203 6.625 12.6797 6.48438 12.5625C6.36719 12.5156 6.25 12.4453 6.13281 12.3984L5.96875 12.375C5.78125 12.375 5.6875 12.4219 5.6875 12.4688C5.6875 12.5156 5.71094 12.5859 5.80469 12.6328L5.92188 12.7266C6.01562 12.7969 6.13281 12.8906 6.25 13.0312C6.34375 13.1484 6.41406 13.2656 6.48438 13.4062L6.57812 13.5938C6.64844 13.8281 6.78906 14.0156 7.02344 14.1328C7.21094 14.25 7.42188 14.3203 7.70312 14.3438C7.89062 14.3672 8.07812 14.3672 8.28906 14.3203L8.54688 14.2969L8.57031 15.7266C8.57031 15.8203 8.52344 15.9141 8.45312 15.9609C8.38281 16.0312 8.28906 16.0547 8.17188 16.0078C6.97656 15.6328 6.01562 14.9297 5.28906 13.8984C4.53906 12.8672 4.1875 11.7188 4.1875 10.4062C4.1875 9.35156 4.42188 8.39062 4.9375 7.5C5.42969 6.63281 6.10938 5.95312 7 5.4375C7.86719 4.94531 8.85156 4.6875 9.92969 4.6875ZM6.46094 12.7734C6.48438 12.75 6.53125 12.75 6.57812 12.7969C6.625 12.8438 6.625 12.8906 6.60156 12.9141C6.55469 12.9609 6.53125 12.9375 6.48438 12.8906C6.4375 12.8438 6.41406 12.8203 6.46094 12.7734ZM6.20312 12.5859C6.22656 12.5625 6.27344 12.5625 6.32031 12.5859C6.36719 12.6094 6.36719 12.6328 6.36719 12.6797C6.34375 12.7266 6.29688 12.7266 6.25 12.6797C6.20312 12.6562 6.17969 12.6328 6.20312 12.5859ZM6.97656 13.4062C6.97656 13.4062 7 13.4062 7.04688 13.4062C7.07031 13.4062 7.09375 13.4297 7.11719 13.4531C7.14062 13.4766 7.16406 13.5 7.16406 13.5234C7.16406 13.5703 7.16406 13.5938 7.16406 13.5938C7.11719 13.6406 7.04688 13.6406 7 13.5703C6.95312 13.5469 6.95312 13.5234 6.95312 13.4766C6.95312 13.4531 6.95312 13.4297 6.97656 13.4062ZM6.69531 13.0781C6.71875 13.0781 6.74219 13.0781 6.76562 13.0781C6.78906 13.0781 6.8125 13.1016 6.83594 13.125C6.88281 13.1953 6.88281 13.2422 6.83594 13.2656C6.8125 13.2891 6.78906 13.2891 6.76562 13.2656C6.74219 13.2656 6.71875 13.2422 6.69531 13.2188C6.64844 13.1484 6.64844 13.1016 6.69531 13.0781Z',
        fill: '#333333',
    },
    google: {
        key: 'google',
        name: 'Google',
        svg: (
            <svg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 40 40'>
                <g fill='none' fillRule='evenodd'>
                    <circle cx='20' cy='20' r='20' fill='#FFF' />
                    <g fillRule='nonzero'>
                        <path
                            fill='#FFF'
                            d='M11.3 1.5C7.6 2.8 4.4 5.6 2.6 9.1 2 10.3 1.5 11.6 1.3 13c-.7 3.3-.2 6.9 1.3 9.9 1 2 2.4 3.7 4.2 5.1 1.6 1.3 3.5 2.3 5.6 2.8 2.6.7 5.3.7 7.8.1 2.3-.5 4.5-1.6 6.3-3.3 1.9-1.7 3.2-3.9 3.9-6.4.8-2.6.9-5.4.4-8.1H16.4v6h8.3c-.3 1.9-1.5 3.7-3.1 4.7-1 .7-2.2 1.1-3.4 1.3-1.2.2-2.5.2-3.7 0-1.2-.2-2.4-.8-3.4-1.5-1.6-1.1-2.9-2.8-3.5-4.7-.7-1.9-.7-4 0-6 .5-1.3 1.2-2.6 2.2-3.6C11 8 12.6 7.1 14.4 6.8c1.5-.3 3-.3 4.5.2 1.2.4 2.4 1.1 3.3 1.9L25 6.1l1.5-1.5c-1.4-1.3-3.1-2.4-4.9-3.1-3.3-1.1-7-1.1-10.3 0z'
                            transform='translate(4 4)'
                        />
                        <path
                            fill='#EA4335'
                            d='M11.3 1.5c3.3-1.1 7-1.1 10.3.1 1.8.7 3.5 1.7 4.9 3.1L25 6.2 22.2 9c-.9-.9-2.1-1.6-3.3-1.9-1.4-.4-3-.5-4.5-.2-1.7.4-3.3 1.3-4.6 2.5-1 1-1.8 2.3-2.2 3.6-1.7-1.3-3.3-2.6-5-3.9 1.8-3.5 5-6.3 8.7-7.6z'
                            transform='translate(4 4)'
                        />
                        <path
                            fill='#FBBC05'
                            d='M1.3 13c.3-1.3.7-2.6 1.3-3.9 1.7 1.3 3.3 2.6 5 3.9-.7 1.9-.7 4 0 6-1.7 1.3-3.3 2.6-5 3.9-1.5-3.1-2-6.6-1.3-9.9z'
                            transform='translate(4 4)'
                        />
                        <path
                            fill='#4285F4'
                            d='M16.3 13.1h14.4c.5 2.7.4 5.5-.4 8.1-.7 2.4-2 4.6-3.9 6.4-1.6-1.3-3.2-2.5-4.9-3.8 1.6-1.1 2.7-2.8 3.1-4.7h-8.3v-6z'
                            transform='translate(4 4)'
                        />
                        <path
                            fill='#34A853'
                            d='M2.6 22.9c1.7-1.3 3.3-2.6 5-3.9.6 1.9 1.9 3.6 3.5 4.7 1 .7 2.2 1.2 3.4 1.5 1.2.2 2.4.2 3.7 0 1.2-.2 2.4-.7 3.4-1.3 1.6 1.3 3.2 2.5 4.9 3.8-1.8 1.6-3.9 2.7-6.3 3.3-2.6.6-5.3.6-7.8-.1-2-.5-3.9-1.5-5.6-2.8-1.7-1.5-3.2-3.3-4.2-5.2z'
                            transform='translate(4 4)'
                        />
                    </g>
                </g>
            </svg>
        ),
        fill: '#DD4B39',
    },
    instagram: {
        key: 'instagram',
        name: 'Instagram',
        path:
            'M10 7.80469C10.4688 7.80469 10.9141 7.94531 11.3359 8.17969C11.7578 8.41406 12.0859 8.74219 12.3203 9.16406C12.5547 9.58594 12.6953 10.0312 12.6953 10.5C12.6953 10.9922 12.5547 11.4375 12.3203 11.8594C12.0859 12.2812 11.7578 12.6094 11.3359 12.8438C10.9141 13.0781 10.4688 13.1953 10 13.1953C9.50781 13.1953 9.0625 13.0781 8.64062 12.8438C8.21875 12.6094 7.89062 12.2812 7.65625 11.8594C7.42188 11.4375 7.30469 10.9922 7.30469 10.5C7.30469 10.0312 7.42188 9.58594 7.65625 9.16406C7.89062 8.74219 8.21875 8.41406 8.64062 8.17969C9.0625 7.94531 9.50781 7.80469 10 7.80469ZM10 12.2578C10.4688 12.2578 10.8906 12.0938 11.2422 11.7422C11.5703 11.4141 11.7578 10.9922 11.7578 10.5C11.7578 10.0312 11.5703 9.60938 11.2422 9.25781C10.8906 8.92969 10.4688 8.74219 10 8.74219C9.50781 8.74219 9.08594 8.92969 8.75781 9.25781C8.40625 9.60938 8.24219 10.0312 8.24219 10.5C8.24219 10.9922 8.40625 11.4141 8.75781 11.7422C9.08594 12.0938 9.50781 12.2578 10 12.2578ZM13.4453 7.6875C13.4453 7.52344 13.375 7.38281 13.2578 7.24219C13.1172 7.125 12.9766 7.05469 12.8125 7.05469C12.625 7.05469 12.4844 7.125 12.3672 7.24219C12.2266 7.38281 12.1797 7.52344 12.1797 7.6875C12.1797 7.875 12.2266 8.01562 12.3672 8.13281C12.4844 8.27344 12.625 8.32031 12.8125 8.32031C12.9766 8.32031 13.1172 8.27344 13.2344 8.13281C13.3516 8.01562 13.4219 7.875 13.4453 7.6875ZM15.2266 8.32031C15.2266 8.76562 15.25 9.49219 15.25 10.5C15.25 11.5312 15.2266 12.2578 15.2031 12.7031C15.1797 13.1484 15.1094 13.5234 15.0156 13.8516C14.875 14.25 14.6406 14.6016 14.3594 14.8828C14.0781 15.1641 13.7266 15.375 13.3516 15.5156C13.0234 15.6328 12.625 15.7031 12.1797 15.7266C11.7344 15.75 11.0078 15.75 10 15.75C8.96875 15.75 8.24219 15.75 7.79688 15.7266C7.35156 15.7031 6.97656 15.6328 6.64844 15.4922C6.25 15.375 5.89844 15.1641 5.61719 14.8828C5.33594 14.6016 5.125 14.25 4.98438 13.8516C4.86719 13.5234 4.79688 13.1484 4.77344 12.7031C4.75 12.2578 4.75 11.5312 4.75 10.5C4.75 9.49219 4.75 8.76562 4.77344 8.32031C4.79688 7.875 4.86719 7.47656 4.98438 7.14844C5.125 6.77344 5.33594 6.42188 5.61719 6.14062C5.89844 5.85938 6.25 5.625 6.64844 5.48438C6.97656 5.39062 7.35156 5.32031 7.79688 5.29688C8.24219 5.27344 8.96875 5.25 10 5.25C11.0078 5.25 11.7344 5.27344 12.1797 5.29688C12.625 5.32031 13.0234 5.39062 13.3516 5.48438C13.7266 5.625 14.0781 5.85938 14.3594 6.14062C14.6406 6.42188 14.875 6.77344 15.0156 7.14844C15.1094 7.47656 15.1797 7.875 15.2266 8.32031ZM14.1016 13.5938C14.1953 13.3359 14.2422 12.9141 14.2891 12.3281C14.2891 12 14.3125 11.5078 14.3125 10.875V10.125C14.3125 9.49219 14.2891 9 14.2891 8.67188C14.2422 8.08594 14.1953 7.66406 14.1016 7.40625C13.9141 6.9375 13.5625 6.58594 13.0938 6.39844C12.8359 6.30469 12.4141 6.25781 11.8281 6.21094C11.4766 6.21094 10.9844 6.1875 10.375 6.1875H9.625C8.99219 6.1875 8.5 6.21094 8.17188 6.21094C7.58594 6.25781 7.16406 6.30469 6.90625 6.39844C6.41406 6.58594 6.08594 6.9375 5.89844 7.40625C5.80469 7.66406 5.73438 8.08594 5.71094 8.67188C5.6875 9.02344 5.6875 9.51562 5.6875 10.125V10.875C5.6875 11.5078 5.6875 12 5.71094 12.3281C5.73438 12.9141 5.80469 13.3359 5.89844 13.5938C6.08594 14.0859 6.4375 14.4141 6.90625 14.6016C7.16406 14.6953 7.58594 14.7656 8.17188 14.7891C8.5 14.8125 8.99219 14.8125 9.625 14.8125H10.375C11.0078 14.8125 11.5 14.8125 11.8281 14.7891C12.4141 14.7656 12.8359 14.6953 13.0938 14.6016C13.5625 14.4141 13.9141 14.0625 14.1016 13.5938Z',
        fill: '#C13584',
    },
    mailru: {
        key: 'mailru',
        name: 'Mail.Ru',
        svg: (
            <svg width='30' height='30' viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                <rect width='20' height='20' rx='10' fill='#005FF9' />
                <g clipPath='url(#clip0)'>
                    <path
                        fillRule='evenodd'
                        clipRule='evenodd'
                        d='M10.0231 4C13.3196 4.0154 16 6.69576 16 10.0077C16 10.4698 15.9538 10.8549 15.8768 11.2401C15.8768 11.2401 15.8306 11.4095 15.8151 11.4865C15.6611 11.9641 15.3992 12.3492 15.0449 12.6264C14.6906 12.8883 14.2593 13.0424 13.828 13.0424C13.7664 13.0424 13.7202 13.0424 13.6739 13.0424C13.0578 12.9961 12.5032 12.6727 12.1797 12.1489C11.6098 12.7343 10.8395 13.0424 10.0231 13.0424C8.34403 13.0424 6.98845 11.6868 6.98845 10.0077C6.98845 8.32863 8.34403 6.97304 10.0231 6.97304C11.7022 6.97304 13.0424 8.32863 13.0424 10.0077V10.9936C13.0424 11.5635 13.4275 11.7946 13.751 11.8254C14.0745 11.8562 14.5058 11.656 14.6598 11.0398C14.7368 10.7009 14.7677 10.362 14.7677 10.0077C14.7677 7.37356 12.6264 5.23235 10.0077 5.23235C7.37356 5.23235 5.23235 7.37356 5.23235 10.0077C5.23235 12.6418 7.37356 14.7831 10.0077 14.7831C10.9166 14.7831 11.81 14.5212 12.5802 14.0282L12.5956 14.0128L13.3967 14.9525L13.3813 14.9679C12.38 15.6457 11.2092 16.0154 10.0077 16.0154C6.69576 16.0154 4 13.3196 4 10.0077C4 6.69576 6.69576 4 10.0077 4H10.0231ZM11.81 10.0077C11.81 11.009 10.9936 11.81 10.0077 11.81C9.00642 11.81 8.20539 10.9936 8.20539 10.0077C8.20539 9.00642 9.02182 8.20539 10.0077 8.20539C10.9936 8.18999 11.81 9.00642 11.81 10.0077Z'
                        fill='#fff'
                    />
                </g>
                <defs>
                    <clipPath id='clip0'>
                        <rect width='12' height='12' fill='#fff' transform='translate(4 4)' />
                    </clipPath>
                </defs>
            </svg>
        ),
    },
    odnoklassniki: {
        key: 'odnoklassniki',
        name: 'Одноклассники',
        path:
            'M12.6953 12.3281C13.1172 12.0469 13.375 11.8359 13.4922 11.6719C13.6328 11.4844 13.6328 11.2266 13.5156 10.9453C13.3984 10.7578 13.2344 10.6172 13.0469 10.5703C12.7656 10.5234 12.5078 10.5938 12.2266 10.8047L11.9453 10.9922C11.7109 11.1328 11.4531 11.2266 11.1953 11.3203C10.7969 11.4375 10.3984 11.4844 10 11.4844C9.57812 11.4844 9.17969 11.4375 8.80469 11.3203C8.52344 11.2266 8.28906 11.1328 8.05469 10.9922L7.77344 10.8047C7.49219 10.5938 7.21094 10.5234 6.97656 10.5703C6.74219 10.6172 6.60156 10.7578 6.50781 10.9453C6.36719 11.2266 6.36719 11.4844 6.50781 11.6719C6.60156 11.8359 6.88281 12.0469 7.30469 12.3281C7.79688 12.6562 8.5 12.8672 9.4375 12.9609L7.16406 15.2344C6.92969 15.4453 6.88281 15.6797 6.97656 15.9375C7.07031 16.1953 7.23438 16.3594 7.51562 16.4531C7.77344 16.5469 8.00781 16.5 8.21875 16.2891L10 14.5078L11.8047 16.3125C11.9922 16.5234 12.2266 16.5703 12.4844 16.4766C12.7422 16.3828 12.9297 16.1953 13.0234 15.9375C13.1172 15.6797 13.0469 15.4453 12.8594 15.2344L10.5859 12.9609C11.4766 12.8672 12.1797 12.6562 12.6953 12.3281ZM6.95312 7.54688C6.95312 8.10938 7.07031 8.60156 7.35156 9.07031C7.63281 9.53906 8.00781 9.89062 8.47656 10.1719C8.94531 10.4531 9.4375 10.5703 10 10.5703C10.5391 10.5703 11.0547 10.4531 11.5234 10.1719C11.9922 9.89062 12.3438 9.53906 12.625 9.07031C12.9062 8.60156 13.0469 8.10938 13.0469 7.54688C13.0469 6.98438 12.9062 6.49219 12.625 6.02344C12.3438 5.55469 11.9922 5.20312 11.5234 4.92188C11.0547 4.64062 10.5391 4.5 10 4.5C9.4375 4.5 8.94531 4.64062 8.47656 4.92188C8.00781 5.20312 7.63281 5.55469 7.35156 6.02344C7.07031 6.49219 6.95312 6.98438 6.95312 7.54688ZM8.5 7.54688C8.5 7.125 8.64062 6.77344 8.94531 6.49219C9.22656 6.21094 9.57812 6.04688 10 6.04688C10.3984 6.04688 10.75 6.21094 11.0547 6.49219C11.3359 6.77344 11.5 7.125 11.5 7.54688C11.5 7.96875 11.3359 8.32031 11.0547 8.60156C10.75 8.88281 10.3984 9.02344 10 9.02344C9.57812 9.02344 9.22656 8.88281 8.94531 8.60156C8.64062 8.32031 8.5 7.96875 8.5 7.54688Z',
        fill: '#ED812B',
    },
    steam: {
        key: 'steam',
        name: 'Steam',
        path:
            'M16.7812 9.75C16.7812 8.54688 16.4531 7.39844 15.8516 6.35938C15.25 5.32031 14.4297 4.5 13.3906 3.89844C12.3516 3.29688 11.2031 2.96875 10 2.96875C8.79688 2.96875 7.70312 3.26953 6.71875 3.81641C5.73438 4.36328 4.91406 5.12891 4.28516 6.05859C3.65625 7.01562 3.30078 8.05469 3.21875 9.20312L6.85547 10.707C7.21094 10.4883 7.62109 10.3789 8.05859 10.3789L9.67188 8.02734V8C9.67188 7.28906 9.91797 6.6875 10.4102 6.19531C10.9023 5.70312 11.5039 5.45703 12.2148 5.45703C12.9258 5.45703 13.5273 5.70312 14.0195 6.19531C14.5117 6.6875 14.7852 7.28906 14.7852 8C14.7852 8.71094 14.5117 9.33984 14.0195 9.83203C13.5 10.3516 12.8711 10.5977 12.1602 10.5703L9.86328 12.2109C9.86328 12.7578 9.69922 13.25 9.31641 13.6328C8.93359 14.0156 8.46875 14.207 7.94922 14.207C7.45703 14.207 7.04688 14.0703 6.71875 13.7695C6.36328 13.4961 6.14453 13.1133 6.0625 12.6758L3.46484 11.6094C3.71094 12.5391 4.14844 13.3867 4.80469 14.1523C5.43359 14.918 6.19922 15.4922 7.10156 15.9023C8.00391 16.3398 8.96094 16.5312 10 16.5312C11.2031 16.5312 12.3516 16.2305 13.3906 15.6289C14.4297 15.0273 15.25 14.1797 15.8516 13.1406C16.4531 12.1016 16.7812 10.9805 16.7812 9.75ZM7.48438 13.25C7.75781 13.3594 8.00391 13.3594 8.27734 13.25C8.55078 13.1406 8.74219 12.9492 8.85156 12.6758C8.96094 12.4023 8.96094 12.1562 8.85156 11.8828C8.74219 11.6094 8.55078 11.418 8.30469 11.3086L7.42969 10.9531C7.78516 10.8164 8.14062 10.8164 8.49609 10.9805C8.85156 11.1445 9.09766 11.3906 9.26172 11.7461C9.42578 12.1289 9.42578 12.4844 9.26172 12.8398C9.09766 13.2227 8.85156 13.4688 8.49609 13.6328C8.11328 13.7969 7.75781 13.7969 7.40234 13.6328C7.04688 13.4961 6.80078 13.25 6.63672 12.9219L7.48438 13.25ZM12.2422 9.69531C12.6797 9.69531 13.0898 9.53125 13.418 9.20312C13.7461 8.875 13.9375 8.49219 13.9375 8C13.9375 7.53516 13.7461 7.125 13.418 6.79688C13.0898 6.46875 12.6797 6.30469 12.2148 6.30469C11.75 6.30469 11.3398 6.46875 11.0117 6.79688C10.6836 7.125 10.5195 7.53516 10.5195 8C10.5195 8.49219 10.6836 8.875 11.0117 9.20312C11.3398 9.53125 11.75 9.69531 12.2422 9.69531ZM12.2422 9.28516C11.8594 9.28516 11.5586 9.17578 11.3125 8.90234C11.0664 8.65625 10.957 8.35547 10.957 8C10.957 7.67188 11.0664 7.37109 11.3125 7.09766C11.5586 6.85156 11.8594 6.71484 12.2148 6.71484C12.5703 6.71484 12.8711 6.85156 13.1445 7.09766C13.3906 7.37109 13.5273 7.67188 13.5273 8C13.5273 8.35547 13.3906 8.65625 13.1445 8.90234C12.8711 9.17578 12.5703 9.28516 12.2422 9.28516Z',
        fill: '#333333',
    },
    telegram: {
        key: 'telegram',
        name: 'Telegram',
        path:
            'M15.2266 6.82031C15.2734 6.49219 15.25 6.28125 15.1094 6.14062C14.9688 6 14.7812 5.97656 14.5469 6.04688L5.24219 9.63281C4.91406 9.77344 4.75 9.91406 4.75 10.0547C4.72656 10.2188 4.84375 10.3359 5.125 10.4062L7.51562 11.1562L13.0469 7.66406C13.1172 7.61719 13.1875 7.59375 13.2578 7.59375C13.3281 7.59375 13.375 7.59375 13.3984 7.64062C13.4219 7.6875 13.3984 7.71094 13.3516 7.75781L8.875 11.8125L8.6875 14.2734C8.80469 14.2734 8.89844 14.25 8.99219 14.2031C9.03906 14.1797 9.08594 14.1328 9.17969 14.0391L10.3516 12.9141L12.7656 14.6953C12.9766 14.8359 13.1641 14.8594 13.3047 14.7891C13.4453 14.7188 13.5625 14.5547 13.6328 14.2734L15.2266 6.82031Z',
        fill: '#0088CC',
    },
    twitch: {
        key: 'twitch',
        name: 'Twitch',
        path:
            'M5.6875 5.25H15.0156V11.6719L12.2734 14.4141H10.2344L8.89844 15.75H7.49219V14.4141H4.98438V7.05469L5.6875 5.25ZM14.0781 11.2031V6.1875H6.55469V12.7734H8.66406V14.1094L10 12.7734H12.5078L14.0781 11.2031ZM12.5078 7.99219H11.5703V10.7344H12.5078V7.99219ZM10 7.99219H9.0625V10.7344H10V7.99219Z',
        fill: '#6441A5',
    },
    twitter: {
        key: 'twitter',
        name: 'Twitter',
        path:
            'M14.7578 8.0625C15.2266 7.71094 15.6484 7.28906 16 6.77344C15.5312 6.98438 15.0625 7.125 14.5938 7.17188C15.1094 6.84375 15.4844 6.39844 15.6719 5.8125C15.1797 6.09375 14.6641 6.30469 14.1016 6.39844C13.8672 6.16406 13.5859 5.97656 13.2812 5.83594C12.9766 5.69531 12.6484 5.625 12.2969 5.625C11.8516 5.625 11.4531 5.74219 11.0781 5.95312C10.7031 6.1875 10.3984 6.49219 10.1875 6.86719C9.95312 7.24219 9.85938 7.66406 9.85938 8.08594C9.85938 8.27344 9.85938 8.46094 9.90625 8.64844C8.89844 8.60156 7.96094 8.36719 7.07031 7.89844C6.17969 7.45312 5.45312 6.84375 4.84375 6.07031C4.60938 6.46875 4.49219 6.89062 4.49219 7.3125C4.49219 7.73438 4.58594 8.13281 4.79688 8.48438C4.98438 8.85938 5.26562 9.14062 5.59375 9.375C5.19531 9.375 4.82031 9.25781 4.49219 9.04688V9.09375C4.49219 9.67969 4.67969 10.1953 5.05469 10.6406C5.42969 11.1094 5.89844 11.3906 6.46094 11.5078C6.22656 11.5547 6.01562 11.5781 5.80469 11.5781C5.66406 11.5781 5.5 11.5781 5.35938 11.5547C5.5 12.0469 5.78125 12.4453 6.20312 12.7734C6.625 13.1016 7.09375 13.2422 7.65625 13.2422C6.74219 13.9453 5.71094 14.2969 4.58594 14.2969C4.35156 14.2969 4.16406 14.2969 4 14.2734C5.125 15.0234 6.39062 15.375 7.77344 15.375C9.20312 15.375 10.4688 15.0234 11.5938 14.2734C12.6016 13.6172 13.3984 12.75 13.9609 11.625C14.5 10.5938 14.7812 9.49219 14.7812 8.36719C14.7812 8.22656 14.7578 8.13281 14.7578 8.0625Z',
        fill: '#1DA1F2',
    },
    wordpress: {
        key: 'wordpress',
        name: 'WordPress',
        path:
            'M10 4.6875C11.0312 4.6875 12.0156 4.96875 12.9062 5.48438C13.7969 6 14.5 6.70312 15.0156 7.59375C15.5312 8.48438 15.8125 9.46875 15.8125 10.5C15.8125 11.5547 15.5312 12.5156 15.0156 13.4062C14.5 14.2969 13.7969 15.0234 12.9062 15.5391C12.0156 16.0547 11.0312 16.3125 10 16.3125C8.94531 16.3125 7.98438 16.0547 7.09375 15.5391C6.20312 15.0234 5.47656 14.2969 4.96094 13.4062C4.44531 12.5156 4.1875 11.5547 4.1875 10.5C4.1875 9.46875 4.44531 8.48438 4.96094 7.59375C5.47656 6.70312 6.20312 6 7.09375 5.48438C7.98438 4.96875 8.94531 4.6875 10 4.6875ZM4.77344 10.5C4.77344 11.5312 5.03125 12.4453 5.57031 13.2891C6.10938 14.1328 6.83594 14.7891 7.72656 15.2109L5.21875 8.36719C4.91406 9.04688 4.77344 9.75 4.77344 10.5ZM10 15.7266C10.5859 15.7266 11.1719 15.6328 11.7344 15.4219L11.6875 15.3516L10.0938 10.9453L8.52344 15.5156C8.99219 15.6562 9.48438 15.7266 10 15.7266ZM10.7266 8.03906L12.6016 13.6641L13.1641 11.8125C13.2812 11.3906 13.375 11.1094 13.4219 10.9219C13.4922 10.6641 13.5391 10.4297 13.5391 10.2422C13.5391 9.96094 13.4922 9.67969 13.3984 9.39844C13.3281 9.23438 13.2344 9.02344 13.0938 8.78906L13.0234 8.69531C12.8828 8.46094 12.7656 8.27344 12.7188 8.13281C12.625 7.94531 12.5781 7.75781 12.5781 7.57031C12.5781 7.33594 12.6484 7.10156 12.8359 6.91406C13 6.72656 13.2109 6.63281 13.4688 6.63281L13.5391 6.65625C13.0469 6.23438 12.5078 5.88281 11.8984 5.64844C11.2891 5.41406 10.6562 5.27344 10 5.27344C9.10938 5.27344 8.26562 5.48438 7.49219 5.90625C6.71875 6.32812 6.10938 6.91406 5.64062 7.61719L5.96875 7.64062C6.20312 7.64062 6.50781 7.64062 6.88281 7.59375L7.35156 7.57031C7.44531 7.57031 7.49219 7.61719 7.53906 7.66406C7.58594 7.73438 7.58594 7.80469 7.5625 7.875C7.53906 7.96875 7.49219 7.99219 7.39844 7.99219L6.78906 8.03906L8.71094 13.7109L9.83594 10.2891L9.03906 8.0625L8.47656 7.99219C8.38281 7.99219 8.33594 7.96875 8.3125 7.89844C8.26562 7.82812 8.26562 7.75781 8.3125 7.6875C8.35938 7.61719 8.42969 7.57031 8.52344 7.57031L9.01562 7.59375C9.39062 7.64062 9.67188 7.64062 9.90625 7.64062C10.1172 7.64062 10.4219 7.64062 10.7969 7.59375L11.2891 7.57031C11.3594 7.57031 11.4297 7.61719 11.4766 7.66406C11.5234 7.73438 11.5234 7.80469 11.5 7.875C11.4531 7.96875 11.4062 7.99219 11.3125 7.99219C11.1016 8.01562 10.9141 8.03906 10.7266 8.03906ZM12.625 15.0234C13.4219 14.5547 14.0547 13.9219 14.5234 13.125C14.9922 12.3281 15.2266 11.4609 15.2266 10.5C15.2266 9.60938 15.0156 8.78906 14.5938 7.99219C14.5938 8.17969 14.6172 8.34375 14.6172 8.53125C14.6172 9.11719 14.4766 9.75 14.2188 10.4062L12.625 15.0234Z',
        fill: '#00A0D2',
    },
    yahoo: {
        key: 'yahoo',
        name: 'Yahoo',
        path:
            'M10.6562 11.3438C12.7891 7.59375 14.1484 5.32031 14.7578 4.5C14.5 4.57031 14.2188 4.59375 13.9375 4.59375C13.6562 4.59375 13.3984 4.57031 13.1641 4.5C12.6484 5.4375 11.875 6.77344 10.7969 8.50781L10 9.77344L9.4375 8.85938C8.21875 6.91406 7.35156 5.46094 6.83594 4.5C6.55469 4.57031 6.27344 4.59375 5.99219 4.59375C5.71094 4.59375 5.47656 4.57031 5.24219 4.5C5.75781 5.25 6.29688 6.11719 6.88281 7.10156C7.23438 7.71094 7.75 8.57812 8.40625 9.75L9.32031 11.3438L9.22656 16.5C9.60156 16.4531 9.85938 16.4062 10 16.4062C10.1406 16.4062 10.375 16.4531 10.75 16.5L10.6562 11.3438Z',
        fill: '#410093',
    },
    yandex: {
        key: 'yandex',
        name: 'Яндекс',
        path:
            'M10.5859 11.9062H11.6641V16.5H12.9531V4.5H11.0312C10.375 4.5 9.76562 4.66406 9.25 4.94531C8.73438 5.22656 8.3125 5.64844 8.00781 6.1875C7.67969 6.77344 7.53906 7.47656 7.53906 8.27344C7.53906 9.89062 8.125 10.9922 9.29688 11.5781L7.04688 16.5H8.54688L10.5859 11.9062ZM11.6641 5.57812V10.8281H10.9609C10.3516 10.8281 9.85938 10.6406 9.53125 10.2656C9.10938 9.84375 8.92188 9.1875 8.92188 8.27344C8.92188 7.35938 9.13281 6.65625 9.55469 6.1875C9.90625 5.78906 10.375 5.57812 10.9844 5.57812H11.6641Z',
        fill: '#FFCC00',
    },
    whatsapp: {
        key: 'whatsapp',
        name: 'WhatsApp',
        svg: (
            <svg
                width='25px'
                height='25px'
                viewBox='0 0 34 34'
                version='1.1'
                xmlns='http://www.w3.org/2000/svg'
                xmlnsXlink='http://www.w3.org/1999/xlink'
            >
                <g id='logo-whatsapp' stroke='none' strokeWidth='1' fill='none' fillRule='evenodd'>
                    <g fillRule='nonzero'>
                        <path
                            d='M21.5849866,18.6134048 C21.5941019,18.6954424 21.6032172,18.7683646 21.6032172,18.8367292 C21.6032172,19.5340483 21.2841823,20.0490617 20.641555,20.3817694 C20.2313673,20.5868633 19.8439678,20.691689 19.4884718,20.691689 L19.283378,20.691689 C18.6407507,20.5868633 17.8932976,20.3316354 17.0319035,19.9168901 C15.9107239,19.3927614 14.8670241,18.458445 13.9053619,17.1184987 C13.2171582,16.1340483 12.8753351,15.3136729 12.8753351,14.6619303 C12.8753351,13.5179625 13.2490617,12.838874 13.9919571,12.6337802 C14.1514745,12.610992 14.3064343,12.597319 14.4568365,12.597319 C14.6072386,12.597319 14.7394102,12.6201072 14.8533512,12.6656836 C14.944504,12.7112601 15.1268097,13.0394102 15.4048257,13.6455764 C15.6919571,14.3428954 15.833244,14.716622 15.833244,14.7621984 C15.833244,14.9353887 15.7238606,15.1359249 15.5050938,15.363807 L15.1085791,15.8788204 C15.1085791,16.0155496 15.3045576,16.3436997 15.6919571,16.8587131 C16.1841823,17.4876676 16.7174263,17.9571046 17.291689,18.2670241 C17.8067024,18.5541555 18.1530831,18.6954424 18.3217158,18.6954424 L18.3581769,18.6954424 C18.4493298,18.6817694 18.636193,18.4949062 18.9233244,18.1302949 C19.2104558,17.7747989 19.397319,17.5970509 19.4884718,17.5970509 C19.6160858,17.5970509 19.9898123,17.7520107 20.6233244,18.0619303 C21.1383378,18.3126005 21.3981233,18.4402145 21.3981233,18.4402145 C21.3981233,18.4402145 21.4619303,18.4994638 21.5849866,18.6134048 Z'
                            id='Path'
                            fill='#FFFFFF'
                        />
                        <path
                            d='M25.969437,16.0565684 L25.969437,17.1367292 C25.6731903,20.1949062 24.2603217,22.5010724 21.744504,24.0597855 C20.4729223,24.8391421 19.0281501,25.2630027 17.4147453,25.3313673 L17.1412869,25.3313673 C15.7785523,25.3313673 14.3702413,24.9576408 12.9163539,24.2147453 C12.6064343,24.319571 11.8042895,24.5747989 10.5099196,24.9895442 C9.51179625,25.2994638 8.71420912,25.563807 8.10348525,25.7780161 C8.08069705,25.7780161 8.06246649,25.7552279 8.05335121,25.7096515 L7.9849866,25.8144772 C7.9849866,25.791689 8.45898123,24.4107239 9.41152815,21.6715818 C9.47077748,21.4983914 9.49812332,21.3160858 9.49812332,21.1201072 C9.49812332,20.9605898 9.4616622,20.7873995 9.39329759,20.6050938 C8.70509383,19.2423592 8.36327078,17.8796247 8.36327078,16.5168901 C8.3997319,14.2836461 9.23378016,12.2828418 10.8699732,10.5190349 C12.2873995,8.99678284 14.2152815,8.11260054 16.6399464,7.87104558 L17.7064343,7.87104558 C20.0217158,8.07613941 21.9085791,8.91930295 23.3761394,10.3959786 C24.8847185,11.9136729 25.7506702,13.8005362 25.969437,16.0565684 Z M24.3013405,18.6680965 C24.497319,17.9024129 24.5930295,17.1686327 24.5930295,16.466756 C24.5930295,14.8077748 23.9868633,13.2764075 22.7699732,11.8817694 C21.3160858,10.1270777 19.4474531,9.25201072 17.1686327,9.25201072 C16.9726542,9.25201072 16.772118,9.25656836 16.5670241,9.27024129 C15.2270777,9.35227882 13.9965147,9.78981233 12.8753351,10.5919571 C10.8016086,12.1050938 9.76702413,14.0603217 9.76702413,16.466756 C9.76702413,16.7538874 9.78525469,17.0319035 9.81715818,17.3099196 C9.92198391,18.3080429 10.2136729,19.2514745 10.6922252,20.1447721 C11.0112601,20.6597855 11.1798928,20.919571 11.189008,20.919571 L10.9155496,21.6077748 C10.5144772,22.8201072 10.2957105,23.4855228 10.263807,23.5994638 L13.080429,22.7061662 C14.2243968,23.3806971 15.3045576,23.7772118 16.3117962,23.8911528 C16.5761394,23.9276139 16.8404826,23.9412869 17.1002681,23.9412869 C18.1530831,23.9412869 19.1512064,23.7316354 20.0900804,23.3077748 C22.2321716,22.3552279 23.6359249,20.8101877 24.3013405,18.6680965 Z'
                            id='Shape'
                            fill='#FFFFFF'
                        />
                        <path
                            d='M32.7101877,10.5053619 C33.5624665,12.5654155 33.9908847,14.7302949 33.9908847,17 C33.9908847,19.2697051 33.5624665,21.4345845 32.7101877,23.4946381 C31.8579088,25.5546917 30.6227882,27.3959786 29.0093834,29.0093834 C27.3959786,30.6227882 25.5546917,31.8579088 23.4946381,32.7101877 C21.4345845,33.5624665 19.2697051,33.9908847 17,33.9908847 C14.7302949,33.9908847 12.5654155,33.5624665 10.5053619,32.7101877 C8.44530831,31.8579088 6.60402145,30.6227882 4.99061662,29.0093834 C3.3772118,27.3959786 2.14209115,25.5546917 1.28981233,23.4946381 C0.432975871,21.4345845 0.0091152815,19.2697051 0.0091152815,17 C0.0091152815,14.7302949 0.437533512,12.5654155 1.28981233,10.5053619 C2.14209115,8.44530831 3.3772118,6.60402145 4.99061662,4.99061662 C6.60402145,3.3772118 8.44530831,2.14209115 10.5053619,1.28981233 C12.5654155,0.437533512 14.7302949,0.0091152815 17,0.0091152815 C19.2697051,0.0091152815 21.4345845,0.437533512 23.4946381,1.28981233 C25.5546917,2.14209115 27.3959786,3.3772118 29.0093834,4.99061662 C30.6227882,6.60402145 31.8579088,8.44530831 32.7101877,10.5053619 Z M25.969437,17.1367292 L25.969437,16.0520107 C25.7506702,13.7959786 24.8847185,11.9136729 23.3761394,10.4005362 C21.9085791,8.92386059 20.0217158,8.08069705 17.7064343,7.87560322 L16.6399464,7.87560322 C14.2107239,8.11715818 12.2873995,8.99678284 10.8699732,10.5235925 C9.23378016,12.2873995 8.39517426,14.2882038 8.36327078,16.5214477 C8.36327078,17.8841823 8.70509383,19.2469169 9.39329759,20.6096515 C9.4616622,20.7919571 9.49812332,20.9651475 9.49812332,21.1246649 C9.49812332,21.3206434 9.47077748,21.5029491 9.41152815,21.6761394 C8.45898123,24.4152815 7.9849866,25.7962466 7.9849866,25.8190349 L8.05335121,25.7142091 C8.06246649,25.7597855 8.08069705,25.7825737 8.10348525,25.7825737 C8.70965147,25.563807 9.51179625,25.3040214 10.5099196,24.9941019 C11.8042895,24.5839142 12.6064343,24.3241287 12.9163539,24.2193029 C14.3702413,24.9621984 15.7785523,25.3359249 17.1412869,25.3359249 L17.4147453,25.3359249 C19.0281501,25.2675603 20.4729223,24.8436997 21.744504,24.0643432 C24.2603217,22.50563 25.6686327,20.1949062 25.969437,17.1367292 Z'
                            id='Shape'
                            fill='#12AF0A'
                        />
                        <path
                            d='M24.5930295,16.466756 C24.5930295,17.1640751 24.497319,17.8978552 24.3013405,18.6680965 C23.6359249,20.8101877 22.2321716,22.3552279 20.0900804,23.3077748 C19.1512064,23.7316354 18.1530831,23.9412869 17.1002681,23.9412869 C16.8359249,23.9412869 16.5715818,23.9230563 16.3117962,23.8911528 C15.3045576,23.7772118 14.2289544,23.3806971 13.080429,22.7061662 L10.263807,23.5994638 C10.3002681,23.4855228 10.5144772,22.8201072 10.9155496,21.6077748 L11.189008,20.919571 C11.1753351,20.919571 11.0112601,20.6597855 10.6922252,20.1447721 C10.2091153,19.2514745 9.91742627,18.3080429 9.81715818,17.3099196 C9.78069705,17.0364611 9.76702413,16.7538874 9.76702413,16.466756 C9.76702413,14.0603217 10.8016086,12.1050938 12.8753351,10.5919571 C13.9965147,9.78981233 15.2270777,9.34772118 16.5670241,9.27024129 C16.772118,9.25656836 16.9726542,9.25201072 17.1686327,9.25201072 C19.4474531,9.25201072 21.3160858,10.1270777 22.7699732,11.8817694 C23.9868633,13.2764075 24.5930295,14.8077748 24.5930295,16.466756 Z M21.6032172,18.8367292 C21.6032172,18.7683646 21.5986595,18.6954424 21.5849866,18.6134048 C21.4573727,18.4994638 21.3981233,18.4402145 21.3981233,18.4402145 C21.3981233,18.4402145 21.1428954,18.3126005 20.6233244,18.0619303 C19.99437,17.7520107 19.6160858,17.5970509 19.4884718,17.5970509 C19.397319,17.5970509 19.2058981,17.7747989 18.9233244,18.1302949 C18.636193,18.4949062 18.4493298,18.6863271 18.3581769,18.6954424 L18.3217158,18.6954424 C18.1485255,18.6954424 17.8067024,18.5541555 17.291689,18.2670241 C16.7174263,17.9571046 16.1841823,17.4876676 15.6919571,16.8587131 C15.3045576,16.3436997 15.1085791,16.0155496 15.1085791,15.8788204 L15.5050938,15.363807 C15.7238606,15.1359249 15.833244,14.9353887 15.833244,14.7621984 C15.833244,14.716622 15.6919571,14.3428954 15.4048257,13.6455764 C15.1313673,13.0394102 14.9490617,12.7112601 14.8533512,12.6656836 C14.7394102,12.6201072 14.6072386,12.597319 14.4568365,12.597319 C14.3064343,12.597319 14.1514745,12.610992 13.9919571,12.6337802 C13.2490617,12.838874 12.8753351,13.5179625 12.8753351,14.6619303 C12.8753351,15.3136729 13.2171582,16.1340483 13.9053619,17.1184987 C14.8670241,18.458445 15.9107239,19.3927614 17.0319035,19.9168901 C17.8932976,20.3316354 18.6407507,20.5868633 19.283378,20.691689 L19.4884718,20.691689 C19.8439678,20.691689 20.2268097,20.5868633 20.641555,20.3817694 C21.2841823,20.0536193 21.6032172,19.5386059 21.6032172,18.8367292 Z'
                            id='Shape'
                            fill='#12AF0A'
                        />
                    </g>
                </g>
            </svg>
        ),
    },
};

const Wrapper = styled.span`
    display: inline-block;
    vertical-align: middle;
    width: ${props => props.size || '25px'};
    height: ${props => props.size || '25px'};
`;

export interface SocialIconProps {
    slug: string;
    title?: string;
    size?: number | string;
    classes?: string;
}

/**
 * Renders specified social by key.
 *
 * @param {string} social
 * @param {number} size
 */
export default function SocialIcon({slug, title, size = 30, classes}: SocialIconProps) {
    const socialObject = SOCIALS[slug] || undefined;

    if (!socialObject) {
        return null;
    }

    if (typeof size === 'number') {
        size = size + 'px';
    }

    return (
        <Wrapper title={title} size={size} className={classes}>
            {typeof socialObject.svg !== 'undefined' ? (
                socialObject.svg
            ) : (
                <svg width={size} height={size} viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'>
                    <rect width='20' height='20' rx='10' fill={socialObject.fill} />
                    <path d={socialObject.path} fill='#fff' />
                </svg>
            )}
        </Wrapper>
    );
}

SocialIcon.displayName = 'SocialIcon';
