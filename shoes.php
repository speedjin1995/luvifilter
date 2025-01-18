<?php
require_once 'php/includes/db_connect.php';
require_once 'php/includes/users.php';

session_start();

if(!isset($_SESSION['userDetail'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.html";</script>';
}
else{
    $todayDate = date("Y-m-d H:i:sa");
    
    $stmt = $db->prepare("SELECT * from shoes WHERE launch_date >= ? ORDER BY launch_date");
    $stmt->bind_param('s', $todayDate);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Shoes</h1>
			</div><!-- /.col -->
			<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="#">Home</a></li>
					<li class="breadcrumb-item active">Shoes</li>
				</ol>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row" id="productList">
            <?php while($row = $result->fetch_assoc()){ ?>
    			<div class="col-12 col-sm-4" id="productDetail" data-productId="<?=$row['product_id'] ?>" data-elId="<?=$row['early_link'] ?>">
    				<div class="card card-solid">
                        <div class="card-body">
    						<div class="row">
                                <div class="col-12">
                                    <h3 class="d-inline-block"><?=$row['product_name'] ?></h3>
                                    <div class="col-12"><img src="<?=$row['product_image'] ?>" class="product-image" alt="Product Image"></div>
                                </div>
                                <div class="col-12"><hr>
                                    <h4 class="mt-3">Size <small>Please select one</small></h4>
                                    <select class="form-control select2bs4" style="width: 100%;">
                                        <option disabled selected value> -- select an size -- </option>
                                        <?php $sizes = json_decode($row['sizes']); ?>
                                        <?php foreach ($sizes as $size) { ?>
                                            <?php if($size->level != 'OOS'){ ?>
                                                <option value="<?=$size->size ?>">UK <?=$size->size ?> (<?=$size->level ?>)</option>
                                            <?php } ?>  
                                        <?php } ?>
                                    </select>
    
                                    <div class="bg-gray py-2 px-3 mt-4">
                                        <h2 class="mb-0">RM <?=number_format($row['price'], 2, '.', ',') ?></h2>
                                        <h4 class="mt-0"><small>Launch Date: <?=$row['launch_date'] ?> </small></h4>
                                    </div>
    
                                    <div class="mt-4">
                                        <div class="btn btn-primary btn-lg btn-flat generate-el">
                                            <i class="fas fa-cart-plus fa-lg mr-2"></i> 
                                            Generate Early Link
                                        </div>
                                    </div>
                                    
                                    <div class="input-group mb-3">
										<input class="form-control" id="gsLink<?=$row['product_id'] ?>" readonly="" aria-invalid="false">
										<div class="input-group-append">
											<button class="input-group-text btn btn-success btn-sm copyToClipboard" style="color: #ffffff; background-color:#007bff; float: right;">
												<i class="fas fa-clipboard"></i>
											</button>
										</div>
									</div>
                                </div>
                            </div>
    					</div><!-- /.card-body -->
    				</div><!-- /.card -->
    			</div><!-- /.col -->
    		<?php } ?>
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<script>
$(function () {
    //Initialize Select2 Elements
    $('.select2').select2();

    //Initialize Select2 Elements
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });
    
    $('#productList').on('click', '.generate-el', function(){
        var nikeUrl = 'https://www.nike.com/my/launch/t/';
        var size = $(this).parents('#productDetail').find('.select2bs4').val();
        var productId = $(this).parents('#productDetail').attr('data-productId');
        var earlyLink = $(this).parents('#productDetail').attr('data-elId');
        var baseUrl = nikeUrl + earlyLink + '?productId=' + productId + '&size=' + size;
        var id = $(this).parents('#productDetail').find('[id^="gsLin"]').attr('id');
        $('#' + id).val(baseUrl);
    });
    
    $('#productList').on('click', '.copyToClipboard', function(){
        var earlyUrl = $(this).parents('.input-group').find('[id^="gsLin"]');
        /* Select the text field */
        earlyUrl.select();
        //earlyUrl.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        document.execCommand("copy");
    });
});

function copyToClipboard() {
    /* Get the text field */
    var copyText = document.getElementById(id);

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);
}
</script>
</body>
</html>
