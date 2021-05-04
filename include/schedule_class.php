<?php

/**
 * Description of schedule_class
 *
 * @author siatex
 */
class schedule {

    var $scheduleData = array();
    var $currentTime = "";
    var $strTtime = array('hourly' => 3600, 'daily' => 86400, 'weekly' => 604800);

    function __construct() {
        add_option("scheduler", "");
        $this->currentTime = time();
        $dataString = get_option('scheduler');
        $this->scheduleData = unserialize($dataString);
    }

    public function init_schedule() {
        foreach ($this->scheduleData as $name => $info) {
            //var_dump($name,$info);
            if ($info[1] < $this->currentTime) {
                //$this->exc($name);
            }
        }
        //var_dump($this->scheduleData);
    }

    public function add_schedule($name = "", $delay = "daily", $ct = "") {
        if ($ct == "") {
            $ct = $this->currentTime;
        }
        if (!array_key_exists($name, $this->scheduleData)) {
            if (is_int($delay)) {
                $tt = $this->currentTime + $delay;
            } else if (!array_key_exists($delay, $this->strTtime)) {
                $delay = 'daily';
                $tt = $this->currentTime + $this->strTtime[$delay];
            }
            $this->scheduleData[$name] = array($this->currentTime, $tt);
            $this->saveScheduleData();
        }
    }

    public function exc($name) {
        if (array_key_exists($name, $this->scheduleData)) {
            $oldInfo = $this->scheduleData[$name];
            $this->scheduleData[$name] = array($oldInfo[1], ($oldInfo[1] + ($oldInfo[1] - $oldInfo[0])));
            $this->saveScheduleData();
            if (function_exists($name)) {
                $name();
//                $url = domain() . "admin/threading.php?ajx_action=$name";
//                
//                $curl = curl_init();
//                curl_setopt($curl, CURLOPT_URL, $url);
//                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//
//                $result = curl_exec($curl);
//                curl_close($curl);
//                var_dump($result);
            }
        }
    }

    public function runSchedule($name) {
        if (array_key_exists($name, $this->scheduleData)) {
            $oldInfo = $this->scheduleData[$name];
            if ($oldInfo[1] < $this->currentTime) {
                $name();
                $this->scheduleData[$name] = array($oldInfo[1], ($oldInfo[1] + ($oldInfo[1] - $oldInfo[0])));
                $this->saveScheduleData();
            }
        }
    }

    public function remove_schedule($name) {
        if (array_key_exists($name, $this->scheduleData)) {
            unset($this->scheduleData[$name]);
            $this->saveScheduleData();
        }
    }

    private function saveScheduleData() {
        update_option("scheduler", serialize($this->scheduleData));
    }

}

class ParallelCurl {

    public $max_requests;
    public $options;
    public $outstanding_requests;
    public $multi_handle;

    public function __construct($in_max_requests = 10, $in_options = array()) {
        $this->max_requests = $in_max_requests;
        $this->options = $in_options;

        $this->outstanding_requests = array();
        $this->multi_handle = curl_multi_init();
    }

    //Ensure all the requests finish nicely
    public function __destruct() {
        $this->finishAllRequests();
    }

    // Sets how many requests can be outstanding at once before we block and wait for one to
    // finish before starting the next one
    public function setMaxRequests($in_max_requests) {
        $this->max_requests = $in_max_requests;
    }

    // Sets the options to pass to curl, using the format of curl_setopt_array()
    public function setOptions($in_options) {

        $this->options = $in_options;
    }

    // Start a fetch from the $url address, calling the $callback function passing the optional
    // $user_data value. The callback should accept 3 arguments, the url, curl handle and user
    // data, eg on_request_done($url, $ch, $user_data);
    public function startRequest($url, $callback, $user_data = array(), $post_fields = null) {

        if ($this->max_requests > 0)
            $this->waitForOutstandingRequestsToDropBelow($this->max_requests);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt_array($ch, $this->options);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (isset($post_fields)) {
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }

        curl_multi_add_handle($this->multi_handle, $ch);

        $ch_array_key = (int) $ch;

        $this->outstanding_requests[$ch_array_key] = array(
            'url' => $url,
            'callback' => $callback,
            'user_data' => $user_data,
        );

        $this->checkForCompletedRequests();
    }

    // You *MUST* call this function at the end of your script. It waits for any running requests
    // to complete, and calls their callback functions
    public function finishAllRequests() {
        $this->waitForOutstandingRequestsToDropBelow(1);
    }

