<?php
/*
Addon Name: Shopify Integration
Unique Name: shopify_integration

Modules:
{
   "29663":{
      "bulk_limit_enabled":"0",
      "limit_enabled":"1",
      "extra_text":"",
      "module_name":"Shopify Integration"
   }
}
Project ID: 250
Addon URI: https://xerochat.com
Author: Xerone IT
Author URI: http://xeroneit.net
Version: 1
Description: Import Shopify products to sell inside Messenger using webview and export products as Ecommerce product
*/

require_once("application/controllers/Home.php"); // loading home controller
require APPPATH . 'modules/shopify_integration/inc/functions.php';
use Automattic\WooCommerce\Client;

class Shopify_integration extends Home
{
	public $addon_data=array();

	public function __construct() 
	{
		parent::__construct();
		// getting addon information in array and storing to public variable
		// addon_name,unique_name,module_id,addon_uri,author,author_uri,version,description,controller_name,installed
		//------------------------------------------------------------------------------------------
		$addon_path=APPPATH."modules/".strtolower($this->router->fetch_class())."/controllers/".ucfirst($this->router->fetch_class()).".php"; // path of addon controller
		$addondata=$this->get_addon_data($addon_path);
		$this->addon_data=$addondata;

		$function_name=$this->uri->segment(2);
		if($function_name!="store" && $function_name!="product")
		{
		      // all addon must be login protected
		      //------------------------------------------------------------------------------------------
		      if ($this->session->userdata('logged_in')!= 1) redirect('home/login', 'location');
		      $this->member_validity();       
		      // if you want the addon to be accessed by admin and member who has permission to this addon
		      //-------------------------------------------------------------------------------------------
		      if(isset($addondata['module_id']) && is_numeric($addondata['module_id']) && $addondata['module_id']>0)
		      {
		           if($this->session->userdata('user_type') != 'Admin' && !in_array($addondata['module_id'],$this->module_access))
		           {
		                redirect('home/login_page', 'location');
		                exit();
		           }
		      }
		}
		$this->load->helper("ecommerce");
		
	}

	public function index()
	{
	    $data['page_title'] = $this->lang->line('Shopify Integration');
	    $data['body'] = 'shopify_app_settings';

	    $where_custom = '';
	    $where_custom="user_id = ".$this->user_id;
	    $table="shopify_config";
	    $this->db->where($where_custom);
	    $data['info']=$this->basic->get_data($table,$where='',$select='',$join='','','','id desc');
	
	    $this->_viewcontroller($data);
	}


	public function add_shopify_settings()
	{
	    $data['table_id'] = 0;
	    $data['shopify_settings'] = array();
	    $data['page_title'] = $this->lang->line('Connect Shopify API');
	    $data['body'] = 'shopify_settings';

	    $this->_viewcontroller($data);
	}
	
	
	
	public function auth_shopify_confirm()
	{
	   $shop= $this->input->get('shop', TRUE);
	   $token= $this->input->get('token', TRUE);
	   if($shop=="" || $token==""){
	       echo "Unauthorized";
	       die();
	   }
	   $insert_data['access_token']=$token;
	   $this->basic->update_data('shopify_config', array('home_url' => $shop,"user_id"=>$this->user_id), $insert_data);
	  
	   header("Location: /shopify_integration");
			die();

	}


	public function edit_shopify_settings($table_id=0)
	{
	    
	    if($table_id==0) exit;
	    $shopify_settings = $this->basic->get_data('shopify_config',array("where"=>array("id"=>$table_id,"user_id"=>$this->user_id)));
	    if (!isset($shopify_settings[0])) $shopify_settings = array();
	    else $shopify_settings = $shopify_settings[0];
	    $data['table_id'] = $table_id;
	    $data['shopify_settings'] = $shopify_settings;
	    $data['page_title'] = $this->lang->line('Connect Shopify API');
	    $data['body'] = 'shopify_settings';

	    $this->_viewcontroller($data);
	}


