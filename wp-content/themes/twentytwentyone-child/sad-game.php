<?php
/*
 * Template Name: Sad game
 *
 */

get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php if (is_user_logged_in()) { ?>
        <header class="entry-header alignwide">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            <?php twenty_twenty_one_post_thumbnail(); ?>
        </header>

        <div class="overlay">
            <div class="message">
                <p data-message="message"><b>Welcome to the sad game!</b><br>
                    Collect coins and avoid boy tears<br>
                    <b>Good luck!</b></p>
                <a class="start">Start</a>
            </div>
        </div>

        <div class="gamefield-div">
            <table>
                <tbody class="sad-game">
                <div class="harold"></div>
                <?php $cells = get_cells();
                foreach ($cells as $cellscol) { ?>
                    <tr>
                        <?php foreach ($cellscol as $cell) {?>
                            <td class="cell" data-x="<?php echo $cell['x'] ?>" data-y="<?php echo $cell['y'] ?>"></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } else { ?>
        <header class="entry-header alignwide">
            <h1>Nothing's here!</h1>
        </header>
    <?php } ?>
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
