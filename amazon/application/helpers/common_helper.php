<?php

function displaydashbord()
{
    $ci = get_instance();
    $data['submodule'] = 'Home';
    $data['smenu_title'] = 'Dashboard';
    $ci->load->view('include/header',$data);        
    $ci->load->view('include/left');    
    $ci->load->view('cdashbord',$data);
    $ci->load->view('include/footer');
}

function displaylogin()
{
    $ci = get_instance();
    $ci->load->view('login');
}

function pr($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}