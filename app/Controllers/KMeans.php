<?php

namespace App\Controllers;

class KMeans extends BaseController
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
            'title' => 'K-Means',
            'segment' => $this->urisegments
        ];
        return view('kmeans', $data);
    }
}
