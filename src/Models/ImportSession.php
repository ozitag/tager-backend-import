<?php

namespace OZiTAG\Tager\Backend\Import\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Core\Models\TModel;

/**
 * Class ImportSession
 * @package OZiTAG\Tager\Backend\Import\Models
 *
 * @property integer $id
 * @property string $status
 * @property string $strategy
 * @property string $error
 * @property string $file_id
 * @property string $created_at
 * @property string $started_at
 * @property string $completed_at
 *
 * @property File $file
 */
class ImportSession extends TModel
{
    public $timestamps = false;

    static $defaultOrder = 'created_at DESC';

    protected $table = 'tager_import_sessions';

    protected $fillable = [
        'status', 'strategy', 'message', 'file_id',
        'created_at', 'started_at', 'completed_at'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
