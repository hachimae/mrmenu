<?php
$page = $this->input->get('page');
$view = $this->input->get('view');
$total = 0;

// admin/list.php
// ------------------------------------

// var_dump( $listCol );
// var_dump( $listData );

// defined global var
$allCol = count($listCol);
$allCol = $allCol>0 ? $allCol : 1;
?>

<?php if( !empty($title) ): ?>
<h3><?php echo $title; ?></h3>
<?php endif; ?>

<div id="list">
<!-- Top Button -->
<?php if( !empty($listTools) ):?>
<ul class="tools">
	<?php foreach($listTools as $key=>$tool): if($key=='edit') continue; if($key=='published') continue; if($key=='unpublished') continue; ?>
	<li class="<?php echo empty($tool['class']) ? $tool : 'tool-default'; ?>">
		<a class="btn" href="<?php echo $tool['action']; ?>" data-action="<?php echo strtolower($tool['name']); ?>"><?php echo $tool['name']; ?></a> 
		
	</li>
	<?php endforeach; ?>
	<!--------------- Start Search Box ----------------->
	<?php if( isset($search_enable) ): ?>
	<li class="tool-search">
		<form action="<?php echo $search_action; ?>" method="get">
			<label for="q">Enter keyword ...</label>
			<input type="text" name="q" id="q" value="<?php echo !empty($searchKeyword) ? $searchKeyword : ''; ?>" />
			<input type="hidden" name="col" value="<?php echo implode(',', $search_field); ?>" />
			<input type="submit" value="Search" class="btn" />
		</form>
	</li>
	<?php endif; ?>
	<!--------------- End Search Box ----------------->
</ul>
<?php endif; ?>
<!--------------- Start Showing Table Section ----------------->
<table cellpadding="0" cellspacing="0" border="0">
	<!--------------- Start THEAD Table Section ----------------->
	<?php if( !empty($listCol) ): ?>
	<thead>
	<tr>
	<?php foreach( $listCol as $key=>$col ): if( $col['class']=='col-id' ){ continue; } ?>
		<th class="<?php echo $col['class']; ?>"><?php 
		switch($col['type']){
			case 'tools':
			case 'empty':
				echo '&nbsp;';
				break;
			case 'check':
				//Display Checkbox
				echo '<input type="checkbox" id="check-all" />';
				break;
			default:
				//Display Head of column
				echo isset($col['name']) ? $col['name'] : $key;
				break;
		}
		?></th>
	<?php endforeach; ?>
	</tr>
	</thead>	
	<?php endif; ?>
	<!--------------- End THEAD Table Section ----------------->
<tbody>
<?php 
if( !empty($listData) ):
foreach((array)$listData as $row){
	echo '<tr>';
	foreach($listCol as $key=>$col){
		if( $col['class']=='col-id' ){
			continue;
		}
		echo '<td class="'.$col['class'].'">';
		switch($col['type']){
			case 'check':
				echo '<input type="checkbox" name="selected[]" value="'.$row['id'].'" />';
				break;
			case 'tools':
				//View for Order
				if($view == 'cancel'){
					$listTools['cancel'] = null;
					if(isset($listTools['cooking'])) echo '<a class="btn" href="'.$listTools['cooking']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['cooking']['view'].'" data-action="cooking">Re-Cooking</a>';
				}else if($view == 'cooking'){
					$listTools['cooking'] = null;
					if(isset($listTools['finished'])) echo '<a class="btn" href="'.$listTools['finished']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['finished']['view'].'" data-action="finished">Finished</a>';
				}else if($view == 'finished'){
					$listTools['cancel'] = null;
				}else{
					if(isset($listTools['cooking'])) echo '<a class="btn" href="'.$listTools['cooking']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['cooking']['view'].'" data-action="cooking">Cooking</a>';
				}
				//view for Menu
				if($view == 'unpublished'){
					if(isset($listTools['published'])) echo '<a class="btn" href="'.$listTools['published']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['published']['view'].'" data-action="published">Published</a>';
				}else if($view == 'published'){
					if(isset($listTools['unpublished'])) echo '<a class="btn" href="'.$listTools['unpublished']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['unpublished']['view'].'" data-action="unpublished">Unpublished</a>';
				}else if($view == 'deleted'){
					$listTools['remove'] = null;
					if(isset($listTools['published'])) echo '<a class="btn" href="'.$listTools['published']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['published']['view'].'" data-action="published">Published</a>';
				}
				if(isset($listTools['edit'])) echo '<a class="btn" href="'.$listTools['edit']['action'].'/'.$row['id'].'" data-action="edit">Edit</a>';
				//For order detail

				if(isset($listTools['cancel'])) echo '<a class="btn" href="'.$listTools['cancel']['action'].'/'.$row['id'].'?page='.$page.'&view='.$listTools['cancel']['view'].'" data-action="cancel">Cancel</a>';
				
				if(isset($row['dish_status']) and ($row['dish_status'] <> 'receive'))
				{
					if(isset($listTools['receive'])) echo '<a class="btn" href="'.$listTools['receive']['action'].'/'.$row['id'].'" data-action="receive">receive again</a>';
				}
				if(isset($listTools['remove'])) echo '<a class="btn" href="'.$listTools['remove']['action'].'/'.$row['id'].'" data-action="remove">Remove</a>';
				
				break;
            case 'order-detail':
				echo '<a href="/app/order/detail/'.$row['id'].'">'.$row[$key].'</a>';
				
                break;
            case 'option-detail':
            	//Calculate Total price
				echo '<a href="/app/order/option/'.$row['id'].'">'.$row[$key].'</a>';
				//Calcualte only non-cancel dish
				if($row['dish_status'] <> 'cancel')
				{
					$total = $total + ($row['price']*$row['quantity'])  ;
				}
				
                break;
			case 'normal':
			default:
				if(($row[$key] == 'cancel') or ($row[$key] == 'deleted')){
					echo '<span style="color:#AE1215;">'.$row[$key].'</span>';
				}else if($row[$key] == 'cooking'){
					echo '<span style="color:#FF7200;">'.$row[$key].'</span>';
				}else if($row[$key] == 'finished'){
					
					echo '<span style="color:#39B330;">'.$row[$key].'</span>';
				}else{
					echo $row[$key];
				}
				
				
				
				break;
		}
		echo '</td>';
	}
	echo '</tr>';
}

