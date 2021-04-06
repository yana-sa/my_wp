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
    <div class="popup" style="">
        <form method="post" data-form="buy_building">
        <h4>Buy a new building!</h4>
        <label for="building_type">Building type:</label>
        <select name="building_type" id="building_type" data-select="building_type"><?php
            $user_id = get_current_user_id();
            $building_types = get_terms('building_type', ['hide_empty' => false]);;
            foreach ($building_types as $building_type) { ?>
                <option value="<?php echo $building_type->slug ?>"><?php echo $building_type->name ?></option>
            <?php } ?>
        </select>
        <p data-price="price"></p>
        <?php $user_balance = get_user_meta($user_id, 'balance'); ?>
        <input type="hidden" name="user_id" data-input="user_id" value="<?php echo $user_id ?>">
            <input type="hidden" name="x" value="">
            <input type="hidden" name="y" value="">
            <p>Your balance is <b><?php echo $user_balance[0]?>$</b></p>
        <input type="submit" value="Buy now!">
        </form>
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
