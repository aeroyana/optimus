<?php

namespace App\Http\Controllers;

use App\facebookGroups;
use App\FacebookPages;
use App\Setting;
use App\TuBlogs;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Illuminate\Http\Request;
use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mockery\CountValidator\Exception;
use Tumblr\API\Client;
use Illuminate\Support\Facades\Auth;

class Settings extends Controller
{

    public function index()
    {
        if(Auth::user()->type != 'admin'){
            return "forbidden";
        }
        
        Prappo::index();
        session_start();

        // Wordpress
        $wpUser = DB::table('settings')->where('field', 'wpUser')->value('value');
        $wpPassword = DB::table('settings')->where('field', 'wpPassword')->value('value');
        $wpUrl = DB::table('settings')->where('field', 'wpUrl')->value('value');

        //Twitter

        $twConKey = DB::table('settings')->where('field', 'twConKey')->value('value');
        $twConSec = DB::table('settings')->where('field', 'twConSec')->value('value');
        $twToken = DB::table('settings')->where('field', 'twToken')->value('value');
        $twTokenSec = DB::table('settings')->where('field', 'twTokenSec')->value('value');
        $twUser = DB::table('settings')->where('field', 'twUser')->value('value');

        //Tumblr
        $tuConKey = DB::table('settings')->where('field', 'tuConKey')->value('value');
        $tuConSec = DB::table('settings')->where('field', 'tuConSec')->value('value');
        $tuToken = DB::table('settings')->where('field', 'tuToken')->value('value');
        $tuTokenSec = DB::table('settings')->where('field', 'tuTokenSec')->value('value');
        $tuDefBlog = DB::table('settings')->where('field', 'tuDefBlog')->value('value');

        //Facebook
        $fbAppId = DB::table('settings')->where('field', 'fbAppId')->value('value');
        $fbAppSec = DB::table('settings')->where('field', 'fbAppSec')->value('value');
        $fbToken = DB::table('settings')->where('field', 'fbAppToken')->value('value');

        try {
            $fb = new Facebook([
                'app_id' => $fbAppId,
                'app_secret' => $fbAppSec,
                'default_graph_version' => 'v2.6',
            ]);
        } catch (\Exception $g) {

        }

        try {
            $permissions = ['user_managed_groups',' manage_pages', 'pages_messaging', 'publish_actions', 'manage_pages', 'publish_pages', 'email', 'user_likes', 'public_profile', 'user_about_me', 'user_posts', 'publish_actions', 'ads_management', 'pages_manage_cta', 'read_page_mailboxes', 'pages_show_list', 'rsvp_event', 'user_events', 'pages_manage_instant_articles', 'user_actions.books', 'user_actions.fitness', 'user_actions.music', 'user_actions.news', 'user_actions.video', 'read_audience_network_insights', 'read_custom_friendlists', 'read_insights', 'user_status', 'user_religion_politics', 'user_hometown', 'user_location', 'user_photos', 'user_relationship_details', 'user_relationships']; // optional
            $helper = $fb->getRedirectLoginHelper();
            $loginUrl = $helper->getLoginUrl(url('') . '/fbconnect', $permissions);
        } catch (\Exception $e) {
            $loginUrl = url('/');
        }


        try {
            $fbPages = FacebookPages::all();
        } catch (\Exception $h) {
            $fbPages = "none";
        }


        // get tumblr blogs

        $tuBlogs = TuBlogs::all();
        $getLang = Setting::where('field', 'lang')->get();
        foreach ($getLang as $lang) {
            $l = $lang->value;
        }

        return view('settings', compact('l', 'twUser', 'tuDefBlog', 'wpUser', 'wpPassword', 'wpUrl', 'twConKey', 'twConSec', 'twToken', 'twTokenSec', 'tuConKey', 'tuConSec', 'tuToken', 'tuTokenSec', 'fbAppId', 'fbAppSec', 'fbToken', 'loginUrl', 'fbPages', 'tuBlogs'));
    }

    public function wpSave(Request $re)
    {
        $url = $re->wpUrl;
        $user = $re->wpUser;
        $pass = $re->wpPassword;
        if ($url == "" || $user == "" || $pass == "") {
            return "Please fill the necessary fields";
        }
        try {
            DB::table('settings')->where('field', 'wpUser')->update(['value' => $user]);
            DB::table('settings')->where('field', 'wpPassword')->update(['value' => $pass]);
            DB::table('settings')->where('field', 'wpUrl')->update(['value' => $url]);

            return "success";

        } catch (\PDOException $e) {

            return $e->getMessage();
        }
    }

