<div class="site-branding-area">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <div class="logo">
                    <h1>
                        <a href="https://<?php echo $shop_data['domain']; ?>"><?php echo $shop_data['name']; ?></a>
                    </h1>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="shopping-item">
                            <a href="https://<?php echo $shop_data['domain']; ?>/cart">Cart  <i class="fa fa-shopping-cart"></i> </a>
                        </div>
            </div>
        </div>
    </div>
</div>
<!-- End site branding area -->

<div id="undefined-sticky-wrapper" class="sticky-wrapper" style="height: 60px;">
    <div class="mainmenu-area">
        <div class="container">
            <div class="row">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="<?php echo current_url(); ?>">Home</a></li>
                        <li><a href="<?php echo current_url(); ?>">Shop page</a></li>
                        <li><a href="https://<?php echo $shop_data['domain']; ?>/collections/">Categories</a></li>
                        
                        <?php foreach($pages as $page){ ?>
                            
                        <li><a href="https://<?php echo $shop_data['domain']; ?>/<?php echo $page['handle']; ?>"><?php echo $page['title']; ?></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End mainmenu area -->

    <div class="slider-area">
            <!-- Slider -->
            <div class="block-slider block-slider4">
                <ul class="" id="bxslider-home4">
                 <?php foreach($categories as $key=>$category){ ?>
                    <li>
                        <img src="<?php echo $category['image']['src']; ?>" width="614" height="194" alt="<?php echo $category['title']; ?>" />
                        <div class="caption-group" style="    background-color: #80808080;padding: 19px;">
                            <h2 class="caption title">
                                <?php echo $category['title']; ?>
                            </h2>
                            <h4 class="caption subtitle"><?php echo $category['body_html']; ?></h4>
                            <a class="caption button-radius" href="https://<?php echo $shop_data['domain']; ?>/collections/<?php echo $category['handle']; ?>"><span class="icon"></span>Shop now</a>
                        </div>
                    </li>
                <?php } ?>
                </ul>
            </div>
            <!-- ./Slider -->
    </div> <!-- End slider area -->






    <div class="single-product-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <?php foreach($products as $product){ ?>

                <div class="col-md-3 col-sm-6">
                    <div class="single-shop-product">
                        <div class="product-upper">
                            <img src="<?php echo $product['image']['src']; ?>" width="243" height="273" alt="">
                        </div>
                        <h2><a href="https://<?php echo $shop_data['domain']; ?>/products/<?php echo $product['handle']; ?>"><?php echo $product['title']; ?></a></h2>
                        <div class="product-carousel-price">
                            <ins>Vendor : <?php echo $product['vendor']; ?></ins>
                        </div>  
                        
                        <div class="product-option-shop">
                            <a class="add_to_cart_button" data-quantity="1" data-product_sku="" data-product_id="70" rel="nofollow" href="https://<?php echo $shop_data['domain']; ?>/products/<?php echo $product['handle']; ?>">View Details</a>
                        </div>                       
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <?php  if(isset($pagination) && $pagination!=""){ ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="product-pagination text-center">
                        <nav>
                          <ul class="pagination">
                          <?php if(isset($pagination['previous_page'])){ ?>  
                            <?php if($pagination['previous_page']=="" || $pagination['previous_page']['rel']=="next"){ ?>
                                <li class="disabled">
                                  <a href="javascript:;" aria-label="Previous" disabled>
                                    <span aria-hidden="true">&laquo; Previous</span>
                                  </a>
                                </li>
                            <?php }else{ ?>
                                <li>
                                  <a href="<?php echo current_url();?>?page_info=<?php echo $pagination['previous_page']['page_info']; ?>&rel=<?php echo $pagination['previous_page']['rel']; ?>" aria-label="Previous" >
                                    <span aria-hidden="true">&laquo; Previous</span>
                                  </a>
                                </li>
                            <?php } 
                            }?>

                            <?php if(isset($pagination['next_page'])){ ?>   
                            <?php if($pagination['next_page']=="" || $pagination['next_page']['rel']=="previous"){ ?>
                                <li class="disabled">
                                  <a href="" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                  </a>
                                </li>
                            <?php }else{ ?>
                                <li>
                                  <a href="<?php echo current_url();?>?page_info=<?php echo $pagination['next_page']['page_info']; ?>&rel=<?php echo $pagination['next_page']['rel']; ?>" aria-label="Next">
                                    <span aria-hidden="true">Next &raquo;</span>
                                  </a>
                                </li>
                            <?php }
                            } ?>
                          </ul>
                        </nav>                        
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>
    </div>




    <div class="footer-top-area">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-6">
                    <div class="footer-about-us">
                        <h2><?php echo $shop_data['name']; ?></h2>
                        <p>
                            Email Address : <?php echo $shop_data['email']; ?><br>
                            Phone No : <?php echo $shop_data['phone']; ?><br>
                            <?php if($shop_data['address1']!=""){ ?>
                             Address  : <?php echo $shop_data['address1']; ?><br>
                            <?php } ?>
                             <?php if($shop_data['address2']!=""){ ?>
                             Other Address : <?php echo $shop_data['address2']; ?><br>
                            <?php } ?>

                            City : <?php echo $shop_data['city']; ?><br>
                            Country : <?php echo $shop_data['country']; ?><br>

                        </p>
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">User Navigation </h2>
                        <ul>
                            <li><a href="#">My account</a></li>
                            <li><a href="#">Order history</a></li>
                            <li><a href="#">Wishlist</a></li>
                            <li><a href="#">Vendor contact</a></li>
                            <li><a href="https://<?php echo $shop_data['domain']; ?>">Front page</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Categories</h2>
                        <ul>
                            <?php foreach($categories as $key=>$category){ ?>
                                <li><a href="https://<?php echo $shop_data['domain']; ?>/collections/<?php echo $category['handle']; ?>"><?php echo $category['title']; ?></a></li>
                            <?php } ?>
                        </ul>                        
                    </div>
                </div>
                
     
            </div>
        </div>
    </div> <!-- End footer top area -->
    
    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="copyright">
                        <p>&copy; <?php echo date('Y'); ?> <?php echo $shop_data['name']; ?>. All Rights Reserved. <a href="https://<?php echo $shop_data['domain']; ?>" target="_blank"><?php echo $shop_data['domain']; ?></a></p>
                    </div>
                </div>
                
      
            </div>
        </div>
    </div> <!-- End footer bottom area -->