	public function shopify_settings_update_action()
	{


	    if (!isset($_POST)) exit;
	    $this->form_validation->set_rules('home_url', $this->lang->line("Website Home URL"), 'trim|required');
	    $table_id = $this->input->post('table_id',true);

	    if ($this->form_validation->run() == FALSE) 
	    {
	        if($table_id == 0) $this->add_shopify_settings();
	        else $this->edit_shopify_settings($table_id);
	    }
	    else
	    {
	       if($table_id == 0){
    	        $this->csrf_token_check();
            	$home_url = strip_tags($this->input->post('home_url',true));
            	$home_url = "https://".preg_replace("(^https?://)", "", $home_url );
            	 $insert_data['home_url'] = $home_url;
            	    $insert_data['user_id'] = $this->user_id;
            	$this->basic->insert_data('shopify_config', $insert_data);     
    	        redirect("https://shopify.marketingkr.com/install.php?shop=".$home_url);	        
	       }else{
	            $this->csrf_token_check();
            	$home_url = strip_tags($this->input->post('home_url',true));
            	$home_url = "https://".preg_replace("(^https?://)", "", $home_url );
            	 $insert_data['home_url'] = "https://".$home_url;
            	    $insert_data['access_token'] = "";  
            	$this->basic->update_data('shopify_config', array('id' => $table_id,"user_id"=>$this->user_id), $insert_data);
    	        redirect("https://shopify.marketingkr.com/install.php?shop=".$home_url);
	       }
	    }
	}




	public function product_list($id=0)
	{
	  $data['body'] = 'product_list';
	  $data['page_title'] = $this->lang->line('Product');
	  $data["iframe"]="1";	  
	  $data["config_id"]=$id;
	  $this->_viewcontroller($data);
	}


	public function product_list_data()
	{ 
	  $this->ajax_check();
	  $shopify_settings = $this->basic->get_data('shopify_config',array("where"=>array("id"=>$this->input->post("shopify_config_id"),"user_id"=>$this->user_id)));
	   if (isset($shopify_settings[0])){
	   		$shop = $shopify_settings[0]['home_url'];
			$token = $shopify_settings[0]['access_token'];
			 $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
			  $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
			  $limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
			  $sort_index = isset($_POST['order'][0]['column']) ? strval($_POST['order'][0]['column']) : 3;
			  $sort = isset($display_columns[$sort_index]) ? $display_columns[$sort_index] : 'product_name';
			  $order = isset($_POST['order'][0]['dir']) ? strval($_POST['order'][0]['dir']) : 'asc';
			  $order_by=$sort." ".$order;

			$query = array(
				"Content-type" => "application/json"
			);
			$total_result=shopify_call($token, $shop, "/admin/api/2021-07/products/count.json", array(), 'GET');
			$total_result=json_decode($total_result['response'], TRUE);
			$total_result=$total_result['count'];

			$products = shopify_call($token, $shop, "/admin/api/2021-07/products.json", array(), 'GET');
			$info = json_decode($products['response'], TRUE);
			$info=$info['products'];
			 $display_columns = 
			  array(
			    "#",
			    "thumbnail",
			    'product_name',
			    'tags',
			    'vendor',
			    'actions',
			    'updated_at',
			  );
			$data=array();
			 foreach($info as $key => $value) 
			  {
			      $updated_at = date("M j, y H:i",strtotime($info[$key]['updated_at']));
			      $info[$key]['updated_at'] =  "<div style='min-width:110px;'>".$updated_at."</div>";
			      $link = $shopify_settings[0]['home_url']."/products/".$info[$key]['handle'];
			      $actions = "<a target='_BLANK' href='".$link."' title='".$this->lang->line("Product Page")."' data-toggle='tooltip' class='btn btn-circle btn-outline-info'><i class='fas fa-eye'></i></a>";      

			      $info[$key]['actions'] = $actions;	    

			      if($info[$key]['image']=='') $url = base_url('assets/img/products/product-1.jpg');
			      else $url = $info[$key]['image']['src'];
			      $info[$key]['thumbnail'] = "<a  target='_BLANK' href='".$link."'><img class='img-fluid' style='height:80px;width:80px;border-radius:4px;border:1px solid #eee;padding:2px;' src='".$url."'></a>";
			      $info[$key]['product_name'] = "<a  target='_BLANK' href='".$link."'>".$info[$key]['title']."</a>";
			      $tags=explode(",",$info[$key]['tags']);
			      $newTag="";
			      foreach($tags as $tag){
			      	$newTag.='<span class="badge badge-info">'.$tag.'</span>';
			      }
			      $info[$key]['tags']=$newTag;
			  }
			  $data['recordsTotal'] = $total_result;
			  $data['recordsFiltered'] = $total_result;
			$data['data']=convertDataTableResult($info, $display_columns ,$start,$primary_key="id");
			echo json_encode($data);
	   }
	  
	}






