<?php


namespace AnyComment\Domain\Builder\Elements;


/**
 * Class CommentFormElement is a class which represents data structure for form element.
 *
 * @package AnyComment\Domain\Builder\Elements
 */
class CommentFormElement extends Element
{
    /**
     * @inheritDoc
     */
    public $component = self::ELEMENT_COMMENT_FORM;

    /**
     * @inheritDoc
     */
    public function getElements(): array
    {
        return [
            [
                "component" => 'comment_form_input',
                "elements" => [],
                "options" => [
                    "text_color" => '#373e44',
                    "background_color" => '#ffffff',
                    "typography_font_size" => ["size" => 15, "unit" => 'px'],
                    "border_radius" => [
                        "unit" => "px",
                        "top" => 5,
                        "right" => 5,
                        "bottom" => 5,
                        "left" => 5,
                        "isLinked" => true
                    ],
                    "border_style" => 'solid',
                    "border_width" => ["unit" => "px", "top" => 1, "right" => 1, "bottom" => 1, "left" => 1, "isLinked" => false],
                    "border_color" => '#d4d6d7',
                    "padding" => [
                        "unit" => "px",
                        "top" => 10,
                        "right" => 10,
                        "bottom" => 10,
                        "left" => 10,
                        "isLinked" => true
                    ],
                ]
            ]
        ];
    }
}
