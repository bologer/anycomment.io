<?php

namespace AnyComment\Domain\Builder;

use AnyComment\Admin\AnyCommentGenericSettings;

class WebsiteBuilder extends DefaultBuilder {
	public static function getDefault( ?string $primaryColor = null ) {
		$default = parent::getDefault( $primaryColor );

		$variables = [
			'global-margin'                   => AnyCommentGenericSettings::get_global_margin(),
			'global-padding'                  => AnyCommentGenericSettings::get_global_padding(),
			'global-background-color'         => AnyCommentGenericSettings::get_global_background_color(),
			'global-background-border-radius' => AnyCommentGenericSettings::get_global_background_border_radius(),
			'global-radius'                   => AnyCommentGenericSettings::get_design_global_radius(),

			'font-size'  => AnyCommentGenericSettings::get_design_font_size(),
//			'font-family' => AnyCommentGenericSettings::get_design_font_family(),
			'link-color' => AnyCommentGenericSettings::get_design_link_color(),
			'text-color' => AnyCommentGenericSettings::get_design_text_color(),

			'semi-hidden-color' => AnyCommentGenericSettings::get_design_semi_hidden_color(),

			'form-field-background-color' => AnyCommentGenericSettings::get_design_form_field_background_color(),

			'attachment-color'            => AnyCommentGenericSettings::get_design_attachment_color(),
			'attachment-background-color' => AnyCommentGenericSettings::get_design_attachment_background_color(),

			'avatar-border-radius' => AnyCommentGenericSettings::get_design_avatar_radius(),
			'parent-avatar-size'   => AnyCommentGenericSettings::get_design_parent_avatar_size(),
			'child-avatar-size'    => AnyCommentGenericSettings::get_design_child_avatar_size(),

//			'btn-radius'                  => AnyCommentGenericSettings::get_design_button_radius(),
//			'btn-color'                   => AnyCommentGenericSettings::get_design_button_color(),
//			'btn-background-color'        => AnyCommentGenericSettings::get_design_button_background_color(),
//			'btn-background-color-active' => AnyCommentGenericSettings::get_design_button_background_color_active(), !!
		];

		$default['global']['typography_font_family'] = AnyCommentGenericSettings::get_design_font_family();
		$default['global']['typography_font_family'] = AnyCommentGenericSettings::get_design_font_family();

		$default['globalWidgets']['button']['text_color'] = AnyCommentGenericSettings::get_design_button_color();
		$default['globalWidgets']['button']['background_color'] = AnyCommentGenericSettings::get_design_button_background_color();
		$default['globalWidgets']['button']['border_radius'] = self::normalizeUnits( AnyCommentGenericSettings::get_design_button_radius() );

		return $default;
	}

	public static function normalizeUnits( $units ) {
		$units = trim( $units );

		if ( preg_match( '/^(\d+)([a-z]{2})$/', $units, $matches ) ) {
			[ $match, $unitValue, $unit ] = $matches;

			return [
				"unit"     => $unit,
				"top"      => $unitValue,
				"right"    => $unitValue,
				"bottom"   => $unitValue,
				"left"     => $unitValue,
				"isLinked" => false
			];
		}

		if ( preg_match( '/^(\d+)([a-z]{2})\s+(\d+)([a-z]{2})$/', $units, $matches ) ) {
			[ $match, $topBottom, $topBottomUnit, $rightLeft ] = $matches;

			return [
				"unit"     => $topBottomUnit,
				"top"      => $topBottom,
				"right"    => $rightLeft,
				"bottom"   => $topBottom,
				"left"     => $rightLeft,
				"isLinked" => false
			];
		}

		if ( preg_match( '/^(\d+)([a-z]{2})\s+(\d+)([a-z]{2})\s+(\d+)([a-z]{2})$/', $units, $matches ) ) {
			[ $match, $top, $topUnit, $rightLeft, $rightLeftUnit, $bottom, $bottomUnit ] = $matches;

			return [
				"unit"     => $topUnit,
				"top"      => $top,
				"right"    => $rightLeft,
				"bottom"   => $bottom,
				"left"     => $rightLeft,
				"isLinked" => false
			];
		}

		if ( preg_match( '/^(\d+)([a-z]{2})\s+(\d+)([a-z]{2})\s+(\d+)([a-z]{2})\s+(\d+)([a-z]{2})$/', $units, $matches ) ) {
			[ $match, $top, $topUnit, $right, $rightUnit, $bottom, $bottomUnit, $left, $leftUnit ] = $matches;

			return [
				"unit"     => $topUnit,
				"top"      => $top,
				"right"    => $right,
				"bottom"   => $bottom,
				"left"     => $left,
				"isLinked" => false
			];
		}

		throw new \InvalidArgumentException( 'Invalid format provided' );
	}
}
