<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $uri;
    protected $urisegments;

    public function __construct()
    {
        $this->uri = service('uri');
        $this->urisegments = $this->uri->getTotalSegments();
    }

    public function index()
    {
        $data = [
            'config' => config('Auth'),
            'title' => 'Dasboard',
            'segment' => $this->urisegments,
        ];
        return view('index', $data);
    }
}
