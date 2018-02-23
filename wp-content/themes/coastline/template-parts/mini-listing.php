<?php
$quote     = $f['ContextData']['Quote'];
$descParts = strip_tags($f['Headline']);
//print_r($descParts[0]);
$intro = ucfirst(strtolower(tokenTruncate($descParts, $textcutoff)));

//echo '<pre>'.print_r($f).'</pre>';
if ($f): ?>
    <div class="fp-featured col-lg-4 text-xs-center">
        <div class="fstar" >
            <img src="<?php echo get_template_directory_uri().'/img/fstarfish.svg'; ?>" alt="Featured Property" class="img-fluid" >
        </div>
        <div class="row">
            <div class="fp-image col-sm-6 col-lg-12">
                <?php if($seo[$f['ID']]['Keyword']) : ?><a href="<?php echo $seo[$f['ID']]['DetailURL']; ?>"><?php endif; ?>
                    <img src="<?php echo $img = $f['PrimaryImage'][$imgSize]; ?>" alt="<?php echo $f['Headline']; ?>" title="<?php echo $f['PrimaryImage']['Caption']; ?>" class="img-fluid" />
                    <?php if($seo[$f['ID']]['Keyword']) : ?></a><?php endif; ?>
                <span class="feat-flag"><?php echo $f['Headline']; ?></span>
            </div>
            <div class="fp-content col-sm-6 col-lg-12">
                <div class="fp-outer text-xs-center">
                    <div class="fp-title"><?php if($seo[$f['ID']]['Keyword']) : ?><a href='<?php echo $seo[$f['ID']]['DetailURL']; ?>'><h3><?php endif; echo $f['InternalName']; if($seo[$f['ID']]['Keyword']) : ?></h3></a><?php endif; ?>
                        <p class="location"><strong><?php echo $f['City']; ?></strong></p>
                    </div>

                    <div class="fp-desc">
                        <p><?php echo $intro; ?></p>
                    </div>

                    <div class="fp-details">

                        <div class="row">
                            <div class="col-xs-6 col-sm-3">
                                <strong>Type</strong>
                                <?php echo $f['Type']; ?>
                            </div>

                            <?php if($beds = $f['Bedrooms']) : ?>
                                <div class="col-xs-6 col-sm-3">
                                    <strong><?php echo $textdata['Beds']; ?></strong>
                                    <?php echo $beds; ?>
                                </div>
                            <?php endif; ?>


                            <?php if($baths = $f['Bathrooms']) : ?>
                                <div class="col-xs-6 col-sm-3">
                                    <strong><?php echo $textdata['Baths']; ?></strong>
                                    <?php echo $baths; ?>
                                </div>
                            <?php endif; ?>

                            <?php if($sleeps = $f['Sleeps']) : ?>
                                <div class="col-xs-6 col-sm-3">
                                    <strong><?php echo $textdata['Sleeps']; ?></strong>
                                    <?php echo $sleeps;  ?>
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
                        <a class="property-link btn btn-outline" href="<?php echo $seo[$f['ID']]['DetailURL']; ?>">View Property</a>
                    <?php endif; ?>

                    <span class="conch"></span><span class="shell"></span><span class="starfish"></span>
                </div>
            </div>
        </div>
    </div>
<?php $count++; endif;
