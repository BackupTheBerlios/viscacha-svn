<?php
class BasicBBCodeTag extends AbstractInlineBBCodeTag {

	public function getTagNames() {
		return array('b', 'i', 'u', 's');
	}

	public function compile(BBCodeToken $token) {
		$tagName = $token->getTagName();
		$tagContent = $this->compiler->compile($token, self::DT_INLINE);
		return "<{$tagName}>{$tagContent}</{$tagName}>";
	}

}
?>