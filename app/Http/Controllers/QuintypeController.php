<?php

namespace App\Http\Controllers;

use Api;
use Meta;
use Seo;
use Caching;
use cache;

class QuintypeController extends Controller
{
    public function __construct()
    {
        $this->client = new Api(getQuintypeAPIHost(Request()));
        $this->config = array_merge($this->client->config(), config('quintype'));
        $this->allStacks = $this->config['layout']['stacks'];
        $this->meta = new Meta();
        $this->seo = new Seo($this->config);
        $this->caching = new Caching();
        $this->defaultCacheParams = [
        'publisherId' => $this->config['publisher-id'],
        'cdnTTLs' => [
          'max-age' => 3 * 60,
          'stale-while-revalidate' => 5 * 60,
          'stale-if-error' => 4 * 60 * 60,
        ],
        'browserTTLs' => [
          'max-age' => 60,
        ],
    ];


   
    }

    public function toView($args)
    {
        
        return array_merge([
          'config' => $this->config,
          'site_url' => url('/'),
          'client' => $this->client,
          'nestedMenuItems' => $this->client->prepareNestedMenu($this->config['layout']['menu']),
        ], $args);
    }

    protected function pageNotFound()
    {
        return response()->view('errors/404', $this->toView([]), 404);
    }

    protected function getAverageRating($story)
    {
        if (sizeof($story['votes']) > 0) {
            $numerator = 0;
            $noOfVoters = 0;
            foreach ($story['votes'] as $key => $value) {
                $numerator += ($key * $value);
                $noOfVoters += $value;
            }
            $averageRatingValue = round(($numerator) / ($noOfVoters), 1);
            $ratingPercentValue = ($averageRatingValue * 100) / (5);
            $ratingValues = array('average-rating' => $averageRatingValue, 'rating-percentage' => $ratingPercentValue, 'rater-count' => $noOfVoters);

            return $ratingValues;
        }
    }

    protected function getSectionNames($story)
    {
        $allSections = array();
        foreach ($story['sections'] as $key => $section) {
            array_push($allSections, $section['display-name']);
        }
        if (sizeof($story['sections']) == 1) {
            $sectionNames = $allSections[0];
        } elseif (sizeof($story['sections']) == 2) {
            $sectionNames = implode(' and ', $allSections);
        } else {
            $lastSectionName = array_pop($allSections);
            $sectionNames = implode(', ', $allSections).' and '.$lastSectionName;
        }

        return $sectionNames;
    }


    public function traveseMap(){ 

        // //getting all the stories
        $params = [
            'story-group' => 'top',
            'limit' => 5000,
        ];
        
        $stories = array_slice($this->client->stories($params), 0, 5000);

        $storyids = [];
        $storyids_insights = [];
        $storyids_axioms = [];

        //getting axioms
        foreach ($stories as $story) {
            
            //getting story elements by story slug
            $storyDetails = $this->client->storyBySlug(['slug' => $story['slug']]);
            $currentStoryId = $story['id'];
            $storyCards = $storyDetails['cards'];

                foreach ($storyCards as $card) {
                    
                    $storyElement = $card['story-elements'];

                    foreach ($storyElement as $element) {
                        
                        if ($element['subtype'] == 'also-read') {
                            
                            $linkedStoryId = $element['metadata']['linked-story-id'];

                            if($linkedStoryId == "9e826251-deab-43c5-8661-1fd7acb6ddce") {

                            } else {

                                 //getting story details by story id
                                $storyDetails = $this->client->storyById($linkedStoryId);

                                foreach ($storyDetails as $info) {
                                    
                                    if($info['hero-image-caption'] == "action") {

                                        $storyids[$linkedStoryId][] = $currentStoryId;

                                    } if($info['hero-image-caption'] == "axiom") {

                                        $storyids_axioms[$linkedStoryId][] = $currentStoryId;

                                    } if($info['hero-image-caption'] == "insight") {
                                        
                                        $storyids_insights[$linkedStoryId][] = $currentStoryId;
                                    }   
                                }
                            }
                        }
                    }
                }


        }

        // return view('static/map', $this->toView([
        //     'stories' => $storyids,
        //     'axioms' => $storyids_axioms,
        //     'insights' => $storyids_insights
        //   ])
        // );

        return $storyids;

    }

}