    // Checks to see if any of the outstanding requests have finished
    private function checkForCompletedRequests() {
        /*
          // Call select to see if anything is waiting for us
          if (curl_multi_select($this->multi_handle, 0.0) === -1)
          return;

          // Since something's waiting, give curl a chance to process it
          do {
          $mrc = curl_multi_exec($this->multi_handle, $active);
          } while ($mrc == CURLM_CALL_MULTI_PERFORM);
         */
        // fix for https://bugs.php.net/bug.php?id=63411
        do {
            $mrc = curl_multi_exec($this->multi_handle, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->multi_handle) != -1) {
                do {
                    $mrc = curl_multi_exec($this->multi_handle, $active);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            } else
                return;
        }

        // Now grab the information about the completed requests
        while ($info = curl_multi_info_read($this->multi_handle)) {

            $ch = $info['handle'];
            $ch_array_key = (int) $ch;

            if (!isset($this->outstanding_requests[$ch_array_key])) {
                die("Error - handle wasn't found in requests: '$ch' in " .
                        print_r($this->outstanding_requests, true));
            }

            $request = $this->outstanding_requests[$ch_array_key];

            $url = $request['url'];
            $content = curl_multi_getcontent($ch);
            $callback = $request['callback'];
            $user_data = $request['user_data'];

            call_user_func($callback, $content, $url, $ch, $user_data);

            unset($this->outstanding_requests[$ch_array_key]);

            curl_multi_remove_handle($this->multi_handle, $ch);
        }
    }

    // Blocks until there's less than the specified number of requests outstanding
    private function waitForOutstandingRequestsToDropBelow($max) {
        while (1) {
            $this->checkForCompletedRequests();
            if (count($this->outstanding_requests) < $max)
                break;

            usleep(10000);
        }
    }

}

//
//// A test script for the ParallelCurl class
//// 
//// This example fetches a 100 different results from Google's search API, with no more
//// than 10 outstanding at any time.
////
//// By Pete Warden <pete@petewarden.com>, freely reusable, see http://petewarden.typepad.com for more
//
//require_once('parallelcurl.php');
//
//define ('SEARCH_URL_PREFIX', 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&rsz=large&filter=0');
//
//// This function gets called back for each request that completes
//function on_request_done($content, $url, $ch, $search) {
//    
//    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);    
//    if ($httpcode !== 200) {
//        print "Fetch error $httpcode for '$url'\n";
//        return;
//    }
//
//    $responseobject = json_decode($content, true);
//    if (empty($responseobject['responseData']['results'])) {
//        print "No results found for '$search'\n";
//        return;
//    }
//
//    print "********\n";
//    print "$search:\n";
//    print "********\n";
//
//    $allresponseresults = $responseobject['responseData']['results'];
//    foreach ($allresponseresults as $responseresult) {
//        $title = $responseresult['title'];
//        print "$title\n";
//    }
//}
//
//// The terms to search for on Google
//$terms_list = array(
//    "John", "Mary",
//    "William", "Anna",
//    "James", "Emma",
//    "George", "Elizabeth",
//    "Charles", "Margaret",
//    "Frank", "Minnie",
//    "Joseph", "Ida",
//    "Henry", "Bertha",
//    "Robert", "Clara",
//    "Thomas", "Alice",
//    "Edward", "Annie",
//    "Harry", "Florence",
//    "Walter", "Bessie",
//    "Arthur", "Grace",
//    "Fred", "Ethel",
//    "Albert", "Sarah",
//    "Samuel", "Ella",
//    "Clarence", "Martha",
//    "Louis", "Nellie",
//    "David", "Mabel",
//    "Joe", "Laura",
//    "Charlie", "Carrie",
//    "Richard", "Cora",
//    "Ernest", "Helen",
//    "Roy", "Maude",
//    "Will", "Lillian",
//    "Andrew", "Gertrude",
//    "Jesse", "Rose",
//    "Oscar", "Edna",
//    "Willie", "Pearl",
//    "Daniel", "Edith",
//    "Benjamin", "Jennie",
//    "Carl", "Hattie",
//    "Sam", "Mattie",
//    "Alfred", "Eva",
//    "Earl", "Julia",
//    "Peter", "Myrtle",
//    "Elmer", "Louise",
//    "Frederick", "Lillie",
//    "Howard", "Jessie",
//    "Lewis", "Frances",
//    "Ralph", "Catherine",
//    "Herbert", "Lula",
//    "Paul", "Lena",
//    "Lee", "Marie",
//    "Tom", "Ada",
//    "Herman", "Josephine",
//    "Martin", "Fanny",
//    "Jacob", "Lucy",
//    "Michael", "Dora",
//);
//
//if (isset($argv[1])) {
//    $max_requests = $argv[1];
//} else {
//    $max_requests = 10;
//}
//
//$curl_options = array(
//    CURLOPT_SSL_VERIFYPEER => FALSE,
//    CURLOPT_SSL_VERIFYHOST => FALSE,
//    CURLOPT_USERAGENT, 'Parallel Curl test script',
//);
//
//$parallel_curl = new ParallelCurl($max_requests, $curl_options);
//
//foreach ($terms_list as $terms) {
//    $search = '"'.$terms.' is a"';
//    $search_url = SEARCH_URL_PREFIX.'&q='.urlencode($terms);
//    $parallel_curl->startRequest($search_url, 'on_request_done', $search);
//}
//
//// This should be called when you need to wait for the requests to finish.
//// This will automatically run on destruct of the ParallelCurl object, so the next line is optional.
//$parallel_curl->finishAllRequests();
//
//
