<?php if ($customer_type == 'SME') :?>
    <div class="elec-usages">
    	<div id="Low" class="usage">
        	<div class="item">
        		<h4><div class="status"></div>LOW</h4>
            	<p><20,000 kwh/yr</p>
            </div>
	    </div>
        <div id="Medium" class="usage">
        	<div class="item">
            	<h4><div class="status"></div>MEDIUM</h4>
            	<p>20,000-40,000 kwh/yr</p>
            </div>
	    </div>
	    <div id="High" class="usage">
        	<div class="item">
    			<h4><div class="status"></div>HIGH</h4>
    			<p>>40,000 kwh/yr</p>
            </div>
    	</div>
    </div> 
<?php else:?>
    <div class="elec-usages">
    	<div id="Low" class="usage">
        	<div class="item" title="1-2 people; <br>1-2 bedrooms; <br>Minimal washing, heating & cooling; <br>Spend minimal time at home">
        		<h4><div class="status"></div>LOW</h4>
            	<p>$0 - $300</p>
            </div>
	    </div>
        <div id="Medium" class="usage">
        	<div class="item" title="3-4 people; <br>3 bedrooms; <br>Regular washing, heating & cooling; <br>Small family, usage mainly in evenings and weekends">
            	<h4><div class="status"></div>MEDIUM</h4>
            	<p>$300 - $600</p>
            </div>
	    </div>
	    <div id="High" class="usage">
        	<div class="item" title="5+ people; <br>5+ bedrooms; <br>Daily washing, heating & cooling Regular use of TV's, computers and appliances; <br>Someone at home most of the time">
    			<h4><div class="status"></div>HIGH</h4>
    			<p>$600+</p>
            </div>
    	</div>
    </div> 
<?php endif;?>