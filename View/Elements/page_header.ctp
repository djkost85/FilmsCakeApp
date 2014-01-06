<?php
/**
 *  Element: pageHeader
 *
 *  Shared page header accross all pages for this app
 */
?>
    <h1>
        <?php echo $title; ?>
    </h1>

    <?php
        // display login form if not logged in, or info about the user if logged in
        if ( AuthComponent::user('id') )
        {
            echo $this->element('login_box_info' );
        }
        else
        {
            echo $this->element('login_box_form' );
        }
    ?>