	public function delete_action()
    {

      $this->ajax_check();
      $this->csrf_token_check();
      $app_table_id = $this->input->post('app_table_id',true);
      $app_info = $this->basic->get_data('shopify_config',array('where'=>array('id'=>$app_table_id,'user_id'=>$this->user_id)));
      if(empty($app_info))
      {
        $response['status'] = 0;
        $response['message'] = $this->lang->line('We could not find any API.');  
        echo json_encode($response);
        exit;
      }
      
      $this->basic->delete_data('shopify_config',array('id'=>$app_table_id,'user_id'=>$this->user_id));
      $response['status'] = 1;
      $response['message'] = $this->lang->line("Shopify API has been deleted successfully.");  
      echo json_encode($response);
    }


    public function store($id=0)
    {
      if($id==0) exit();
      $where_simple = array("shopify_config.id"=>$id);
      $where = array('where'=>$where_simple);
      $store_data = $this->basic->get_data("shopify_config",$where);

      if(!isset($store_data[0]))
      {
        echo '<br/><h2 style="border:1px solid red;padding:15px;color:red">'.$this->lang->line("Store not found.").'</h2>';
        exit();
      }
      $shop = $store_data[0]['home_url'];
			$token = $store_data[0]['access_token'];
      $user_id = $store_data[0]['user_id'];
      
      $shop_data=shopify_call($token, $shop, "/admin/api/2021-07/shop.json", array(), 'GET');
			$shop_data=json_decode($shop_data['response'], TRUE);
			$shop_data=$shop_data['shop'];

			$smart_collections=shopify_call($token, $shop, "/admin/api/2021-07/smart_collections.json", array(), 'GET');
			$smart_collections=json_decode($smart_collections['response'], TRUE);
			$smart_collections=$smart_collections['smart_collections'];


			$custom_collections=shopify_call($token, $shop, "/admin/api/2021-07/custom_collections.json", array(), 'GET');
			$custom_collections=json_decode($custom_collections['response'], TRUE);
			$custom_collections=$custom_collections['custom_collections'];


			$categories=array_merge($smart_collections,$custom_collections);


			$pages=shopify_call($token, $shop, "/admin/api/2021-07/pages.json", array(), 'GET');
			$pages=json_decode($pages['response'], TRUE);
			$pages=$pages['pages'];

			$rel = '';
			if(isset($_GET['rel'])){
				$rel=$_GET['rel'];
			}
			$page_info = '';
			if(isset($_GET['page_info'])){
				$page_info=$_GET['page_info'];
			}
			$array = array(
					'limit' => 30,
					'page_info' => $page_info,
					'rel' => $rel
				);
			$products=shopify_call($token, $shop, "/admin/api/2021-07/products.json", $array, 'GET');
			$header_product="";
			$pagination=array();
			if(isset($products['headers']['link'])){
				$header_product=$products['headers']['link'];
				$header_product=explode(",",$header_product);
				
				if(count($header_product)==2){
				    $pagination=array(
				        "previous_page"=>get_headers_from_curl_response_shopify_product($header_product[0]),
				        "next_page"=>get_headers_from_curl_response_shopify_product($header_product[1]),
				        );
				}else if(count($header_product)==1){
						$pager=get_headers_from_curl_response_shopify_product($header_product[0]);
					
				    if($pager['rel']="next"){
					    $pagination=array(
					        "previous_page"=>"",
					        "next_page"=>$pager,
					        );
				  	}else if($pager['rel']="previous"){
				  		$pagination=array(
					        "previous_page"=>$pager,
					        "next_page"=>"",
					        );
				  	}
				}
			}





			$products=json_decode($products['response'], TRUE);
			$products=$products['products'];
				
				
	

      $data = array('body'=>"store_single","page_title"=>$shop_data['name']." | Store","shop_data"=>$shop_data,"categories"=>$categories,"pages"=>$pages,"products"=>$products,"pagination"=>$pagination);

      $this->load->view('bare-theme', $data);
    }

    public function product($product_id=0)
    {
      if($product_id==0) exit();      
      $where_simple = array("woocommerce_product.id"=>$product_id,"woocommerce_product.status"=>"1");
      $where = array('where'=>$where_simple);
      $join = array(' woocommerce_config'=>"woocommerce_product.woocommerce_config_id=woocommerce_config.id,left");  
      $select = array("woocommerce_product.*","currency_icon","currency_position","decimal_point","thousand_comma","attributes","categories");   
      $product_data = $this->basic->get_data("woocommerce_product",$where,$select,$join);

      if(!isset($product_data[0]))
      {
        echo '<br/><h1 style="text-align:center">'.$this->lang->line("Product not found.").'</h1>';
        exit();
      }
      
      $update_visit_count_sql = "UPDATE woocommerce_product SET visit_count=visit_count+1 WHERE id=".$product_id;
      $this->basic->execute_complex_query($update_visit_count_sql);

      $user_id = isset($product_data[0]["user_id"]) ? $product_data[0]["user_id"] : 0;
      $data = array('body'=>"product_single","page_title"=>$product_data[0]['product_name']);

      $data["product_data"] = $product_data[0];
      $data['current_product_id'] = isset($product_data[0]['id']) ? $product_data[0]['id'] : 0;
      $data['current_store_id'] = isset($product_data[0]['woocommerce_config_id']) ? $product_data[0]['woocommerce_config_id'] : 0;

      $this->load->view('bare-theme', $data);
    }