    public function twSave(Request $re)
    {
        $twConKey = $re->twConKey;
        $twConSec = $re->twConSec;
        $twToken = $re->twToken;
        $twToeknSec = $re->twTokenSec;
        $twUser = $re->twUser;

        if ($twConKey == "" || $twConSec == "" || $twToken == "" || $twToeknSec == "") {
            return "Please fill the necessary fields";
        }

        try {
            DB::table('settings')->where('field', 'twConKey')->update(['value' => $twConKey]);
            DB::table('settings')->where('field', 'twConSec')->update(['value' => $twConSec]);
            DB::table('settings')->where('field', 'twToken')->update(['value' => $twToken]);
            DB::table('settings')->where('field', 'twTokenSec')->update(['value' => $twToeknSec]);
            DB::table('settings')->where('field', 'twUser')->update(['value' => $twUser]);

            return "success";

        } catch (\PDOException $e) {

            return $e->getMessage();
        }
    }

    public function tuSave(Request $re)
    {
        $tuConKey = $re->tuConKey;
        $tuConSec = $re->tuConSec;
        $tuToken = $re->tuToken;
        $tuTokenSec = $re->tuTokenSec;
        $tuDefBlog = $re->tuDefBlog;

        if ($tuConKey == "" || $tuConSec == "" || $tuToken == "" || $tuTokenSec == "") {
            return "Please fill the necessary fields";
        }

        try {
            DB::table('settings')->where('field', 'tuConKey')->update(['value' => $tuConKey]);
            DB::table('settings')->where('field', 'tuConSec')->update(['value' => $tuConSec]);
            DB::table('settings')->where('field', 'tuToken')->update(['value' => $tuToken]);
            DB::table('settings')->where('field', 'tuTokenSec')->update(['value' => $tuTokenSec]);
            DB::table('settings')->where('field', 'tuDefBlog')->update(['value' => $tuDefBlog]);

            return "success";

        } catch (\PDOException $e) {

            return $e->getMessage();
        }
    }

