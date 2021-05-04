<?php get_header(); ?> 
<div class="bulk-post-category">
    <div class="container">
        <div class="content">
            <div class="custom-body-title"><h3><?php echo $TITLE ?></h3></div>
            <div class="row flex-rev">
                <div class="col-md-9">
                    <article>
                        <div class="search-result">
                            <?php search_result(); ?>
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
