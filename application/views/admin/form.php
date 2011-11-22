<?php

// display form
// -----------------------------

?>
<?php if(isset($title)): ?><h3><?php echo $title; ?></h3><?php endif; ?>

<form action="<?php echo $form['action']; ?>" method="post" class="form-area" enctype="multipart/form-data">

<?php if(isset($formError)): ?>
<div class="form-error"><?php echo $formError; ?></div>
<?php endif; ?>

<?php 
// var_dump( $form['field'] );
foreach($form['field'] as $name=>$item): 

$value = !empty($item['value']) ? $item['value'] : false;
$class = !empty($item['class']) ? $item['class'] : false;

if( $item['type']=='hidden' ){
	echo '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	continue;
}

?>
<div class="form-row">
	<label for="<?php echo $name; ?>"><?php echo $item['label']; ?></label>
	<?php 
	switch($item['type']){
		case 'price_value':
			echo '<div class="input" id="price_value">';
			echo '<ul class="inputs-list">';
			echo '<li class="add-more"><a href="#" class="btn info">Add more</a></li>';
				
			if( isset($item['value']) ){
				foreach($item['value'] as $value){
					echo '<li><input type="text" name="meta_name[]" class="mini" placeholder="Name" value="'.$value['name'].'" />&nbsp; ';
					echo '<select name="meta_pricetype[]" class="mini"><option value="inc">+</option><option value="dec"'.($value['type']=='dec' ? ' selected="selected"' : '').'>-</option></select>&nbsp; ';
					echo '<input type="text" name="meta_price[]" class="mini" placeholder="Price" value="'.$value['value'].'" />';
					echo '<input type="hidden" name="meta_value[]" value="'.$value['id'].'" />';
					echo '<a href="#" class="btn remove-row">Remove</a>';
					echo '</li>';
				}
			}else{
				echo '<li><input type="text" name="meta_name[]" class="mini" placeholder="Name" />&nbsp; ';
				echo '<select name="meta_pricetype[]" class="mini"><option value="inc">+</option><option value="dec">-</option></select>&nbsp; ';
				echo '<input type="text" name="meta_price[]" class="mini" placeholder="Price" />';
				echo '<a href="#" class="btn remove-row">Remove</a>';
				echo '</li>';
			}

			echo '</ul>';
			echo "
			<script type=\"text/javascript\">
			$(document).ready(function(){
				
				/** optional price */
				var priceValue = $('#price_value');
				priceValue && (function($, priceValue){
					
					priceValue.find('.add-more a').live('click', function(e){
						e.preventDefault();
						priceValue.find('ul').append('<li><input type=\"text\" name=\"meta_name[]\" class=\"mini\" placeholder=\"Name\">&nbsp; <select name=\"meta_pricetype[]\" class=\"mini\"><option value=\"inc\">+</option><option value=\"dec\">-</option></select>&nbsp; <input type=\"text\" name=\"meta_price[]\" class=\"mini\" placeholder=\"Price\"><a href=\"#\" class=\"btn remove-row\">Remove</a></li>');
						$.fancybox.resize();
					});
			
					priceValue.find('.remove-row').live('click', function(e){
						e.preventDefault();
						$(this).parent('li').remove();
						$.fancybox.resize();
					});
			
				})(jQuery, priceValue);
			
			});
			</script>
			";
			echo '</div>';
			break;
		case 'media':
			echo '<div class="input">';
			echo '<input type="file" name="'.$name.'" id="'.$name.'" class="media-uploader" />';
			if( !empty($item['value']) ){
				$thumbnail = '/media/uploads/'.$item['value']['file'];
				echo '<div class="old-image"><a href="'.$thumbnail.'" class="open-modal"><img src="'.$thumbnail.'" /></a></div>';
			}
			echo '</div>';
			break;
		case 'child':
			echo '<div class="input ajax-load" data-action="'.$item['action'].'">';
			echo 'load list from: '.$item['action'];
			echo '<input type="hidden" name="'.$name.'[]" value="" />';
			echo '</div>';
			break;
			

		case 'textarea':
			echo '<div class="input"><textarea name="'.$name.'" id="'.$name.'" class="'.$class.'">'.$value.'</textarea></div>';
			break;
		case 'radio':
			echo '<div class="input">';
			echo '<ul class="inputs-list">';
			foreach($item['data'] as $key=>$label){
				$value = isset($item['value']) ? $item['value'] : false;
				echo '<li><label class="radio-item"><input type="radio" name="'.$name.'" value="'.$key.'"'.($value==$key ? ' checked="checked"' : '').' /> <span>'.$label.'</span></label></li>';
			}
			echo '</ul>';
			echo '</div>';
			break;
		case 'select':
			echo '<div class="input">';
			echo '<select name="'.$name.'" id="'.$name.'" class="'.$class.'">';
			echo '<option>---</option>';
			$disable_id = isset($disable_id) ? $disable_id : false;
			foreach($item['data'] as $key=>$label){
				// if( $disable_id==$key ){ continue; }

				if( $value==$key ){
					echo '<option value="'.$key.'" selected="selected">'.$label.'</option>';
				}else{
					echo '<option value="'.$key.'">'.$label.'</option>';
				}
			}
			echo '</select>';
			if($title == 'Add New Menu'){echo '  <a href="/app/category">Add Category</a>';}
			echo '</div>';
			break;
		case 'checkbox':
			break;
		case 'float':
			echo '<div class="input"><input type="text" name="'.$name.'" id="'.$name.'" class="'.$class.'" value="'.number_format($value, 2, '.', '').'" /></div>';
			break;
		case 'text':
		default:
			echo '<div class="input"><input type="text" name="'.$name.'" id="'.$name.'" class="'.$class.'" value="'.$value.'" /></div>';
			break;
	}
		
	?>
	
</div>

<?php endforeach; ?>

<div class="form-row actions">
<?php 
switch($form_type)
{
	case 'edit':
		echo '<input type="submit" value="Save Changes" class="btn primary" />';
		break;
	case 'add':
	default:
		echo '<input type="submit" value="Save" class="btn primary" />';
		break;
}
?>
</div>

</form>