else: 
?>
	<tr><td class="error empty-data" colspan="<?php echo $allCol; ?>">empty</td></tr>
<?php endif; ?>

</tbody>
<?php if( !empty($listPage) ): ?>
<tfoot>
	<tr>
		<td class="pagination actions" colspan="<?php echo $allCol; ?>"><?php echo $listPage; ?></td>
	</tr>
</tfoot>
<?php endif; ?>

</table>
<?php if(isset($listMeta) and ($listMeta['thispage'] == 'detail') ){ ?>
	<div class='total'>
	 <?php 
		//Total means Price + Addion Price + Vat + Charge
		// calVat();
		// calCharge();
		$additon_price = 0;
		foreach ($listMeta['addition_price'] as $key=>$value)
		{
			$additon_price += $value;
		}
		
		$total = $total + $additon_price;
		$charge =  ($total*$listMeta['charge'])/100;
		$vat =  ($total*$listMeta['vat'])/100;
		$cess = 0;
		$currency = " THB";
		
		echo 'Charge '.$listMeta['charge'].'% : '.$charge.' THB<br>';
		echo 'Vat '.$listMeta['vat'].' % : '.$vat.' THB<br>';
		echo 'Total : '.($total + $charge + $vat + $cess).$currency; 
		echo '<br>';	
		?>
	</div>
<?php } ?>
<!--------------- End Showing Table Section ----------------->
<?php if( !empty($listMeta) ){ ?>
<a href="../<?php echo $listMeta['prepage']; ?>/<?php echo $listMeta['num']; ?>"> Back </a>
<?php } ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	
	var checkAll = $('#check-all'),
		checkCol = $('.col-check input'),
		toolsButton = $('.tools a');

	checkAll.length && (function($, checkAll, checkCol){
		
		checkAll.click(function(){
			if( $(this).attr('checked') ){
				checkCol.attr('checked', true);
			}else{
				checkCol.attr('checked', false);
			}
		});

	})(jQuery, checkAll, checkCol);

	toolsButton.length && (function($, toolsButton){
		
		toolsButton.click(function(e){
			switch( $(this).data('action') ){
				case 'add':
					break;
				case 'remove':
					e.preventDefault();

					var dataValue = 'action=multiRemove&_='+Math.random();
					$('tbody .col-check input:checked').each(function(){
						dataValue += '&'+$(this).attr('name')+'='+$(this).val();
					});
					//alert(dataValue);
					$.ajax({
						url: $(this).attr('href'),
						data: dataValue,
						type: 'post',
						dataType: 'json',
						success: function(response){
							if( response.success ){
								window.location.reload();
							}else{
								alert( response.error );
							}
						}
					});
					break;
				case 'receive':
					e.preventDefault();
					
					var dataValue = 'action=multiReceive&_='+Math.random();
					$('tbody .col-check input:checked').each(function(){
						dataValue += '&'+$(this).attr('name')+'='+$(this).val();
					});
					
					$.ajax({
						url: $(this).attr('href'),
						data: dataValue,
						type: 'post',
						dataType: 'json',
						success: function(response){
							if( response.success ){
								window.location.reload();
							}else{
								alert( response.error );
							}
						}
					
					});					
					break;
				case 'cooking':
					e.preventDefault();
					
					var dataValue = 'action=multiCooking&_='+Math.random();
					$('tbody .col-check input:checked').each(function(){
						dataValue += '&'+$(this).attr('name')+'='+$(this).val();
					});
					
					$.ajax({
						url: $(this).attr('href'),
						data: dataValue,
						type: 'post',
						dataType: 'json',
						success: function(response){
							if( response.success ){
								window.location.reload();
							}else{
								alert( response.error );
							}
						}
					});					
					break;
				case 'finished':
					e.preventDefault();
					
					var dataValue = 'action=multiFinished&_='+Math.random();
					$('tbody .col-check input:checked').each(function(){
						dataValue += '&'+$(this).attr('name')+'='+$(this).val();
					});
					
					$.ajax({
						url: $(this).attr('href'),
						data: dataValue,
						type: 'post',
						dataType: 'json',
						success: function(response){
							if( response.success ){
								window.location.reload();
							}else{
								alert( response.error );
							}
						}
					});					
					break;
				case 'print':
					e.preventDefault();

					if( $('tbody .col-check input:checked').length<=0 ){
						alert('You must select desk to print QRCode.');
						return false;
					}

					var dataValue = '';
					$('tbody .col-check input:checked').each(function(){
						dataValue += '&id[]='+$(this).val();
					});
					var dataUrl = $(this).attr('href')+'?'+dataValue;

					window.open(dataUrl, 'PrintQRCode', 'width=800,height=800,status=no,location=no,menuBar=no');
					break;
				default: 
					e.preventDefault();
					break;
			}
		});

	})(jQuery, toolsButton);

});
</script>