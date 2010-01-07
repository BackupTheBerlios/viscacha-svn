<?php
/**
 * Viscacha - Flexible Website Management Solution
 *
 * Copyright (C) 2004 - 2010 by Viscacha.org
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @copyright	Copyright (c) 2004-2010, Viscacha.org
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License
 */

/**
 * Detect mime type by extension and vice versa.
 *
 * @package		Core
 * @subpackage	FileSystem
 * @author		Matthias Mohr
 * @since 		1.0
 * @abstract
 */
abstract class MimeType {

	/**
	 * Mapping table for mime type to file extensions.
	 * All data should be lowercase.
	 * @var array
	 */
	public static $data = array(
		'applicaiton/x-bytecode.python' => array('pyc'),
		'application/andrew-inset' => array('ez'),
		'application/atom+xml' => array('atom'),
		'application/base64' => array('mme'),
		'application/book' => array('boo', 'book'),
		'application/clariscad' => array('ccad'),
		'application/commonground' => array('dp'),
		'application/drafting' => array('drw'),
		'application/excel' => array('xl'),
		'application/freeloader' => array('frl'),
		'application/futuresplash' => array('spl'),
		'application/groupwise' => array('vew'),
		'application/hta' => array('hta'),
		'application/i-deas' => array('unv'),
		'application/inf' => array('inf'),
		'application/java-archive' => array('jar'),
		'application/javascript' => array('js'),
		'application/json' => array('json'),
		'application/marc' => array('mrc'),
		'application/mathml+xml' => array('mathml'),
		'application/mbedlet' => array('mbd'),
		'application/mime' => array('aps'),
		'application/mspowerpoint' => array('ppz'),
		'application/msword' => array('doc', 'dot', 'w6w', 'wiz', 'word'),
		'application/netmc' => array('mcp'),
		'application/octet-stream' => array('a', 'arc', 'arj', 'dat', 'dll', 'dms', 'dump', 'lhx', 'o', 'saveme', 'so', 'zoo'),
		'application/oda' => array('oda'),
		'application/ogg' => array('ogg'),
		'application/pdf' => array('pdf'),
		'application/pkcs7-signature' => array('p7s'),
		'application/pkix-crl' => array('crl'),
		'application/postscript' => array('ai', 'eps', 'ps'),
		'application/pro_eng' => array('part', 'prt'),
		'application/rdf+xml' => array('rdf'),
		'application/rss+xml' => array('rss'),
		'application/rtf' => array('rtf'),
		'application/set' => array('set'),
		'application/smil' => array('smi', 'smil'),
		'application/solids' => array('sol'),
		'application/sounder' => array('sdr'),
		'application/srgs' => array('gram'),
		'application/srgs+xml' => array('grxml'),
		'application/step' => array('step', 'stp'),
		'application/streamingmedia' => array('ssm'),
		'application/vda' => array('vda'),
		'application/vnd.fdf' => array('fdf'),
		'application/vnd.google-earth.kml+xml' => array('kml'),
		'application/vnd.google-earth.kmz' => array('kmz'),
		'application/vnd.hp-hpgl' => array('hgl', 'hpg', 'hpgl'),
		'application/vnd.mozilla.xul+xml' => array('xul'),
		'application/vnd.ms-cab-compressed' => array('cab'),
		'application/vnd.ms-excel' => array('xls'),
		'application/vnd.ms-excel.addin.macroEnabled.12' => array('xlam'),
		'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => array('xlsb'),
		'application/vnd.ms-excel.sheet.macroEnabled.12' => array('xlsm'),
		'application/vnd.ms-excel.template.macroEnabled.12' => array('xltm'),
		'application/vnd.ms-pki.certstore' => array('sst'),
		'application/vnd.ms-pki.pko' => array('pko'),
		'application/vnd.ms-pki.seccat' => array('cat'),
		'application/vnd.ms-powerpoint' => array('pot', 'ppa', 'pps', 'ppt', 'pwz'),
		'application/vnd.ms-powerpoint.addin.macroEnabled.12' => array('ppam'),
		'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => array('pptm'),
		'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => array('ppsm'),
		'application/vnd.ms-powerpoint.template.macroEnabled.12' => array('potm'),
		'application/vnd.ms-project' => array('mpp'),
		'application/vnd.ms-word.document.macroEnabled.12' => array('docm'),
		'application/vnd.ms-word.template.macroEnabled.12' => array('dotm'),
		'application/vnd.nokia.configuration-message' => array('ncm'),
		'application/vnd.nokia.ringing-tone' => array('rng'),
		'application/vnd.oasis.opendocument.chart' => array('odc'),
		'application/vnd.oasis.opendocument.database' => array('odb'),
		'application/vnd.oasis.opendocument.formula' => array('odf'),
		'application/vnd.oasis.opendocument.graphics' => array('odg'),
		'application/vnd.oasis.opendocument.graphics-template' => array('otg'),
		'application/vnd.oasis.opendocument.image' => array('odi'),
		'application/vnd.oasis.opendocument.presentation' => array('odp'),
		'application/vnd.oasis.opendocument.presentation-template' => array('otp'),
		'application/vnd.oasis.opendocument.spreadsheet' => array('ods'),
		'application/vnd.oasis.opendocument.spreadsheet-template' => array('ots'),
		'application/vnd.oasis.opendocument.text' => array('odt'),
		'application/vnd.oasis.opendocument.text-master' => array('odm'),
		'application/vnd.oasis.opendocument.text-template' => array('ott'),
		'application/vnd.oasis.opendocument.text-web' => array('oth'),
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('pptx'),
		'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => array('ppsx'),
		'application/vnd.openxmlformats-officedocument.presentationml.template' => array('potx'),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('xlsx'),
		'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => array('xltx'),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('docx'),
		'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => array('dotx'),
		'application/vnd.rn-realplayer' => array('rnx'),
		'application/vnd.wap.wbxml' => array('wbxml'),
		'application/vnd.wap.wmlc' => array('wmlc'),
		'application/vnd.wap.wmlscriptc' => array('wmlsc'),
		'application/vnd.xara' => array('web'),
		'application/vocaltec-media-desc' => array('vmd'),
		'application/vocaltec-media-file' => array('vmf'),
		'application/voicexml+xml' => array('vxml'),
		'application/wordperfect' => array('wp', 'wp6'),
		'application/wordperfect6.0' => array('w60', 'wp5'),
		'application/wordperfect6.1' => array('w61'),
		'application/x-123' => array('wk1'),
		'application/x-aim' => array('aim'),
		'application/x-authorware-bin' => array('aab'),
		'application/x-authorware-map' => array('aam'),
		'application/x-authorware-seg' => array('aas'),
		'application/x-bcpio' => array('bcpio'),
		'application/x-bsh' => array('bsh'),
		'application/x-bzip' => array('bz'),
		'application/x-bzip2' => array('boz', 'bz2'),
		'application/x-cdlink' => array('vcd'),
		'application/x-chat' => array('cha', 'chat'),
		'application/x-chess-pgn' => array('pgn'),
		'application/x-cocoa' => array('cco'),
		'application/x-compressed' => array('tgz', 'z'),
		'application/x-conference' => array('nsc'),
		'application/x-cpio' => array('cpio'),
		'application/x-cpt' => array('cpt'),
		'application/x-deepv' => array('deepv'),
		'application/x-director' => array('dcr', 'dir', 'dxr'),
		'application/x-dvi' => array('dvi'),
		'application/x-elc' => array('elc'),
		'application/x-envoy' => array('env', 'evy'),
		'application/x-esrehber' => array('es'),
		'application/x-excel' => array('xlb', 'xlc', 'xld', 'xlk', 'xll', 'xlm', 'xlt', 'xlv'),
		'application/x-freelance' => array('pre'),
		'application/x-gsp' => array('gsp'),
		'application/x-gss' => array('gss'),
		'application/x-gtar' => array('gtar'),
		'application/x-gzip' => array('gz'),
		'application/x-hdf' => array('hdf'),
		'application/x-helpfile' => array('help'),
		'application/x-httpd-imap' => array('imap'),
		'application/x-httpd-php' => array('php', 'php3', 'php4', 'phtml'),
		'application/x-httpd-phps' => array('phps'),
		'application/x-ima' => array('ima'),
		'application/x-internett-signup' => array('ins'),
		'application/x-inventor' => array('iv'),
		'application/x-ip2' => array('ip'),
		'application/x-java-class' => array('class'),
		'application/x-java-commerce' => array('jcm'),
		'application/x-koan' => array('skd', 'skm', 'skp', 'skt'),
		'application/x-latex' => array('latex', 'ltx'),
		'application/x-lha' => array('lha'),
		'application/x-livescreen' => array('ivy'),
		'application/x-lotus' => array('wq1'),
		'application/x-lzh' => array('lzh'),
		'application/x-lzx' => array('lzx'),
		'application/x-mac-binhex40' => array('hqx'),
		'application/x-macbinary' => array('bin'),
		'application/x-magic-cap-package-1.0' => array('mc$'),
		'application/x-mathcad' => array('mcd'),
		'application/x-meme' => array('mm'),
		'application/x-mif' => array('mif'),
		'application/x-mix-transfer' => array('nix'),
		'application/x-msdownload' => array('exe', 'msi'),
		'application/x-msexcel' => array('xla', 'xlw'),
		'application/x-navi-animation' => array('ani'),
		'application/x-navidoc' => array('nvd'),
		'application/x-navimap' => array('map'),
		'application/x-navistyle' => array('stl'),
		'application/x-netcdf' => array('cdf', 'nc'),
		'application/x-newton-compatible-pkg' => array('pkg'),
		'application/x-nokia-9000-communicator-add-on-software' => array('aos'),
		'application/x-omc' => array('omc'),
		'application/x-omcdatamaker' => array('omcd'),
		'application/x-omcregerator' => array('omcr'),
		'application/x-pagemaker' => array('pm4', 'pm5'),
		'application/x-pcl' => array('pcl'),
		'application/x-pixclscript' => array('plx'),
		'application/x-pkcs10' => array('p10'),
		'application/x-pkcs12' => array('p12'),
		'application/x-pkcs7-certreqresp' => array('p7r'),
		'application/x-pkcs7-mime' => array('p7c', 'p7m'),
		'application/x-pkcs7-signature' => array('p7a'),
		'application/x-project' => array('mpc', 'mpt', 'mpv', 'mpx'),
		'application/x-qpro' => array('wb1'),
		'application/x-rar-compressed' => array('rar'),
		'application/x-sdp' => array('sdp'),
		'application/x-sea' => array('sea'),
		'application/x-seelogo' => array('sl'),
		'application/x-shar' => array('shar'),
		'application/x-sprite' => array('spr', 'sprite'),
		'application/x-stuffit' => array('sit'),
		'application/x-sv4cpio' => array('sv4cpio'),
		'application/x-sv4crc' => array('sv4crc'),
		'application/x-tar' => array('tar'),
		'application/x-tbook' => array('sbk', 'tbk'),
		'application/x-tex' => array('tex'),
		'application/x-texinfo' => array('texi', 'texinfo'),
		'application/x-troff' => array('roff', 't', 'tr'),
		'application/x-troff-man' => array('man'),
		'application/x-troff-me' => array('me'),
		'application/x-troff-ms' => array('ms'),
		'application/x-visio' => array('vsd', 'vst', 'vsw'),
		'application/x-vnd.audioexplosion.mzz' => array('mzz'),
		'application/x-vnd.ls-xpix' => array('xpix'),
		'application/x-wais-source' => array('src', 'wsrc'),
		'application/x-winhelp' => array('hlp'),
		'application/x-wintalk' => array('wtk'),
		'application/x-wpwin' => array('wpd'),
		'application/x-wri' => array('wri'),
		'application/x-x509-ca-cert' => array('cer', 'der'),
		'application/x-x509-user-cert' => array('crt'),
		'application/xhtml+xml' => array('xht', 'xhtml'),
		'application/xml' => array('xml', 'xsl'),
		'application/xml-dtd' => array('dtd'),
		'application/xslt+xml' => array('xslt'),
		'application/zip' => array('zip'),
		'audio/basic' => array('au'),
		'audio/it' => array('it'),
		'audio/make' => array('funk', 'my'),
		'audio/make.my.funk' => array('pfunk'),
		'audio/mid' => array('rmi'),
		'audio/mpeg' => array('m2a', 'mp3', 'mpga'),
		'audio/s3m' => array('s3m'),
		'audio/tsp-audio' => array('tsi'),
		'audio/tsplayer' => array('tsp'),
		'audio/vnd.qcelp' => array('qcp'),
		'audio/voxware' => array('vox'),
		'audio/x-adpcm' => array('snd'),
		'audio/x-aiff' => array('aif', 'aifc', 'aiff'),
		'audio/x-gsm' => array('gsd', 'gsm'),
		'audio/x-jam' => array('jam'),
		'audio/x-liveaudio' => array('lam'),
		'audio/x-mod' => array('mod'),
		'audio/x-mpequrl' => array('m3u'),
		'audio/x-ms-wax' => array('wax'),
		'audio/x-ms-wma' => array('wma'),
		'audio/x-nspaudio' => array('la', 'lma'),
		'audio/x-pn-realaudio' => array('ram', 'rm', 'rmm'),
		'audio/x-pn-realaudio-plugin' => array('rmp', 'rpm'),
		'audio/x-psid' => array('sid'),
		'audio/x-realaudio' => array('ra'),
		'audio/x-twinvq' => array('vqf'),
		'audio/x-twinvq-plugin' => array('vqe', 'vql'),
		'audio/x-vnd.audioexplosion.mjuicemediafile' => array('mjf'),
		'audio/x-voc' => array('voc'),
		'audio/x-wav' => array('wav'),
		'audio/xm' => array('xm'),
		'chemical/x-pdb' => array('pdb', 'xyz'),
		'i-world/i-vrml' => array('ivr'),
		'image/bmp' => array('bm', 'bmp'),
		'image/cgm' => array('cgm'),
		'image/cmu-raster' => array('rast'),
		'image/fif' => array('fif'),
		'image/florian' => array('flo', 'turbot'),
		'image/g3fax' => array('g3'),
		'image/gif' => array('gif'),
		'image/ief' => array('ief', 'iefs'),
		'image/jpeg' => array('jfif-tbnl', 'jpe', 'jpeg', 'jpg'),
		'image/jutvision' => array('jut'),
		'image/naplps' => array('nap', 'naplps'),
		'image/pict' => array('pic', 'pict'),
		'image/pjpeg' => array('jfif'),
		'image/png' => array('png', 'x-png'),
		'image/svg+xml' => array('svg', 'svgz'),
		'image/tiff' => array('tif', 'tiff'),
		'image/vnd.adobe.photoshop' => array('psd'),
		'image/vnd.djvu' => array('djv', 'djvu'),
		'image/vnd.microsoft.icon' => array('ico'),
		'image/vnd.net-fpx' => array('fpx'),
		'image/vnd.rn-realflash' => array('rf', 'swf'),
		'image/vnd.rn-realpix' => array('rp'),
		'image/vnd.wap.wbmp' => array('wbmp'),
		'image/vnd.xiff' => array('xif'),
		'image/x-cmu-raster' => array('ras'),
		'image/x-dwg' => array('dwg', 'dxf', 'svf'),
		'image/x-jg' => array('art'),
		'image/x-jps' => array('jps'),
		'image/x-niff' => array('nif', 'niff'),
		'image/x-pcx' => array('pcx'),
		'image/x-pict' => array('pct'),
		'image/x-portable-anymap' => array('pnm'),
		'image/x-portable-bitmap' => array('pbm'),
		'image/x-portable-greymap' => array('pgm'),
		'image/x-portable-pixmap' => array('ppm'),
		'image/x-quicktime' => array('qif', 'qti', 'qtif'),
		'image/x-rgb' => array('rgb'),
		'image/x-xwindowdump' => array('xwd'),
		'image/xbm' => array('xbm'),
		'image/xpm' => array('xpm'),
		'message/rfc822' => array('eml', 'mht', 'mhtml'),
		'model/iges' => array('iges', 'igs'),
		'model/mesh' => array('mesh', 'msh', 'silo'),
		'model/vnd.dwf' => array('dwf'),
		'model/x-pov' => array('pov'),
		'multipart/x-gzip' => array('gzip'),
		'multipart/x-ustar' => array('ustar'),
		'music/x-karaoke' => array('kar'),
		'paleovu/x-pv' => array('pvu'),
		'text/asp' => array('asp'),
		'text/calendar' => array('ics', 'ifb'),
		'text/css' => array('css'),
		'text/csv' => array('csv'),
		'text/html' => array('acgi', 'htm', 'html', 'htmls', 'htx'),
		'text/mcf' => array('mcf'),
		'text/pascal' => array('pas'),
		'text/plain' => array('asc', 'c++', 'com', 'conf', 'cxx', 'def', 'g', 'idc', 'list', 'log', 'lst', 'mar', 'sdml', 'text', 'txt'),
		'text/richtext' => array('rtx'),
		'text/scriplet' => array('wsc'),
		'text/tab-separated-values' => array('tsv'),
		'text/uri-list' => array('uni', 'unis', 'uri', 'uris'),
		'text/vnd.abc' => array('abc'),
		'text/vnd.fmi.flexstor' => array('flx'),
		'text/vnd.rn-realtext' => array('rt'),
		'text/vnd.wap.wml' => array('wml'),
		'text/vnd.wap.wmlscript' => array('wmls'),
		'text/webviewhtml' => array('htt'),
		'text/x-asm' => array('asm', 's'),
		'text/x-audiosoft-intra' => array('aip'),
		'text/x-c' => array('c', 'cc', 'cpp'),
		'text/x-component' => array('htc'),
		'text/x-fortran' => array('f', 'f77', 'f90', 'for'),
		'text/x-h' => array('h', 'hh'),
		'text/x-java-source' => array('jav', 'java'),
		'text/x-la-asf' => array('lsx'),
		'text/x-m' => array('m'),
		'text/x-pascal' => array('p'),
		'text/x-script' => array('hlb'),
		'text/x-script.csh' => array('csh'),
		'text/x-script.elisp' => array('el'),
		'text/x-script.ksh' => array('ksh'),
		'text/x-script.lisp' => array('lsp'),
		'text/x-script.perl' => array('pl'),
		'text/x-script.perl-module' => array('pm'),
		'text/x-script.phyton' => array('py'),
		'text/x-script.rexx' => array('rexx'),
		'text/x-script.sh' => array('sh'),
		'text/x-script.tcl' => array('tcl'),
		'text/x-script.tcsh' => array('tcsh'),
		'text/x-script.zsh' => array('zsh'),
		'text/x-server-parsed-html' => array('shtml', 'ssi'),
		'text/x-setext' => array('etx'),
		'text/x-sgml' => array('sgm', 'sgml'),
		'text/x-speech' => array('spc', 'talk'),
		'text/x-uil' => array('uil'),
		'text/x-uuencode' => array('uu', 'uue'),
		'text/x-vcalendar' => array('vcs'),
		'video/animaflex' => array('afl'),
		'video/avs-video' => array('avs'),
		'video/mpeg' => array('m1v', 'm2v', 'mpa', 'mpe', 'mpeg', 'mpg'),
		'video/ogg' => array('ogv'),
		'video/quicktime' => array('moov', 'mov', 'qt'),
		'video/vdo' => array('vdo'),
		'video/vnd.mpegurl' => array('m4u', 'mxu'),
		'video/vnd.rn-realvideo' => array('rv'),
		'video/vnd.vivo' => array('viv', 'vivo'),
		'video/vosaic' => array('vos'),
		'video/x-amt-demorun' => array('xdr'),
		'video/x-amt-showrun' => array('xsr'),
		'video/x-atomic3d-feature' => array('fmf'),
		'video/x-dl' => array('dl'),
		'video/x-dv' => array('dif', 'dv'),
		'video/x-fli' => array('fli'),
		'video/x-flv' => array('flv'),
		'video/x-gl' => array('gl'),
		'video/x-isvideo' => array('isu'),
		'video/x-motion-jpeg' => array('mjpg'),
		'video/x-mpeq2a' => array('mp2'),
		'video/x-ms-asf' => array('asf'),
		'video/x-ms-asf-plugin' => array('asx'),
		'video/x-ms-wm' => array('wm'),
		'video/x-ms-wmv' => array('wmv'),
		'video/x-ms-wmx' => array('wmx'),
		'video/x-msvideo' => array('avi'),
		'video/x-qtc' => array('qtc'),
		'video/x-scm' => array('scm'),
		'video/x-sgi-movie' => array('movie', 'mv'),
		'windows/metafile' => array('wmf'),
		'x-conference/x-cooltalk' => array('ice'),
		'x-music/x-midi' => array('mid', 'midi'),
		'x-world/x-3dmf' => array('3dm', '3dmf', 'qd3', 'qd3d'),
		'x-world/x-svr' => array('svr'),
		'x-world/x-vrml' => array('vrml', 'wrl', 'wrz'),
		'x-world/x-vrt' => array('vrt'),
		'xgl/drawing' => array('xgz'),
		'xgl/movie' => array('xmz')
	);

