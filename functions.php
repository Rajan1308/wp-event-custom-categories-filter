<?php

/*
* *******************
* Custom - Categories filter
* *******************
*/
add_filter( 'tribe-events-bar-filters', function($filters){
    $selected_category = '';
    if ( ! empty( $_REQUEST['tribe-category-search'] ) ) {
        $category = sanitize_title ($_REQUEST['tribe-category-search'] );
    } 

    $event_categories = get_categories_list();
    // echo "<pre>";
    // print_r($event_categories);
    // echo "</pre>";

    $event_categories_html = sprintf('<option value="">%s</option>', 'Select Category');
    foreach ($event_categories as $event_tax) {
      // print_r($event_tax);
        $event_categories_html .= sprintf('<option value="%2$s" %3$s>%1$s</option>',
            $event_tax, $event_tax, selected($event_tax,$selected_category, false ));
        // echo "<hr /><br />".$event_categories_html;

    }
    

    $filters['tribe-category-search'] = array(
        'name'    => 'tribe-category-search',
        'caption' => esc_html__( 'Category', 'the-events-calendar' ),
        'html'    => '<select type="text" name="tribe-category-search" id="tribe-category-search">' . $event_categories_html . '</select>',
    );

    return $filters;
}, 1, 1 );



function get_categories_list() {

    $r = wp_cache_get("get_categories_list");
    if ( $r ) {
        return $r;
    }
    $all_tags = get_terms( array(
                'taxonomy' => Tribe__Events__Main::TAXONOMY,
                'hide_empty' => false,
            ) );

    $categories_by_slug = array();

    foreach ($all_tags as $terms_ID => $term) {
      $categories_by_slug[$term->slug] = $term->name;
    }
    wp_cache_add("get_categories_list", $categories_by_slug);
    return $categories_by_slug;
}

/**
 * Filter posts by our Category
*/
add_filter('pre_get_posts', function($query) {
    
  if ( !empty( $_REQUEST['tribe-category-search'] ) ) {
    $category_slug = sanitize_title( $_REQUEST['tribe-category-search'] );
    $tax_query[] = array(
      'taxonomy'         => Tribe__Events__Main::TAXONOMY,
      'field'            => 'slug',
      'terms'            => [$category_slug],
      'include_children' => true,
      'operator'         => 'IN',
    );
    $query->set( 'tax_query', $tax_query );
  }
  return $query;
}, 55 );
