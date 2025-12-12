<?php
/**
 * Custom Comments Template for Business Profile Reviews
 * 
 * @package Business Showcase & Networking Hub
 */

if ( post_password_required() ) {
    return;
}

$rating_summary = business_showcase_get_rating_summary( get_the_ID() );
?>

<div id="reviews" class="business-reviews-wrapper">
    
    <!-- Rating Summary -->
    <?php if ( $rating_summary['count'] > 0 ) : ?>
        <div class="reviews-summary">
            <div class="summary-rating">
                <div class="summary-score">
                    <span class="score-number"><?php echo esc_html( number_format( $rating_summary['average'], 1 ) ); ?></span>
                    <span class="score-max">/5</span>
                </div>
                <div class="summary-stars">
                    <?php echo business_showcase_display_star_rating( $rating_summary['average'] ); ?>
                </div>
                <div class="summary-count">
                    <?php 
                    printf(
                        esc_html( _n( 'Based on %d review', 'Based on %d reviews', $rating_summary['count'], 'business-showcase-networking-hub' ) ),
                        $rating_summary['count']
                    );
                    ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Reviews List -->
    <?php if ( have_comments() ) : ?>
        <div class="reviews-list-container">
            <h3 class="reviews-title">
                <?php
                $comments_number = get_comments_number();
                printf(
                    esc_html( _n( '%d Review', '%d Reviews', $comments_number, 'business-showcase-networking-hub' ) ),
                    number_format_i18n( $comments_number )
                );
                ?>
            </h3>
            
            <ol class="reviews-list">
                <?php
                wp_list_comments( array(
                    'callback' => 'business_showcase_custom_comment',
                    'style'    => 'ol',
                    'type'     => 'comment',
                ) );
                ?>
            </ol>
            
            <?php
            // Comment pagination
            if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
            ?>
                <nav class="comment-navigation">
                    <div class="nav-previous">
                        <?php previous_comments_link( __( '← Older Reviews', 'business-showcase-networking-hub' ) ); ?>
                    </div>
                    <div class="nav-next">
                        <?php next_comments_link( __( 'Newer Reviews →', 'business-showcase-networking-hub' ) ); ?>
                    </div>
                </nav>
            <?php endif; ?>
            
        </div>
    <?php endif; ?>
    
    <!-- Review Form -->
    <?php if ( comments_open() ) : ?>
        <div class="review-form-container">
            <h3 class="review-form-title">
                <?php esc_html_e( 'Write a Review', 'business-showcase-networking-hub' ); ?>
            </h3>
            
            <?php
            $commenter = wp_get_current_commenter();
            $req = get_option( 'require_name_email' );
            $aria_req = ( $req ? " aria-required='true'" : '' );
            
            $fields = array(
                'author' => '<div class="comment-form-author">' .
                            '<label for="author">' . esc_html__( 'Name', 'business-showcase-networking-hub' ) . 
                            ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
                            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . 
                            '" size="30"' . $aria_req . ' /></div>',
                
                'email'  => '<div class="comment-form-email">' .
                            '<label for="email">' . esc_html__( 'Email', 'business-showcase-networking-hub' ) . 
                            ( $req ? ' <span class="required">*</span>' : '' ) . '</label>' .
                            '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . 
                            '" size="30"' . $aria_req . ' /></div>',
                
                'url'    => '<div class="comment-form-url">' .
                            '<label for="url">' . esc_html__( 'Website', 'business-showcase-networking-hub' ) . '</label>' .
                            '<input id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) . 
                            '" size="30" /></div>',
            );
            
            $args = array(
                'fields'               => $fields,
                'comment_field'        => '<div class="comment-form-comment">' .
                                          '<label for="comment">' . esc_html__( 'Your Review', 'business-showcase-networking-hub' ) . 
                                          ' <span class="required">*</span></label>' .
                                          '<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>' .
                                          '</div>',
                'title_reply'          => '',
                'title_reply_to'       => esc_html__( 'Reply to %s', 'business-showcase-networking-hub' ),
                'cancel_reply_link'    => esc_html__( 'Cancel Reply', 'business-showcase-networking-hub' ),
                'label_submit'         => esc_html__( 'Submit Review', 'business-showcase-networking-hub' ),
                'class_submit'         => 'submit btn-submit-review',
                'submit_button'        => '<button type="submit" name="%1$s" id="%2$s" class="%3$s">%4$s</button>',
                'comment_notes_before' => '',
                'comment_notes_after'  => '',
            );
            
            comment_form( $args );
            ?>
        </div>
    <?php else : ?>
        <p class="reviews-closed">
            <?php esc_html_e( 'Reviews are closed for this business.', 'business-showcase-networking-hub' ); ?>
        </p>
    <?php endif; ?>
    
</div>