	/**
	 * Returns extensions for a specified mime type.
	 *
	 * This function returns an array with file extensions for this mime type.
	 * The array contains the extensions without dot and written in lowercase.
	 * If you set the second parameter to boolean true only the first extension from the array will be returrned as string.
	 * NULL will be returned if no extension is found.
	 *
	 * Internal note: This is a simple mime type to extension mapping.
	 *
	 * @param string Mime Type
	 * @param boolean false (default) to return extension array, true to return first element only
	 * @return array|string Array with extensions or null
	 */
	public static function getExtensions($mimeType, $single = false) {
		$mimeType = strtolower($mimeType);
		if (isset(self::$data[$mimeType]) == true && count(self::$data[$mimeType]) > 0) {
			if ($single == false) {
				return self::$data[$mimeType];
			}
			else {
				return reset(self::$data[$mimeType]); // Return first element of the array
			}
		}
		else {
			return null;
		}
	}

	/**
	 * Returns the mime type for a specified file.
	 *
	 * You can specify the path to a file or an extension (with our without leading dot).
	 * If you specify a valid file name we try to use the fileinfo extension to 
	 * determine the mime type, if this fails we use the mapping array.
	 * If you specify an invalid file or just an extension we use the mapping array.
	 * This function returns a mime type (in lowercase) for an extension.
	 * NULL will be returned if no mime type is found.
	 * 
	 * Note: Be careful when specifying files like .htaccess or README. 
	 * This could be a valid file or an extension, this function assumes that
	 * every input is a vlid file and, if this fails, we check for the extension mapping.
	 * 
	 * @link http://www.php.net/fileinfo
	 * @param string File extension without dot
	 * @return string Mime type
	 */
	public static function getMimeType($file) {
		$mimeType = false;
		if (class_exists('finfo', false) && file_exists($file)) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			if ($finfo) {
				$mimeType = $finfo->file($file);
			}
		}
		if ($mimeType === false) {
			$ext = new File($file);
			$mimeType = Arrays::find(self::$data, $ext->extension());
		}
		return ($mimeType === false) ? null : $mimeType;
	}

}
?>