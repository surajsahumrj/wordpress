<?php
$selected_blogs = get_post_meta(get_the_ID(), '_selected_medical_blogs', true);

if (!empty($selected_blogs) && is_array($selected_blogs)) {
    echo '<div class="medical-blog-cards-container">';

    $args = array(
        'post_type'      => 'medical_blog',
        'post__in'       => $selected_blogs,
        'posts_per_page' => -1,
        'orderby'        => 'post__in', // keeps order of selected IDs
    );

    $medical_blogs_query = new WP_Query($args);

    if ($medical_blogs_query->have_posts()) {
        while ($medical_blogs_query->have_posts()) {
            $medical_blogs_query->the_post();

            $card_excerpt       = get_post_meta(get_the_ID(), '_card_excerpt', true);
            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
            ?>

            <div class="medical-blog-card" 
                 <?php if ($featured_image_url) : ?> 
                     style="background-image: url('<?php echo esc_url($featured_image_url); ?>');"
                 <?php endif; ?>>
                <div class="card-content">
                    <h3><?php the_title(); ?></h3>
                    <div class="card-excerpt-wrapper">
                        <?php if (!empty($card_excerpt)) : ?>
                            <p class="card-excerpt-text"><?php echo esc_html($card_excerpt); ?></p>
                        <?php endif; ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="read-more-btn">Read More</a>
                </div>
            </div>

            <?php
        }
        wp_reset_postdata();
    }

    echo '</div>';
}
?>
