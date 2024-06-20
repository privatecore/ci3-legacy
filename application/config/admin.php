<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['pagination']['num_links'] = 3; // number of links before and after current page
$config['pagination']['page_query_string'] = TRUE; // using query string instead of URI segments
$config['pagination']['query_string_segment'] = 'offset'; // change default 'per_page' query string to 'offset'
$config['pagination']['use_page_numbers'] = FALSE;

$config['pagination']['full_tag_open'] = '<ul class="pagination pagination-sm no-margin">';
$config['pagination']['full_tag_close'] = '</ul>';

$config['pagination']['cur_tag_open'] = '<li class="active"><a>';
$config['pagination']['cur_tag_close'] = '</a></li>';

$config['pagination']['limit_tag_open'] = '<select id="limit" class="form-control" onchange="location = this.value;">';
$config['pagination']['limit_tag_close'] = '</select>';

$config['pagination']['order_tag_open'] = '<select id="order" class="form-control" onchange="location = this.value;">';
$config['pagination']['order_tag_close'] = '</select>';
