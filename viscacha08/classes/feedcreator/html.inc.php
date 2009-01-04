<?php
/**
 * HTMLCreator is a FeedCreator that writes an HTML feed file to a specific
 * location, overriding the createFeed method of the parent FeedCreator.
 * The HTML produced can be included over http by scripting languages, or serve
 * as the source for an IFrame.
 * All output by this class is embedded in <div></div> tags to enable formatting
 * using CSS.
 *
 * @author Pascal Van Hecke
 * @since 1.7
 */
class HTML extends FeedCreator {

	var $contentType = "text/html";

	/**
	 * Contains HTML to be output at the start of the feed's html representation.
	 */
	var $header;

	/**
	 * Contains HTML to be output at the end of the feed's html representation.
	 */
	var $footer ;

	/**
	 * Contains HTML to be output between entries. A separator is only used in
	 * case of multiple entries.
	 */
	var $separator;

	/**
	 * Used to prefix the stylenames to make sure they are unique
	 * and do not clash with stylenames on the users' page.
	 */
	var $stylePrefix;

	/**
	 * Determines whether the links open in a new window or not.
	 */
	var $openInNewWindow = true;

	var $imageAlign ="right";

	/**
	 * In case of very simple output you may want to get rid of the style tags,
	 * hence this variable.  There's no equivalent on item level, but of course you can
	 * add strings to it while iterating over the items ($this->stylelessOutput .= ...)
	 * and when it is non-empty, ONLY the styleless output is printed, the rest is ignored
	 * in the function createFeed().
	 */
	var $stylelessOutput ="";

	/**
	 * Writes the HTML.
	 * @return	string	the scripts's complete text
	 */
	function createFeed() {
		// if there is styleless output, use the content of this variable and ignore the rest
		if ($this->stylelessOutput!="") {
			return $this->stylelessOutput;
		}

		//set an openInNewWindow_token_to be inserted or not
		if ($this->openInNewWindow) {
			$targetInsert = " target='_blank'";
		}

		// use this array to put the lines in and implode later with "document.write" javascript
		$feedArray = array();
		if ($this->image!=null) {
			$imageStr = "<a href='".$this->image->link."'".$targetInsert.">".
							"<img src='".$this->image->url."' border='0' alt='".
							$this->htmlspecialchars(FeedCreator::iTrunc($this->image->title,100)).
							"' align='".$this->imageAlign."' ";
			if ($this->image->width) {
				$imageStr .=" width='".$this->image->width. "' ";
			}
			if ($this->image->height) {
				$imageStr .=" height='".$this->image->height."' ";
			}
			$imageStr .="/></a>";
			$feedArray[] = $imageStr;
		}

		if ($this->title) {
			$feedArray[] = "<div class='".$this->stylePrefix."title'><a href='".$this->link."' ".$targetInsert." class='".$this->stylePrefix."title'>".
				$this->htmlspecialchars(FeedCreator::iTrunc($this->title,100))."</a></div>";
		}
		$description = $this->getDescription($this->encoding);
		if ($description) {
			$feedArray[] = "<div class='".$this->stylePrefix."description'>".
				str_replace("]]>", "", str_replace("<![CDATA[", "", $encoding)).
				"</div>";
		}

		if ($this->header) {
			$feedArray[] = "<div class='".$this->stylePrefix."header'>".$this->htmlspecialchars($this->header)."</div>";
		}

		for ($i=0;$i<count($this->items);$i++) {
			if ($this->separator and $i > 0) {
				$feedArray[] = "<div class='".$this->stylePrefix."separator'>".$this->htmlspecialchars($this->separator)."</div>";
			}

			if ($this->items[$i]->title) {
				if ($this->items[$i]->link) {
					$feedArray[] =
						"<div class='".$this->stylePrefix."item_title'><a href='".$this->items[$i]->link."' class='".$this->stylePrefix.
						"item_title'".$targetInsert.">".$this->htmlspecialchars(FeedCreator::iTrunc($this->items[$i]->title,100)).
						"</a></div>";
				} else {
					$feedArray[] =
						"<div class='".$this->stylePrefix."item_title'>".
						$this->htmlspecialchars(FeedCreator::iTrunc($this->items[$i]->title,100)).
						"</div>";
				}
			}
			$description = $this->items[$i]->getDescription($this->encoding);
			if ($description) {
				$feedArray[] =
				"<div class='".$this->stylePrefix."item_description'>".
					str_replace("]]>", "", str_replace("<![CDATA[", "", $description)).
					"</div>";
			}
		}
		if ($this->footer) {
			$feedArray[] = "<div class='".$this->stylePrefix."footer'>".$this->htmlspecialchars($this->footer)."</div>";
		}

		$feed= "".join($feedArray, "\r\n");
		return $feed;
	}

	/**
	 * Overrrides parent to produce .html extensions
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		return $this->dir.strtolower(get_class($this)).".html";
	}
}
?>