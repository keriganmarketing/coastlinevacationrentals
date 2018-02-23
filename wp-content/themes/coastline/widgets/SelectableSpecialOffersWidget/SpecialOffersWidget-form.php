<p class="description">
    Enable the special offers to display in this widget. You can sort them by dragging them.
</p>

<!--Widget title-->
<h3>Title</h3>
<p>
    <input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $ins['title']; ?>">
</p>


<label>
    <h3>Items per row:</h3>
    <select name="<?php echo $this->get_field_name('items_per_row'); ?>" id="<?php echo $this->get_field_id('items_per_row'); ?>">
        <?php
            for($i=1; $i<5; $i++){
                $selected = '';
                if($i == (int) $ins['items_per_row'] )
                {
                    $selected = 'selected';
                }
                ?>
                <option value="<?php echo $i; ?>" <?php echo $selected; ?>>
                    <?php echo $i; ?>
                </option>
            <?php }
        ?>
    </select>
</label>


<!--Select Special Offers-->
<h3>Select Special Offers</h3>
<ul class="sortable special_offers_sortable_list">
       <?php
       //Compare current specials with the ones in the app.
       $existentspob = $this->getSpecialOffers();
       $existentsp = array_keys($existentspob);
       if(is_array($ins['special_offers']) && count($ins['special_offers'])){
        foreach($ins['special_offers'] as $spoffid => $name){
            if(!in_array($spoffid, $existentsp)) continue;
            ?>
            <li>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name('special_offers'); ?>[<?php echo $spoffid; ?>]" checked value="<?php echo $spoffid; ?>">
                <b><?php echo $this->getSpecialOffers()[$name]; ?></b>
                <span class="draggme">&equiv;</span>
            </label>
            </li>
                <?php
        }
       }

       $bpspecialoffers = $existentspob;
        if(is_array($bpspecialoffers) && count($bpspecialoffers)){
            foreach($bpspecialoffers as $spoffid => $name){
            $checked = '';
            if(!empty($ins['special_offers'][$spoffid])) continue;
            ?>
            <li>
            <label>
                <input type="checkbox" name="<?php echo $this->get_field_name('special_offers'); ?>[<?php echo $spoffid; ?>]" value="<?php echo $spoffid; ?>">
                <b><?php echo $name; ?></b>
                <span class="draggme">&equiv;</span>
            </label>
            </li>
                <?php
        }
        }else{
            echo "<li><p>There are no special offers available.</p></li>";
        }
    ?>
</ul>
<script>
$('.sortable').sortable();
</script>
