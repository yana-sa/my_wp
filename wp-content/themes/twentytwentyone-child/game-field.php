<?php
/*
 * Template Name: Game field
 *
 */

get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header alignwide">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
        <?php twenty_twenty_one_post_thumbnail(); ?>
    </header>

    <div class="gamefield-div">
        <table>
            <tbody>
            <?php $cells = get_cells();
                foreach ($cells as $cellscol) { ?>
                <tr>
            <?php foreach ($cellscol as $cell) { ?>
                <td class="cell" data-x="<?php echo $cell['x'] ?>" data-y="<?php echo $cell['y'] ?>"></td>
            <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <footer class="entry-footer default-max-width">
        <?php twenty_twenty_one_entry_meta_footer(); ?>
    </footer><!-- .entry-footer -->

    <?php if (!is_singular('attachment')) : ?>
        <?php get_template_part('template-parts/post/author-bio'); ?>
    <?php endif; ?>

</article><!-- #post-${ID} -->
<?php
get_footer();
?>
