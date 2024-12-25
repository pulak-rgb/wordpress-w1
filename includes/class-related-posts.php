<?php
/**
 * Class Related_Posts
 * Handles the logic for fetching and displaying related posts.
 */

class Related_Posts {

    // Hook into WordPress
    public function init() {
        add_action( 'the_content', array( $this, 'display_related_posts' ) );
    }

    /**
     * Display related posts under the post content
     *
     * @param string $content The post content.
     * @return string Modified content with related posts.
     */
    public function display_related_posts( $content ) {
        if ( is_single() && is_main_query() ) {
            global $post;

            // Get the categories of the current post
            $categories = get_the_category( $post->ID );
            if ( empty( $categories ) ) {
                return $content;
            }

            // Get the first category (to simplify)
            $category_id = $categories[0]->term_id;

            // Fetch related posts based on category
            $related_posts = $this->get_related_posts( $category_id );

            if ( ! empty( $related_posts ) ) {
                // Build the related posts section
                $related_posts_html = '<h3>Related Posts:</h3><ul>';

                foreach ( $related_posts as $related_post ) {
                    $related_posts_html .= $this->get_related_post_item( $related_post );
                }

                $related_posts_html .= '</ul>';

                // Append related posts below the content
                $content .= $related_posts_html;
            }
        }

        return $content;
    }

    /**
     * Get related posts based on category
     *
     * @param int $category_id The category ID.
     * @return array The related posts.
     */
    private function get_related_posts( $category_id ) {
        // Query to fetch related posts in the same category, excluding the current post
        $args = array(
            'category__in'   => array( $category_id ),
            'posts_per_page' => 5,
            'post__not_in'   => array( get_the_ID() ),
            'orderby'        => 'rand',  // Shuffle the posts
            'post_status'    => 'publish',
        );

        $related_query = new WP_Query( $args );

        return $related_query->posts;
    }

    /**
     * Generate HTML for a single related post item
     *
     * @param object $post The related post object.
     * @return string The HTML for the related post item.
     */
    private function get_related_post_item( $post ) {
        // Get the post title and permalink
        $post_title = esc_html( get_the_title( $post->ID ) );
        $post_url   = esc_url( get_permalink( $post->ID ) );

        // Get the post thumbnail (if it exists)
        $thumbnail = get_the_post_thumbnail( $post->ID, 'thumbnail' );

        if ( ! $thumbnail ) {
            // Default image if no thumbnail exists
            $thumbnail = '<img src="' . esc_url( plugin_dir_url( __FILE__ ) . 'images/default-thumbnail.jpg' ) . '" alt="No Thumbnail">';
        }

        // Build the HTML for the related post
        return sprintf(
            '<li><a href="%s">%s</a>%s</li>',
            $post_url,
            $post_title,
            $thumbnail
        );
    }
}
