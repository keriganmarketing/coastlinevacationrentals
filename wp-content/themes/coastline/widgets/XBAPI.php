<?php
namespace Kigo\Themes\instaparent;
/**
 * XBAPI is Bapi Extended.
 */
class XBAPI extends \BAPI {

    private $base_url;
    private $api_key;

    /**
     *
     * @throws Exception if api key is not defined
     */
    function __construct() {

        //ApiKey
        $this->api_key = get_option('api_key');

        //if(empty($this->api_key)) throw new Exception('API KEY is empty');
        //Solution data
        $solution_data = getbapisolutiondata();


        //BaseURL
        $this->base_url = $solution_data['site']['BaseURL'];

        parent::__construct($this->api_key, $this->base_url);
    }




    /**
     * This function gets all properties of this solution.
     * @return array   returns an array;
     */
    function getAllProperties($propsToRequest = null) {

        if (empty($propsToRequest)) {
            $propIdStr = $this->xconnect('/ws/?method=search&entity=property');
            $propId = json_decode($propIdStr);

            $maxnum = 20;

            $props = array();
            $propsToRequest = array_chunk($propId->result, $maxnum); //request max elements.
        }

        foreach ($propsToRequest as $requestIds) {
            $ids = implode(',', $requestIds);
            $propsRequested = json_decode($this->xconnect('/ws/?method=get&entity=property&pagesize='.$maxnum.'&seo=1&ids=' . $ids));
            $props = array_merge($props, $propsRequested->result);
        }

        return $props;
    }


    /**
     *
     * @param string $findIds
     * @return array of objects
     */
    function getPropertyFinders($findIds=[]){

        if(empty($findIds))
        {
            $findIds = $this->search('searches',null, true)['result'] ? : [];
        }
        else
            $findIds = array_values($findIds);



        $maxnum = 10;
        $pfToRequest = array_chunk($findIds, $maxnum);





        $pfinders = [];

        foreach ($pfToRequest as $pfToRequestids) {
            $get_options =
                [
                    'seo'=> '1'
                ];
            $pfinders_ = $this->get('searches',$pfToRequestids,$get_options)['result'] ?:[];
            $pfinders = array_merge($pfinders, $pfinders_);
        }

        return $pfinders;
    }


    function getSpecialOffers($findIds=[])
    {


        if(empty($findIds))
        {
            $findIds  = $this->search('specials', null, true)['result']?:[];
        }
        else
            $findIds = array_values($findIds);

        $maxnum = 10;
        $spoffRequest = array_chunk($findIds, $maxnum);

        $spoffers = [];

        foreach ($spoffRequest as $spoffRequestids) {

            $spoffers_ = $this->get('specials',$spoffRequestids, $get_options)['result']?:[];
            $spoffers = array_merge($spoffers, $spoffers_);
        }


        return $spoffers;
    }



    /**
     * Makes a request to the bapi via webservice.
     * @param $requestString
     * @return string
     */
    private function xconnect($requestString) {

        //Method 1:
        $context =  stream_context_create(array(
            'http' => array(
                'method' => "GET",
                'header' => "User-Agent: InstaSites Agent\r\nReferer: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."\r\n"
            )
        ));

        $output = file_get_contents($this->base_url . $requestString . '&apikey=' . $this->api_key,false, $context );


        //Method 2:
        if(empty($output))
        {
            $nconnection = curl_init();
            curl_setopt($nconnection, CURLOPT_USERAGENT, 'InstaSites Agent');
            curl_setopt($nconnection, CURLOPT_URL, $this->base_url . $requestString . '&apikey=' . $this->api_key);
            curl_setopt($nconnection, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($nconnection);
            curl_close($nconnection);
        }

        return $output;
    }

}
