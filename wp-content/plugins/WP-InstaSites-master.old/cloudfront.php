<?php

require_once(dirname( __FILE__ ).'/aws/aws-autoloader.php');
use Aws\CloudFront\CloudFrontClient;

function create_cf_distro($origin,$cname){	
	// Instantiate the S3 client with your AWS credentials and desired AWS region
	$client = CloudFrontClient::factory(array(
		'key'    => AWS_ACCESS_KEY,
		'secret' => AWS_SECRET_KEY,
	));
	$exceptionMessage = "";
	try {
	$result = $client->createDistribution(array(
		'Aliases' => array('Quantity' => 1, 'Items' => array($cname)),
		'CacheBehaviors' => array('Quantity' => 0),
		'Comment' => 'InstaSite Signup',
		'Enabled' => true,
		'CallerReference' => 'InstaSites-'.$origin,
		'DefaultCacheBehavior' => array(
			'MinTTL' => 3600,
			'ViewerProtocolPolicy' => 'allow-all',
			'TargetOriginId' => 'InstaSites-'.$origin,
			'TrustedSigners' => array(
				'Enabled'  => true,
				'Quantity' => 1,
				'Items'    => array('self')
			),
			'ForwardedValues' => array(
				'QueryString' => true,
				'Cookies' => array(
					'Forward' => 'none'
				)
			),
			'TrustedSigners' => array(
				'Enabled' => false,
				'Quantity' => 0
			)
		),
		'DefaultRootObject' => '',
		'Logging' => array(
			'Enabled' => false,
			'Bucket' => '',
			'Prefix' => '',
			'IncludeCookies' => true,
		),
		'Origins' => array(
			'Quantity' => 1,
			'Items' => array(
				array(
					'Id' => 'InstaSites-'.$origin,
					'DomainName' => $origin,
					'CustomOriginConfig' => array(
						// HTTPPort is required
						'HTTPPort' => 80,
						// HTTPSPort is required
						'HTTPSPort' => 443,
						// OriginProtocolPolicy is required
						'OriginProtocolPolicy' => 'http-only',
					)
				)
			)
		),
		'PriceClass' => 'PriceClass_All',
	));
	} catch (Exception $e) {
		$exceptionMessage =  $e->getMessage();
	}
	//printf('%s - %s - %s', $result['Status'], $result['Location'], $result['DomainName']) . "\n";
	if($result['Status']=="InProgress"){
		return $result;
	}
	//we check if the exceptionMessage was updated meaning there was an exception
	if($exceptionMessage != ''){
		$resultException = array('CreatingDistrib' => false, 'Message' => $exceptionMessage);
		return $resultException;
	}
	//return false;
}

function modify_cf_distro($origin,$cname){	
	//echo $cname; exit();
	//$cname = 'www.mydomain.com'; //remove hard-coded stuff
	//$did = 'E5DX7PPTMIQN'; //remove hard-coded stuff
	$did = get_option('bapi_cloudfrontid'); 
	
	// Instantiate the S3 client with your AWS credentials and desired AWS region
	$client = CloudFrontClient::factory(array(
		'key'    => AWS_ACCESS_KEY,
		'secret' => AWS_SECRET_KEY,
	));
	
	$r = $client->getDistributionConfig(array('Id'=>$did));
	$etag = $r['ETag'];
	$cref = $r['CallerReference'];
	$od = $r['Origins']['Items'][0]['DomainName'];
	//print_r($r);exit();
	$exceptionMessage = "";
	try{
	$result = $client->updateDistribution(array(
		'CallerReference' => $cref,
		'Aliases' => array('Quantity' => 1, 'Items' => array($cname)),
		'DefaultRootObject' => '',
		'Origins' => array(
			'Quantity' => 1,
			'Items' => array(
				array(
					'Id' => 'InstaSites-'.$origin,
					'DomainName' => $od,
					'CustomOriginConfig' => array(
						// HTTPPort is required
						'HTTPPort' => 80,
						// HTTPSPort is required
						'HTTPSPort' => 443,
						// OriginProtocolPolicy is required
						'OriginProtocolPolicy' => 'http-only',
					)
				)
			)
		),
		'DefaultCacheBehavior' => array(
			'MinTTL' => 3600,
			'ViewerProtocolPolicy' => 'allow-all',
			'TargetOriginId' => 'InstaSites-'.$origin,
			'TrustedSigners' => array(
				'Enabled'  => true,
				'Quantity' => 1,
				'Items'    => array('self')
			),
			'ForwardedValues' => array(
				'QueryString' => true,
				'Cookies' => array(
					'Forward' => 'none'
				)
			),
			'TrustedSigners' => array(
				'Enabled' => false,
				'Quantity' => 0
			)
		),
		'CacheBehaviors' => array('Quantity' => 0),
		'Comment' => 'InstaSite Signup',
		'ViewerCertificate' => array(
			'CloudFrontDefaultCertificate' => true,
		),
		'Enabled' => true,
		'Logging' => array(
			'Enabled' => false,
			'Bucket' => '',
			'Prefix' => '',
			'IncludeCookies' => true,
		),
		'PriceClass' => 'PriceClass_All',
		'Id' => $did, 
		'IfMatch' => $etag
	));
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	//printf('%s - %s - %s', $result['Status'], $result['Location'], $result['DomainName']) . "\n";
	if($result['Status']=="InProgress"){
		//print_r($result);exit();
		return $result;
	}
	//we check if the exceptionMessage was updated meaning there was an exception
	if($exceptionMessage != ''){
		$resultException = array('CreatingDistrib' => false, 'Message' => $exceptionMessage);
		return $resultException;
	}
	//return false;
}
?>