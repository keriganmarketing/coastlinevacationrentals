<?php
/**
 * The template for displaying all rentals.
 *
 * @package coastline_vr
 */

get_header(); ?>
    <div id="support-mast">

    </div>

    <div id="mid">
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">

                <?php while (have_posts()) :
                the_post(); ?>
                <div class="container">
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>

                    <div class="row">
                        <div class="rentals">
                            <?php //Get All Properties


                            $imgSize = 'ThumbnailURL';
                            $args    = array(
                                'meta_query'     => array(
                                    array(
                                        'key'     => 'bapikey',
                                        'value'   => $bapikeys,
                                        'compare' => 'IN'
                                    ),
                                ),
                                'post_type'      => 'page',
                                'posts_per_page' => -1,
                                'orderby'        => 'menu_order',

                            );

                            if ( ! $idsList) {
                                $args['meta_query'][0]['value']   = 'property:';
                                $args['meta_query'][0]['compare'] = 'LIKE';
                            }

                            $posts = get_posts($args);

                            $featured = [];
                            foreach ($posts as $post) {
                                $data = json_decode(get_post_meta($post->ID, 'bapi_property_data')[0], true);

                                //echo '<!--',print_r($data),'-->';

                                //if($data['Status'] == "A") {

                                if ($idsList) {
                                    foreach ($idsList as $key => $value) {
                                        if ($value['id'] == $data['ID']) {
                                            $featured[$key] = $data;
                                        }
                                    }
                                } else {
                                    $featured[] = $data;
                                }

                                //}
                            }

                            global $bapi_all_options;
                            $textdata = getbapitextdata();
                            $key      = $bapi_all_options['api_key'];
                            $config   = unserialize($bapi_all_options['bapi_sitesettings_raw']);
                            $seo      = json_decode(get_option('bapi_keywords_array'), true);

                            //Parse and assign SEO data to be refereneced by key
                            foreach ($seo as $key => $value) {
                                $seo[$value['pkid']] = $value;
                                unset($seo[$key]);
                            }

                            echo $before_widget;

                            if ( ! empty($title)) {
                                echo $before_title . $title . $after_title;
                            }
                            ?>
                            <div class="row-fluid">
                                <?php
                                foreach ($featured as $i => $f) {
                                    include(locate_template('template-parts/listing-tile.php'));
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; // End of the loop. ?>
                </div>
            </main><!-- #main -->
        </div><!-- #primary -->
    </div>
<?php get_footer();
