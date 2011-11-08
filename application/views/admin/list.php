<?php

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
<?php if( !empty($listTools) ): ?>
<ul class="tools">
	<?php foreach($listTools as $key=>$tool): if($key=='edit') continue; ?>
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
				if(isset($listTools['edit'])) echo '<a class="btn" href="'.$listTools['edit']['action'].'/'.$row['id'].'" data-action="edit">Edit</a>';
				if(isset($listTools['change'])) echo '<a class="btn" href="'.$listTools['change']['action'].'/'.$row['id'].'" data-action="change">Change</a>';
				if(isset($listTools['remove'])) echo '<a class="btn" href="'.$listTools['remove']['action'].'/'.$row['id'].'" data-action="remove">Remove</a>';
				if(isset($listTools['cooking'])) echo '<a class="btn" href="'.$listTools['cooking']['action'].'/'.$row['id'].'" data-action="cooking">Cooking</a>';
				if(isset($listTools['cancel'])) echo '<a class="btn" href="'.$listTools['cancel']['action'].'/'.$row['id'].'" data-action="cancel">Cancel</a>';
				
				break;
            case 'order-detail':
				echo '<a href="/app/order/detail/'.$row['id'].'">'.$row[$key].'</a>';
                break;
			case 'normal':
			default:
				echo $row[$key];
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
<tbody>

<?php if( !empty($listPage) ): ?>
<tfoot>
	<tr>
		<td class="pagination actions" colspan="<?php echo $allCol; ?>"><?php echo $listPage; ?></td>
	</tr>
</tfoot>
<?php endif; ?>

</table>
<!--------------- End Showing Table Section ----------------->
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