<?php
/*
 * Template Name: User Activity Statistics Page
 *
 */
get_header();
$months = array_reduce(range(1,12),function($rslt,$m) {
    $rslt[$m] = date('F',mktime(0,0,0,$m,10));
    return $rslt;
});
?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header alignwide">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <?php twenty_twenty_one_post_thumbnail(); ?>
        </header>
        <div class="entry-content alignwide">
            <div class="chart-div">
                <div class="display-month" data-month="display-month"><?php echo $months[3]?></div>
                <canvas id="statchart" class="statchat" width="600" height="400"></canvas>
                <div class="display-descr">Y axis - number of posts, X axis- days</div>
            </div>
            <div class="form-stat">
                <form method="post" data-form="select-month">
                    <label for="select-month"><h4>Select month and check statistics: </label>
                    <select name="select-month" id="select-month" class="select-month" data-select="select-month"><?php
                        foreach ($months as $num => $name) {?>
                            <option data-selected="month" value="<?php echo $num ?>"><?php echo $name ?></option>
                        <?php } ?>
                    </select><br><br>
                    <input type="submit" class="show-month" value="Show statistics">
                </form>
            </div>
        </div>

        <footer class="entry-footer default-max-width">
            <?php twenty_twenty_one_entry_meta_footer(); ?>
        </footer><!-- .entry-footer -->

        <?php if ( ! is_singular( 'attachment' ) ) : ?>
            <?php get_template_part( 'template-parts/post/author-bio' ); ?>
        <?php endif; ?>

    </article><!-- #post-${ID} -->
<?php
get_footer();
?>