<?php

namespace App\Api;

class FocusedImage {
    public function __construct($slug, $metadata) {
        $this->slug = $slug;
        $this->metadata = $metadata;
    }

    private function findLeftBound($imageWidth, $halfCropWidth, $focusPoint) {
        if ($focusPoint - $halfCropWidth < 0) {
            return 0;
        }
        else if ($focusPoint + $halfCropWidth > $imageWidth) {
            return $imageWidth - $halfCropWidth;
        }
        else {
            return $focusPoint - $halfCropWidth;
        }
    }

    private function findBounds($imageWidth, $cropWidth, $focusPoint) {
        $leftBound = $this->findLeftBound($imageWidth, $cropWidth / 2, $focusPoint);
        if ($leftBound + $cropWidth > $imageWidth) {
            return ($imageWidth - $cropWidth);
        }
        else {
            return $leftBound;
        }
    }

    private function imageBounds($imageDimensions, $aspectRatio, $focusPoint) {
        if ($imageDimensions[0] * $aspectRatio[1] < $imageDimensions[1] * $aspectRatio[0]) {
            // Use the entire width
            $expectedHeight = ($imageDimensions[0] * $aspectRatio[1]) / $aspectRatio[0];
            $bound          = $this->findBounds($imageDimensions[1], $expectedHeight, $focusPoint[1]);
            return [0, round($bound), $imageDimensions[0], round($expectedHeight)];
        } else {
            // Use the entire height
            $expectedWidth = ($imageDimensions[1] * $aspectRatio[0]) / $aspectRatio[1];
            $bound         = $this->findBounds($imageDimensions[0], $expectedWidth, $focusPoint[0]);
            return [round($bound), 0, round($expectedWidth), $imageDimensions[1]];
        }
    }

    private function imgixPath($opts) {
        if(empty($opts))
            return "";

        $args = [];
        foreach($opts as $key => $value) {
            array_push($args, $key . "=" . rawurlencode(is_array($value) ? join(",", $value) : $value ));
        }

        return "?" . join("&", $args);
    }

    public function path($aspectRatio, $opts) {
        $rectOpts = [];
        if($this->metadata && isset($this->metadata["height"]) && isset($this->metadata["width"]) && isset($this->metadata["focus-point"])) {
            $rectOpts["rect"] = $this->imageBounds([$this->metadata["width"], $this->metadata["height"]], $aspectRatio, $this->metadata["focus-point"]);
        }

        return rawurlencode($this->slug) . $this->imgixPath(array_merge($rectOpts, $opts));
    }
}
