<?php if ($customer_type == 'SME') :?>
    <div class="gas-usages">
    	<div id="Low" class="usage">
        	<div class="item">
        		<h4><div class="status"></div>LOW</h4>
            	<p><50,000 MJ/yr</p>
            </div>
	    </div>
        <div id="Medium" class="usage">
        	<div class="item">
            	<h4><div class="status"></div>MEDIUM</h4>
            	<p>50,000-100,000 MJ/yr</p>
            </div>
	    </div>
	    <div id="High" class="usage">
        	<div class="item">
    			<h4><div class="status"></div>HIGH</h4>
    			<p>>100,000 MJ/yr</p>
            </div>  
    	</div>
    </div>
<?php else:?>
    <div class="gas-usages">
    	<div id="Low" class="usage">
        	<div class="item" title="1-2 people; <br>1-2 bedrooms; <br>Gas heating used few hours a day">
        		<h4><div class="status"></div>LOW</h4>
            	<p>$0 - $150</p>
            </div>
	    </div>
        <div id="Medium" class="usage">
        	<div class="item" title="3-4 people; <br>3 bedrooms; <br>Gas heating used 6-8 hours a day">
            	<h4><div class="status"></div>MEDIUM</h4>
            	<p>$150 - $300</p>
            </div>
	    </div>
	    <div id="High" class="usage">
        	<div class="item" title="5+ people; <br>5+ bedrooms; <br>Gas heating used most hours of the day">
    			<h4><div class="status"></div>HIGH</h4>
    			<p>$300+</p>
            </div>  
    	</div>
    </div>
<?php endif;?>