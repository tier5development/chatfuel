<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
/**
 * @const BASE_API_URL
 */
const BASE_API_URL = "http://rets-cache.homelasvegas.com/api/rets/v2/global_search?";
const UPLOADS_DIR = __DIR__."/uploads/";
try {
    processURL();
} catch (Exception $e) {
    // handling exception
    $error = array('text' =>  $e->getMessage());
    $parent = array();
    array_push($parent,$error);
    $obj  = new stdClass();
    $obj->messages = $parent;
    echo json_encode($obj);
}
/**
 * This function searches for the the query string present in the url
 * @param null
 * @return string
 * @throws exception if no search query given or invalid action specified
 */
function processURL() {
    $url			= "";
    if(isset($_GET['per_page'])) {
        $per_page = $_GET['per_page'];
    } else {
        $per_page = 7;
    }
    if (isset($_GET['listing_id'])) {
        $listing_id 	= $_GET['listing_id'];
        $url .= "listing_id=".$listing_id;
        request($url,1);
    }
    if (isset($_GET['city'])) {
        $city 		= $_GET['city'];
        $url .= "city=".$city;
        request($url,2,(isset($per_page)) ? $per_page : 7);
    }
    if (isset($_GET['postal_code'])) {
        $postal_code	= $_GET['postal_code'];
        $url .= "postal_code=".$postal_code;
        request($url,3,(isset($per_page)) ? $per_page : 7);
    }
    if (isset($_GET['address'])) {
        $address	= $_GET['address'];
        $url .= "address=".$address;
        request($url,4,(isset($per_page)) ? $per_page : 7);
    }

    if (!isset($listing_id) && !isset($city) && !isset($postal_code) && !isset($address) && !strlen($listing_id) && !strlen($city) && !strlen($postal_code) && !strlen($address)) {
        throw new Exception("Error Processing Request. No search query given", 1);
    }

}

/**
 * This function does the curl request to the realtor api
 * @param  string $url url where request is being made
 * @return array
 * @throws exception if no url has been passed
 */
function request($url = null,$choice = 1,$per_page = 8) {
    if(is_null($url)){
        throw new Exception("No URL has been passed to make a request", 1);
    }
    if (isset($url) && strlen($url)) {
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => BASE_API_URL.$url,
            CURLOPT_USERAGENT => 'Requesting search....'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        switch ($choice){
            case 1: listidSearch($resp);
                    break;
            default:listidSearch($resp);
                    break;
        }

        // Close request to clear up some resources
        curl_close($curl);
    } else {
        throw new Exception("No URL has been passed to make a request", 1);
    }
}

function listidSearch($resp = null) {
    if(is_null($resp)){
        throw new Exception("No response found.", 1);
    }

    if (count($resp)) {
        $elements = array();
        $resp = json_decode($resp);
        if(isset($resp->success) && $resp->success) {
            $elements_btn_array = listingIdSearchButtons($resp);
            $elem_objects       = listingIdSearchElements($resp,$elements_btn_array);
            array_push($elements, $elem_objects);
            // payload
            $payload = new stdClass();
            $payload->template_type = "generic";
            $payload->image_aspect_ratio = "square";
            $payload->elements = $elements;
            // configure gallery
            $attachment = new stdClass();
            $attachment->type = "template";
            $attachment->payload = $payload;
            $gallery_view  = new stdClass();
            $gallery_view->messages[] = ['attachment' => $attachment];
            print_r(json_encode($gallery_view));
        } else {
            $msg = array('text' =>  "No Search Results!");
            $parent = array();
            array_push($parent,$msg);
            $obj  = new stdClass();
            $obj->messages = $parent;
            $variables_obj = new stdClass();
            $variables_obj->demo  =404;
            $obj->set_attributes = $variables_obj;
            print_r(json_encode($obj));
        }
    } else {
        $msg = array('text' =>  "No Search Results!");
        $parent = array();
        array_push($parent,$msg);
        $obj  = new stdClass();
        $obj->messages = $parent;
        print_r(json_encode($obj));
    }
}

function listingIdSearchButtons($resp_arr) {
    $btn_obj_details	    = new stdClass();
    $btn_obj_details->type  ="web_url";
    $btn_obj_details->url   = "http://search.homelasvegas.com/idx/details/listing/b015/".$resp_arr->results->data[0]->MLSNumber;
    $btn_obj_details->title = "View Listing Details";

    $btn_obj_agent              = new stdClass();
    $btn_obj_agent->type        = "show_block";
    $btn_obj_agent->block_names = ["View Listing Agent"];

    $btn_obj_virtual_tour	    = new stdClass();
    $btn_obj_virtual_tour->type  ="web_url";
    $btn_obj_virtual_tour->url   = "https://www.propertypanorama.com/instaview/las/".$resp_arr->results->data[0]->MLSNumber;
    $btn_obj_virtual_tour->title = "Virtual Tour";

    $elements_btn_array[0] = $btn_obj_details;
    $elements_btn_array[1] = $btn_obj_agent;
    $elements_btn_array[2] = $btn_obj_virtual_tour;

    return $elements_btn_array;
}

