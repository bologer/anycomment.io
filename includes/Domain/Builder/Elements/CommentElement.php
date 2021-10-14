<?php


namespace AnyComment\Domain\Builder\Elements;


/**
 * Class CommentElement is a class which represents data structure for single comment element.
 *
 * @package frontend\packages\builder\elements
 */
class CommentElement extends Element
{
    /**
     * @inheritDoc
     */
    public $component = self::ELEMENT_COMMENT;

    /**
     * @inheritDoc
     */
    public function getElements(): array
    {
        return [
            [
                "component" => 'comment_header',
                "elements" => [
                    [
                        "component" => 'comment_author_name',
                        "elements" => [],
                        "options" => [
                            "text_align" => '',
                            "text_color" => '',
                            "typography_font_size" => ["unit" => 'px', "size" => 15],
                            "typography_font_family" => '',
                            "typography_font_weight" => 400,
                            "typography_line_height" => ["unit" => 'em', "size" => ''],
                            "typography_letter_spacing" => ["unit" => 'em', "size" => 0],
                            "margin" => [
                                "unit" => "px",
                                "top" => "",
                                "right" => "",
                                "bottom" => 6,
                                "left" => "",
                                "isLinked" => false
                            ],
                            "padding" => [
                                "unit" => "px",
                                "top" => "",
                                "right" => "",
                                "bottom" => "",
                                "left" => "",
                                "isLinked" => true
                            ],
                        ]
                    ],
                    [
                        "component" => 'comment_date',
                        "elements" => [],
                        "options" => [
                            "text_align" => '',
                            "text_color" => '',
                            "typography_font_size" => ["unit" => 'px', "size" => 12],
                            "typography_font_family" => '',
                            "typography_font_weight" => 400,
                            "typography_line_height" => ["unit" => 'px', "size" => 12.5],
                            "typography_letter_spacing" => ["unit" => 'em', "size" => 0],
                            "margin" => [
                                "unit" => "px",
                                "top" => "",
                                "right" => "",
                                "bottom" => "",
                                "left" => "",
                                "isLinked" => true
                            ],
                            "padding" => [
                                "unit" => "px",
                                "top" => "",
                                "right" => "",
                                "bottom" => "",
                                "left" => "",
                                "isLinked" => true
                            ],
                        ]
                    ]
                ],
                "options" => []
            ],
            [
                "component" => 'comment_body',
                "elements" => [
                    [
                        "component" => 'comment_text',
                        "elements" => [],
                        "options" => [
                            "text_align" => '',
                            "text_color" => '',
                            "typography_font_size" => ["unit" => 'px', "size" => 15],
                            "typography_font_family" => '',
                            "typography_font_weight" => 400,
                            "typography_line_height" => ["unit" => 'em', "size" => 1.5],
                            "typography_letter_spacing" => ["unit" => 'em', "size" => 0],
                            "typography_text_transform" => '',
                            "typography_font_style" => '',
                            "margin" => [
                                "unit" => "px",
                                "top" => 5,
                                "right" => "",
                                "bottom" => 5,
                                "left" => "",
                                "isLinked" => false
                            ],
                            "padding" => [
                                "unit" => "px",
                                "top" => "",
                                "right" => "",
                                "bottom" => "",
                                "left" => "",
                                "isLinked" => true
                            ],
                        ]
                    ]
                ],
                "options" => []
            ],
        ];
    }
}
