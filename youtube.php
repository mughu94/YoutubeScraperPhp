<?php
 /**
 * mughu.me
 * https://github.com/mughu94/YoutubeScraperCurlPhp
 */

$url = 'https://www.youtube.com/results?q=bismillah';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$results = curl_exec($ch);
curl_close($ch);

if (!empty($results)) {
	preg_match_all('/window\["ytInitialData"\]\s*=\s*\{(.+?)\};/s', $results, $matches);
	if (isset($matches[1][0])) {
		$json = json_decode("{" . $matches[1][0] . "}", true);
	}
    //print_r('<pre>');
    //die(print_r($json));
	if (isset($json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents']) && is_array($json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'])) {
		foreach ($json['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'] as $contents) {
			if (isset($contents['itemSectionRenderer']['contents']) && is_array($contents['itemSectionRenderer']['contents'])) {
				$videos['items'] = array();
				foreach ($contents['itemSectionRenderer']['contents'] as $item) {
					if (isset($item['videoRenderer'])) {
						$item  = $item['videoRenderer'];
						$vidid = (isset($item['videoId'])) ? $item['videoId'] : '';
						if (empty($vidid)) continue;
						$videos['items'][] = array(
							'id' => array(
								'videoId' => $vidid
							),
							'url' => "https://www.youtube.com/watch?v=" . $vidid,
							'title' => ((isset($item['title']['runs'][0]['text'])) ? $item['title']['runs'][0]['text'] : ''),
							'thumbHigh' => ((isset($item['thumbnail']['thumbnails']['0']['url'])) ? $item['thumbnail']['thumbnails']['0']['url'] : ''),
							'channelTitle' => ((isset($item['ownerText']['runs']['0']['text'])) ? $item['ownerText']['runs']['0']['text'] : ''),
							'channelId' => ((isset($item['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'])) ? $item['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'] : ''),
							'channelUrl' => ((isset($item['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'])) ? "https://www.youtube.com/channel/" . $item['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'] : ''),
							'publishedAt' => ((isset($item['publishedTimeText']['simpleText'])) ? $item['publishedTimeText']['simpleText'] : ''),
							'duration' => ((isset($item['lengthText']['simpleText'])) ? $item['lengthText']['simpleText'] : ''),
							'viewCount' => ((isset($item['viewCountText']['simpleText'])) ? preg_replace('/(,)|(\s*views?)$/i', "", $item['viewCountText']['simpleText']) : '')
						);
					}
				}
				if (!empty($videos['items'])) break;
			}
		}
	}
	print_r('<pre>');
	print_r($videos);
}

?>
