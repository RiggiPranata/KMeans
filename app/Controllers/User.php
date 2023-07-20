<?php

namespace App\Controllers;

class User extends BaseController
{
    protected $uri;
    protected $urisegments;
    protected $builder;

    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
        $db      = \Config\Database::connect();
        $this->builder = $db->table('users');
    }

    public function index()
    {
        $data = [
            'config' => config('Auth'),
            'title' => 'Profile',
            'segment' => $this->urisegments,
        ];
        return view('profile', $data);
    }
}
