<?php
/**
 * RSSCreator10 is a FeedCreator that implements RDF Site Summary (RSS) 1.0.
 *
 * @see http://www.purl.org/rss/1.0/
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSS10 extends FeedCreator {

	/**
	 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
	 * The feed will contain all items previously added in the same order.
	 * @return    string    the feed's complete text 
	 */
	function createFeed() {     
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		if ($this->cssStyleSheet=="") {
			$cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		}
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rdf:RDF\n";
		$feed.= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
		$feed.= "    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n"; 
		$feed.= "    xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"\n";
		$feed.= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
		$feed.= "    <channel rdf:about=\"".$this->syndicationURL."\">\n";
		$feed.= "        <title>".htmlspecialchars($this->title)."</title>\n";
		$feed.= "        <description>".htmlspecialchars($this->description)."</description>\n";
		$feed.= "        <link>".$this->link."</link>\n";
		if ($this->image!=null) {
			$feed.= "        <image rdf:resource=\"".$this->image->url."\" />\n";
		}
		$now = new FeedDate();
		$feed.= "       <dc:date>".htmlspecialchars($now->iso8601())."</dc:date>\n";
		$feed.= "        <items>\n";
		$feed.= "            <rdf:Seq>\n";
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "                <rdf:li rdf:resource=\"".htmlspecialchars($this->items[$i]->link)."\"/>\n";
		}
		$feed.= "            </rdf:Seq>\n";
		$feed.= "        </items>\n";
		$feed.= "    </channel>\n";
		if ($this->image!=null) {
			$feed.= "    <image rdf:about=\"".$this->image->url."\">\n";
			$feed.= "        <title>".$this->image->title."</title>\n";
			$feed.= "        <link>".$this->image->link."</link>\n";
			$feed.= "        <url>".$this->image->url."</url>\n";
			$feed.= "    </image>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");
		
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <item rdf:about=\"".htmlspecialchars($this->items[$i]->link)."\">\n";
			//$feed.= "        <dc:type>Posting</dc:type>\n";
			$feed.= "        <dc:format>text/html</dc:format>\n";
			if ($this->items[$i]->date!=null) {
				$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "        <dc:date>".htmlspecialchars($itemDate->iso8601())."</dc:date>\n";
			}
			if ($this->items[$i]->source!="") {
				$feed.= "        <dc:source>".htmlspecialchars($this->items[$i]->source)."</dc:source>\n";
			}
			if ($this->items[$i]->author!="") {
				$feed.= "        <dc:creator>".htmlspecialchars($this->items[$i]->author)."</dc:creator>\n";
			}
			$feed.= "        <title>".htmlspecialchars(strip_tags(strtr($this->items[$i]->title,"\n\r","  ")))."</title>\n";
			$feed.= "        <link>".htmlspecialchars($this->items[$i]->link)."</link>\n";
			$feed.= "        <description>".htmlspecialchars($this->items[$i]->description)."</description>\n";
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "        ");
			$feed.= "    </item>\n";
		}
		$feed.= "</rdf:RDF>\n";
		return $feed;
	}
}

?>