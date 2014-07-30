<div class='property-search'>
	<form action='' method='post'>
		<ul class='page-sub-menu'>
			<li class='search-by'>
					<div class='label'>Search By:</div>
			</li>
			<li>
				<label>State</label>
				<ul  class='search-states<?php if(count($properties)==0) echo ' show' ?>'>
					<?php foreach ($states as $key => $state): ?>
						<li>
							<?php $id=Text::machine_name($state->name); ?>
							<?php isset($_POST['state-'.$id]) ? $checked='checked' : $checked='' ?>
							<input type="checkbox" name="state-<?php echo $id ?>" value="<?php echo $id ?>" id="state-<?php echo $id ?>" <?php echo $checked ?>>
							<label for='state-<?php echo $id ?>'><?php echo $state->name ?></label>
						</li>
					<?php endforeach ?>
				</ul>
			</li>
			<li>
				<label>Valuation</label>
				<ul class='search-valuation<?php if(count($properties)==0) echo ' show' ?>'>
					<?php for ($i=0; $i < 5; $i++):?>
						<li>
							<?php $id=($i*5).'-'.(($i*5)+5);?>
							<?php isset($_POST['valuation-'.$id]) ? $checked='checked' : $checked='' ?>
							<input type="checkbox" name="valuation-<?php echo $id ?>" value="<?php echo $id ?>" id="valuation-<?php echo $id ?>" <?php echo $checked ?>>
							<label for='valuation-<?php echo $id ?>'>$<?php echo $i*5 ?>M - $<?php echo ($i*5)+5 ?>M</label>
						</li>
					<?php endfor; ?>
				</ul>
			</li>
			<li>
				<label>Capitalisation Rate</label>
				<ul class='search-cap-rate<?php if(count($properties)==0) echo ' show' ?>'>
					<?php for ($i=0; $i < 5; $i++):?>
						<li>
							<?php 
								$startValue=($i+10)/2;
								$endValue=($i+10)/2 + 0.5;
								$endReadable=' - '.$endValue.'%';
								if($i==4){
									$endReadable='+';
									$endValue='+';
								} 
								$id=$startValue.'-'.str_replace('.','_',$endValue);							
							?>
							<?php isset($_POST['cap-rate-'.str_replace('.','_',$id)]) ? $checked='checked' : $checked='' ?>
							<input type="checkbox" name="cap-rate-<?php echo $id ?>" value="<?php echo $id ?>" id="cap-rate-<?php echo $id ?>" <?php echo $checked ?>>
							<label for='cap-rate-<?php echo $id ?>'><?php echo $startValue ?>%<?php echo $endReadable ?></label>
						</li>
					<?php endfor; ?>
				</ul>
			</li>
			<li class='last'>
				<label>Land Area</label>
				<ul class='search-land-area<?php if(count($properties)==0) echo ' show' ?>'>
					<?php for ($i=0; $i < 7; $i++):?>
						<li>
							<?php 

							$startValue=($i*5000)/2;
							$endValue=(($i*5000)+5000)/2;
							$endReadable=' - '.number_format($endValue).'<span class="lowercase">m</span><sup>2</sup>';
							if($i==6){
								$endReadable='+'; $endValue='+';
							} 
							$id=$startValue.'-'.$endValue;
							
							
							?>
							<?php isset($_POST['land-area-'.$id]) ? $checked='checked' : $checked='' ?>
							<input type="checkbox" name="land-area-<?php echo $id ?>" value="<?php echo $id ?>" id="land-area-<?php echo $id ?>" <?php echo $checked ?>>
							<label for='land-area-<?php echo $id ?>'>
								<?php echo number_format($startValue) ?><span class="lowercase">m</span><sup>2</sup> <?php echo $endReadable ?>
							</label>
						</li>
					<?php endfor; ?>
				</ul>
			</li>
			
		</ul>	
		<div class='submit-btn'><input type="submit" name="advanced-property-search-submit" value="Submit search"></div>
		<div class='submit-btn'><input type="submit" name="advanced-property-search-clear" value="Clear Results"></div>	
		<?php global $advancedSearchResultMessage;?>
		<div class='advanced-search-result-message'><?php echo $advancedSearchResultMessage ?></div>
	</form>
	
	
	<div class='property-search-results'>
		<?php if(count($properties) > 0 ): ?>
		<table border='0'>
			<tr>
				<?php foreach ($properties[0] as $key => $property): ?>
					<?php if($key=='nid') continue; ?>
					<th>
						<?php
							$orderDirection='-asc';
							$symbol='';
							if(isset($_GET['order'])){
								if($_GET['order']==$key.'-asc'){
									$orderDirection='-desc';
									$symbol='<span class="arrow">&#x25B2;</span>';
								} 
								if($_GET['order']==$key.'-desc'){
									 $orderDirection='-asc';
									 $symbol='<span class="arrow">&#x25BC;</span>';
								}
							}

						?>
						<a href='<?php echo strtok($_SERVER['REQUEST_URI'],'?').'?order='.$key.$orderDirection ?>' ><?php echo Text::humanize($key); ?><?php echo $symbol ?></a>
					</th>
				<?php endforeach ?>
			</tr>
			<?php foreach ($properties as $key => $property): ?>
				
			<tr>
				<?php foreach ($property as $_key => $value): ?>
					<?php
						if($_key=='nid') continue;
						if($_key=='valuation') $value= '$'.nice_number($value);
						if($_key=='capitalisation') $value = $value.' %';
						if($_key=='land_area') $value = number_format($value).' <span class="lowercase">m</span><sup>2</sup>';
					?>
					<td><?php echo $value ?></td>
				<?php endforeach ?>	
			</tr>		
			<?php endforeach ?>
		</table>	
			
		<?php endif; ?>
	</div>
	
</div>