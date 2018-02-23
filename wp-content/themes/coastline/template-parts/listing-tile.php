<?php
$quote     = $f['ContextData']['Quote'];
$descParts = strip_tags($f['Description']);
//print_r($descParts[0]);
$intro = ucfirst(strtolower(wp_trim_words($descParts, 35, '... <a href="'.$seo[$f['ID']]['DetailURL'].'">read more</a>')));

//echo '<pre>'.print_r($f).'</pre>';
if ($f): ?>
    <div class="fp-featured big">
        <div class="row no-gutter">
            <div class="fp-image col-sm-6">
                <div class="image-wrapper">
                <a href="<?php echo $seo[$f['ID']]['DetailURL']; ?>">
                    <img src="<?php echo $img = $f['PrimaryImage'][$imgSize]; ?>" alt="<?php echo $f['Headline']; ?>" title="<?php echo $f['PrimaryImage']['Caption']; ?>" class="img-fluid" />
                </a>
                </div>
            </div>
            <div class="fp-content fp-outer col-sm-6">
                <div class="text-xs-center">
                    <div class="fp-title"><a href='<?php echo $seo[$f['ID']]['DetailURL']; ?>'><h3><?php echo $f['InternalName']; ?></h3></a>
                        <p class="location"><strong><?php echo $f['City']; ?></strong></p>
                    </div>

                    <div class="fp-desc">
                        <p><?php echo $intro; ?></p>

                    </div>



                    <div class="fp-details">

                        <div class="row">
                            <div class="col-xs-6 col-sm-4">
                                <strong>Type</strong>
                                <?php echo $f['Type']; ?>
                            </div>

                            <?php if($minstay = $f['MinStay']) : ?>
                                <div class="col-xs-6 col-sm-4">
                                    <strong>Minimum Stay</strong>
                                    <?php echo $minstay;  ?> nights
                                </div>
                            <?php endif; ?>

                            <?php if($beds = $f['Bedrooms']) : ?>
                                <div class="col-xs-6 col-sm-4">
                                    <strong><?php echo $textdata['Beds']; ?></strong>
                                    <?php echo $beds; ?>
                                </div>
                            <?php endif; ?>


                            <?php if($baths = $f['Bathrooms']) : ?>
                                <div class="col-xs-6 col-sm-4">
                                    <strong><?php echo $textdata['Baths']; ?></strong>
                                    <?php echo $baths; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($sleeps = $f['Sleeps']) : ?>
                                <div class="col-xs-6 col-sm-4">
                                    <strong><?php echo $textdata['Sleeps']; ?></strong>
                                    <?php echo $sleeps;  ?>
                                </div>
                            <?php endif; ?>

                            <?php if($sqft = $f['AdjLivingSpace']) : ?>
                                <div class="col-xs-6 col-sm-4">
                                    <strong><?php echo $f['AdjLivingSpaceUnit']; ?></strong>
                                    <?php echo $sqft;  ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>


                    <div class="fp-rates">
                        <?php if($quote['QuoteDisplay']['value']) { ?>
                            <?php if($prefix = $quote['QuoteDisplay']['prefix']) : ?><span class="prefix"><?php echo $prefix; ?>:</span><?php endif; ?>
                            <span class="price"><?php echo $quote['QuoteDisplay']['value']; ?></span>
                            <?php if($suffix = $quote['QuoteDisplay']['suffix']) : ?><span class="suffix">/<?php echo $suffix; ?></span><?php endif; ?>
                        <?php } ?>
                    </div>

                    <?php if($seo[$f['ID']]['Keyword']) : ?>
                        <a class="property-link btn btn-outline" href="<?php echo $seo[$f['ID']]['DetailURL']; ?>">Property Details</a>
                    <?php endif; ?>

                    <?php if($f['IsBookable'] == 1) : ?>
                        <a class="property-link btn btn-info" href="<?php echo $f['ContextData']['SEO']['BookingURL']; ?>">Book Now</a>
                    <?php endif; ?>

                    <span class="conch"></span><span class="shell"></span><span class="starfish"></span>
                </div>
            </div>
        </div>
    </div>
    <?php $count++; endif;