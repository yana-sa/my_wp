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
            <?php
            for ($i = 10; $i >= 1; $i--) {
            $query = new WP_Query([
                'post_type' => 'cell',
                'nopaging' => true,
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key' => '_y',
                        'value' => $i
                    ],
                    [
                        'key' => '_x',
                        'value' => range(1, 10),
                    ]
                ],
                'orderby'  => 'meta_value_num',
                'order'    => 'DESC'
            ]); ?>
                <tr>
            <?php while ($query->have_posts()) {
                $query->the_post(); ?>
                <td class="cell" data-x="<?php echo get_post_meta(get_the_ID(), '_x')[0] ?>" data-y="<?php echo get_post_meta(get_the_ID(), '_y')[0] ?>"></td>
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
