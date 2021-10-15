<style>
	.blue{
		color: #2C9BB3 !important;
	}
</style>

<section class="section">
	<div class="section-header">
		   <h1><i class="fab fa-shopify"></i> <?php echo $page_title; ?></h1>
		   <div class="section-header-breadcrumb">
		     <div class="breadcrumb-item"><a href="<?php echo base_url('shopify_integration'); ?>"><?php echo $this->lang->line("Shopify Integration"); ?></a></div>
		     <div class="breadcrumb-item"><?php echo $page_title; ?></div>
		   </div>
	</div>

	
 	<?php $this->load->view('admin/theme/message');?>


	
	<div class="section-body">
	  <div class="row">
	    <div class="col-12">
	        <form action="<?php echo base_url("shopify_integration/shopify_settings_update_action"); ?>" method="POST">
	        <input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo $this->session->userdata('csrf_token_session'); ?>">
        	<input type="hidden" name="table_id" value="<?php echo $table_id ?>">
	        <div class="card">
	          <div class="card-header"><h4 class="card-title"><i class="fas fa-plus-circle"></i> <?php echo $this->lang->line("Shopify API Settings"); ?></h4></div>
	          <div class="card-body"> 
                <p><?php echo $this->lang->line("Enter Shopify Url like https://test2021fa.myshopify.com . After Clicking Save button you will be redirected to install our Public to installation page of our public app in your store."); ?> </p>   
	              <div class="row">
	               
		           
		                <div class="col-12">
		                  <div class="form-group">
		                    <label for=""><?php echo $this->lang->line("Website Home URL");?> *</label>
		                    <input name="home_url" value="<?php echo isset($shopify_settings['home_url']) ? $shopify_settings['home_url'] : set_value('home_url'); ?>" class="form-control" type="text">  
		                    <span class="red"><?php echo form_error('home_url'); ?></span>
		                  </div>
		                </div>
	              </div>
	          </div>

	          <div class="card-footer bg-whitesmoke">
	            <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> <?php echo $this->lang->line("Save & Proceed to Install App");?></button>
	            <button class="btn btn-secondary btn-lg float-right" onclick='goBack("shopify_integration")' type="button"><i class="fa fa-remove"></i>  <?php echo $this->lang->line("Cancel");?></button>
	          </div>
	        </div>
	      </form>
	    </div>
	  </div>
	</div>
	   				

</section>