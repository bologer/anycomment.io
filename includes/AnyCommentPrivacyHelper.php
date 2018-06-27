<?php

namespace anycomment\helpers;


class AnyCommentPrivacyHelper {
	/**
	 * Get GDRP example of the text.
	 * @return string|
	 */
	public function getGdprExample() {
		$text = __( "What personal data we collect and why we collect it:", "anycomment" );
		$text .= __( "\nWhen you authorize via some of the available social networks, we collect the following information about you: first name, last name, login (when available), avatar URL, email (when available or access given).", "anycomment" );
		$text .= __( "\nSome of the information may vary from social to social. For example, VK.com give access to email only when you accept it while authorizing.", "anycomment" );
		$text .= __( "\nWe record information about only when social network allows us to have it.", "anycomment" );

		return $text;
	}
}