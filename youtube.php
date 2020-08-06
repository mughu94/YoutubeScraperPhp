<?php
 /**
 * mughu.me
 * https://github.com/mughu94/YoutubeScraperPhp
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
		$json_data = json_decode("{" . $matches[1][0] . "}", true);
	}
    //print_r('<pre>');
    //die(print_r($json_data));
	if (isset($json_data['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents']) && is_array($json_data['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'])) {
		foreach ($json_data['contents']['twoColumnSearchResultsRenderer']['primaryContents']['sectionListRenderer']['contents'] as $konten) {
			if (isset($konten['itemSectionRenderer']['contents']) && is_array($konten['itemSectionRenderer']['contents'])) {
				$videoku['items'] = array();
				foreach ($konten['itemSectionRenderer']['contents'] as $item_jadi) {
					if (isset($item_jadi['videoRenderer'])) {
						$item_jadi  = $item_jadi['videoRenderer'];
						$video_id = (isset($item_jadi['videoId'])) ? $item_jadi['videoId'] : '';
						if (empty($video_id)) continue;
						$videoku['items'][] = array(
							'id' => array(
								'videoId' => $video_id
							),
							'url' => "https://www.youtube.com/watch?v=" . $video_id,
							'title' => ((isset($item_jadi['title']['runs'][0]['text'])) ? $item_jadi['title']['runs'][0]['text'] : ''),
							'thumbHigh' => ((isset($item_jadi['thumbnail']['thumbnails']['0']['url'])) ? $item_jadi['thumbnail']['thumbnails']['0']['url'] : ''),
							'channelTitle' => ((isset($item_jadi['ownerText']['runs']['0']['text'])) ? $item_jadi['ownerText']['runs']['0']['text'] : ''),
							'channelId' => ((isset($item_jadi['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'])) ? $item_jadi['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'] : ''),
							'channelUrl' => ((isset($item_jadi['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'])) ? "https://www.youtube.com/channel/" . $item_jadi['ownerText']['runs']['0']['navigationEndpoint']['browseEndpoint']['browseId'] : ''),
							'publishedAt' => ((isset($item_jadi['publishedTimeText']['simpleText'])) ? $item_jadi['publishedTimeText']['simpleText'] : ''),
							'duration' => ((isset($item_jadi['lengthText']['simpleText'])) ? $item_jadi['lengthText']['simpleText'] : ''),
							'viewCount' => ((isset($item_jadi['viewCountText']['simpleText'])) ? preg_replace('/(,)|(\s*views?)$/i', "", $item_jadi['viewCountText']['simpleText']) : '')
						);
					}
				}
				if (!empty($videoku['items'])) break;
			}
		}
	}
	print_r('<pre>');
	print_r($videoku);
}
