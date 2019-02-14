<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'OwnerID',
        'OwnerName',
        'OwnerURL',
        'OwnerAvatar',
	
        'RepoID',
        'Name',
        'URL',
        'Description',
        'Stars',
        'Forks',
        'Issues',
        'CreatedDate',
        'PushedDate',
    ];



}
