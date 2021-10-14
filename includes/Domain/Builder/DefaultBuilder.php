<?php

namespace AnyComment\Domain\Builder;

use AnyComment\Domain\Builder\Elements\CommentElement;
use AnyComment\Domain\Builder\Elements\CommentFormElement;
use AnyComment\Domain\Builder\Elements\Element;

class DefaultBuilder
{
    /**
     * Get list of required elements in the builder.
     *
     * @return string[]
     */
    public static function getRequiredElements()
    {
        return [
            Element::ELEMENT_COMMENT,
            Element::ELEMENT_COMMENT_FORM
        ];
    }

    /**
     * Get default value of builder.
     *
     * @param string|null $primaryColor Primary color set as default.
     * @return array
     */
    public static function getDefault(?string $primaryColor = null)
    {
        return [
            "global" => [
                "primary_color" => $primaryColor ?? '#825cff',
                "secondary_color" => '#b6c1c6',
                "text_color" => '#333333',
                "accent_color" => '#3281fd',
                "muted_color" => '#999999',
                "typography_font_family" => 'Noto Sans',
                "typography_font_weight" => 400,
                "background_color" => '',
            ],
            "globalWidgets" => [
                "button" => [
                    "background_color" => '', // taken from global.primary_color
                    "text_color" => '#ffffff',
                    "typography_font_size" => ["size" => 14, "unit" => 'px'],
                    "padding" => [
                        "unit" => "px",
                        "top" => 10,
                        "right" => 20,
                        "bottom" => 10,
                        "left" => 20,
                        "isLinked" => false
                    ],
                    "border_radius" => [
                        "unit" => "px",
                        "top" => 5,
                        "right" => 5,
                        "bottom" => 5,
                        "left" => 5,
                        "isLinked" => true
                    ],
                    "border_style" => '',
                    "border_width" => [
                        "unit" => "px",
                        "top" => 0,
                        "right" => 0,
                        "bottom" => 0,
                        "left" => 0,
                        "isLinked" => true
                    ],
                    "border_color" => '',
                ]
            ],
            "elements" => [
                (new CommentFormElement())->getAsArray(),
                (new CommentElement())->getAsArray()
            ],
            'precomputed' => [
                'fonts' => [
                    "Noto Sans" => [
                        "fontFamily" => "Noto Sans",
                        "fontWeights" => [400]
                    ]
                ]
            ]
        ];
    }
}
