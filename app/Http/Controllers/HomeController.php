<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class HomeController extends QuintypeController
{
	public function __construct()
	{
		parent::__construct();
		$this->fields = 'id,headline,slug,url,hero-image-s3-key,hero-image-metadata,first-published-at,last-published-at,alternative,published-at,author-name,author-id,sections,story-template,summary,metadata,hero-image-attribution,hero-image-caption,cards,subheadline,authors';
		$this->loadMoreFields = 'sections,hero-image-s3-key,hero-image-metadata,hero-image-caption,headline,subheadline,author-name,summary,first-published-at,slug,id,story-content-id';
	}

	public function index(Request $request)
	{
		$page = ['type' => 'home'];

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}


  //       if (Cache::get('test1') === null)
		// {
		//     echo "Value wasn't cached.";
		//     Cache::put('test1', 'I was tested at TECHarr', 60); 
		// }
		// else{
		// 	echo "getting value from cache";
		// 	echo '<br>';
		// 	echo Cache::get('test1');
		// }



        //$user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     return redirect('/profile');
        // }
        // else{
        //     $isLoggedIn = 'false';
        // }

        //getting all the section names
		$allSections = $this->config['sections'];

        //getting list of Moderators
		$authors = $this->getModerators();

        //getting home page collection
		$homeCollection = $this->client->getCollections('home-page', []);

        //getting stories from the collections result
		$stories = $homeCollection['items'];

		$storyCount = sizeof($stories);

        //$strng = "http://insurge-web.staging.quintype.io/psychology/2017/08/02/3-ways-clean-energy-will-make-big-oil-extinct-in-12-to-32-years-without-subsidies"

        $commentCount = array();
         // for( $i = 0; $i<3; $i++ ) {
         //     $mystr = base64_encode("https://insurge-web.staging.quintype.io/".$stories[$i]['story']['slug']);
         //     $response = file_get_contents("https://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/commeznts.json");
         //     $response = json_decode($response, true);
         //     array_push($commentCount, $response);
         // }

        //Set SEO meta tags.
		$setSeo = $this->seo->home($page['type']);
		$this->meta->set($setSeo->prepareTags());

		return response(view('home/index', $this->toView([
			'stories' => $stories,
			'sections'=> $allSections,
			'authors' => $authors, 
			'storyCount' => $storyCount,
			'page' => $page,
			'meta' => $this->meta,
			'isLoggedIn' => $isLoggedIn,
            'commentCount' => $commentCount,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ['locationId' => 'home', 'storyGroup' => 'top', 'storiesToCache' => []])));
	}

	public function hometwo(Request $request){
		$page = ['type' => 'home'];

        $user = $request->cookie('session-cookie');

        if(isset($user)) {
        	$userInfo = $this->client->getMemberMetadata($user);
        	echo $userInfo;
        }

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        //getting all the section names
		$allSections = $this->config['sections'];

        //getting list of Moderators
		$authors = $this->getModerators();

        //getting home page collection
		$homeCollection = $this->client->getCollections('home-page', []);

        //getting stories from the collections result
		$stories = $homeCollection['items'];

        //Set SEO meta tags.
		$setSeo = $this->seo->home($page['type']);
		$this->meta->set($setSeo->prepareTags());

		return response(view('hometwo/index', $this->toView([
			'stories' => $stories,
			'sections'=> $allSections,
			'authors' => $authors, 
			'page' => $page,
			'meta' => $this->meta,
        //  'isLoggedIn' => $isLoggedIn,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ['locationId' => 'home', 'storyGroup' => 'top', 'storiesToCache' => []])));
	}

	public function getModerators(){

		$authors = $this->client->getAuthors();
		$array = [];
		foreach ($authors as $author) {
			if((strpos($author['bio'], 'moderator')) || (strpos($author['bio'], 'Moderator')) ) {
				$array[] = $author; 
			}
		}
		$authors = $array;
		return $authors;
	}

	public function storyWithoutDate(Request $request, $category, $slug)
	{
		return $this->story($category, $slug);
	}

	public function storyWithDate($category, $y, $m, $d, $slug)
	{
		return $this->story($category, $slug);
	}

	public function story(Request $request, $category, $y,$m,$d,$slug)
	{

		$allSections = $this->config['sections'];

		$page = ['type' => 'story'];

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
			$now = new DateTime();
			$date = $now->format('Y-m-d H:i:s'); 
		}
		else{
			$isLoggedIn = 'false';
		}

        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

		$authorDetails = [];

        //story information
		$story = $this->client->storyBySlug(['slug' => $slug]);

        //getting story comments
		//$comments = $this->client->storyComments($story['story-content-id']);
		$comments = "";

        //getting also read element stories data
		$also_read = $this->getAlsoReadStory($story);

        //author details
		$authorDetails = $this->client->getAuthor($story['author-id']);

        //Set SEO meta tags.
		$setSeo = $this->seo->story($page['type'], $story);
		$this->meta->set($setSeo->prepareTags());

		return response(view('story/index', $this->toView([
			'story' => $story,
			'sections'=> $allSections,
			'authorDetails' => $authorDetails,
			'comments' => $comments,
			'alsoread' => $also_read,
			'page' => $page,
			'meta' => $this->meta,
			'isLoggedIn' => $isLoggedIn,
        //'userInfo' => $userInfo,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, [])));
	}

    //get also read story ids
	public function getAlsoReadStories($story) {
		$also_read_story_ids = array();
		foreach ($story['cards'] as $card) {
			foreach ($card['story-elements'] as $StoryElement) {
				if($StoryElement['subtype'] == 'also-read') {
					$also_read_story_ids[] = $StoryElement['metadata']['linked-story']['id'];
				}
			}
		}
		return $also_read_story_ids;
	}


    //get also read story data
	public function getAlsoReadStory($story) {

		$also_read_ids = array_unique($this->getAlsoReadStories($story));
		$AlsoReadArray = array();

		foreach ($also_read_ids as $id) {

            //fetching story information from story id
			$story = $this->client->storyById($id)['story'];

			$readarray['linked-story-id'] = $story['story-content-id'];
			$readarray['subheadline'] = $story['subheadline'];
			$readarray['hero-image-caption'] = $story['hero-image-caption'];
			$readarray['slug'] = $story['slug'];

			$AlsoReadArray[] = $readarray;

		}
		return $AlsoReadArray;
	}

     //Axiom Page
	public function axiom(Request $request, $category, $y,$m,$d,$slug)
	{
		$page = ['type' => 'story'];
		$authorDetails = [];

		$site_url = $this->config['sketches-host'];
        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        //story information
		$axiom = $this->client->storyBySlug(['slug' => $slug]);

		$traverse = $this->traveseMap();

		$story_id = $axiom['story-content-id'];

		$traverse = $traverse[$story_id];

		$allSections = $this->config['sections'];

        //Set SEO meta tags.
		$setSeo = $this->seo->story($page['type'], $axiom);
		$this->meta->set($setSeo->prepareTags());

		return response(view('axiom/index', $this->toView([
			'axiom' => $axiom,
			'sections'=> $allSections,
			'site_url' => $site_url,
			'page' => $page,
			'meta' => $this->meta,
        // 'userInfo' => $userInfo,
        // 'isLoggedIn' => $isLoggedIn,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, [])));
	}


    //Insight Page
	public function insight(Request $request, $category, $y,$m,$d,$slug)
	{
		$page = ['type' => 'story'];
		$authorDetails = [];

        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        //story information
		$insight = $this->client->storyBySlug(['slug' => $slug]);

		$allSections = $this->config['sections'];         

        //Set SEO meta tags.
		$setSeo = $this->seo->story($page['type'], $insight);
		$this->meta->set($setSeo->prepareTags());

		return response(view('insight/index', $this->toView([
			'insight' => $insight,
			'sections'=> $allSections,
			'page' => $page,
			'meta' => $this->meta,
        // 'isLoggedIn' => $isLoggedIn,
        // 'userInfo' => $userInfo,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, [])));
	}


    //Action Page
	public function action(Request $request, $category, $y,$m,$d,$slug)
	{
		$page = ['type' => 'story'];
		$authorDetails = [];

        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        //story information
		$action = $this->client->storyBySlug(['slug' => $slug]);

		$allSections = $this->config['sections'];         

        //Set SEO meta tags.
		$setSeo = $this->seo->story($page['type'], $action);
		$this->meta->set($setSeo->prepareTags());

		return response(view('action/index', $this->toView([
			'action' => $action,
			'sections'=> $allSections,
			'page' => $page,
			'meta' => $this->meta,
        // 'userInfo' => $userInfo,
        // 'isLoggedIn' => $isLoggedIn,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, [])));
	}


	public function section(Request $request, $sectionSlug, $subSectionSlug = '')
	{
		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}

		$page = ['type' => 'section'];

        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        

		$allSections = $this->config['sections'];

		$main_section = $this->client->getSectionDetails($sectionSlug, $allSections);

		if(empty($main_section)) {
			return $this->pageNotFound();
		} else { 
			$breadcrumb1 = $main_section['slug'];
			$section_id = $main_section['id'];
			$breadcrumb2 = "";
		}

		$sectionName = $main_section['display-name'];
		$sectionSlug = $main_section['slug'];

		if($subSectionSlug == "") {
			$collection_slug = $sectionSlug;
			$section_details = count($main_section);
			if($section_details == 0){
				return $this->pageNotFound();
			}
		} else {

			$main_sub_section = $this->client->getSectionDetails($subSectionSlug, $allSections);
			$section_id = $main_sub_section['id'];
			$section_details = count($main_sub_section);
			if($section_details == 0){
				$collection_slug = $sectionSlug;
                //return $this->pageNotFound();
			}else {
				$collection_slug = $subSectionSlug."-".$sectionSlug;
			}

			$breadcrumb2 = $main_sub_section['slug'];
		}

		$storyCount = 6;
		$params = [
		'story-group' => 'top',
		'limit' => $storyCount + 1,
		'fields' => $this->fields,
		];


        // Getting the collections Data as per the slug
		$stories = $this->client->getCollections($sectionSlug, $params);

		$sectionHeadline = $stories['name'];
		$sectionSummary = $stories['summary'];

		if($stories['total-count'] == 0) {
			return $this->pageNotFound();
		}

		$collection_name = $stories['slug'];
		$collection_id = $stories['id'];

        //Set SEO meta tags.
		$setSeo = $this->seo->section($page['type'], $collection_name, $section_id);
		$this->meta->set($setSeo->prepareTags());

        $commentCount = array();
         for( $i = 0; $i<6; $i++ ) {
             $mystr = base64_encode("https://insurge-web.staging.quintype.io/".$stories['items'][$i]['story']['slug']);
             $response = file_get_contents("https://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

		if (sizeof($stories) > 0) {
			return response(view('section/index', $this->toView([
				'stories' => $stories,
				'sections'=> $allSections,
				'collectionId' => $collection_id,
				'breadcrumb1' => $breadcrumb1,
				'breadcrumb2' => $breadcrumb2,
				'sectionName' => $sectionName,
				'sectionSlug' => $sectionSlug,
				'sectionHeadline' => $sectionHeadline,
				'sectionSummary' => $sectionSummary,
				'page' => $page,
				'meta' => $this->meta,
				'loadMoreFields' => $this->loadMoreFields,
				'storyCount' => $storyCount,
				'showLoadMore' => (sizeof($stories) + 1) > $storyCount,
                'isLoggedIn' => $isLoggedIn,
                'commentCount' => $commentCount,
				])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ['locationId' => $collection_id, 'storyGroup' => $params['story-group'], 'storiesToCache' => []])));
		} else {
			return view('errors/no_results', $this->toView([]));
		}
	}

	public function author(Request $request, $authorId)
	{

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}
        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

		$authorDetails = $this->client->getAuthor($authorId);

		$allSections = $this->config['sections'];

		$storyCount = 6;


		$params = [
		'author-id' => $authorId,
		'sort' => 'latest-published',
		'limit' => $storyCount + 1,
		'fields' => $this->fields,
		];
		$authorStories = $this->client->search($params);

		$authorStories_firstPublished = $this->client->search($params);

		usort($authorStories_firstPublished, function($a, $b)
		{
			return strcmp($a["first-published-at"], $b["first-published-at"]);
		});

		$joiningDate = $authorStories_firstPublished[0]['first-published-at'];

		$commentCount = array();
		$lengthStory = sizeof($authorStories);
         for( $i = 0; $i<$lengthStory; $i++ ) {
             $mystr = base64_encode("https://insurge-web.staging.quintype.io/".$authorStories[$i]['slug']);
             $response = file_get_contents("https://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

		return view('author/index', $this->toView([
			'authorDetails' => $authorDetails,
			'authorStories' => $authorStories,
			'joiningDate' => $joiningDate,
			'sections'=> $allSections,
			'authorId' => $authorId,
			'storyCount' => $storyCount,
			'params' => $params,
			'sort' => 'latest-published',
			'isLoggedIn' => $isLoggedIn,
			'commentCount' => $commentCount,
          // 'userInfo' => $userInfo,

			])
		);
	}


	public function authorsListing(Request $request)
	{
        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

		$authors = $this->getModerators();

		$allSections = $this->config['sections'];

		$this->page = ['type' => 'author'];

		return view('authors_page', $this->toView([
			'authors' => $authors,
			'sections'=> $allSections,
            // 'userInfo' => $userInfo,
            // 'isLoggedIn' => $isLoggedIn,
			])
		);
	}




	public function tag(Request $request)
	{
		$page = ['type' => 'tag'];

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}

        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        


		$tag = $request->tag;
		$storyCount = 8;
		$params = [
		'story-group' => 'top',
		'tag' => $tag,
		'limit' => $storyCount + 1,
		];
		$pickedStories = array_slice($this->client->stories($params), 0, $storyCount);

		$allSections = $this->config['sections'];

        //Set SEO meta tags.
		$setSeo = $this->seo->tag($tag);
		$this->meta->set($setSeo->prepareTags());

		$commentCount = array();
		$lengthStory = sizeof($pickedStories);
         for( $i = 0; $i<$lengthStory; $i++ ) {
             $mystr = base64_encode("http://insurge-web.staging.quintype.io/".$pickedStories[$i]['slug']);
             $response = file_get_contents("http://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

		if (sizeof($pickedStories) < 1) {
			return view('errors/no_results', $this->toView([]));
		} else {
			return view('tag/index', $this->toView([
				'stories' => $pickedStories,
				'tag' => $tag,
				'page' => $page,
				'sections'=> $allSections,
				'meta' => $this->meta,
				'loadMoreFields' => $this->loadMoreFields,
				'storyCount' => $storyCount,
				'showLoadMore' => (sizeof($pickedStories) + 1) > $storyCount,
                'isLoggedIn' => $isLoggedIn,
                'commentCount' => $commentCount,
            // 'userInfo' => $userInfo,
				])
			);
		}
	}

	public function search(Request $request)
	{

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}

		$page = ['type' => 'search'];

		$allSections = $this->config['sections'];


        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        

		$searchKey = $request->q;
		$storyCount = 6;
		$params = [
		'q' => $searchKey,
		'limit' => $storyCount + 1,
		'fields' => $this->fields,
		];
		$pickedStories = array_slice($this->client->search($params), 0, $storyCount);

        $commentCount = array();
        $lengthStory = sizeof($pickedStories);
         for( $i = 0; $i<$lengthStory; $i++ ) {
             $mystr = base64_encode("https://insurge-web.staging.quintype.io/".$pickedStories[$i]['slug']);
             $response = file_get_contents("https://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

       //Set SEO meta tags.
		$setSeo = $this->seo->search($searchKey);
		$this->meta->set($setSeo->prepareTags());
		if (sizeof($pickedStories) < 1) {
			return view('errors/no_results', $this->toView([]));
		} else {
			return view('search/index', $this->toView([
				'stories' => $pickedStories,
				'searchKey' => $searchKey,
				'sections' => $allSections,
				'page' => $page,
				'meta' => $this->meta,
				'params' => $params,
				'loadMoreFields' => $this->loadMoreFields,
				'storyCount' => $storyCount,
				'showLoadMore' => (sizeof($pickedStories) + 1) > $storyCount,
                'isLoggedIn' => $isLoggedIn,
                'commentCount' => $commentCount,
            // 'userInfo' => $userInfo,
				])
			);
		}
	}

	public function loginPage(Request $request)
	{
		if(!isset($_COOKIE["session-cookie"]))
		{
			echo "<script language='javascript' type='text/javascript'>";
			echo "</script>";
			echo "<script>location.href='/'</script>";
		}
		

		$page = ['type' => 'profile'];

        //getting all the section names
		$allSections = $this->config['sections'];

        //getting home page collection
		$homeCollection = $this->client->getCollections('home-page', []);
        //getting stories from the collections result
		$stories = $homeCollection['items'];

        $commentCount = array();
        $lengthStory = sizeof($stories);
         for( $i = 0; $i<3; $i++ ) {
             $mystr = base64_encode("http://insurge-web.staging.quintype.io/".$stories[$i]['story']['slug']);
             $response = file_get_contents("http://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

		$storyCount = sizeof($stories);
        //Set SEO meta tags.
		$setSeo = $this->seo->home($page['type']);
		$this->meta->set($setSeo->prepareTags());
		return response(view('profile/index', $this->toView([
			'stories' => $stories,
			'sections'=> $allSections,
			'storyCount' => $storyCount,
			'page' => $page,
			'meta' => $this->meta,
            'commentCount' => $commentCount,
			])))->withHeaders($this->caching->buildCacheHeaders(array_merge($this->defaultCacheParams, ['locationId' => 'home', 'storyGroup' => 'top', 'storiesToCache' => []])));
	}

    

	public function logout(Request $request)
	{
		\Cookie::queue(
			\Cookie::forget('session-cookie')
			);
		return response()->json([
			'amount' =>  "deleted"
			]);
	}

	public function mostDiscussedArticles(Request $request){
		$page = ['type' => 'home'];

		if(isset($_COOKIE["session-cookie"]))
		{
			$isLoggedIn = 'true';
		}
		else{
			$isLoggedIn = 'false';
		}

		$allSections = $this->config['sections'];
        // $user = $request->cookie('session-cookie');

        // if(isset($user))
        // {
        //     $isLoggedIn = 'true';
        //     $userInfo = $this->client->getCurrentMember($user);
        // }
        // else{
        //     $isLoggedIn = 'false';
        //     $userInfo = null;
        // }

        //getting all the section names


        //getting home page collection
		$mostDiscussedArticles = $this->client->getCollections('most-discussed-articles', []);

        //getting stories from the collections result
		$stories = $mostDiscussedArticles['items'];

        $commentCount = array();
        $lengthStory = sizeof($stories);
         for( $i = 0; $i<$lengthStory; $i++ ) {
             $mystr = base64_encode("http://insurge-web.staging.quintype.io/".$stories[$i]['story']['slug']);
             $response = file_get_contents("http://metype-api.staging.quintype.com/api/v1/accounts/49/pages/".$mystr."/comments.json");
             $response = json_decode($response, true);
             array_push($commentCount, $response);
         }

        //Set SEO meta tags.
		$setSeo = $this->seo->home($page['type']);
		$this->meta->set($setSeo->prepareTags());

		return view('mostDiscussedArticles/index', $this->toView([
			'stories' => $stories,
			'page' => $page,
			'sections'=> $allSections,
			'isLoggedIn' => $isLoggedIn,
			'meta' => $this->meta,
            'commentCount' => $commentCount,

			])
		);


	} 
}