    private function get_product_list_array($shopify_config_id=0,$default_where="",$order_by="")
    {
      $where_simple = array("woocommerce_config_id"=>$woocommerce_config_id);
      if(isset($default_where['product_name'])) {
        $product_name = $default_where['product_name'];
        $this->db->where(" product_name LIKE "."'%".$product_name."%'");
        unset($default_where['product_name']);
      }
      if(is_array($default_where) && !empty($default_where))
      {
        foreach($default_where as $key => $value) 
        {
          $where_simple[$key] = $value;
        }
      }      
      if($order_by=="") $order_by = "product_name ASC";     
      $product_list = $this->basic->get_data("shopify_product",array("where"=>$where_simple),$select='',$join='',$limit='',$start=NULL,$order_by);
      
      // echo $this->db->last_query();
      return $product_list;
    }

    public function copy_url($id=0)
    {
      $data['product_list'] = $this->get_product_list_array($id);
      $where_simple = array("woocommerce_config.id"=>$id);
      $where = array('where'=>$where_simple);
      $store_data = $this->basic->get_data("shopify_config",$where);
      if(!isset($store_data[0])) exit();
      $data["store_data"] = $store_data[0];
      $data['body'] = "copy_url";
      $data['iframe'] = "1";
      $this->_viewcontroller($data);
    }



	public function activate()
	{
		$this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        $purchase_code=$this->input->post('purchase_code');
        $this->addon_credential_check($purchase_code,strtolower($addon_controller_name)); // retuns json status,message if error
        
        //this addon system support 2-level sidebar entry, to make sidebar entry you must provide 2D array like below
        $sidebar=array(); 
        // mysql raw query needed to run, it's an array, put each query in a seperate index, create table query must should IF NOT EXISTS
        $sql=
        array
        (
        	0 => "INSERT INTO `menu` (`name`, `icon`, `url`, `serial`, `module_access`, `have_child`, `only_admin`, `only_member`, `add_ons_id`, `is_external`, `header_text`, `is_menu_manager`, `custom_page_id`) VALUES('Shopify Integration', 'fab fa-shopify', 'shopify_integration', (SELECT serial FROM menu as menu2 WHERE url='ecommerce'), '29663', '0', '0', '0', (SELECT id FROM add_ons WHERE project_id='250'), '0', '', '0', 0);",
            1 => "CREATE TABLE `shopify_config` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL , `access_token` TEXT NULL , `home_url` TEXT NOT NULL , `currency` TEXT NOT NULL DEFAULT 'USD' , `currency__format` TEXT NOT NULL DEFAULT '$ {{amount}} USD' , `custom_collections` LONGTEXT NULL , `smart_collections` LONGTEXT NULL , `last_updated_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
        );
        //send blank array if you does not need sidebar entry,send a blank array if your addon does not need any sql to run
        $this->register_addon($addon_controller_name,$sidebar,$sql,$purchase_code);
    }


    public function deactivate()
    {        
    	$this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]
        // only deletes add_ons,modules and menu, menu_child1 table entires and put install.txt back, it does not delete any files or custom sql
        $this->unregister_addon($addon_controller_name);      
    }

    public function delete()
    {        
    	$this->ajax_check();

        $addon_controller_name=ucfirst($this->router->fetch_class()); // here addon_controller_name name is Comment [origianl file is Comment.php, put except .php]

        // mysql raw query needed to run, it's an array, put each query in a seperate index, drop table/column query should have IF EXISTS
        $sql=array
        (       
        	0=> "DELETE FROM `menu` WHERE `url` = 'shopify_integration'",
        	1=> "DROP TABLE IF EXISTS `shopify_config`;"
        );  
        
        // deletes add_ons,modules and menu, menu_child1 table ,custom sql as well as module folder, no need to send sql or send blank array if you does not need any sql to run on delete
        $this->delete_addon($addon_controller_name,$sql);         
    }

 


}