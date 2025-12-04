<?php

namespace Wpshop\AbcPagination;

use Wpshop\AbcPagination\Admin\Settings;

class Glossary {

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param Settings $settings
     */
    public function __construct( Settings $settings ) {
        $this->settings = $settings;
    }

    /**
     * @return void
     */
    public function init() {
        add_action( 'init', [ $this, '_register_post_type' ] );
        add_action( 'init', [ $this, '_setup_default_hooks' ], 9 );
    }

    /**
     * @return void
     */
    public function _setup_default_hooks() {
        add_filter( 'abc_pagination/glossary/post_type_public', function () {
            return (bool) $this->settings->get_value( 'is_public_glossary' );
        } );
    }

    /**
     * @return void
     */
    public function _register_post_type() {
        if ( ! $this->settings->get_value( 'enable_glossary' ) ) {
            return;
        }
        register_post_type(
            apply_filters( 'abc_pagination/glossary/post_type', 'glossary' ),
            apply_filters( 'abc_pagination/glossary/register_post_type_args', [
                'label'    => __( 'Glossary', 'abc-pagination' ),
                'public'   => apply_filters( 'abc_pagination/glossary/post_type_public', false ),
                'show_ui'  => true,
                'supports' => [ 'title', 'editor' ], //'author','thumbnail','excerpt'
            ] )
        );
    }
}
