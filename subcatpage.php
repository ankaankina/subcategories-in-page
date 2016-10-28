<?php
	/*
	Plugin Name: Subcategories in Page
	Description: bla-bla-bla...
	*/

function subcat_settings_init()
{
    // register a new setting for "wporg" page
    register_setting('subcat', 'subcat_options');

    // register a new section in the "wporg" page
    add_settings_section(
        'subcat_section_developers',
        __('Section.', 'subcat'),
        'subcat_section_developers_cb',
        'subcat'
    );

    // register a new field in the "wporg_section_developers" section, inside the "wporg" page
    add_settings_field(
        'subcat_field_pill', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __('Category', 'subcat'),
        'subcat_field_category_cb',
        'subcat',
        'subcat_section_developers',
        [
            'label_for'         => 'subcat_field_category',
            'class'             => 'subcat_row',
            'subcat_custom_data' => 'custom',
        ]
    );
}
add_action('admin_init', 'subcat_settings_init');
function subcat_section_developers_cb($args)
{
    ?>
    <p id="<?= esc_attr($args['id']); ?>"><?= esc_html__('Choose section.', 'subcat'); ?></p>
    <?php
}
function subcat_field_category_cb($args)
{
    // get the value of the setting we've registered with register_setting()
    $options = get_option('subcat_options');
    // output the field
    ?>
    <select id="<?= esc_attr($args['label_for']); ?>"
            data-custom="<?= esc_attr($args['subcat_custom_data']); ?>"
            name="subcat_options[<?= esc_attr($args['label_for']); ?>]"
    >
		<?php  $cat_args = array(
				'parent'				=> 0,
				'taxonomy'			=> 'category' );

			$catlist = get_categories($cat_args);
			foreach ($catlist as $categories_item) {
				?>
        <option value="<?php echo $categories_item->term_id;?>" <?php
				if ( isset($options[$args['label_for']]) && $options[$args['label_for']] == $categories_item->term_id  ) {
					?> selected=""
					<?php
				}  ?>>
            <?php echo esc_html( $categories_item->cat_name, 'subcat'); ?>
        </option>
				<?php }
				?>
    </select>
    <?php

}

 add_action( 'admin_menu', 'admin_actions' );
	function admin_actions() {
		add_options_page('Topics', 'Subcategories in Page', 'manage_options', __FILE__, 'subcatpage_admin');
	}

	function subcatpage_admin() { ?>

		<div class="wrap">
			<h1>Settings to the plugin 'Categories in Page'</h1>
			<h2><small>This plugin will output subcategories of a chosen category on the page. <br> That allows user to read posts/news which belong to a certain topic.</small></h2>
			<h3>Please, choose a category.</h3>

			 <form action="options.php" method="post">
			<?php
			settings_fields('subcat');
			do_settings_sections('subcat');
	submit_button('Save Settings');
	?>
</form>
		</div>
		<?php
		}

class subcat_widget extends WP_Widget {

	/*creating a widget*/
	function __construct() {
		parent::__construct(
			'subcat_widget',
			'Subcategories', // widget's title
			array( 'description' => 'Outputting subcategories of a category that can be chosen in Settings.' ) // widget's description
		);
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		} else {
			$title = __( 'Subcategories', 'subcat_widget_domain' );
		}
	}

	/*widget's front-end*/
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']); //apllying filters to a title (not nesessary)
        $options = get_option('subcat_options', []);
        // //$options = $options['subcat_field_category'];
        $args = [
            'type' => 'post',
            'child_of' => $options['subcat_field_category'],
            'parent' => '',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'hierarchical' => 1,
            'exclude' => '',
            'include' => '',
            'number' => '',
            'taxonomy' => 'category',
            'pad_counts' => false,
        ];

        $categories = get_categories($args);
        foreach ($categories as $subcat_item) {
            echo '<li>' . $subcat_item->cat_name . '</li>';
        };


    }

}


add_action('widgets_init', 'subcatpageWidgetInit');
function subcatpageWidgetInit() {
	register_widget('subcat_widget');
}
?>