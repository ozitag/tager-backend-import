<?php

namespace OZiTAG\Tager\Backend\Import\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Core\Http\FormRequest;
use OZiTAG\Tager\Backend\Crud\Requests\CrudFormRequest;

/**
 * Class ImportStoreRequest
 * @package OZiTAG\Tager\Backend\Import\Requests
 *
 * @property string $strategy
 * @property string $file
 */
class ImportStoreRequest extends CrudFormRequest
{
    public function fileScenarios()
    {
        return [
            'file' => config('tager-import.fileScenario')
        ];
    }

    public function rules()
    {
        return [
            'strategy' => 'required|string',
            'file' => ['required', new FileRule()]
        ];
    }
}
