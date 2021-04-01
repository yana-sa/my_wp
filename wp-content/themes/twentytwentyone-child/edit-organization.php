<?php
/*
 * Template Name: Edit organization Page
 *
 */
edit_organization();
get_header();
?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <header class="entry-header alignwide">
            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            <?php twenty_twenty_one_post_thumbnail(); ?>
        </header>

        <div class="entry-content alignwide">
            <div class="wrap-form org-edit">
                <div class="orgform-section">
                    <h4 class="wrap-h4">Edit organization
                </div>
                <?php $org_data = edit_organization_data(); ?>
                    <form method="post" class="form-org">
                        <div class="orgform-section">
                            <label for="org_name">Organization name</label>
                            <input type="text" id="org_name" name="org_name" value="<?php echo $org_data['name']; ?>">
                        </div>
                        <div class="orgform-section">
                            <label for="org_desc">Organization description</label>
                            <input type="text" id="org_desc" name="org_desc" value="<?php echo $org_data['descr']; ?>">
                        </div>
                        <div class="orgform-section">
                            <label for="org_members">Edit members</label>
                            <div class="orgform-select"><?php
                                $users = get_users();
                                foreach ($users as $user) { ?>
                                    <input type="checkbox" name="member[]" value="<?php echo $user->ID ?>" <?php echo (in_array($user->ID, $org_data['members'][0])) ? 'checked' : '' ?>>
                                    <label for="member[]"><?php echo $user->display_name ?></label><br>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if (!empty($org_data['parent'][0])) { ?>
                            <div class="orgform-section">
                                <label>Parent organization: <b>"<?php echo get_the_title($org_data['parent'][0]) ?>"</b></label>
                            </div>
                            <?php if (get_current_user_id() == $org_data['parent_leader'][0]) {?>
                            <div class="orgform-section">
                                <label for="org_leader">Change organization leader</label>
                                <select id="org_leader" name="org_leader">
                                    <option value="<?php $org_data['leader'][0]; ?>">
                                        <?php $leader = get_userdata($org_data['leader'][0]);
                                        echo $leader->display_name; ?>
                                    </option>
                                    <?php $users = get_users(['exclude' => [$org_data['leader'][0]]]);
                                    foreach ($users as $user) { ?>
                                        <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php }
                        } ?>
                        <input type="hidden" id="org_id" name="org_id" value="<?php echo $org_data['id']; ?>">
                        <div class="orgform-section">
                            <input type="submit" id="org_edit" name="org_edit" value="Edit!">
                        </div>
                    </form>
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