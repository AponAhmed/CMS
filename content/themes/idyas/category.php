
<?php get_header(); ?> 

<div class="container">
    <div class="bulk-post-category">
        <div class="content">
            <div class="custom-body-title">
                <h3><?php echo function_exists("custom_body_title") ? custom_body_title() : "" ?></h3>
                <?php breadcrumb(); ?>
            </div>
            <article>
                <?php echo short_details(get_content()); ?>
                <?php postByTerm(); ?>
            </article>
        </div>
    </div> 
    <!--Container close in footer-->
    <?php
    get_footer();
    