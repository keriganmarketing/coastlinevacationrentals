<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: Contact Page
*/
global $bapi_all_options;
$bapi_solutiondata = $bapi_all_options['bapi_solutiondata'];
$bapi_solutiondata = json_decode(wp_unslash($bapi_solutiondata),TRUE);
?>
<?php get_header(); ?>
<article class="full-width-page">	
<div class="row-fluid">
	<article class="span12">
		<div class="row-fluid">
  <div class="span8 module shadow contact-form">
    <div class="pd2">
    <h3 class="widget-title"><?php echo $textDataArray['Contact Us']; ?></h3>
    
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
 the_content();  endwhile; endif; ?>

      <div id="bapi-inquiryform" class="bapi-inquiryform" data-templatename="tmpl-contactus-form" data-log="0" data-shownamefield="1" data-showemailfield="1" data-showphonefield="1" data-showdatefields="0" data-shownumberguestsfields="0" data-showleadsourcedropdown="1" data-showcommentsfield="1"></div>
    </div>
  </div>
  <aside class="span4 module shadow contact-right-side">
    <div class="pd2"> <?php if(!empty($bapi_solutiondata['Site'])){ ?>
      <h3><span class="glyphicons home"><i></i><?php echo $textDataArray['Our Address']; ?></span></h3>
      <div class="officemap">
		<img src="//maps.googleapis.com/maps/api/staticmap?center=<?php echo $bapi_solutiondata['Office']['Latitude']; ?>,<?php echo $bapi_solutiondata['Office']['Longitude']?>&zoom=8&size=500x250&maptype=roadmap&markers=color:blue%7Clabel:%20%7C<?php echo $bapi_solutiondata['Office']['Latitude']; ?>,<?php echo $bapi_solutiondata['Office']['Longitude']; ?>&sensor=false&sensor=false&key=AIzaSyAY7wxlnkMG6czYy9K-wM4OWXs0YFpFzEE" />
        <div class="pagination-centered"><small><a href="//maps.google.com/?q=<?php echo $bapi_solutiondata['Office']['Latitude']; ?>,<?php echo $bapi_solutiondata['Office']['Longitude']; ?>" target="_blank"><?php echo $textDataArray['View Larger Map']; ?></a></small></div>
	  </div>
      <div class="contact-info vcard">
		<?php if(!empty($bapi_solutiondata['Office'])){ ?>
        <div class="contact-type"><span class="fn org"><?php echo $bapi_solutiondata['SolutionName']; ?></span><br />
			<span class="adr"><span class="street-address">
				<?php if(!empty($bapi_solutiondata['Office']['Address1'])){echo $bapi_solutiondata['Office']['Address1'];echo '<br/>';} ?>
				<?php if(!empty($bapi_solutiondata['Office']['Address2'])){echo $bapi_solutiondata['Office']['Address2'];echo '<br/>';} ?>
			</span>
			<span class="locality"><?php echo $bapi_solutiondata['Office']['City']; ?></span>,&nbsp;<span class="region"><?php echo $bapi_solutiondata['Office']['State']; ?></span><br/><span class="postal-code"><?php echo $bapi_solutiondata['Office']['PostalCode']; ?></span><br/><span class="country"><?php echo $bapi_solutiondata['Office']['Country']; ?></span></span></div>
        <hr/>
        <h3><span class="glyphicons conversation"><i></i><?php echo $textDataArray['Talk to Us']; ?></span></h3>
        <div class="contact-type tel">
		<?php if(!empty($bapi_solutiondata['Office']['PrimaryPhone'])){ ?>
          <div class="value"><span class='phonenumber-caption'><?php echo $textDataArray['Phone']; ?>:&nbsp;</span><?php echo $bapi_solutiondata['Office']['PrimaryPhone']; ?></div>
        <?php } ?>          
        <?php if(!empty($bapi_solutiondata['Office']['TollfreeNumber'])){ ?>
          <div class="value"><span class='phonenumber-caption'><?php echo $textDataArray['Toll Free']; ?>:&nbsp;</span><?php echo $bapi_solutiondata['Office']['TollfreeNumber']; ?></div>
        <?php } ?>
        <?php if(!empty($bapi_solutiondata['Office']['FaxNumber'])){ ?>
          <div class="value"><span class='phonenumber-caption'><?php echo $textDataArray['Fax']; ?>:&nbsp;</span><?php echo $bapi_solutiondata['Office']['FaxNumber']; ?></div>
        <?php } ?> </div>
        <?php if($bapi_solutiondata['Office']['HasEmail']){ ?>
        <div class="contact-type">
          <div class="email"><span class='phonenumber-caption'><?php echo $textDataArray['Email']; ?>:&nbsp;</span><a href="mailto:<?php echo $bapi_solutiondata['Office']['Email']; ?>"><?php echo $bapi_solutiondata['Office']['Email']; ?></a></div>
        </div>
       <?php } ?></div>
	<?php } ?>
    <?php } ?> </div>
  </aside>
</div>        
    </article>
</div>
</article>
<?php get_footer(); ?>
