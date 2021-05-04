<?php get_header(); ?>   
<article>
    <div class="row">
        <div class="col-6 col w6">

        </div>
        <div class="col-6 col w6">
            <h2><?php echo get_post_title() ?></h2>	
            <?php
            echo get_content()
            ?>
        </div>
    </div>

</article>
<?php get_footer(); ?>  