<?php
/*
 * Template Name: Organizations Page
 *
 */
get_header();
?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header alignwide">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            <?php twenty_twenty_one_post_thumbnail(); ?>
        </header>

        <div class="entry-content alignwide">
            <div class="wrap-list">
                <h4 class="wrap-h4">Organizations with subsidiaries</h4>
                <ul class="orglist">
                    <?php $organizations = organizations_data();
                    foreach ($organizations as $organization) { ?>
                        <li class="org_list"><a href="<?php $organization['link'] ?>"><?php echo $organization['name'] ?></a></li>
                        <?php if (!empty($organization['subsidiaries'])) { ?>
                            <ul>
                                <?php foreach ($organization['subsidiaries'] as $subsidiary) { ?>
                                    <li class="org_sub_list">
                                        <a href="<?php $subsidiary['link'] ?>"><?php echo $subsidiary['name'] ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php }
                    } ?>
                </ul>
            </div>

            <div class="wrap-list">
                <h4 class="wrap-h4"><a href="<?php echo get_permalink(get_page_by_path('add-organization')) ?>" class="">Add organization</a>
                <h4 class="wrap-h4"><a href="<?php echo get_permalink(get_page_by_path('edit-organization')) ?>" class="">Edit my organizations</a>
            </div>
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