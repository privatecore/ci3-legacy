<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['page_limits'] = [ 10, 20, 50, 70, 100 ];
$config['page_dirs'] = [ 'asc', 'desc' ];
$config['num_links'] = 2; // number of links before and after current page
$config['page_query_string'] = TRUE; // using query string instead of URI segments
$config['query_string_segment'] = 'page'; // change default 'per_page' query string to 'offset'
$config['use_page_numbers'] = TRUE;

$config['full_tag_open'] = '<ul class="pagination-custom text-center">';
$config['full_tag_close'] = '</ul>';

$config['first_link'] = '<span class="fa fa-angle-double-left"></span>';
$config['first_tag_open'] = '<li>';
$config['first_tag_close'] = '</li>';

$config['last_link'] = '<span class="fa fa-angle-double-right"></span>';
$config['last_tag_open'] = '<li>';
$config['last_tag_close'] = '</li>';

$config['prev_link'] = '<span class="fa fa-angle-left"></span>';
$config['prev_tag_open'] = '<li>';
$config['prev_tag_close'] = '</li>';

$config['next_link'] = '<span class="fa fa-angle-right"></span>';
$config['next_tag_open'] = '<li>';
$config['next_tag_close'] = '</li>';

$config['cur_tag_open'] = '<li class="active"><span>';
$config['cur_tag_close'] = '</span></li>';

$config['num_tag_open'] = '<li>';
$config['num_tag_close'] = '</li>';