    public function fbSave(Request $re)
    {
        $appId = $re->fbAppId;
        $appSec = $re->fbAppSec;
        $token = $re->fbToken;
        $fbPages = $re->fbPages;
        if ($appId == "" || $appSec == "") {
            return "Please fill the necessary fields";
        }
        try {
            DB::table('settings')->where('field', 'fbAppId')->update(['value' => $appId]);
            DB::table('settings')->where('field', 'fbAppSec')->update(['value' => $appSec]);
            DB::table('settings')->where('field', 'fbAppToken')->update(['value' => $token]);
            DB::table('settings')->where('field', 'fbDefPage')->update(['value' => $fbPages]);
            return "success";
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    public function fbConnect()
    {
        session_start();
        $fb = new Facebook([
            'app_id' => Data::get('fbAppId'),
            'app_secret' => Data::get('fbAppSec'),
            'default_graph_version' => 'v2.5',
        ]);

        $helper = $fb->getRedirectLoginHelper();
        $_SESSION['FBRLH_state']=$_GET['state'];

        try {
            $accessToken = $helper->getAccessToken();
            $_SESSION['token'] = $accessToken;
            DB::table('settings')->where('field', 'fbAppToken')->update(['value' => $accessToken]); // save user access token to database
            $this->saveFbPages(); // save facebook pages and token
            $this->saveFbGroups(); // save facebook groups to database

        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            return '[a] Graph returned an error: ' . $e->getMessage();

        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            return '[a] Facebook SDK returned an error: ' . $e->getMessage();

        }


        try {

            $response = $fb->get('/me', $_SESSION['token']);
        } catch (FacebookResponseException $e) {
            return '[b] Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            return '[b] Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        return redirect('settings');


    }

    public function config($valueOf)
    {
        return DB::table('settings')->where('field', $valueOf)->value('value');
    }

    public function test()
    {

        $this->saveFbPages();

    }

    public function saveFbPages()
    {

        $fb = new Facebook([
            'app_id' => $this->config('fbAppId'),
            'app_secret' => $this->config('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);


        try {

            $response = $fb->get('me/accounts', $this->config('fbAppToken'));
            $body = $response->getBody();
            $data = json_decode($body, true);
            FacebookPages::truncate();
            foreach ($data['data'] as $no => $filed) {

                $facebookPages = new FacebookPages();
                $facebookPages->pageId = $filed['id'];
                $facebookPages->pageName = $filed['name'];
                $facebookPages->pageToken = $filed['access_token'];
                $facebookPages->save();

            }

        } catch (FacebookResponseException $e) {
            return 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            return 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

    }

    public function saveFbGroups()
    {

        $fb = new Facebook([
            'app_id' => $this->config('fbAppId'),
            'app_secret' => $this->config('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);


        try {

            $response = $fb->get('me/groups', $this->config('fbAppToken'));
            $body = $response->getBody();
            $data = json_decode($body, true);
            facebookGroups::truncate();

            foreach ($data['data'] as $no => $field) {

                $facebookGroup = new facebookGroups();
                $facebookGroup->pageId = $field['id'];
                $facebookGroup->pageName = $field['name'];
                $facebookGroup->privacy = $field['privacy'];
                $facebookGroup->save();
            }

        } catch (FacebookResponseException $e) {

            return 'Graph returned an error: ' . $e->getMessage();
        } catch (FacebookSDKException $e) {

            return 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }

    public function total_likes()
    {
        $fb = new Facebook([
            'app_id' => $this->config('fbAppId'),
            'app_secret' => $this->config('fbAppSec'),
            'default_graph_version' => 'v2.6',
        ]);

        try {

            $response = $fb->get('me/accounts', $this->config('fbAppToken'));
            $body = $response->getBody();
            $data = json_decode($body, true);
            $total_likes = 0;
            foreach ($data['data'] as $no => $content) {
                $page = $fb->get('/me?fields=fan_count', $content['access_token']);
                $fan_count = json_decode($page->getBody(), true);
                $likes = $fan_count['fan_count'];
                $total_likes += $likes;


            }

            return $total_likes;

        } catch (FacebookResponseException $e) {

            return 'Graph returned an error: ' . $e->getMessage();
        } catch (FacebookSDKException $e) {

            return 'Facebook SDK returned an error: ' . $e->getMessage();
        }
    }

    public function tuSync()
    {
        $consumerKey = Data::get('tuConKey');
        $consumerSecret = Data::get('tuConSec');
        $token = Data::get('tuToken');
        $tokenSecret = Data::get('tuTokenSec');

        $tuClient = new Client($consumerKey, $consumerSecret, $token, $tokenSecret);

        try {
            $tuBlogName = $tuClient->getUserInfo()->user->blogs;

            foreach ($tuBlogName as $no => $de) {
                if (!TuBlogs::where('blogName', $de->name)->exists()) {
                    $blogs = new TuBlogs();
                    $blogs->blogName = $de->name;
                    $blogs->blogTitle = $de->title;
                    $blogs->save();
                }

            }


        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return redirect('settings');
    }

    public function lang(Request $re)
    {
        $value = $re->value;
        try {
            Setting::where('field', 'lang')->update(['value' => $value]);
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * notifications settings
     * pusher notification
     */
    public function notifyIndex()
    {
        $appId = Data::get('notifyAppId');
        $appKey = Data::get('notifyAppKey');
        $appSec = Data::get('notifyAppSecret');
        return view('notifysettings', compact('appId', 'appKey', 'appSec'));
    }

    /**
     * @param Request $re
     * save notification settings
     * @return string
     */
    public function notifySave(Request $re)
    {
        try {
            DB::table('settings')->where('field', 'notifyAppId')->update(['value' => $re->appId]);
            DB::table('settings')->where('field', 'notifyAppKey')->update(['value' => $re->appKey]);
            DB::table('settings')->where('field', 'notifyAppSecret')->update(['value' => $re->appSec]);
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }

    public function skypeSave(Request $re)
    {
        try {
            DB::table('settings')->where('field', 'skypeUser')->update(['value' => $re->skypeUser]);
            DB::table('settings')->where('field', 'skypePass')->update(['value' => $re->skypePass]);
            return "success";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function configIndex(){
        $path = "* * * * * php ".app_path()."/artisan schedule:run >> /dev/null 2>&1";
        return view('config',compact('path'));
    }


}
