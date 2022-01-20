<?php
/**
 * Header V1 Template
 * 
 * @package Thim_Starter_Theme
 */
?>

<div class="container">
    <div class="row">
        <div class="navigation col-sm-12">
            <div class="tm-table">

                <div class="width-logo table-cell sm-logo">
                    <?php do_action( 'thim_header_logo' ); ?>
                    <?php do_action( 'thim_header_sticky_logo' ); ?>
                </div>

                <nav class="width-navigation table-cell table-right main-navigation">
                    <div class="inner-navigation">
                        <?php get_template_part( 'templates/header/main-menu' ); ?>
                    </div>
                </nav>
                <div class="menu-mobile-effect navbar-toggle" data-effect="mobile-effect">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </div>
            </div>
        </div>
    </div>
</div>