<?php

use App\Api\FocusedImage;

function assetPath($file)
{
    $cdn = config('quintype.asset-host');

    return $cdn.elixir($file, config('quintype.publisher-name').'/assets');
}

function focusedImageUrl($slug, $aspectRatio, $metadata, $opts)
{
    $cdn = config('quintype.image-cdn');
    $image = new FocusedImage($slug, $metadata);

    return $cdn.'/'.$image->path($aspectRatio, $opts);
}

/**
@menuType string (section, link or tag)
@menuSlug string (Link, or slug of the corresponding section and tag)
@parentHierarchy array (Parent slugs in correct heirarchy)
IMPORTANT: Any change in this method has to be duplicated in twig extension method prepareMenuUrl() in template.js
**/
function prepareMenuUrl($menuType, $menuSlug, $parentHierarchy = [])
{
  switch($menuType){
    case 'section':
      if(sizeof($parentHierarchy) > 0){
        return '/section/'.implode($parentHierarchy, "/").'/'.$menuSlug;
      }
      return '/section/'.$menuSlug;
      break;
    case 'link':
      return $menuSlug;
      break;
    case 'tag':
      return '/tag?tag='.$menuSlug;
      break;
    default:
      return '#';
      break;
  }
}

function getPhotoStoryImages($story) {
  $photoArray = [
    ['image-s3-key' => $story['hero-image-s3-key'],
    'image-metadata' => $story['hero-image-metadata'],
    'title' => $story['hero-image-caption'], ],
  ];
  foreach ($story['cards'] as $card) {
    foreach ($card['story-elements'] as $key => $element) {
      if ($element['type'] == 'image') {
        array_push($photoArray, ['image-s3-key' => $element['image-s3-key'],
        'image-metadata' => $element['image-metadata'],
        'title' => $element['title'], ]);
      }
    }
   }
  return $photoArray;
}

function dateIsoFormat($data) {
  return date(DATE_ISO8601, $data);
}

function decode64($data) {
  return base64_decode($data);
}
