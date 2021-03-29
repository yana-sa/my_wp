<?php
/*
 * Template Name: Add organization Page
 *
 */
get_header();
add_new_organization();
?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header alignwide">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            <?php twenty_twenty_one_post_thumbnail(); ?>
        </header>

        <div class="entry-content alignwide">
            <div class="wrap-form">
                <form method="post" class="form-org">
                    <div class="orgform-section">
                        <label for="org_name">Organization name</label>
                        <input type="text" id="org_name" name="org_name">
                    </div>
                    <div class="orgform-section">
                        <label for="org_desc">Organization description</label>
                        <input type="text" id="org_desc" name="org_desc">
                    </div>
                    <div class="orgform-section">
                        <label for="org_members">Add members</label>
                        <div class="orgform-select">
                            <?php $users = get_users();
                            foreach ($users as $user) { ?>
                                <div class="orgform-option">
                                    <input type="checkbox" id="org_member" name="org_member[]"
                                           value="<?php echo $user->ID ?>">
                                    <label for="org_member"><?php echo $user->display_name ?></label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <details>
                        <summary><b>Click to add the organization as a subsidiary</b></summary>
                        <?php $query = new WP_Query([
                            'post_type' => 'organization',
                            'meta_key' => '_leader',
                            'meta_value' => get_current_user_id()
                        ]);
                        if ($query->have_posts()) { ?>
                            <div class="orgform-section">
                                <label for="org_parent">Select parent organization</label>
                                <select id="org_parent" name="org_parent">
                                    <option value="none">None</option>
                                    <?php
                                    while ($query->have_posts()) {
                                        $query->the_post(); ?>
                                        <option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                            <div class="orgform-section">
                                <label for="org_leader">Select organization leader</label>
                                <select id="org_leader" name="org_leader">
                                    <option value="<?php get_current_user_id(); ?>">Me</option>
                                    <?php $users = get_users(['exclude' => [get_current_user_id()]]);
                                    foreach ($users as $user) { ?>
                                        <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" id="org_leader" name="org_leader" value="<?php echo get_current_user_id(); ?>">
                        <?php } ?>
                    </details>
                    <div class="orgform-section">
                        <input type="submit" id="org_submit" name="org_submit" value="Submit!">
                    </div>
                </form>
            </div>
            <?php ?>
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