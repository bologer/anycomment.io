import React from 'react';
import LoginSocialList from "~/components/LoginSocialList";
import {useOptions, useSettings} from "~/hooks/setting";
import {FormikBag} from "formik";
import {FormValues} from "~/components/SendComment";

export interface CommentFormGuestProps {
    formik: FormikBag<{}, FormValues>
}

export default function CommentFormGuest({formik}: CommentFormGuestProps) {

    const settings = useSettings();
    const options = useOptions();
    const translations = settings.i18;

    /**
     * Check whether it is required to show guest fields such as name, email, website.
     *
     * @returns {*}
     */
    function showGuestFields() {
        return options.isFormTypeGuests || options.isFormTypeAll;
    }

    const inputs: string[] = settings.options.guestInputs;

    let elementInputs: React.ReactElement[] = [];

    inputs.forEach(el => {
        if (el === 'name') {
            elementInputs.push(
                <div className="anycomment anycomment-form__inputs-item anycomment-form__inputs-name">
                    <label form="anycomment-author-name">{translations.name} <span
                        className="anycomment-label-import">*</span></label>
                    <input type="text" name="author_name" id="anycomment-author-name"
                           value={formik.values.author_name}
                           required={true}
                           onChange={formik.handleChange}
                    />
                </div>);

        } else if (el === 'email') {
            elementInputs.push(
                <div className="anycomment anycomment-form__inputs-item anycomment-form__inputs-email">
                    <label form="anycomment-author-email">{translations.email} <span
                        className="anycomment-label-import">*</span></label>
                    <input type="email" name="author_email" id="anycomment-author-email"
                           value={formik.values.author_email}
                           required={true}
                           onChange={formik.handleChange}
                    />
                </div>);
        } else if (el === 'website') {
            elementInputs.push(
                <div className="anycomment-form__inputs-item anycomment-form__inputs-website">
                    <label form="anycomment-author-website">{translations.website}</label>
                    <input type="text" name="author_website" id="anycomment-author-website"
                           value={formik.values.author_website}
                           onChange={formik.handleChange}
                    />
                </div>);
        }
    });

    return (
        <div className={"anycomment anycomment-form__inputs anycomment-form__inputs-" + elementInputs.length}>
            {elementInputs}
        </div>
    );
}