function listingIdSearchElements($resp_arr,$elements_btn_array) {
    $elem_objects = new stdClass();
    $elem_objects->title = $resp_arr->results->data[0]->PublicAddress;
    $elem_objects->image_url =  convertImageUrl($resp_arr->results->data[0]->propertyimage[0]->Encoded_image);
    $elem_objects->subtitle = $resp_arr->results->data[0]->ListPrice.'\n'.$resp_arr->results->data[0]->propertyfeature->PropertyType;
    $elem_objects->buttons = $elements_btn_array;
    return $elem_objects;
}

function convertImageUrl($encodedImage){
    $filename_path = md5(time().uniqid()).".jpg";
    $decoded=base64_decode($encodedImage);
    if(!file_exists('uploads')) {
        mkdir('uploads',777);
        chmod('uploads',777);
    }
    file_put_contents(UPLOADS_DIR.$filename_path,$decoded);
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/uploads/".$filename_path;
    return $actual_link;
}



/*function listSearch($resp = null) {
    if(is_null($resp)){
        throw new Exception("No response found.", 1);
    }
    if (count($resp)) {
        $elements = array();
        $elements_btn_array = array();
        $messages = array();
        $attachment_arr = array();
        $resp_arr = json_decode($resp);
        $counter  = 0;
        if (isset($_GET['start'])) {
            $paginate_start = $_GET['start'];
        } else {
            $paginate_start = 0;
        }
        if (isset($_GET['end'])) {
            $paginate_end  = $_GET['end'];
        } else {
            $paginate_end  = 2;
        }
        if (gettype($resp_arr) === 'object') {
            $msg = array('text' =>  "No Search Results!");
            $parent = array();
            array_push($parent,$msg);
            $obj  = new stdClass();
            $obj->messages = $parent;
            $variables_obj = new stdClass();
            $variables_obj->demo  =404;
            $obj->set_attributes = $variables_obj;
            print_r(json_encode($obj));
        } else {
            if (count($resp_arr)) {
                //count total number of objects
                $counter = count($resp_arr);
                if (array_key_exists($paginate_start, $resp_arr) && array_key_exists($paginate_end, $resp_arr)) {
                    for ($i=$paginate_start; $i < $paginate_end ; $i++) {
                        $btn_obj	= new stdClass();
                        $btn_obj->type ="phone_number";
                        $btn_obj->url = $resp_arr[$i]->office_phone_number;
                        $btn_obj->title = "Call";
                        $elements_btn_array[0] = $btn_obj;
                        //array_push($elements_btn_array[0], $btn_obj);
                        // creating element object
                        $elem_objects = new stdClass();
                        $elem_objects->title = $resp_arr[$i]->full_name;
                        $elem_objects->image_url = "http://159.203.81.237/test/GLVAR_transparent-logo.jpg";
                        $elem_objects->subtitle = $resp_arr[$i]->office_name;
                        $elem_objects->buttons = $elements_btn_array;
                        array_push($elements, $elem_objects);
                        // payload
                        $payload = new stdClass();
                        $payload->template_type = "list";
                        $payload->top_element_style = "large";
                        $payload->elements = $elements;
                        // configure chart
                        $attachment = new stdClass();
                        $attachment->type = "template";
                        $attachment->payload = $payload;
                        $list_view  = new stdClass();
                        $list_view->messages[] = ['attachment' => $attachment];
                    }
                    // print_r($counter);
                    // if counter is more than 2 need to have a pagination
                    if ($counter > 2) {
                        // set user attribute here
                        $variables_obj = new stdClass();
                        $variables_obj1 = new stdClass();
                        $variables_obj1->demo  =200;
                        $variables_obj1->page_strt = $paginate_start+2;
                        $variables_obj1->page_end = $paginate_end+2;
                        $list_view->set_attributes = $variables_obj1;
                    } else {
                        $variables_obj = new stdClass();
                        $variables_obj->demo  =404;
                        $list_view->set_attributes = $variables_obj;
                    }
                    print_r(json_encode($list_view));
                } else {
                    $msg = array('text' =>  "No More Results!");
                    $parent = array();
                    array_push($parent,$msg);
                    $obj  = new stdClass();
                    $obj->messages = $parent;
                    $variables_obj = new stdClass();
                    $variables_obj->demo  =404;
                    $obj->set_attributes = $variables_obj;
                    print_r(json_encode($obj));
                }

            } else {
                $msg = array('text' =>  "No Search Results!");
                $parent = array();
                array_push($parent,$msg);
                $obj  = new stdClass();
                $obj->messages = $parent;
                $variables_obj = new stdClass();
                $variables_obj->demo  =404;
                $obj->set_attributes = $variables_obj;
                print_r(json_encode($obj));
            }

        }
    } else {
        $msg = array('text' =>  "No Search Results!");
        $parent = array();
        array_push($parent,$msg);
        $obj  = new stdClass();
        $obj->messages = $parent;
        print_r(json_encode($obj));
    }
}*/