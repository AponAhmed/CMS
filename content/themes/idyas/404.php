<?php get_header(); ?> 
<div class="bulk-post-category">
    <div class="container">
        <div class="content">
            <div class="custom-body-title"><h3>404 Page Not Found !</h3></div>
            <div class="row flex-rev">
                <div class="col-md-9">
                    <article>
                        <?php echo search_box() ?>
                        <strong  class='notFound'>404 - Page Not Found !<br>
                            The page you are looking for might have been removed,  </em>Search a keyword what you looking.</strong>
                        <div class="search-items-404">
                                <?php echo pagePredictor(); ?>
                        </div>
                    </article>
                </div>
                <div class="col-md-3">
                    <?php echo sideBar(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
