<?php get_header(); ?> 
<div class="container">
    <section class="single-post-area">
        <article>
            <div class="row">
                <div class="col-sm-5 col-6 col w6 ">
                    <?php echo feature_image(false, 600) ?>
                </div>
                <div class="col-sm-6 col-6 col w6">
                    <div class='entry-header'>
                        <h2 class="page_title"><?php echo get_post_title() ?></h2>
                        <?php echo do_shortcode("[get_price]"); ?>
                    </div>
                    <?php echo function_exists('moreInfo_details') ? moreInfo_details() : '' ?>      
                    <hr>
                    <p><strong>Tag:</strong>
                        <?php echo get_post_terms_link(false, 'tag') ?>
                    <p>
                </div>
                <!--                <div class="col col-sm-2 w2">
                                    <div class='bigBtn'><?php //echo do_shortcode("[get_price]");                     ?></div>
                                    <p><strong>Category:</strong>
                <?php //echo get_post_terms_link() ?>
                                    </p>
                                </div>-->
            </div>
        </article>
    </section>
    <?php relatedPostSlider() ?>
    <?php moreDetailsTab() ?>
    <!--Container close in footer-->
    <?php
    get_footer();
    ?>