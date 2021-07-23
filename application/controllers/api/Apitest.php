<?php
// use marketsepeti\Libraries\REST_Controller;
if (!defined('BASEPATH')) exit('No direct script access allowed');

// require APPPATH . '/libraries/REST_Controller.php';
// require(APPPATH.'libraries/REST_Controller.php');
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . '/libraries/CreatorJwt.php';
// require APPPATH . 'libraries/Format.php';

class Api extends REST_Controller
{
  var $postData = null;
  var $headerData = null;

  function __construct()
  {
    parent::__construct();
    $this->config->set_item('cache_path', APPPATH . '/cache/api/');
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'ms_'));
    setHeaders();
    $this->postData = $this->post();
    $this->headerData = getheader();
    $this->jwtObject = new CreatorJwt();

    if (checkToken($this->postData, $this->headerData)) {
      $this->isValid = true;
    } else {
      $this->isValid = false;
    }
    recache();
  }

  /**
   * Send OTP method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function sendotp_post()
  {

    $res = array();
    $email_exist = $this->email_exists($this->postData['email']);
    if (!$email_exist) {
      $rand_no = rand(10000, 99999);
      if ($this->email_model->sent_otp('user', $this->postData['email'], $rand_no, $this->postData['username'], 10)) {
        $res['status'] = 200;
        $res['OTP'] = $rand_no;
        $res['message'] = 'OTP sent to your entered email address.';
      } else {
        $res['status'] = 500;
        $res['message'] = 'Something went wrong!!!';
      }
    } else {
      $res['status'] = 404;
      $res['message'] = 'Email already exists';
    }

    echo json_encode($res);
    exit;
  }



  /**
   * register method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function register_post()
  {
    $phone = '';
    $email = '';
    if (is_numeric($this->postData['phone'])) {
      $phone = $this->postData['phone'];
      if ($this->chk_phone_exist($this->postData['phone'])) {
        $res['status'] = 202;
        $res['message'] = 'Phone Already Exist !!';
        echo json_encode($res);
        exit;
      } else {
        $phone = $this->postData['phone'];
      }
    } else {
      $email = $this->postData['phone'];
      if ($this->chk_mail_exist($this->postData['phone'])) {
        $res['status'] = 201;
        $res['message'] = 'Email Already Exist !!';
        echo json_encode($res);
        exit;
      } else {
        $email = $this->postData['phone'];
      }
    }

    $regData = [
      "email"     => $email,
      "phone"     => $phone,
      "username"  => $this->postData['username'],
      "surname"   => $this->postData['surname'],
      "address1"  => $this->postData['address1'],
      "city"  => $this->postData['city'],
      "zip"  => $this->postData['zip'],
      "creation_date" => time(),
      "device_token" => $this->postData['device_token'],
      "password"  => sha1($this->postData['password'])
    ];



    if ($this->db->insert('user', $regData)) {

      $insert_id = $this->db->insert_id();
      $addr["user_id"] = $insert_id;
      $addr['fname']      = $this->postData['username'];
      $addr['email']      = $email;
      $addr['address_1']  = $this->postData['address1'];
      $addr['phone']      = $phone;
      $addr['lname']      = $this->postData['surname'];
      $addr['zipcode']    = $this->postData['zip'];
      $addr['country']    = 'Germany';
      $addr['address_type'] = 'shipping';
      $this->db->insert('user_address', $addr);
      $addr['address_type'] = 'billing';
      $this->db->insert('user_address', $addr);

      $jwtToken = $this->jwtObject->GenerateToken([
        'user_id' => $insert_id
      ]);



      if (update('user', ['jwt' => $jwtToken], ['user_id' => $insert_id])) {

        $user_data = selectSingle('user', ['user_id' => $insert_id]);
        $all_add = selectAll('user_address', array('user_id' => $insert_id));
        //print_r($all_add); 
        foreach ($all_add as $addr) {
          if ($addr->address_type == 'billing') {
            $billing_add = [
              'address_id' => $addr->address_id,
              'fname' => $addr->fname,
              'email' => $addr->email,
              'address_1' => $addr->address_1,
              'city' => $addr->city,
              'phone' => $addr->phone,
              'lname' => $addr->lname,
              'zipcode' => $addr->zipcode
            ];
          } else {
            $shipping_add = [
              'address_id' => $addr->address_id,
              'fname' => $addr->fname,
              'email' => $addr->email,
              'address_1' => $addr->address_1,
              'city' => $addr->city,
              'phone' => $addr->phone,
              'lname' => $addr->lname,
              'zipcode' => $addr->zipcode
            ];
          }
        }

        if ($user_data) {
          $res['shipping_add'] = $shipping_add;
          $res['billing_add'] = $billing_add;
          $res['user_data'] = $user_data;
          $res['authorization'] = $jwtToken;
          $res['status'] = 200;
          $res['message'] = 'Data inserted successfully.';
        } else {

          $res['status'] = 500;
          $res['message'] = 'Something went wrong!!';
        }
      }
    } else {
      $res['status'] = 500;
      $res['message'] = 'Something went wrong!!';
    }
    echo json_encode($res);
    exit;
  }


  /**
   * Login method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function login_post()
  {
    $data_exist = $this->check_auth($this->postData['email'], sha1($this->postData['password']));

    if (is_numeric($this->postData['email'])) {
      $field = array('phone' => $this->postData['email'], 'password' => sha1($this->postData['password']));
    } else {
      $field = array('email' => $this->postData['email'], 'password' => sha1($this->postData['password']));
    }
    $user_data = selectSingle('user', $field);

    $jwtToken = $this->jwtObject->GenerateToken([
      'user_id' => $user_data->user_id
    ]);

    if ($data_exist > 0) {
      if (!empty($this->postData['device_token'])) {
        update('user', ['device_token' => $this->postData['device_token']], ['user_id' => $user_data->user_id]);
      }
      if (update('user', ['jwt' => $jwtToken], ['user_id' => $user_data->user_id])) {
      }

      $all_add = selectAll('user_address', array('user_id' => $user_data->user_id));
      //print_r($all_add); 
      foreach ($all_add as $addr) {
        if ($addr->address_type == 'billing') {
          $billing_add = [
            'address_id' => $addr->address_id,
            'fname' => $addr->fname,
            'email' => $addr->email,
            'address_1' => $addr->address_1,
            'city' => $addr->city,
            'phone' => $addr->phone,
            'lname' => $addr->lname,
            'zipcode' => $addr->zipcode
          ];
        } else {
          $shipping_add = [
            'address_id' => $addr->address_id,
            'fname' => $addr->fname,
            'email' => $addr->email,
            'address_1' => $addr->address_1,
            'city' => $addr->city,
            'phone' => $addr->phone,
            'lname' => $addr->lname,
            'zipcode' => $addr->zipcode
          ];
        }
      }
      echo json_encode(['status' => 200, 'data' => $user_data, 'billing_add' => $billing_add, 'shipping_add' => $shipping_add, 'message' => 'Login Successful.', 'authorization' => $jwtToken]);
      exit;
    } else {
      echo json_encode(['status' => 500, 'data' => "", 'message' => 'Invalid Username Password.']);
      exit;
    }
  }



  /**
   * Forgot Password method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function forgot_pass_post()
  {
    $res = array();
    $email_exist = $this->email_exists($this->postData['email']);
    if (!$email_exist) {
      $res['status'] = 500;
      $res['message'] = 'Invalid Email.';
      //$this->response(['status' => 500,'data'=>"",'message' => 'Invalid Email'], REST_Controller::HTTP_OK);
    } else {
      $rand_no = rand(10000, 99999);
      if ($this->email_model->sent_otp('user', $email_exist->email, $rand_no, $email_exist->username, 11)) {
        $res['status'] = 200;
        $res['user_id'] = $email_exist->user_id;
        $res['OTP'] = $rand_no;
        $res['message'] = 'OTP sent to your entered email address.';
      } else {
        $res['status'] = 500;
        $res['message'] = 'Something went wrong!!!';
      }
      //$this->response(['status' => 200,'message' => 'Invalid Username Password'], REST_Controller::HTTP_OK);
    }
    echo json_encode($res);
    exit;
  }


  /**
   * Forgot Password method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function reset_resend_otp_post()
  {
    $res = array();
    $user_data = selectSingle('user', ['user_id' => $this->postData['id']]);
    // print_r($user_data); exit;
    if ($user_data) {
      $rand_no = rand(10000, 99999);
      if ($this->email_model->sent_otp('user', $user_data->email, $rand_no, $user_data->username, 11)) {
        $res['status'] = 200;
        $res['user_id'] = $this->postData['id'];
        $res['OTP'] = $rand_no;
        $res['message'] = 'OTP sent to your entered email address.';
      } else {
        $res['status'] = 500;
        $res['message'] = 'Something went wrong!!!';
      }
      //$this->response(['status' => 200,'message' => 'Invalid Username Password'], REST_Controller::HTTP_OK);
    }
    echo json_encode($res);
    exit;
  }

  /**
   * Reset Password method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function reset_pass_post()
  {
    $res = array();
    $password  = sha1($this->postData['password']);
    if (update('user', ['password' =>  $password], ['user_id' => $this->postData['id']])) {
      $res['status'] = 200;
      $res['message'] = 'Password Reset Successfully..';
      //$this->response(['status' => 500,'data'=>"",'message' => 'Invalid Email'], REST_Controller::HTTP_OK);
    } else {
      $res['status'] = 500;
      $res['message'] = 'Something went wrong!!!';
      //$this->response(['status' => 200,'message' => 'Invalid Username Password'], REST_Controller::HTTP_OK);
    }
    echo json_encode($res);
    exit;
  }


  /**
   * Get Slides method
   * @description this function use to register user
   * @param string form data
   * @return json array
   *  !     optimized
   *  TODO  Sub method
   */
  public function get_slide_list()
  {
    $res = array();
    $this->db->select('slides_id'); // ! Added by sk
    $this->db->order_by('slides_id', 'desc');
    $this->db->where('uploaded_by', 'admin');
    $this->db->where('status', 'ok');
    $this->db->limit(10);
    $slide_data = $this->db->get('slides')->result_array();
	//print_r($slide_data); exit;
    if ($slide_data > 0) {
      foreach ($slide_data as $slides) {
        $all_slide[] = ['image' => resize_images($this->crud_model->file_view('slides', $slides['slides_id'], '100', '', 'no', 'src', '', '', '.jpg'), 650, 250, false)];
      }
      return $all_slide;
    } else {
      return $res;
    }
  }


  /**
   * Get Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   *  ! optimized 
   *  ! Cache 
   */
  function get_category_get()
  {
    $this->db->select('category_id,category_name,banner'); // ! Added by sk
    $this->db->order_by('category_id', 'desc');
    $this->db->where('digital=', NULL);
    $this->db->limit(4);
    $all_categories = $this->db->get('category')->result_array();

    if ($all_categories > 0) {
      foreach ($all_categories as $category) {
        $all_category[] = [
          'category_id' => $category['category_id'],
          'category_name' => $category['category_name'],
          'category_image' => resize_images(base_url() . 'uploads/category_image/' . $category['banner'], 500, 500, false)
        ];
      }
      if (!empty($all_category)) {
        $data = json_encode(['status' => 200, 'cat_data' => $all_category]);
      } else {
        $data = json_encode(['status' => 404, 'cat_data' => []]);
      }
      echo $data;
      exit;
    } else {
      $data = json_encode(['status' => 404, 'cat_data' => []]);
      exit;
    }
  }


  /**
   * Get Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! optimized
   * TODO  Sub method
   */
  function get_category($lang)
  {
    $res = array();
    $this->db->select('category_id,category_name,banner'); // ! Added by sk
    $this->db->order_by('category_id', 'desc');
    $this->db->where('digital=', NULL);
    $this->db->limit(8);
    $all_categories = $this->db->get('category')->result_array();
    if ($all_categories > 0) {
      foreach ($all_categories as $category) {
        if(is_file(FCPATH . 'uploads/category_image/' . $category['banner'])){         
          $prod_image = resize_images(base_url() . 'uploads/category_image/' . $category['banner'], 100, 100, 400);
        }
        $mCatData = $this->CategorylangCheck($category['category_id'], $lang);
        $all_category[] = ['category_id' => $category['category_id'], 'category_name' => $mCatData['title'], 'category_image' => $prod_image];
      }
      //exit;
      return $all_category;
    } else {
      return $res;
    }
  }


  /**
   * Get Cheapest Product
   * @description this function use to register user
   * @param string form data
   * @return json array
   * TODO  Sub method
   */
  function get_cheaper_product($pin_code, $user_id, $lang)
  {

    $res = array();
   
    $cheaper_product = $this->db->query("select product_id, title, category, sub_category, current_stock, specification, unit, selling_count, sale_price, bottle_deposit, title_german,title_turkey,perorderunit from product where areapin LIKE '%$pin_code%' AND is_deleted = 0 AND status='ok' AND (featured='ok' OR vendor_featured = 'ok') ORDER BY updated_date DESC limit 9 ")->result();
    if (!empty($user_id)) {
      if ($this->crud_model->get_type_name_by_id('user', $user_id, 'wishlist') !== 'null') {
        $wished = json_decode($this->crud_model->get_type_name_by_id('user', $user_id, 'wishlist'));
      } else {
        $wished = array();
      }
    } else {
      $wished = array();
    }


    if ($cheaper_product > 0) {

      foreach ($cheaper_product as $product) {
        if ($product->current_stock == null) {
          $product_stock = 0;
        } else {
          $product_stock = $product->current_stock;
        }

        if (in_array($product->product_id, $wished)) {
          $is_fav = True;
        } else {
          $is_fav = False;
        }
        $prod_image = resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, false);

        $prd_data = $this->langCheck($product->product_id, $lang);
        $all_product[] = ['title' => $prd_data['title'], 'sale_price' => $this->crud_model->get_product_price($product->product_id), 'product_id' => $product->product_id, 'category_id' => $product->category, 'sub_category_id' => $product->sub_category, 'prod_image' => $prod_image, 'current_stock' => $product_stock, 'specification' => $product->specification, 'unit' => $product->unit,'tax'=>$this->crud_model->get_product_tax($product->product_id),'bottle_deposit'=>$product->bottle_deposit,'eng_title'=>$product->title,'german_title'=>$product->title_german,'turkish_title'=>$product->title_turkey,'is_fav' => $is_fav,'product_unit_per_order'=>$product->perorderunit];
      }

      return $all_product;
    } else {
      return $res;
    }
  }


  /**
   * Get Cheapest Product
   * @description this function use to register user
   * @param string form data
   * @return json array
   * TODO  Sub method
   */
  function get_best_selling_product($pin_code, $user_id, $limit, $start, $lang)
  {

    $res = array();
    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
    $this->db->where('status', 'ok');
    $best_selling_product = selectAll('product', array('areapin LIKE' => '%' . $pin_code . '%'), array('product_id', 'title', 'category', 'sub_category', 'current_stock', 'specification', 'unit', 'selling_count','bottle_deposit','title_german','title_turkey','perorderunit'), $limit, $start, 'selling_count', 'desc');

    if (!empty($user_id)) {
      if ($this->crud_model->get_type_name_by_id('user', $user_id, 'wishlist') !== 'null') {
        $wished = json_decode($this->crud_model->get_type_name_by_id('user', $user_id, 'wishlist'));
      } else {
        $wished = array();
      }
    } else {
      $wished = array();
    }


    if ($best_selling_product > 0) {


      foreach ($best_selling_product as $product) {

        if ($product->current_stock == null) {
          $product_stock = 0;
        } else {
          $product_stock = $product->current_stock;
        }

        if (in_array($product->product_id, $wished)) {
          $is_fav = True;
        } else {
          $is_fav = False;
        }
        $prod_image = resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, false);
        $prd_data = $this->langCheck($product->product_id, $lang);

        $all_product[] = ['title' => $prd_data['title'], 'sale_price' => $this->crud_model->get_product_price($product->product_id), 'product_id' => $product->product_id, 'category_id' => $product->category, 'sub_category_id' => $product->sub_category, 'prod_image' => $prod_image, 'current_stock' => $product_stock, 'specification' => $product->specification, 'unit' => $product->unit, 'tax'=>$this->crud_model->get_product_tax($product->product_id),'bottle_deposit'=>$product->bottle_deposit,'eng_title'=>$product->title,'german_title'=>$product->title_german,'turkish_title'=>$product->title_turkey, 'is_fav' => $is_fav,'product_unit_per_order'=>$product->perorderunit];
      }

      return $all_product;
    } else {
      return $res;
    }
  }


  /**
   * Get Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function get_category_list_get()
  {
    $res = array();
    $this->db->order_by('category_sequence', 'desc');
    $this->db->where('digital=', NULL);
    $all_categories = $this->db->get('category')->result_array();

    if ($all_categories > 0) {
      foreach ($all_categories as $category) {
        $all_category[] = ['category_id' => $category['category_id'], 'category_name' => $category['category_name'], 'category_image' => resize_images(base_url() . 'uploads/category_image/' . $category['banner'], 500, 500, 500)];
      }
      $res['status'] = 200;
      $res['slide_data'] = $all_category;
    } else {
      $res['status'] = 500;
      $res['message'] = 'Something went wrong!!!';
    }
    echo json_encode($res);
    exit;
  }




  /**
   * Get Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * 0.0062<br/>0.0107<br/>0.0576<br/>0.0318<br/>
   * 0.0038<br/>0.0094<br/>0.0543<br/>0.0436<br/>
   * ! optimized & chaced
   */
  function get_home_content_post()
  {
    //clean_custom_cache();

    $pin_code = $this->postData['areaPincode'];
    $product_present = $this->db->query("select product_id from product where areapin LIKE '%$pin_code%' AND is_deleted = 0 AND status='ok'")->result();
    if(empty($product_present)){
          echo json_encode(['status' => 300,'message' => 'Pincode Not Present']);
          exit;
    }
	
    $cache_key = "get_home_content_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id'];
    $data = $this->cache->get($cache_key);

    if (empty($data)) {
      $slide_data = $this->get_slide_list();
	  
      $category_data = $this->get_category($this->postData['lang_id']);
	  
      $cheaper_prod_data = $this->get_cheaper_product($this->postData['areaPincode'], $this->postData['user_id'], $this->postData['lang_id']);
      $best_selling_product = $this->get_best_selling_product($this->postData['areaPincode'], $this->postData['user_id'], $this->postData['limit'], $this->postData['start'], $this->postData['lang_id']);

     $data = json_encode(['status' => 200, 'bannerList' => $slide_data, 'categoriesList' => $category_data, 'cheaperProductList' => $cheaper_prod_data, 'mostSellingProductList' => $best_selling_product]);
    
	
     $this->cache->save($cache_key, $data, 30000);
      echo $data;
      exit;
    } else {
      echo $data;
      exit;
    }
  }


   function pincode_exist_post()
  {
    //clean_custom_cache();
    if ($this->postData) {
      $pin_code = $this->postData['areaPincode'];
      $product_present = $this->db->query("select product_id from product where areapin LIKE '%$pin_code%' AND is_deleted = 0 AND status='ok'")->result();
      if(empty($product_present)){
            echo json_encode(['status' => 500,'message' => 'Pincode Not Present']);
            exit;
      }else{
         echo json_encode(['status' => 200,'message' => 'Pincode Present']);
            exit;
      }
    }else{
      echo json_encode(['status' => 500,'message' => 'Something Went Wrong']);
            exit;
    }

  }


  /**
   * Get ALL Best Selling Product List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function all_best_selling_product_post()
  {
    //clean_custom_cache();
    $res = array();
	$cache_key = "all_best_selling_product_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id'];
   $data = $this->cache->get($cache_key);

    if (empty($data)) {
    if (!empty($this->postData['areaPincode'])) {
      $best_selling_product = $this->get_best_selling_product($this->postData['areaPincode'], $this->postData['user_id'], $this->postData['limit'], $this->postData['start'], $this->postData['lang_id']);
      echo json_encode(['status' => 200, 'mostSellingProductList' => $best_selling_product]);
    } else {
      echo json_encode(['status' => 500, 'mostSellingProductList' => $res]);
    }
	}else{
		echo $data;
	}
    exit;
  }


  /**
   * Page Data
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function get_content_data_post()
  {
    $res = array();
    $page_data = selectSingle('general_settings', ['general_settings_id' => $this->postData['id']]);

    if ($page_data > 0) {
      $title = translate($page_data->type);
      $res['status'] = 200;
      $res['title'] = $title;
      $res['description'] = $page_data->value;
    } else {
      $res['status'] = 500;
      $res['title'] = '';
      $res['description'] = '';
    }
    echo json_encode($res);
    exit;
  }


  function dashboard_post()
  {

    if ($this->isValid) {
      // logic 
      $this->response(['status' => 200, 'data' => ['user_id' => $this->postData['user_id']], 'message' => 'Successfully...'], REST_Controller::HTTP_OK);
    } else {
      $this->response(['status' => 500, 'data' => "", 'message' => 'Token miss Match.'], REST_Controller::HTTP_OK);
    }
  }


  function email_exists($email)
  {
    $this->db->where('email', $email);
    $query = $this->db->get('user');
    if ($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }


  function check_auth($email_phone, $pass)
  {

    if (is_numeric($email_phone)) {
      $field = array('phone' => $email_phone, 'password' => $pass);
    } else {
      $field = array('email' => $email_phone, 'password' => $pass);
    }


    $this->db->where($field);
    $query = $this->db->get('user');

    if ($query->num_rows() > 0) {
      return $query->row();
    } else {
      return false;
    }
  }


  function chk_phone_exist($phone)
  {
    $field = array('phone' => $phone);
    $this->db->where($field);
    $query = $this->db->get('user');
    if ($query->num_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }


  function chk_mail_exist($email)
  {
    $field = array('email' => $email);
    $this->db->where($field);
    $query = $this->db->get('user');
    if ($query->num_rows() > 0) {
      return true;
    } else {
      return false;
    }
  }


  /**
   * Get All Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function get_all_category_list_post()
  {
    
    //clean_custom_cache();
$cache_key = "get_all_category_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id'];
   $return = $this->cache->get($cache_key);

    if (empty($return)) {
    $res = array();
    $this->db->order_by('category_sequence', 'asc');
   // $this->db->order_by('category_id', 'desc');
    $this->db->where('digital', NULL);
    $all_categories = $this->db->get('category')->result_array();
    if ($all_categories > 0) {
      $i = 0;
      foreach ($all_categories as $row) {
        if ($this->postData['lang_id'] == 2) {
          $main_cat = $row['category_name_german'];
        } elseif ($this->postData['lang_id'] == 3) {
          $main_cat = $row['category_name_turkey'];
        } else {
          $main_cat = $row['category_name'];
        }

        if ($this->crud_model->if_publishable_category($row['category_id'])) {
          $cat_image = base_url() . 'uploads/category_image/' . $row['banner'];
          $all_cat[$i]['id'] = $row['category_id'];
          $all_cat[$i]['category_name'] = $main_cat;
          $all_cat[$i]['banner'] = resize_images($cat_image, 200, 00, false);
          $sub_categories = json_decode($row['data_subdets'], true);
          if (empty($sub_categories)) {
            $all_cat[$i]['SubCategory'] = [];
          } else {
            $k = 0;
            foreach ($sub_categories as $row1) {
              if ($this->postData['lang_id'] == 2) {
                $sub_cat = $row1['sub_name_german'];
              } elseif ($this->postData['lang_id'] == 3) {
                $sub_cat = $row1['sub_name_turkey'];
              } else {
                $sub_cat = $row1['sub_name'];
              }
              $all_cat[$i]['SubCategory'][$k]['id'] = $row1['sub_id'];
              $all_cat[$i]['SubCategory'][$k]['sub_name'] = $sub_cat;
              $k++;
            }
          }
          //print_r($sub_categories);

        }
        $i++;
      }
      
    $return = json_encode(['status' => 200, 'catList' => $all_cat]);
       $this->cache->save($cache_key, $return, 30000);

      echo $return;
    } else {
      echo json_encode(['status' => 500, 'catList' => $res]);
    }
  }else{
     echo $return;
  }
  
    exit;
  }



  /**
   * Get All Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function get_product_by_category_post()
  {
    $res = array();
    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
     $this->db->where('status', 'ok');
    $cat_product = selectAll('product', array('category' => $this->postData['catId'], 'sub_category' => $this->postData['subId'], 'areapin LIKE' => '%' . $this->postData['areaPincode'] . '%'), array('product_id', 'title', 'description', 'sale_price', 'current_stock', 'specification', 'unit', 'bottle_deposit','title_german','title_turkey','perorderunit'), $this->postData['limit'], $this->postData['start'], null, null);

    if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
      $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
    } else {
      $wished = array();
    }

    foreach ($cat_product as $product) {
      if ($product->current_stock == null) {
        $product_stock = 0;
      } else {
        $product_stock = $product->current_stock;
      }
      if (in_array($product->product_id, $wished)) {
        $is_fav = True;
      } else {
        $is_fav = False;
      }
      $prd_data = $this->langCheck($product->product_id, $this->postData['lang_id']);
      $all_prod[] = [
        'product_id' => $product->product_id,
        'title' => $prd_data['title'],
        'description' => $prd_data['description'],
        'sale_price' => $this->crud_model->get_product_price($product->product_id),
        'prod_stock' => $product_stock,
        'product_image' => resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, false),
        'specification' => $product->specification,
        'unit' => $product->unit,
         'tax'=>$this->crud_model->get_product_tax($product->product_id),
         'bottle_deposit'=>$product->bottle_deposit,
         'eng_title'=>$product->title,
         'german_title'=>$product->title_german,
         'turkish_title'=>$product->title_turkey,
        'is_fav' => $is_fav,
        'product_unit_per_order'=>$product->perorderunit

      ];
    }

    if ($cat_product > 0) {

      echo json_encode(['status' => 200, 'productByCat' => $all_prod]);
    } else {
      echo json_encode(['status' => 500, 'productByCat' => $res]);
    }
    exit;
  }

  /**
   * Get Cheapest Product
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function get_all_cheaper_product_list_post()
  {
    //clean_custom_cache();
    $res = array();
    $pin_code = $this->postData['areaPincode'];
    $limit = $this->postData['limit'];
    $start = $this->postData['start'];
	 $cache_key = "get_all_cheaper_product_list_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id'];
   $data = $this->cache->get($cache_key);

    if (empty($data)) {
	
    $cheaper_product = $this->db->query("select product_id, title, category, sub_category, current_stock, specification, unit, selling_count,sale_price,bottle_deposit,title_german,title_turkey,perorderunit from product where areapin LIKE '%$pin_code%' AND is_deleted = 0 AND status='ok' AND (featured='ok' OR vendor_featured = 'ok') ORDER BY updated_date DESC limit  $start , $limit ")->result();

    if (!empty($this->postData['user_id'])) {
      if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
        $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
      } else {
        $wished = array();
      }
    } else {
      $wished = array();
    }


    if (!empty($cheaper_product)) {
      foreach ($cheaper_product as $product) {
        if ($product->current_stock == null) {
          $product_stock = 0;
        } else {
          $product_stock = $product->current_stock;
        }
        if (in_array($product->product_id, $wished)) {
          $is_fav = True;
        } else {
          $is_fav = False;
        }
        $prod_image = resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
        $prd_data = $this->langCheck($product->product_id, $this->postData['lang_id']);

        $all_product[] = ['title' => $prd_data['title'], 'sale_price' => $this->crud_model->get_product_price($product->product_id), 'product_id' => $product->product_id, 'category_id' => $product->category, 'sub_category_id' => $product->sub_category, 'prod_image' => $prod_image, 'current_stock' => $product_stock, 'specification' => $product->specification, 'unit' => $product->unit,'tax'=>$this->crud_model->get_product_tax($product->product_id),'bottle_deposit'=>$product->bottle_deposit,'eng_title'=>$product->title,'german_title'=>$product->title_german,'turkish_title'=>$product->title_turkey,'is_fav' => $is_fav,'product_unit_per_order'=>$product->perorderunit];
      }
		
	$data = json_encode(['status' => 200, 'cheaperProduct' => $all_product]);
     $this->cache->save($cache_key, $data, 30000);
      echo $data;
    } else {
      echo json_encode(['status' => 500, 'cheaperProduct' => $res]);
    }
	}else{
		echo $data;
	}
	
    exit;
  }


  /**
   * Get All Category List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! optimized
   */
  function get_product_details_by_id_post()
  {
    $res = array();
	
	// $cache_key = "get_Product_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id']."_".$this->postData['id'];
 //   $data = $this->cache->get($cache_key);
	// if (empty($data)) {
	
    $this->db->select('
      product_id,
      current_stock,
      category,
      sub_category,
      specification,
      unit,
      bottle_deposit,
      title_german,
      title_turkey,
      perorderunit
    ');
    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
    $product_data = $this->db->get_where('product', array('product_id' => $this->postData['id'], 'status' => 'ok'))->result_array();

    $all_review = selectAll('product_reviews', ['product_id' => $this->postData['id']], ['COUNT(ratings) as total_count'], null, null);
    $review_sum = selectAll('product_reviews', ['product_id' => $this->postData['id']], ['SUM(ratings) as total_rate'], null, null);


    if ($all_review[0]->total_count > 0) {
      $rates = $review_sum[0]->total_rate / $all_review[0]->total_count;
      if (!empty($rates)) {
        for ($l = 0; $l < 5; $l++) {
          if ($rates > 0)
            $reviewRes['starRating'][$l] = 'starActiveColor';
          else
            $reviewRes['starRating'][$l] = 'starInactiveColor';
          $rates--;
        }
      } else {
        for ($l = 0; $l < 5; $l++) {
          $reviewRes['starRating'][$l] = 'starInactiveColor';
        }
      }
    } else {
      for ($l = 0; $l < 5; $l++) {
        $reviewRes['starRating'][$l] = 'starInactiveColor';
      }
    }

    if ($this->postData) {
      if (!empty($this->postData['user_id'])) {
        if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
          $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
        } else {
          $wished = array();
        }
      } else {
        $wished = array();
      }

      if ($product_data[0]['current_stock'] == null) {
        $product_stock = 0;
      } else {
        $product_stock = $product_data[0]['current_stock'];
      }
      $prodimage = $this->crud_model->file_view('product', $product_data[0]['product_id'], '', '', 'thumb', 'src', 'multi', 'all');
	  foreach($prodimage as $img){
		  $prod_image[] = resize_images($img, 400, 400, false);
	  }
      if (in_array($product_data[0]['product_id'], $wished)) {
        $is_fav = True;
      } else {
        $is_fav = False;
      }

      $prd_data = $this->langCheck($product_data[0]['product_id'], $this->postData['lang_id']);

      $all_product = [
        'title' => $prd_data['title'],
        'sale_price' => $this->crud_model->get_product_price($product_data[0]['product_id']),
        'product_id' => $product_data[0]['product_id'],
        'category_id' => $product_data[0]['category'],
        'sub_category_id' => $product_data[0]['sub_category'],
        'prod_image' => $prod_image,
        'current_stock' => $product_stock,
        'description' => $prd_data['description'],
        'specification' => $product_data[0]['specification'],
        'unit' => $product_data[0]['unit'],
        'tax'=>$this->crud_model->get_product_tax($product_data[0]['product_id']),
        'bottle_deposit'=>$product_data[0]['bottle_deposit'],
        'eng_title'=>$product_data[0]['title'],
        'german_title'=>$product_data[0]['title_german'],
        'turkish_title'=>$product_data[0]['title_turkey'],
        'is_fav' => $is_fav,
        'product_unit_per_order'=>$product_data[0]['perorderunit']
      ];
	 $data =  json_encode(['status' => 200, 'prodDetails' => $all_product, 'tot_review' => $all_review[0]->total_count, 'total_review' => $reviewRes]);
	 //$this->cache->save($cache_key, $data, 30000);
      echo $data;
    } else {
      echo json_encode(['status' => 500, 'catList' => $res]);
    }
	
	// }else{
	//  echo $data;
	// }
    exit;
  }
 

  /**
   * Get Cart Product List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function my_cart_post()
  {
    $res = array();

    if ($this->postData) {
      $i = 0;
      $total = 0;
      $tot_kg = 0;
      $tot_gram = 0;
      $tot_pice = 0;
      $tot_ml = 0;
      $tot_liter = 0;
      $areapin = '%' . $this->postData[0]['areaPincode'] . '%';
      $lang_id = $this->postData[0]['lang_id'];
      $cart_data = [];
      $sub_cat_arr = [];
      $bottle_deposite = []; 
      $item_total = [];
      $related_data = [];
      $prod_id_arr = [];
      $cartFromDb = [];
      $emptyCart = [];
      $tot_with_tax = 0;
      //print_r($this->postData); exit;
      foreach ($this->postData as $product) {
        $this->db->where('is_deleted', 0); 
        $product_data[] = $this->db->get_where('product', array('product_id' => $product['product_id'], 'status' => 'ok'))->result_array();

        if(!empty($product_data[$i])){
            $cartFromDb[] = ['eng_title' =>$product['eng_title'],'german_title' =>$product['german_title'],'turkish_title' =>$product['turkish_title'],'tax' =>$product['tax'],'deposite' =>$product['deposite'],'product_img' =>$product['product_img'],'unit' =>$product['unit'],'specification' => $product['specification'],'product_stock' => $product['product_stock'],'product_id' => $product['product_id'],'product_title' => $product['product_title'],'product_qty' => $product['product_qty'],'product_amt' => $product['product_amt'],'areaPincode' => $product['areaPincode']
            ];

              if ($product_data[$i][0]['tax_type'] == 'percent') {
                $tax[] = $this->crud_model->get_product_tax($product_data[$i][0]['product_id']) * $this->postData[$i]['product_qty'];
              } else if ($product_data[$i][0]['tax_type'] == 'amount') {
                $tax[] = $product_data[$i][0]['tax'] * $this->postData[$i]['product_qty'];
              }

              if ($product_data[$i][0]['unit'] == 'kg' and !empty($product_data[$i][0]['specification'])) {
                $tot_kg_spec = $product_data[$i][0]['specification'] * $product['product_qty'];
                $tot_kg = $tot_kg_spec + $tot_kg;
              } else if ($product_data[$i][0]['unit'] == 'ml' and !empty($product_data[$i][0]['specification'])) {
                $tot_ml_spec = $product_data[$i][0]['specification'] * $product['product_qty'];
                $tot_ml = $tot_ml_spec + $tot_ml;
              } else if ($product_data[$i][0]['unit'] == 'l' and !empty($product_data[$i][0]['specification'])) {
                $tot_liter_spec = $product_data[$i][0]['specification'] * $product['product_qty'];
                $tot_liter = $tot_liter_spec + $tot_liter;
              } else if ($product_data[$i][0]['unit'] == 'g' and !empty($product_data[$i][0]['specification'])) {
                $tot_gram_spec = $product_data[$i][0]['specification'] * $product['product_qty'];
                $tot_gram = $tot_gram_spec + $tot_gram;
              } else if ($product_data[$i][0]['unit'] == 'piece' and !empty($product_data[$i][0]['specification'])) {
                $tot_pice_spec = $product_data[$i][0]['specification'] * $product['product_qty'];
                $tot_pice = $tot_pice_spec + $tot_pice;
              }

              $sub_cat_arr[] = $product_data[$i][0]['sub_category'];
              $prod_id_arr[] = $product_data[$i][0]['product_id'];


              $item_total[] = $this->postData[$i]['product_qty'] * $this->crud_model->get_product_price($product_data[$i][0]['product_id']);

              if ($product_data[$i][0]['bottle_deposit'] > 0) {

                $bottle_deposite[] = $this->postData[$i]['product_qty'] * $product_data[$i][0]['bottle_deposit'];
              }

        }else{
            $emptyCart[] = ['product_id' => $product['product_id']];
        }
        // $prod_image = resize_images($this->crud_model->file_view('product', $product_data[$i][0]['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 100, 100, 500);
       
        //  $cart_data[] = ["product_id" => $product_data[$i][0]['product_id'], "product_qty" => $this->postData[$i]['product_qty'], "product_img" => $prod_image, "product_amt" => $this->crud_model->get_product_price($product_data[$i][0]['product_id']), "product_title" => $product_data[$i][0]['title'], "specification" => $product_data[$i][0]['specification'], "unit" => $product_data[$i][0]['unit']];

        $i++;
      }

        if(!empty($sub_cat_arr)){
            $uniq_cat = array_unique($sub_cat_arr);
            //echo implode(",",$uniq_cat);
           // $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
             //$this->db->where('status', 'ok');
            $query = $this->db->query('SELECT product_id,title,current_stock,specification,unit,bottle_deposit,title_german,title_turkey,perorderunit FROM product WHERE is_deleted = 0 AND status = "ok" AND `sub_category` IN (' . implode(",", $uniq_cat) . ') AND product_id NOT IN(' . implode(",", $prod_id_arr) . ') AND areapin LIKE "' . $areapin . '" LIMIT 0, 20')->result_array();
             
            foreach ($query as $related) {

              $related_prod_image = resize_images($this->crud_model->file_view('product', $related['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);

              if ($related['current_stock'] == null) {
                $product_stock = 0;
              } else {
                $product_stock = $related['current_stock'];
              }

               $prd_data = $this->langCheck($related['product_id'], $lang_id);


              $related_data[] = ["product_id" => $related['product_id'], "product_img" => $related_prod_image, "product_amt" => $this->crud_model->get_product_price($related['product_id']), "product_title" =>  $prd_data['title'], "stock" => $product_stock, "specification" => $related['specification'], "unit" => $related['unit'],"tax"=>$this->crud_model->get_product_tax($related['product_id']),"bottle_deposit" =>$related['bottle_deposit'],"eng_title"=>$related['title'],"german_title"=>$related['title_german'],"turkish_title"=>$related['title_turkey'],"product_unit_per_order"=>$related['perorderunit']];
            }
          }
         
      if (!empty($bottle_deposite)) {
        $btl_deposite = array_sum($bottle_deposite);
      } else {
        $btl_deposite = 0;
      }

      if(!empty($item_total)){
              //$tot_with_tax = array_sum($item_total) + array_sum($tax) + $btl_deposite;
              $tot_with_tax = array_sum($item_total) + $btl_deposite;

              if ($tot_with_tax > 50) {
                $shipping_cost = 0;
                $total_with_ship = array_sum($item_total) + $shipping_cost;
                $total = $tot_with_tax + $shipping_cost;
              } else {
                $shipping_cost = 5;
                $total_with_ship = array_sum($item_total) + $shipping_cost;
                $total = $tot_with_tax + $shipping_cost;
              }
        }


      $tot_kg_grm = 0;
      $tot_ltr_ml = 0;
      if ($tot_gram >= 1000) {
        $tot_kg_grm = ($tot_gram * 0.001) + $tot_kg;
      } else {
        $tot_kg_grm = ($tot_gram * 0.001) + $tot_kg;
      }
      if ($tot_ml >= 1000) {
        $tot_ltr_ml = ($tot_ml * 0.001) +  $tot_liter;
      } else {
        $tot_ltr_ml = ($tot_ml * 0.001) + $tot_liter;
      }
      // echo $tot_kg; 

      //  $tot_liter = $tot_ltr_ml + $tot_liter;
      $tot_liter = round($tot_ltr_ml, 2);
      //$tot_kg = $tot_kg_grm + $tot_kg;
      $tot_kg = round($tot_kg_grm, 2);

      echo json_encode(['status' => 200, 'cartData' => $cart_data, 'item_count' => count($prod_id_arr), 'prod_total' => round(array_sum($item_total), 2), 'total_with_ship' => round($total_with_ship, 2), 'shipping_cost' => $shipping_cost, 'total_tax' => array_sum($tax), 'deposit' => $btl_deposite, 'total' => round($total, 2), 'total_kg' => $tot_kg, 'total_liter' => $tot_liter, 'total_pices' => $tot_pice, 'related_data' => $related_data, 'cartFromDb' => $cartFromDb, 'emptyCart' => $emptyCart ]);
    } else {
      echo json_encode(['status' => 500, 'cartData' => $res]);
    }
    exit;
  }



  /**
   * Get Cart Product List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function update_prod_quantity_post()
  {
    $res = array();

    if ($this->postData) {
      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
      $product_data = $this->db->get_where('product', array('product_id' => $this->postData['product_id'], 'status' => 'ok'))->result_array();



      $getprunit = $this->db->get_where("product", array("product_id" => $this->postData['product_id']))->row()->perorderunit;

      if ($getprunit == 0) {
        $getprunit = 1000;
      }

      if ($this->postData['product_qty'] > $getprunit) {
        echo json_encode(['status' => 201, 'message' => 'Your Purchasing limit is over for this product']);
        exit;
      }

      if ($product_data[0]['current_stock'] >= $this->postData['product_qty'] and $this->postData['product_qty'] <= $getprunit) {
        echo json_encode(['status' => 200, 'message' => 'Stock Present']);
      } else {
        echo json_encode(['status' => 500, 'message' => 'Sorry!!! No Stock']);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something went wrong!!!']);
    }
    exit;
  }



  /**
   * Checkout 
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function checkout_post()
  {

    // error_reporting(-1);
    // ini_set('display_errors', 'On');

    $res = array();

    if ($this->postData) {

      $related_prod = $this->crud_model->product_list_set('related', 6, $this->postData['checkout'][0]['product_id']);
      $user_details = $this->db->get_where('user', array('user_id' => $this->postData['user_id']))->result_array();
      

      foreach ($related_prod as $rec) {

        $prod_image = resize_images($this->crud_model->file_view('product', $rec['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
        
        $prd_data = $this->langCheck($rec['product_id'], $this->postData['lang_id']);
        
        $related[] = [
          'product_id' => $rec['product_id'],
          'title' => $prd_data['title'],
          'description' => $prd_data['description'],
          'sale_price' => $this->crud_model->get_product_price($rec['product_id']),
          'prod_image' => $prod_image
        ];
      }

      echo json_encode(['status' => 200, 'relatedProduct' => $related, 'userDetail' => $user_details, 'message' => 'CheckOut Data Present']);
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something went wrong!!!', 'relatedProduct' => $res, 'userDetail' => $res,]);
    }
    exit;
  }


  /**
   * Go to Checkout 
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function goto_checkout_post()
  {
    $res = array();
    if ($this->isValid) {
      if ($this->postData) {

         if($this->postData['total'] < 25){
             echo json_encode(['status' => 500, 'message' => 'Amount must be greater than 25']);
             exit;
          }
          
        $user_shipping_add = selectSingle('user_address', ['address_id' => $this->postData['shipping_add_id']], 'fname,lname,address_1,address_2,zipcode,email,phone');

        $shipping_add = [
          'firstname' => $user_shipping_add->fname,
          'lastname' => $user_shipping_add->lname,
          'address1' => $user_shipping_add->address_1,
          'address2' => $user_shipping_add->address_2,
          'zip' => $user_shipping_add->zipcode,
          'email' => $user_shipping_add->email,
          'phone' => $user_shipping_add->phone,
        ];

        foreach ($this->postData['checkout'] as $product) {

          $rowid = md5($product['product_id']);
          $subtotal = $product['product_qty'] * $this->crud_model->get_product_price($product['product_id']);
          $carted[$rowid] = array(
            'id' => $product['product_id'],
            'qty' => $product['product_qty'],
            'option' => '',
            'price' => $this->crud_model->get_product_price($product['product_id']),
            'name' => $this->crud_model->get_type_name_by_id('product', $product['product_id'], 'title'),
            'shipping' => $this->crud_model->get_shipping_cost($product['product_id']),
            'tax' => $this->crud_model->get_product_tax($product['product_id']),
            'image' => resize_images($this->crud_model->file_view('product', $product['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, false),
            'coupon' => '',
            'subtotal' => $subtotal
          );
          $this->add_product_selling_count($product['product_id']);
        }
        $exchange = exchange('usd');
        $vat_per  = '';
        $vat      = $this->crud_model->cart_total_it('tax');

        // if ($this->crud_model->get_type_name_by_id('business_settings', '3', 'value') == 'product_wise') {
        //     $shipping = $this->crud_model->cart_total_it('shipping');
        // } else {
        //     $shipping = $this->crud_model->get_type_name_by_id('business_settings', '2', 'value');
        // }

        $shipping     = $this->postData['shipping_cost'];
        $grand_total     = $this->postData['total'];
        $product_details = json_encode($carted);

        //echo json_encode($this->postData['shipping_details']); exit;
        if ($this->postData['payment_type'] == 1) {
          $payment_type = 'cash_on_delivery';
        } else {
          $payment_type = 'pay_by_card_door';
        }

        if ($this->postData['shipping_add_id'] > 0) {
          $shipping_add_id = $this->postData['shipping_add_id'];
        } else {
          $shipping_add_id = 0;
        }

        if ($this->postData['billing_add_id'] > 0) {
          $billing_add_id = $this->postData['billing_add_id'];
        } else {
          $billing_add_id = 0;
        }



         

        if (!empty($payment_type)) {
          $data['product_details']   = $product_details;
          $data['shipping_address']  = json_encode($shipping_add);
          $data['vat']               = $vat;
          $data['vat_percent']       = $vat_per;
          $data['shipping']          = $shipping;
          $data['delivery_status']   = '[]';
          $data['payment_type']      = $payment_type;
          $data['payment_status']    = '[]';
          $data['payment_details']   = '';
          $data['grand_total']       = $grand_total;
          $data['deposit']           = $this->postData['deposit'];

          // $data['sale_datetime']     = strtotime($this->postData['sale_datetime']);
          // $data['delivary_datetime'] = strtotime($this->postData['delivary_datetime']);

          $data['sale_datetime']     = strtotime(date('d.m.Y H:i'));
         // $data['delivary_datetime'] = strtotime(date('Y-M-d H:i:s', strtotime('+ 2 hours')));

        if(isset($this->postData['isSchedule'])){

          if($this->postData['isSchedule'] === 1){
           
            $delivary_datetime = strtotime($this->postData['delivary_datetime']);
          }else{
            
              //$delivary_datetime = strtotime(date('Y-M-d H:i:s', time()+14400));
              $delivary_datetime = strtotime(date('Y-M-d H:i:s', strtotime('+ 2 hours')));
          }
			   
          $data['delivary_datetime'] = $delivary_datetime;
         }else{
            $data['delivary_datetime'] = strtotime($this->postData['delivary_datetime']);
			    // $data['delivary_datetime'] = strtotime(date('Y-M-d H:i:s', strtotime('+ 4 hours')));
         }

 
          //$data['delivary_datetime'] = $delivary_datetime;

          $data['shippingaddress_id'] = $shipping_add_id;
          $data['billingaddress_id'] = $billing_add_id;
          $this->db->insert('sale', $data);

          $sale_id = $this->db->insert_id();
          $data['buyer'] = $this->postData['user_id'];
          $vendors = $this->crud_model->vendors_in_sale($sale_id);

          $delivery_status = array();
          $payment_status = array();
          foreach ($vendors as $p) {
            $delivery_status[] = array('vendor' => $p, 'status' => 'pending', 'comment' => '', 'delivery_time' => '');
            $payment_status[] = array('vendor' => $p, 'status' => 'due');
          }
          if ($this->crud_model->is_admin_in_sale($sale_id)) {
            $delivery_status[] = array('admin' => '', 'status' => 'pending', 'comment' => '', 'delivery_time' => '');
            $payment_status[] = array('admin' => '', 'status' => 'due');
          }
          $data['sale_code'] = date('Ym', $data['sale_datetime']) . $sale_id;
          $data['delivery_status'] = json_encode($delivery_status);
          $data['payment_status'] = json_encode($payment_status);
          $data['vendor_id'] = $vendors[0];


          $this->db->where('sale_id', $sale_id);
          // $this->db->update('sale', $data);
          if ($this->db->update('sale', $data)) {
            $this->notification_add($this->postData['user_id'], $this->postData['user_id'], $sale_id, 1, 'order_placed', 'thanks_for_order_with_our_shop_our_delivery_boy_contact_you_soon', $this->postData['lang_id']);

            $vender_data = selectAll('user', ['vendor_id' => $vendors[0]], 'user_id');

            if (!empty($vender_data)) {
              foreach ($vender_data as $del_boy) {
                $del_data = [
                  'vendor_id' => $vendors[0],
                  'buyer' => $this->postData['user_id'],
                  'delivery_boy_id' => $del_boy->user_id,
                  'sale_id' => $sale_id,
                ];
                $this->db->insert('delivery_order_assigns', $del_data);

                $this->notification_add($del_boy->user_id, $this->postData['user_id'], $sale_id, 2, 'order_placed', 'order_placed_by_customer', $this->postData['lang_id']);
              }
            }
          }

          foreach ($carted as $value) {
            // ! Added conditions for Deleted vendor is_deleted = 0
            $catSubCate = $this->db->select('category,sub_category')->get_where('product', array('product_id' => $value['id'], 'is_deleted' => 0))->row();
            $this->crud_model->decrease_quantity($value['id'], $value['qty']);

            $data1['type']         = 'destroy';
            $data1['category']     = $catSubCate->category;
            $data1['sub_category'] = $catSubCate->sub_category;
            $data1['product']      = $value['id'];
            $data1['quantity']     = $value['qty'];
            $data1['total']        = 0;
            $data1['reason_note']  = 'sale';
            $data1['sale_id']      = $sale_id;
            $data1['datetime']     = time();
            $this->db->insert('stock', $data1);
          }

         
           //print_r($data); exit;
          //$this->crud_model->digital_to_customer($sale_id);
          //$this->email_model->email_invoice($sale_id);
          echo json_encode(['status' => 200]);
        } else {
          echo json_encode(['status' => 200, 'message' => 'Payment Methos Not Selected..']);
        }
      } else {
        echo json_encode(['status' => 500]);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => '']);
    }

    exit;
  }


  /**
   * Order List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function my_order_post()
  {
    $res = array();

    if ($this->isValid) {
      if ($this->postData) {

        // $this->benchmark->mark('code_start');

        $this->db->select(['sale_id', 'sale_code', 'grand_total', 'sale_datetime', 'delivery_status']);
        $this->db->where('buyer', $this->postData['user_id']);
        $page_data = $this->db->order_by("sale_id", "desc")->get('sale', $this->postData['limit'], $this->postData['start'])->result_array();

        // $this->benchmark->mark('code_end');

        // echo $this->benchmark->elapsed_time('code_start', 'code_end'); 

        $i = 0;
        // $this->benchmark->mark('1');
        foreach ($page_data as $sale) {

          $prod_data[$i]['sale_id'] = $sale['sale_id'];
          $prod_data[$i]['sale_code'] = $sale['sale_code'];
          // $prod_data[$i]['vat'] = $sale['vat'];
          // $prod_data[$i]['vat_percent'] = $sale['vat_percent'];
          // $prod_data[$i]['shipping'] = $sale['shipping'];
          $prod_data[$i]['grand_total'] = $sale['grand_total'];
          $prod_data[$i]['sale_datetime'] = date('d.m.Y H:i', $sale['sale_datetime']);
          $del_status = json_decode($page_data[$i]['delivery_status']);
          $prod_data[$i]['delv_status'] = $del_status[0]->status;
          // $prod_detail = json_decode($page_data[$i]['product_details']);
          // foreach ($prod_detail as $prod) {
          //   $prd_data = $this->langCheck($prod->id, $this->postData['lang_id']);
          //   $prod_data[$i]['prod_data'][] = ['prod_id' => $prod->id, 'qty' => $prod->qty, 'price' => $this->crud_model->get_product_price($prod->id), 'name' => $prd_data['title'], 'image' => $prod->image, 'subtotal' => $prod->subtotal];
          // }
          // $prod_data[$i]['item_count'] = count($prod_data[$i]['prod_data']);

          $i++;
        }
        // $this->benchmark->mark('2');

        // echo $this->benchmark->elapsed_time('1', '2'); 

        echo json_encode(['status' => 200, 'order_data' => $prod_data]);
      } else {
        echo json_encode(['status' => 500, 'order_data' => $res]);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => 'You are not Authoriz']);
    }

    exit;
  }


  /**
   * Order List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function delete_order_post()
  {
    $res = array();
    if ($this->postData) {
       echo json_encode(['status' => 500, 'message' => 'Sorry you did not delete order']);
      // if ($this->db->delete('sale', array('sale_id' => $this->postData['sale_id']))) {
      //   echo json_encode(['status' => 200, 'message' => 'Order Deleted Successfully']);
      // } else {
      //   echo json_encode(['status' => 500, 'message' => 'Order Not Deleted']);
      // }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something Went Wrong']);
    }
    exit;
  }



  /**
   * Invoice Details
   * @description this function use to register user
   * @param string form data
   * @return json array
   *  ! optimized 
   */
  function invoice_detail_post()
  {
    
    $res = array();

    if ($this->isValid) {
      if ($this->postData) {
        
        $user_data = selectSingle('user', ['user_id' => $this->postData['user_id']]);

        $this->db->where('sale_id', $this->postData['sale_id']);
        $page_data = $this->db->get('sale')->result();

        $this->db->select('title,message,date_time,order_details_status');
        $this->db->where('sale_id', $this->postData['sale_id']);
        $track_data = $this->db->order_by("id", "asc")->get('order_tracking')->result_array();

        $tax_tot = 0;
        foreach ($track_data as $track) {
          $track_detail[] = [
            'title' => translate_notification($track['title'], $this->postData['lang_id']),
            'message' => translate_notification($track['message'], $this->postData['lang_id']),
            'date_time' => date('d.m.Y H:i', strtotime($track['date_time'])),
            'order_details_status' => $track['order_details_status']
          ];
        }

        if ($page_data[0]->shippingaddress_id > 0) {
          $shippnig_add = selectSingle('user_address', ['address_id' => $page_data[0]->shippingaddress_id], 'fname,lname,address_1,address_2,city,zipcode,address_type');
          $prod_data['ship_add'] = [
            'fname' => $shippnig_add->fname,
            'lname' => $shippnig_add->lname,
            'address1' => $shippnig_add->address_1,
            'address2' => $shippnig_add->address_2,
            'city' => $shippnig_add->city,
            'zip' => $shippnig_add->zipcode,
            'address_type' => $shippnig_add->address_type
          ];
        } else {
          $shippnig_add = selectSingle('user', ['user_id' => $this->postData['user_id']], 'address1,address2,city,zip');
          $prod_data['ship_add'] = [
            'address1' => $shippnig_add->address1,
            'address2' => $shippnig_add->address2,
            'city' => $shippnig_add->city,
            'zip' => $shippnig_add->zip,
            'address_type' => 'shipping'
          ];
        }

        if ($page_data[0]->billingaddress_id > 0) {
          $billing_add = selectSingle('user_address', ['address_id' => $page_data[0]->billingaddress_id], 'fname,lname,address_1,address_2,city,zipcode,address_type');
          $prod_data['bill_add'] = [
            'fname' => $billing_add->fname,
            'lname' => $billing_add->lname,
            'address1' => $billing_add->address_1,
            'address2' => $billing_add->address_2,
            'city' => $billing_add->city,
            'zip' => $billing_add->zipcode,
            'address_type' => $billing_add->address_type
          ];
        } else {
          $billing_add = selectSingle('user', ['user_id' => $this->postData['user_id']], 'address1,address2,city,zip');
          $prod_data['bill_add'] = [
            'address1' => $billing_add->address1,
            'address2' => $billing_add->address2,
            'city' => $billing_add->city,
            'zip' => $billing_add->zip,
            'address_type' => 'billing'
          ];
        }
        if ($page_data[0]->shipping > 0) {
          $total = $page_data[0]->grand_total - $page_data[0]->deposit - $page_data[0]->shipping;
        } else {
          $total = $page_data[0]->grand_total - $page_data[0]->deposit - $page_data[0]->shipping;
        }
        $prod_data['sale_id'] = $page_data[0]->sale_id;
        $prod_data['sale_code'] = $page_data[0]->sale_code;
        $prod_data['vat'] = $page_data[0]->vat;
        $prod_data['vat_percent'] = $page_data[0]->vat_percent;
        $prod_data['shipping'] = $page_data[0]->shipping;
        $prod_data['total'] = $total;
        $prod_data['deposit'] = $page_data[0]->deposit;
        $prod_data['grand_total'] = $page_data[0]->grand_total;
        $prod_data['sale_datetime'] = date('d.m.Y H:i', $page_data[0]->sale_datetime);
        $prod_data['delivary_datetime'] = date('d.m.Y H:i', $page_data[0]->delivary_datetime);
        $prod_data['payment_type'] = $page_data[0]->payment_type;

        $del_status = json_decode($page_data[0]->delivery_status);
        $prod_data['delv_status'] = $del_status[0]->status;
        $prod_detail = json_decode($page_data[0]->product_details);
        //  if($product_data[$i][0]['tax_type']=='percent'){
        //     $tax[] = $this->crud_model->get_product_price($product_data[$i][0]['product_id']) * $product_data[$i][0]['tax'] / 100;
        // }else if($product_data[$i][0]['tax_type']=='amount'){
        //     $tax[] = $product_data[$i][0]['tax'];
        // }


        $food_tax = 0;
        $non_food_tax = 0;
        $product_tax = 0;
        foreach ($prod_detail as $prod) {
          $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
           $this->db->where('status', 'ok');
          $prod_tax = selectSingle('product', ['product_id' => $prod->id], ['tax', 'tax_type','specification','unit']);

          if (empty($prod_tax->tax)) {
            $tax = 0;
          } else {
            $tax = $prod_tax->tax;
          }

          if ($prod_tax->tax_type == 'percent') {
            $tax_n[] = $this->crud_model->get_product_tax($prod->id) * $prod->qty;
          } else if ($prod_tax->tax_type == 'amount') {
            $tax_n[] = $tax * $prod->qty;
          }
          $prd_data = $this->langCheck($prod->id, $this->postData['lang_id']);
          $prod_data['prod_data'][] = ['prod_id' => $prod->id, 'qty' => $prod->qty, 'price' => $prod->price, 'name' => $prd_data['title'], 'image' => $prod->image, 'subtotal' => $prod->subtotal, 'note' => $prod->note, 'specification' => $prod_tax->specification, 'unit' => $prod_tax->unit];
          
          /* get product tax START */
          $product_detail =  $this->db->select('tax,sale_price')->where('product_id', $prod->id)->get('product')->result_array();
          $product_tax = $product_detail[0]['tax'];
          $product_sale_price = $product_detail[0]['sale_price'];                                          
                                            
          if($product_tax=='5'){
              $food_tax += ($product_sale_price*$prod->qty)*(5/105);
             
          }elseif($product_tax=='16'){
              $non_food_tax += ($product_sale_price*$prod->qty)*(16/116);
              
          }


          /* get product tax END */ 
        }

        $tax_tot = array_sum($tax_n);
        $prod_data['tot_tax'] = $tax_tot;
        $prod_data['item_count'] = count($prod_data['prod_data']);
        $prod_data['food_tax'] = $food_tax;
        $prod_data['non_food_tax'] = $non_food_tax;

        // echo count($prod_data);
        echo json_encode(['status' => 200, 'order_data' => $prod_data, 'billing_detail' => $user_data, 'track_detail' => $track_detail]);
      } else {
        echo json_encode(['status' => 500, 'order_data' => $res]);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => '']);
    }

    exit;
  }


  /**
   * Product Rating
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function product_rating_post()
  {
    $res = array();
    if ($this->postData) {

      if ($this->postData['rating'] <= 5) {
        if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'yes') {
          echo 'success';
        } else if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'no') {
          echo 'already';
          exit;
        }
      } else {
        echo 'failure';
      }

      // echo count($prod_data);
      echo json_encode(['status' => 200]);
    } else {
      echo json_encode(['status' => 500]);
    }
    exit;
  }


  /**
   * Product Rating
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function product_rating_count_post()
  {
    $res = array();

    if ($this->postData) {

      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
       $this->db->where('status', 'ok');
      $rating_data = selectSingle('product', ['product_id' => $this->postData['prod_id']], ['rating_total']);

      echo json_encode(['status' => 200, 'rating_total' => $rating_data->rating_total]);
    } else {
      echo json_encode(['status' => 500, 'rating_total' => $res]);
    }
    exit;
  }


  /**
   * Product Add To Wish
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function add_wish_post()
  {
    $res = array();
    if ($this->postData) {

      if ($this->crud_model->add_wish_app($this->postData['prod_id'], $this->postData['user_id']) == 'yes') {
        $wish_data = "Successfully updated";
      } else {
        $wish_data = "Data Not updated";
      }

      echo json_encode(['status' => 200, 'add_wish' => $wish_data]);
    } else {
      echo json_encode(['status' => 500, 'add_wish' => $res]);
    }
    exit;
  }





  /**
   * Product Remove From Wish
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function remove_wish_post()
  {
    $res = array();
    if ($this->postData) {
      $wish_data = $this->crud_model->remove_wish_app($this->postData['prod_id'], $this->postData['user_id']);
      echo json_encode(['status' => 200]);
    } else {
      echo json_encode(['status' => 500, 'add_wish' => $res]);
    }
    exit;
  }


  /**
   * Product Wish List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function wish_list_post()
  {
    $res = array();

    if ($this->isValid) {
      if ($this->postData) {

        // old datacode start here
        // $wish_data = $this->crud_model->wished_num_app($this->postData['user_id']);
        // foreach ($wish_data as $pid) {
        //   $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
        //   $prod_array = selectSingle('product', ['product_id' => $pid], ['product_id', 'title', 'description', 'sale_price', 'current_stock', 'specification', 'unit']);
        //   $prd_data = $this->langCheck($prod_array->product_id, $this->postData['lang_id']);
        //   $prod_data[] = [
        //     "product_id" => $prod_array->product_id,
        //     "title" => $prd_data['title'],
        //     "description" => $prd_data['description'],
        //     "specification" => $prod_array->specification,
        //     "unit" => $prod_array->unit,
        //     "sale_price" => $this->crud_model->get_product_price($prod_array->product_id),
        //     "stock" => $prod_array->current_stock,
        //     "prod_image" => resize_images($this->crud_model->file_view('product', $prod_array->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 100, 100, 400)
        //   ];
        // }
        // old datacode Ends here

        // New code start here Optimized Code
        $wish_data = $this->crud_model->wished_num_app($this->postData['user_id']);
        if (!empty($wish_data)) {
          if ($this->postData['lang_id'] == 1) {
            $this->db->select(['product_id', 'title', 'description', 'sale_price', 'current_stock', 'specification', 'unit','perorderunit']);
          } elseif ($this->postData['lang_id'] == 2) {
            $this->db->select(['product_id', 'title_german as title', 'description_german as description', 'sale_price', 'current_stock', 'specification', 'unit','perorderunit']);
          } elseif ($this->postData['lang_id'] == 3) {
            $this->db->select(['product_id', 'title_turkey as title', 'description_turkey as description', 'sale_price', 'current_stock', 'specification', 'unit','perorderunit']);
          }
          $this->db->where_in('product_id', $wish_data);
          
          $wish_data = selectAll('product', ['is_deleted' => 0,'status' => 'ok']);


          foreach ($wish_data as $pid) {
            $prod_data[] = [
              "product_id" => $pid->product_id,
              "title" => $pid->title,
              "description" => $pid->description,
              "specification" => $pid->specification,
              "unit" => $pid->unit,
              "tax"=>$this->crud_model->get_product_tax($pid->product_id),
              "bottle_deposit" => $pid->bottle_deposit,
              "eng_title" => $pid->title,
              "german_title" => $pid->title_german,
              "turkish_title" => $pid->title_turkey,
              "sale_price" => $this->crud_model->get_product_price($pid->product_id),
              "stock" => $pid->current_stock,
              "prod_image" => resize_images($this->crud_model->file_view('product', $pid->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400),
              "product_unit_per_order" => $pid->perorderunit,
            ];
          }
          // New code Ends here Optimized Code

          echo json_encode(['status' => 200, 'list_wish' => $prod_data]);
        } else {
          echo json_encode(['status' => 500, 'list_wish' => $res]);
        }
      } else {
        echo json_encode(['status' => 500, 'list_wish' => $res]);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => '']);
    }

    exit;
  }


  /**
   * Update Billing Address
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function update_billing_address_post()
  {
    $res = array();
    if ($this->postData) {
      $data['address1'] = $this->postData['address1'];
      $data['address2'] = $this->postData['address2'];
      $data['city'] = $this->postData['city'];
      $data['zip'] = $this->postData['zip'];

      if (update('user', $data, ['user_id' => $this->postData['user_id']])) {
        $user_data = selectSingle('user', ['user_id' => $this->postData['user_id']]);
        echo json_encode(['status' => 200, 'userData' => $user_data]);
      } else {
        echo json_encode(['status' => 500, 'userData' => $res]);
      }

      // echo count($prod_data);

    } else {
      echo json_encode(['status' => 500, 'order_data' => $res]);
    }
    exit;
  }


  /**
   * Update User Billing Address
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function update_customer_billing_address_post()
  {
    $res = array();
    if ($this->postData) {
      $data['fname'] = $this->postData['fname'];
      $data['lname'] = $this->postData['lname'];
      $data['address_1'] = $this->postData['address_1'];
      $data['city'] = $this->postData['city'];
      $data['zipcode'] = $this->postData['zipcode'];
      $data['address_type'] = $this->postData['address_type'];

      if (update('user_address', $data, ['address_id' => $this->postData['billing_add_id']])) {
        $user_data = selectSingle('user_address', ['address_id' => $this->postData['billing_add_id']]);
        echo json_encode(['status' => 200, 'userData' => $user_data]);
      } else {
        echo json_encode(['status' => 500, 'userData' => $res]);
      }
    } else {
      echo json_encode(['status' => 500, 'order_data' => $res]);
    }
    exit;
  }



  /**
   * Update User Shipping Address
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function update_customer_shipping_address_post()
  {
    $res = array();
    if ($this->postData) {
      $data['fname'] = $this->postData['fname'];
      $data['lname'] = $this->postData['lname'];
      $data['address_1'] = $this->postData['address_1'];
      $data['city'] = $this->postData['city'];
      $data['zipcode'] = $this->postData['zipcode'];
      $data['address_type'] = $this->postData['address_type'];

      if (update('user_address', $data, ['address_id' => $this->postData['shipping_add_id']])) {
        $user_data = selectSingle('user_address', ['address_id' => $this->postData['shipping_add_id']]);
        echo json_encode(['status' => 200, 'userData' => $user_data]);
      } else {
        echo json_encode(['status' => 500, 'userData' => $res]);
      }
    } else {
      echo json_encode(['status' => 500, 'order_data' => $res]);
    }
    exit;
  }






  /**
   * Search Product API
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function get_search_prod_post()
  {

    $res = array();
    if ($this->postData) {

      // $this->db->select('*');
      $this->db->select('product_id,title,title_german,title_turkey'); // ! Added by sk
      $this->db->from('product');
      /*if ($this->postData['lang_id'] == 2) {
        $this->db->like('title_german', $this->postData['searchText']);
      } elseif ($this->postData['lang_id'] == 3) {
        $this->db->like('title_turkey', $this->postData['searchText']);
      } else {
        $this->db->like('title', $this->postData['searchText']);
      }
      */
      $this->db->group_start();
      $this->db->like('title', $this->postData['searchText'],'both');
      $this->db->or_like('title_german', $this->postData['searchText'],'both');
      $this->db->or_like('title_turkey', $this->postData['searchText'],'both');      
      $this->db->group_end();
      $this->db->like('areapin', $this->postData['areaPincode']);


      //$this->db->where('area_pin', $this->postData['areaPincode']);
      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
      $this->db->where('status', 'ok');
      $this->db->limit($this->postData['limit'], $this->postData['start']);

      
      $query = $this->db->get()->result_array();
     // echo json_encode(['sql' => $this->db->last_query(),'response'=>$query]);exit;

      if (!empty($query)) {
        foreach ($query as $prod) {
          $prd_data = $this->langCheck($prod['product_id'], $this->postData['lang_id']);
          $prod_image = resize_images($this->crud_model->file_view('product', $prod['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
          $prod_data[] = [
            'id' => $prod['product_id'],
            'name' => $prd_data['title'],
            'prod_img' => $prod_image
          ];
        }
       // print_r($prod_data); exit;
        echo json_encode(['status' => 200, 'searchData' => $prod_data]);
      } else {
        echo json_encode(['status' => 500, 'searchData' => $res]);
      }
    } else {
      echo json_encode(['status' => 500, 'searchData' => $res]);
    }
    exit;
  }



  /**
   * Product Add Review
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function add_review_post()
  {
    $res = array();
    if ($this->postData) {



      if ($this->postData['rating'] <= 5) {
        $rate = $this->postData['rating'];
        if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'yes') {
        } else if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'no') {
        }
      } else {
        $rate = 0;
      }

      $data = [
        'user_id' => $this->postData['user_id'],
        'title' => $this->postData['title'],
        'comments' => $this->postData['comments'],
        'product_id' => $this->postData['prod_id'],
        'ratings' => $rate
      ];

      if (insert('product_reviews', $data)) {
        $review_data = "Successfully updated";
      } else {
        $review_data = "Data Not Saved";
      }

      echo json_encode(['status' => 200, 'review' => $review_data]);
    } else {
      echo json_encode(['status' => 500, 'review' => $res]);
    }
    exit;
  }



  /**
   * List of Review
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function review_list_post()
  {
    $res = array();
    if ($this->postData) {

      $reviewRes = array();

      $all_review = selectAll('product_reviews', ['product_id' => $this->postData['product_id']], null, $this->postData['limit'], $this->postData['start']);


      foreach ($all_review as $review) {
        $user_detail = selectSingle('user', ['user_id' => $review->user_id], ['user_id', 'username', 'surname']);

        $rates = $review->ratings;
        if (!empty($review->ratings)) {
          for ($l = 0; $l < 5; $l++) {
            if ($rates > 0)
              $reviewRes['starRating'][$l] = 'starActiveColor';
            else
              $reviewRes['starRating'][$l] = 'starInactiveColor';
            $rates--;
          }
        } else {
          for ($l = 0; $l < 5; $l++) {
            $reviewRes['starRating'][$l] = 'starInactiveColor';
          }
        }
        $user_image = resize_images($this->crud_model->file_view('user', $review->user_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
        $review_list[] = [
          'user_id' => $review->user_id,
          'review_id' => $review->review_id,
          'title' => $review->title,
          'comments' => $review->comments,
          'review_id' => $review->review_id,
          'userName' => $user_detail->username . ' ' . $user_detail->surname,
          'userImage' => $user_image,
          'rating' => $reviewRes,
          'rating_count' => $review->ratings,
          'added_by' => date('d.m.Y H:i', strtotime($review->added_at))
        ];
      }


      echo json_encode(['status' => 200, 'review' => $review_list]);
    } else {
      echo json_encode(['status' => 500, 'review' => $res]);
    }
    exit;
  }



  /**
   * List of Review
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function review_count_post()
  {
    $res = array();
    if ($this->postData) {

      $reviewRes = array();

      $all_review = selectAll('product_reviews', ['product_id' => $this->postData['prod_id']], null, null, null);
      //print_r($all_review);
      echo count($all_review);
      exit;
      echo json_encode(['status' => 200, 'review_count' => count($all_review)]);
    } else {
      echo json_encode(['status' => 500, 'review' => 0]);
    }
    exit;
  }


  /**
   * List of Vendor
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function vendor_list_get()
  {
    $res = array();
    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
    $all_vendor = selectAll('vendor', ['status' => 'approved'], ['vendor_id', 'name', 'email', 'company', 'display_name', 'address1', 'address2', 'country', 'city', 'zip', 'state']);
    if (!empty($all_vendor)) {
      echo json_encode(['status' => 200, 'all_vendor' => $all_vendor]);
    } else {
      echo json_encode(['status' => 500, 'all_vendor' => $all_vendor]);
    }
    exit;
  }


  /**
   * Vendor details
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function vendor_details_post()
  {
    $res = array();

    if ($this->postData) {
      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
      $vendor_details = selectSingle('vendor', ['vendor_id' => $this->postData['vendor_id'], 'status' => 'approved'], ['vendor_id', 'name', 'email', 'company', 'display_name', 'address1', 'address2', 'country', 'city', 'zip', 'state']);

      echo json_encode(['status' => 200, 'vendor_details' => $vendor_details]);
    } else {
      echo json_encode(['status' => 500, 'vendor_details' => $res]);
    }
    exit;
  }




  /**
   * Product Add To Wish
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function list_address_post()
  {
    $res = array();
    if ($this->postData) {
      $list_address = selectAll('user_address', array('user_id' => $this->postData['user_id']), null, $this->postData['limit'], $this->postData['start'], 'address_id', 'desc');

      foreach ($list_address as $address) {
        if ($address->address_type == 'billing') {
          $billing_address[] = [
            'id' => $address->address_id,
            'fname' => $address->fname,
            'lname' => $address->lname,
            'address_1' => $address->address_1,
            'address_2' => $address->address_2,
            'city' => $address->city,
            'zipcode' => $address->zipcode,
          ];
        } else {
          $shipping_address[] = [
            'id' => $address->address_id,
            'fname' => $address->fname,
            'lname' => $address->lname,
            'address_1' => $address->address_1,
            'address_2' => $address->address_2,
            'city' => $address->city,
            'zipcode' => $address->zipcode,
          ];
        }
      }

      echo json_encode(['status' => 200, 'billing_address' => $billing_address, 'shipping_address' => $shipping_address]);
    } else {
      echo json_encode(['status' => 500]);
    }
    exit;
  }



  /**
   * Product Add To Wish
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function add_address_post()
  {
    $res = array();
    if ($this->postData) {
      $addData = [
        "address_1"  => $this->postData['address_1'],
        "address_2"   => $this->postData['address_2'],
        "city"  => $this->postData['city'],
        "zipcode"  => $this->postData['zip'],
        "user_id"  => $this->postData['user_id'],
        "address_type"  => $this->postData['address_type']
      ];


      if (insert('user_address', $addData)) {
        echo json_encode(['status' => 200, 'message' => 'Data Saved Successfully.']);
      } else {
        echo json_encode(['status' => 500, 'message' => 'Something Went Wrong.']);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something went wrong']);
    }
    exit;
  }



  /**
   * register method
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function change_password_post()
  {

    if (!empty($this->postData)) {

      if (update('user', ['password' => sha1($this->postData['password'])], ['user_id' => $this->postData['user_id']])) {

        $user_data = selectSingle('user', ['user_id' => $this->postData['user_id']]);

        $res['status'] = 200;
        $res['message'] = 'Password Change successfully.';
        $res['user_data'] = $user_data;
      } else {
        $res['status'] = 500;
        $res['message'] = 'Something went wrong!!';
      }
    } else {
      $res['status'] = 500;
      $res['message'] = 'Something went wrong!!';
    }
    echo json_encode($res);
    exit;
  }


  function notification_add($user_id, $friend_id, $order_id, $type, $title, $message, $lang_id)
  {
    $title_lang_var = $title;
    $message_lang_var = $message;

    $title = translate_notification($title,$lang_id);
    $message = translate_notification($message,$lang_id);

    $user_data = selectSingle('user', ['user_id' => $user_id], ['device_token', 'role']);

    if (insert('notifications', ['user_id' => $user_id, 'friend_id' => $friend_id, 'order_id' => $order_id, 'type' => $type, 'title' => $title_lang_var, 'message' => $message_lang_var])) {

      if ($user_data->role == 1) {

        if (!empty($user_data->device_token)) {
          $this->getSendPushNotificationDeliveryBoy($message, $title, $user_data->device_token, 1, $order_id);
        }
      } else {
        if (!empty($user_data->device_token)) {
          $this->getSendPushNotification($message, $title, $user_data->device_token, 1, $order_id);
        }
      }
      return true;
    } else {
      return false;
    }
  }




  /**
   * New Search Product API
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function search_prod_list_post()
  {

    $res = array();
    if ($this->postData) {
        if (!empty($this->postData['searchText'])) {
         $this->db->select('*');
        //$this->db->select('product_id', 'title', 'category', 'sub_category', 'current_stock', 'specification', 'unit', 'selling_count','bottle_deposit','title_german','title_turkey'); // ! Added by sk
        $this->db->from('product');
        $this->db->group_start();
        $this->db->like('title', $this->postData['searchText'],'both');
        $this->db->or_like('title_german', $this->postData['searchText'],'both');
        $this->db->or_like('title_turkey', $this->postData['searchText'],'both');      
        $this->db->group_end();
        $this->db->like('areapin', $this->postData['areaPincode']);
        $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
        $this->db->where('status', 'ok');
        $this->db->limit($this->postData['limit'], $this->postData['start']);
        $query = $this->db->get()->result_array();
        
         if (!empty($this->postData['user_id'])) {
            if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
              $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
            } else {
              $wished = array();
            }
          } else {
            $wished = array();
          }

        if (!empty($query)) {
          foreach ($query as $prod) {

             if ($prod['current_stock'] == null) {
                  $product_stock = 0;
                } else {
                  $product_stock = $prod['current_stock'];
                }

                if (in_array($prod['product_id'], $wished)) {
                  $is_fav = True;
                } else {
                  $is_fav = False;
                }
            $prd_data = $this->langCheck($prod['product_id'], $this->postData['lang_id']);
            $prod_image = resize_images($this->crud_model->file_view('product', $prod['product_id'], '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, false);


            $prod_data[] = [
              'product_id' => $prod['product_id'],
              'title' => $prd_data['title'],
              'sale_price' => $this->crud_model->get_product_price($prod['product_id']),
              'category_id' => $prod['category'],
              'sub_category_id' => $prod['sub_category'],
              'prod_image' => $prod_image,
              'current_stock' => $product_stock,
              'specification' => $prod['specification'],
              'unit' => $prod['unit'],
              'tax'=>$this->crud_model->get_product_tax($prod['product_id']),
              'bottle_deposit'=>$prod['bottle_deposit'],
              'eng_title'=>$prod['title'],
              'german_title'=>$prod['title_german'],
              'turkish_title'=>$prod['title_turkey'],
              'is_fav' => $is_fav,
              'product_unit_per_order'=>$prod['perorderunit']
            ];

          }
        
          echo json_encode(['status' => 200, 'searchData' => $prod_data]);
        } else {
          echo json_encode(['status' => 500, 'searchData' => $res]);
        }
      }else{

        $this->db->where('is_deleted', 0); // ! Added conditions for Deleted vendor is_deleted = 0
        $this->db->where('status', 'ok');
        $best_selling_product = selectAll('product', array('areapin LIKE' => '%' . $this->postData['areaPincode'] . '%'), array('product_id', 'title', 'category', 'sub_category', 'current_stock', 'specification', 'unit', 'selling_count','bottle_deposit','title_german','title_turkey'), $this->postData['limit'], $this->postData['start'], 'selling_count', 'desc');

        if (!empty($this->postData['user_id'])) {
          if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
            $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
          } else {
            $wished = array();
          }
        } else {
          $wished = array();
        }

        

        if ($best_selling_product > 0) {


              foreach ($best_selling_product as $product) {

                if ($product->current_stock == null) {
                  $product_stock = 0;
                } else {
                  $product_stock = $product->current_stock;
                }

                if (in_array($product->product_id, $wished)) {
                  $is_fav = True;
                } else {
                  $is_fav = False;
                }
                $prod_image = resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
                $prd_data = $this->langCheck($product->product_id, $lang);

                $all_product[] = ['title' => $prd_data['title'], 'sale_price' => $this->crud_model->get_product_price($product->product_id), 'product_id' => $product->product_id, 'category_id' => $product->category, 'sub_category_id' => $product->sub_category, 'prod_image' => $prod_image, 'current_stock' => $product_stock, 'specification' => $product->specification, 'unit' => $product->unit, 'tax'=>$this->crud_model->get_product_tax($product->product_id),'bottle_deposit'=>$product->bottle_deposit,'eng_title'=>$product->title,'german_title'=>$product->title_german,'turkish_title'=>$product->title_turkey, 'is_fav' => $is_fav];
              }

              echo json_encode(['status' => 200, 'searchData' => $all_product]);
            } else {
              echo json_encode(['status' => 500, 'searchData' => $res]);
            }
        
      }
    } else {
      echo json_encode(['status' => 500, 'searchData' => $res]);
    }
    exit;
  }


   /**
   * Popular Search List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! Optimized
   */


public function popular_search_list_post()
  { 

      

        if (!empty($this->postData)) {
            $this->db->where('is_deleted', 0); 
             $this->db->where('status', 'ok');
             $popular_product = selectAll('product', array('product_id' => $this->postData['product_id']));
             if(!empty($popular_product)){
               $res['status'] = 200;
               $res['message'] = 'Product Exist!!';
            }else{
                $res['status'] = 500;
                $res['message'] = 'Something went wrong!!';
            }
          } else {
           $res['status'] = 500;
           $res['message'] = 'Something went wrong!!';
        }
    echo json_encode($res);
    exit;
        
        
  }

  /**
   * Notification List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! Optimized
   */
  public function notification_list_post()
  {
    $res = array();
    if ($this->isValid) {
      if (!empty($this->postData)) {

        $list_notification = selectAll('notifications', array('user_id' => $this->postData['user_id']), null, $this->postData['limit'], $this->postData['start'], 'id', 'desc');

        foreach ($list_notification as $notify) {

          $user_data = selectSingle('user', ['user_id' => $notify->friend_id], 'user_id,username,surname,email,phone');

          $notifc_data[] = [
            'id' => $notify->id,
            'user_id' => $notify->user_id,
            'friend_id' => $notify->friend_id,
            'friend_data' => [
              'fname' => $user_data->username,
              'lname' => $user_data->surname,
              'email' => $user_data->email,
              'phone' => $user_data->phone,
              'image' => resize_images($this->crud_model->file_view('user', $user_data->user_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400)
            ],
            'order_id' => $notify->order_id,
            'type' => $notify->type,
            'status' => $notify->status,
            'title' => translate_notification($notify->title, $this->postData['lang_id']),
            'message' => translate_notification($notify->message, $this->postData['lang_id']),
            'created' => date('d.m.Y H:i', strtotime($notify->created))
          ];
        }

        if (!empty($list_notification)) {
          echo json_encode(['status' => 200, 'list_notification' => $notifc_data]);
        } else {
          echo json_encode(['status' => 200, 'list_notification' => $res]);
        }
      } else {
        echo json_encode(['status' => 500, 'list_notification' => $res]);
      }
    } else {
      echo json_encode(['status' => 400, 'message' => '']);
    }


    exit;
  }


  /**
   * Notification List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function delete_notification_post()
  {
    $res = array();
    if (!empty($this->postData)) {

      if ($this->db->delete('notifications', array('id' => $this->postData['id']))) {
        echo json_encode(['status' => 200, 'message' => 'Notification Deleted Successfully']);
      } else {
        echo json_encode(['status' => 500, 'message' => 'Notification Not Deleted']);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something Went Wrong']);
    }

    exit;
  }



  /**
   * Notification List
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! Optimized
   */
  public function unread_notification_list_post()
  {
    $res = array();
    if (!empty($this->postData)) {

      $unread_notification = selectAll('notifications', array('user_id' => $this->postData['user_id'], 'status' => 0), null, $this->postData['limit'], $this->postData['start'], 'id', 'desc');

      foreach ($unread_notification as $notify) {

        $user_data = selectSingle('user', ['user_id' => $notify->friend_id], 'user_id,username,surname,email,phone');

        $notifc_data[] = [
          'id' => $notify->id,
          'user_id' => $notify->user_id,
          'friend_id' => $notify->friend_id,
          'friend_data' => [
            'fname' => $user_data->username,
            'lname' => $user_data->surname,
            'email' => $user_data->email,
            'phone' => $user_data->phone,
            'image' => resize_images($this->crud_model->file_view('user', $user_data->user_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400)
          ],
          'order_id' => $notify->order_id,
          'type' => $notify->type,
          'status' => $notify->status,
          'title' => $notify->title,
          'message' => $notify->message,
          'created' => date('d M Y h:i a', strtotime($notify->created))
        ];
      }
      if (!empty($unread_notification)) {
        echo json_encode(['status' => 200, 'list_notification' => $notifc_data]);
      } else {
        echo json_encode(['status' => 200, 'list_notification' => $res]);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something Went Wrong']);
    }

    exit;
  }


  /**
   * Notification List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function unread_to_read_notification_post()
  {
    $res = array();
    if (!empty($this->postData)) {

      if (update('notifications', ['status' => 1], ['id' => $this->postData['id']])) {
        echo json_encode(['status' => 200, 'message' => 'Message Read']);
      } else {
        echo json_encode(['status' => 500, 'message' => 'Message Unread']);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something Went Wrong']);
    }

    exit;
  }



  /**
   * Notification List
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function unread_notification_counter_post()
  {
    $res = array();
    if (!empty($this->postData)) {

      if (!empty($this->postData['user_id'])) {
        if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
          $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
        } else {
          $wished = array();
        }
      } else {
        $wished = array();
      }

      $order_data = selectAll('sale', ['buyer' => $this->postData['user_id']], ['sale_id']);

      if (!empty($order_data)) {
        $order_count = count($order_data);
      } else {
        $order_count = 0;
      }


      $unread_notification = selectAll('notifications', array('user_id' => $this->postData['user_id'], 'status' => 0), array('COUNT(status) as Counter'), null, null, null, null);
      if ($unread_notification[0]->Counter > 0) {
        $not_counter = $unread_notification[0]->Counter;
      } else {
        $not_counter = 0;
      }

      echo json_encode(['status' => 200, 'notification_counter' => $not_counter, 'fav_count' => count($wished), 'order_count' => $order_count]);
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something Went Wrong']);
    }

    exit;
  }





  /**
   * Get Cheapest Product
   * @description this function use to register user
   * @param string form data
   * @return json array
   * ! Optimized
   */
  function get_category_by_product_post()
  {
   //clean_custom_cache();
    $res = array();
	$cache_key = "get_category_by_product_post_" . $this->postData['areaPincode'] . "_" . $this->postData['lang_id']. "_" . $this->postData['catId'];
   $data = $this->cache->get($cache_key);

    if (empty($data)) {
    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
    $this->db->where('status', 'ok');

    $product_by_cat = selectAll(
      'product',
      array(
        'areapin LIKE' => '%' . $this->postData['areaPincode'] . '%',
        'category' => $this->postData['catId']
      ),
      'current_stock,product_id,sale_price,category,sub_category,specification,unit,bottle_deposit,title_german,title_turkey,perorderunit',
      $this->postData['start'],
      $this->postData['limit'],
      'product_id',
      'desc'
    );

    if (!empty($this->postData['user_id'])) {
      if ($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist') !== 'null') {
        $wished = json_decode($this->crud_model->get_type_name_by_id('user', $this->postData['user_id'], 'wishlist'));
      } else {
        $wished = array();
      }
    } else {
      $wished = array();
    }


    if ($product_by_cat > 0) {

      foreach ($product_by_cat as $product) {

        if ($product->current_stock == null) {
          $product_stock = 0;
        } else {
          $product_stock = $product->current_stock;
        }

        if (in_array($product->product_id, $wished)) {
          $is_fav = True;
        } else {
          $is_fav = False;
        }

        $prod_image = resize_images($this->crud_model->file_view('product', $product->product_id, '', '', 'thumb', 'src', 'multi', 'one'), 200, 200, 400);
        $prd_data = $this->langCheck($product->product_id, $this->postData['lang_id']);
        $all_product[] = ['title' => $prd_data['title'], 'sale_price' => $product->sale_price, 'product_id' => $product->product_id, 'category_id' => $product->category, 'sub_category_id' => $product->sub_category, 'prod_image' => $prod_image, 'current_stock' => $product_stock, 'description' => $prd_data['description'], 'specification' => $product->specification, 'unit' => $product->unit,'tax'=>$this->crud_model->get_product_tax($product->product_id),'bottle_deposit'=>$product->bottle_deposit,'eng_title'=>$product->title,'german_title'=>$product->title_german,'turkish_title'=>$product->title_turkey, 'is_fav' => $is_fav, 'product_unit_per_order'=>$product->perorderunit];
      }
		$data = json_encode(['status' => 200, 'all_product' => $all_product]); 
		$this->cache->save($cache_key, $data, 30000);
      echo $data;
      exit;
    } else {
      echo json_encode(['status' => 200, 'all_product' => $res]);
      exit;
    }
  }else{
	  echo $data;
  }
	
	
  }


  public function coupon_check_post()
  {
    $getdata = json_decode(file_get_contents("php://input"));
    $para1 = $getdata->coupon_code;
    $para2 = $getdata->zipcode;
    $cpndtt = explode("&", $getdata->coupon_data);
    if (!in_array($para1, $cpndtt)) {


      if (count($getdata->cartData) > 0) {
        $cc = $getdata->coupon_count + 1;
        $carted = $getdata->cartData;
        if ($cc < 10) {
          $c = $this->db->get_where('coupon', array('code' => $para1));
          $coupon = $c->result_array();
          if ($c->num_rows() > 0) {
            foreach ($coupon as $row) {
              $spec = json_decode($row['spec'], true);
              $coupon_id = $row['coupon_id'];
              $till = strtotime($row['till']);
            }
            if ($till > time()) {
              $ro = $spec;
              $type = $ro['discount_type'];
              $value = $ro['discount_value'];
              $set_type = $ro['set_type'];
              $set = json_decode($ro['set']);
              if ($set_type !== 'total_amount') {
                $dis_pro = array();
                $set_ra = array();
                if ($set_type == 'all_products') {
                  //$set_ra[] = $this->db->get('product')->result_array();
                  $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
                  $this->db->where('status', 'ok');
                  $set_ra[] = $this->db->like('areapin', $para2)->get('product')->result_array();
                } else {
                  foreach ($set as $p) {
                    if ($set_type == 'product') {
                      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
                      $this->db->where('status', 'ok');
                      $set_ra[] = $this->db->get_where('product', array('product_id' => $p))->result_array();
                    } else {
                      $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
                      $this->db->where('status', 'ok');
                      $set_ra[] = $this->db->get_where('product', array($set_type => $p))->result_array();
                    }
                  }
                }

                foreach ($set_ra as $set) {
                  foreach ($set as $n) {
                    $dis_pro[] = $n['product_id'];
                  }
                }
                $discount = 0;
                $cnt = 0;
                foreach ($carted as $items) {
                  // echo $carted[$cnt]->product_id;
                  if (in_array($carted[$cnt]->product_id, $dis_pro)) {
                    $base_price = $this->crud_model->get_product_price($carted[$cnt]->product_id) * $carted[$cnt]->product_qty;
                    if ($type == 'percent') {
                      $discount += $base_price * $value / 100;
                    } else if ($type == 'amount') {
                      $discount += $value;
                    }
                  }
                  $cnt++;
                }

                if ($getdata->coupon_data == "") {
                  $cpndata = $getdata->coupon_code;
                } else {
                  $cpndata = $getdata->coupon_data . "&" . $getdata->coupon_code;
                }
                echo json_encode(array(
                  "coupon_data" => $cpndata,
                  "coupon_count" => $cc,
                  "coupon_code" => $getdata->coupon_code,
                  "item_count" => $getdata->item_count,
                  "discount_total" => $discount,
                  "prod_total" => $getdata->prod_total - $discount,
                  "total_with_ship" => $getdata->total_with_ship - $discount,
                  "shipping_cost" => $getdata->shipping_cost,
                  "total_tax" => $getdata->total_tax,
                  "total" => $getdata->total - $discount,
                  "total_kg" => $getdata->total_kg,
                  "total_liter" => $getdata->total_liter
                ));
              } else {

                echo json_encode(array(
                  "coupon_data" => $cpndata,
                  "coupon_count" => $cc,
                  "coupon_code" => $getdata->coupon_code,
                  "item_count" => $getdata->item_count,
                  "discount_total" => $value,
                  "prod_total" => $getdata->prod_total - $value,
                  "total_with_ship" => $getdata->total_with_ship - $value,
                  "shipping_cost" => $getdata->shipping_cost,
                  "total_tax" => $getdata->total_tax,
                  "total" => $getdata->total - $value,
                  "total_kg" => $getdata->total_kg,
                  "total_liter" => $getdata->total_liter
                ));
              }
            } else {
              echo json_encode(array("status" => "failed", "msg" => "Coupen Code Expired!"));
            }
          } else {
            echo json_encode(array("status" => "failed", "msg" => "Wrong coupon code entered!"));
          }
        } else {
          echo json_encode(array("status" => "failed", "msg" => "Too many coupon request!"));
        }
      } else {
        echo json_encode(array("status" => "failed", "msg" => "Product Data is not available"));
      }
    } else {
      echo json_encode(array("status" => "failed", "msg" => "Coupen Code already applied"));
    }


    exit;
  }




  function langCheck($prod_id, $lang_id)
  {

    if ($lang_id == 2) {
      $lang = '_german';
    } elseif ($lang_id == 3) {
      $lang = '_turkey';
    } else {
      $lang = '';
    }

    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
    $this->db->where('status', 'ok');
    $prod_data = selectSingle('product', ['product_id' => $prod_id], ['title' . $lang . '', 'description' . $lang . '']);
    $this->db->select(['title' . $lang . '', 'description' . $lang . '']);
    $this->db->from("product");
    $this->db->where('product_id ', $prod_id);
    $q = $this->db->get();
    $res_data = $q->result_array();
    $prd_data = [
      'title' => $res_data[0]['title' . $lang],
      'description' => $res_data[0]['description' . $lang]
    ];


    return $prd_data;
  }



  function CategorylangCheck($cat_id, $lang_id)
  {

    if ($lang_id == 2) {
      $lang = '_german';
    } elseif ($lang_id == 3) {
      $lang = '_turkey';
    } else {
      $lang = '';
    }

    $this->db->select(['category_name' . $lang . '']);
    $this->db->from("category");
    $this->db->where('category_id ', $cat_id);
    $q = $this->db->get();
    $res_data = $q->result_array();
    $mcat_data = ['title' => $res_data[0]['category_name' . $lang]];


    return $mcat_data;
  }


  function SubCategorylangCheck($sub_cat_id, $lang_id)
  {

    if ($lang_id == 2) {
      $lang = '_german';
    } elseif ($lang_id == 3) {
      $lang = '_turkey';
    } else {
      $lang = '';
    }

    $this->db->select(['sub_category_name' . $lang . '']);
    $this->db->from("sub_category");
    $this->db->where('sub_category_id ', $sub_cat_id);
    $q = $this->db->get();
    $res_data = $q->result_array();
    $sub_cat_data = ['title' => $res_data[0]['sub_category_name' . $lang]];


    return $sub_cat_data;
  }



  function getSendPushNotification($message, $title, $playerData, $notiType, $order_id)
  {
    // $include_player_id = array(
    //   $playerData
    // );
    // $headcontent = [
    //   "en" => $title
    // ];
    // $content = [
    //   "en" => $message
    // ];
    // $fields = array(
    //   'app_id' => "4276e15d-d6fb-4a4a-9c14-2771d7bfab66",
    //   'headings' => $headcontent,
    //   'include_player_ids' => $include_player_id,
    //   'android_group'  => "Market septi app",
    //   'ios_badgeType' => 'Increase',
    //   'data' => array("notificationType" => $notiType, "order_id" => $order_id),
    //   'ios_badgeCount' => 1,
    //   'small_icon' => "icon",
    //   'large_icon' => "icon",
    //   'android_group_message' => array("en" => "You have $[notif_count] new messages"),
    //   'contents' => $content
    // );


    // $fields = json_encode($fields);
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic MzcxOWUwZDYtYWM3ZC00NTZkLWE3YWUtNDUxY2RjZDkyOGIz'));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // curl_setopt($ch, CURLOPT_HEADER, FALSE);
    // curl_setopt($ch, CURLOPT_POST, TRUE);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    // $response = curl_exec($ch);

    // curl_close($ch);
    return true;
  }




  function getSendPushNotificationDeliveryBoy($message, $title, $playerData, $notiType, $order_id)
  {
   
    $include_player_id = array(
      $playerData
    );
    $headcontent = [
      "en" => $title
    ];
    $content = [
      "en" => $message
    ];
    $fields = array(
      'app_id' => "fad6e127-5371-4061-a93a-fb8f5950d2b4",
      'headings' => $headcontent,
      'include_player_ids' => $include_player_id,
      'android_group'  => "Market septi app",
      'ios_badgeType' => 'Increase',
      'data' => array("notificationType" => $notiType, "order_id" => $order_id),
      'ios_badgeCount' => 1,
      'small_icon' => "icon",
      'large_icon' => "icon",
      'android_group_message' => array("en" => "You have $[notif_count] new messages"),
      'contents' => $content
    );


    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YTgwZTVmYjktYzkxNi00NGJjLTk3OTAtZDEwMDJmMjcwNWIy'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    $response = curl_exec($ch);
    curl_close($ch);
  }



  /**
   * Increse Product Selling Count
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function add_product_selling_count($product_id)
  {

    $this->db->where('is_deleted', 0); // ! Added conditions for Deleted Product is_deleted = 0
    $this->db->where('status', 'ok');
    $product_sell_count = selectSingle('product', ['product_id' => $product_id], ['selling_count']);
    $total_sell_count = 0;
    $total_sell_count = $product_sell_count->selling_count + 1;
    if (update('product', ['selling_count' => $total_sell_count], ['product_id' => $product_id])) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * Page Data
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  public function get_page_data_post()
  {
    $res = array();
    $page_data = selectSingle('page', ['parmalink' => $this->postData['parmalink'], 'status' => 'ok'], 'page_name,parmalink,parts');
    if (!empty($page_data)) {

      $data = json_decode($page_data->parts, true);

      $responseData['page_name'] = ucfirst($page_data->page_name);

      if ($this->postData['lang_id'] == 1) {
        $responseData['page_name'] = $data[0]['content'];
      } elseif ($this->postData['lang_id'] == 2) {
        $responseData['page_name'] = $data[0]['content_german'];
      } elseif ($this->postData['lang_id'] == 3) {
        $responseData['page_name'] = $data[0]['content_turkey'];
      } else {
        $responseData['page_name'] = ["page_name" => []];
      }
      echo json_encode([
        "status"  => 200,
        "msg"     => ucfirst($page_data->page_name),
        "data"    => $responseData
      ]);
    } else {
      echo json_encode([
        "status"  => 200,
        "msg"     => "No Page Found",
        "data"    => $res
      ]);
    }
    exit;
  }

  function get_faq_post()
  {
    $res = array();
    $faq = $this->db->get_where('business_settings', array('type' => "faqs"))->row()->value;
    if (!empty($faq)) {
      $deco_faq = json_decode($faq, true);
      if (!empty($faq)) {
        foreach ($deco_faq as $i => $row) {
          if ($this->postData['lang_id'] == 2) {

              if(!empty($row['question_german'])){

                   $faq_arr[$i]['question'] = $row['question_german'];
              }else{

                   $faq_arr[$i]['question'] = $row['question'];
              }

              if($row['answer_german'] == '<p><br></p>'){

                if($row['answer'] == '<p><br></p>'){
                     $faq_arr[$i]['answer'] = 'Keine Antwort';
                }else{
                  $faq_arr[$i]['answer'] = $row['answer'];
                }
                  
              }else{
                $faq_arr[$i]['answer'] = $row['answer_german'];
              }
           
            
          } elseif ($this->postData['lang_id'] == 3) {

            if(!empty($row['question_turkey'])){
                   $faq_arr[$i]['question'] = $row['question_turkey'];
              }else{
                   $faq_arr[$i]['question'] = $row['question'];
              }

              if($row['answer_turkey'] == '<p><br></p>'){
                if($row['answer'] == '<p><br></p>'){
                     $faq_arr[$i]['answer'] = 'Cevapsız';
                }else{
                  $faq_arr[$i]['answer'] = $row['answer'];
                }
                   
              }else{
                $faq_arr[$i]['answer'] = $row['answer_turkey'];
              } 
            
          } else {
            $faq_arr[$i]['question'] = $row['question'];

             if($row['answer'] == '<p><br></p>'){
                   $faq_arr[$i]['answer'] = 'No Answer Found';
              }else{
                $faq_arr[$i]['answer'] = $row['answer'];
              }
            
          }
        }
      
        echo json_encode(['status' => 200, 'questionAndAnswerList' => $faq_arr]);
      } else {
        echo json_encode(['status' => 500, 'questionAndAnswerList' => $res]);
      }
    } else {
      echo json_encode(['status' => 500, 'questionAndAnswerList' => $res]);
    }
    exit;
  }



  /**
   * Product Edit Review
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function edit_review_post()
  {
    $res = array();
    if ($this->postData) {



      if ($this->postData['rating'] <= 5) {
        $rate = $this->postData['rating'];
        if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'yes') {
        } else if ($this->crud_model->set_rating_api($this->postData['prod_id'], $this->postData['rating'], $this->postData['user_id']) == 'no') {
        }
      } else {
        $rate = 0;
      }

      $data = [
        'title' => $this->postData['title'],
        'comments' => $this->postData['comments'],
        'ratings' => $rate
      ];

      if (update('product_reviews', $data, ['review_id' => $this->postData['review_id']])) {

        $review_data = "Successfully updated";
      } else {
        $review_data = "Data Not Saved";
      }

      echo json_encode(['status' => 200, 'review' => $review_data]);
    } else {
      echo json_encode(['status' => 500, 'review' => $review_data]);
    }
    exit;
  }




  /**
   * Product Edit Review
   * @description this function use to register user
   * @param string form data
   * @return json array
   */
  function delete_review_post()
  {
    $res = array();
    if ($this->postData) {

      if ($this->db->delete('product_reviews', array('review_id' => $this->postData['review_id']))) {
        echo json_encode(['status' => 200, 'message' => 'Review Deleted Successfully']);
      } else {
        echo json_encode(['status' => 500, 'message' => 'Review Not Deleted']);
      }
    } else {
      echo json_encode(['status' => 500, 'message' => 'Something went wrong']);
    }
    exit;
  }




function get_time_get()
  { 

     echo date_default_timezone_get();
      echo '  1 ****** '; echo ' ****** ';
 //      echo time();
 //      echo '  2 ****** '; echo ' ****** ';
       echo strtotime(date('d.m.Y H:i'));
       echo '  3 ****** '; echo ' ****** ';
       echo date('d.m.Y H:i');
 //      echo '  4 <br>';
 //      echo strtotime('+2 hour');
 //      echo '  5 ****** '; echo ' ****** ';
 //      echo date('Y-M-d H:i:s', strtotime('+2 hours'));
 //      echo '  6 <br>'; echo '<br>';
 //      echo strtotime(date('Y-M-d H:i:s', strtotime('+ 4 hours')));
 // echo '  9 ****** '; echo ' ****** ';
 //      echo date('Y-M-d H:i:s', strtotime('+ 4 hours'));


      //  echo '  7 ####### '; echo ' ######## ';
      //  echo date('d.m.Y H:i', 1600872960);

      //  echo '  8 Delivery Dte :';

      //   echo date('d.m.Y H:i', 1600880168);
      //  echo ' ####### '; echo ' ######## ';



      // exit;
  }



}



/* End of file install.php */
/* Location: ./system/application/controllers/install.php */
