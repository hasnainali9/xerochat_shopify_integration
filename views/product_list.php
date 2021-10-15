<section class="section section_custom pt-1">
    
  <?php $this->load->view('admin/theme/message'); ?>

  <div class="section-body">
    <div class="row">
      <div class="col-12">
        <div class="card no_shadow">
          <div class="card-body data-card p-0 pt-1 pr-3">
            <div class="row">
              <div class="col-7 col-md-9">
                <?php echo 
                '<div class="input-group mb-3" id="searchbox">
                  <div class="input-group-prepend d-none">
                  <input type="hidden" class="form-control" id="search_store_id" autofocus name="search_store_id" value="'.$config_id.'">
                  </div>
                  <input type="text" class="form-control" id="search_value" autofocus name="search_value" placeholder="'.$this->lang->line("Search...").'" style="max-width:400px;">
                  <div class="input-group-append">
                    <button class="btn btn-primary" type="button" id="search_action"><i class="fas fa-search"></i> <span class="d-none d-sm-inline">'.$this->lang->line("Search").'</span></button>
                  </div>
                </div>'; ?>                                          
              </div>          

   
            </div>

            <div class="table-responsive2">
                <input type="hidden" id="put_page_id">
                <table class="table table-bordered" id="mytable">
                  <thead>
                    <tr>
                      <th>#</th>      
                      <th><?php echo $this->lang->line("Thumb")?></th>                   
                      <th><?php echo $this->lang->line("Product")?></th>                   
                      <th><?php echo $this->lang->line("Tags")?></th> 
                      
                         <th><?php echo $this->lang->line("Vendor")?></th> 
                         
                      <th><?php echo $this->lang->line("Actions")?></th>                     
                      <th><?php echo $this->lang->line("Updated at")?></th>                   
                  	</tr>
                  </thead>
                </table>
            </div>
          </div>
        </div>
      </div>       
        
    </div>
  </div>          

</section>



<script>

	var base_url="<?php echo site_url(); ?>";

	var perscroll;
	var table1 = '';
  var shopify_config_id = '<?php echo $config_id;?>';
	table1 = $("#mytable").DataTable({
	  serverSide: true,
	  processing:true,
	  bFilter: false,
	  order: [[ 3, "asc" ]],
	  pageLength: 10,
	  ajax: {
	      url: base_url+'shopify_integration/product_list_data',
	      type: 'POST',
	      data: function ( d )
	      {
	          d.shopify_config_id = shopify_config_id;
	      }
	  },
	  language: 
	  {
	    url: "<?php echo base_url('assets/modules/datatables/language/'.$this->language.'.json'); ?>"
	  },
	  dom: '<"top"f>rt<"bottom"lip><"clear">',
	  columnDefs: [	   
	    {
	        targets: [2,4,5,6],
	        className: 'text-center'
	    },
	    {
	        targets: [1,2,5],
	        sortable: false
	    }
	  ],
	  fnInitComplete:function(){  // when initialization is completed then apply scroll plugin
	         if(areWeUsingScroll)
	         {
	           if (perscroll) perscroll.destroy();
	           perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	         }
	     },
	     scrollX: 'auto',
	     fnDrawCallback: function( oSettings ) { //on paginition page 2,3.. often scroll shown, so reset it and assign it again 
	         if(areWeUsingScroll)
	         { 
	           if (perscroll) perscroll.destroy();
	           perscroll = new PerfectScrollbar('#mytable_wrapper .dataTables_scrollBody');
	         }
	     }
	});


	$("document").ready(function(){	   

      	$(document).on('keypress', '#search_value', function(e) {
        	if(e.which == 13) $("#search_action").click();
      	});

      	$(document).on('click', '#search_action', function(event) {
        	event.preventDefault(); 
        	table1.draw();
      	});

 

       

	});

</script>




<style type="text/css">
  ins{text-decoration: none;}
</style>
