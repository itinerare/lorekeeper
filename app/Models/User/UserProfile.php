<?php

namespace App\Models\User;

use App\Models\Model;
use App\Traits\Commentable;

class UserProfile extends Model
{
    use Commentable;

    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $fillable = [
        'text', 'parsed_text',
    ];

    /**
     * The table associated with the model.
     *
     * @var array
     */
    protected $table = 'user_profiles';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    public $primaryKey = 'user_id';

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user this profile belongs to.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User');
    }
}
